<?php

namespace App\Console\Commands;

use DateTime;
use Carbon\Carbon;
use App\Models\User;
use App\Models\EmployeeJob;
use App\Models\Organization;
use App\Models\JobSchedAudit;
use Illuminate\Console\Command;
use App\Models\ScheduleJobAudit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class SyncUserProfile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:SyncUserProfile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Or Create User Profile';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $task = ScheduleJobAudit::Create([
            'job_name' => $this->signature,
            'start_time' => Carbon::now(),
            'status','Initiated'
        ]);


        $this->info("Update/Create - User Profile");
        $this->SyncUserProfile();
        $this->info( now() );        

        // Update the Task Audit log
        $task->end_time = Carbon::now();
        $task->status = 'Completed';
        $task->save();

        return 0;

    }

    protected function SyncUserProfile()
    {

         // Get the latest success job's start time 
        // $last_job = ScheduleJobAudit::where('job_name', $this->signature)
        //                 ->where('status','Completed')
        //                 ->orderBy('end_time', 'desc')->first();
        // $last_start_time = $last_job ? $last_job->start_time : '2000-01-01' ; 

        $new_sync_at = Carbon::now();

        $employees = EmployeeJob::whereNotIn('guid', ['', ' '])
            ->whereNotIn('email', ['', ' '])
            // ->where(function ($query) use ($last_start_time) {
            //     $query->where('date_updated', '>=', $last_start_time)
            //         ->orWhere('date_deleted', '>=', $last_start_time);
            // })
            //->whereNotNull('date_updated')
            //->whereIn('employee_id',['105823', '060061', '107653',
            //'115637','131116','139238','145894','146113','152843','152921','163102'] )
            ->orderBy('emplid')
            ->orderBy('job_indicator','desc')      // Secondary, then Primary 
            ->orderBy('empl_rcd')
            ->get(['id', 'emplid', 'empl_rcd', 'email', 'guid', 'idir', 
                'first_name', 'last_name', 'name', 'appointment_status', 'empl_ctg', 'job_indicator',
                'date_updated', 'date_deleted']);


        // Step 1 : Create and Update User Profile
        $this->info( now() );
        $this->info('Step 1 - Create and Update User Profile' );

        $organization = Organization::where('code', 'GOV')->first();
        $password = Hash::make(env('SYNC_USER_PROFILE_SECRET'));
        foreach ($employees as $employee) {

            // Check the user by GUID 
            $user = User::where('guid', $employee->guid)->first();
        
            if ($user) {
                if ($employee->email) {
                    if (!(strtolower(trim($user->email)) == strtolower(trim($employee->email))) )  {
                        $this->info('Step 1: User ' . $employee->email . ' - ' . 
                                $employee->guid . ' has difference email address with same GUID.');
                    }

                    $acctlock = $employee->date_deleted ? true : false;

                    if (!($user->acctlock == $acctlock and 
                          $user->emplid == $employee->emplid and
                          $user->employee_job_id = $employee->id)) {

                        $user->acctlock = $acctlock;
                        $user->employee_job_id = $employee->id;
                        $user->emplid = $employee->emplid;
                        $user->last_sync_at = $new_sync_at;
                        $user->save();
                    }
                }
            } else {

                $user = User::whereRaw("lower(email) = '". strtolower(addslashes($employee->email))."'") 
                            ->first();
                                                      
                if ($user) {
                    if ( strtolower(trim($user->email)) == strtolower(trim($employee->email)) &&
                            (!($user->guid)) )  {
                        $user->guid = $employee->guid;
                        //$user->reporting_to = $reporting_to;
                        $user->acctlock = $employee->date_deleted ? true : false;
                        $user->last_sync_at = $new_sync_at;
                        $user->organization_id = $organization->id;
                        $user->employee_job_id = $employee->id;
                        $user->emplid = $employee->emplid;
                        $user->save();
                    }

                } else {
                    $user = User::create([
                        'guid' => $employee->guid,
                        'name' => $employee->first_name . ' ' . $employee->last_name,
                        'email' => $employee->email,
                        'password' => $password,
                        'acctlock' => $employee->date_deleted ? true : false,
                        'last_sync_at' => $new_sync_at,
                        'organization_id' => $organization->id,
                        'employee_job_id' => $employee->id,
                        'emplid' => $employee->emplid,

                    ]);
                }

            }
        
        }

        // Step 2 : Lock Inactivate User account
        $this->info( now() );        
        $this->info('Step 2 - Lock Out Inactivate User account');

        $users = User::where('organization_id', $organization->id)
                    ->where('acctlock', 0)
                    ->whereExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('employee_jobs')
                            ->whereColumn('employee_jobs.guid', 'users.guid')
                            ->whereNotNull('date_deleted');
                            ;
                    })->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('employee_jobs')
                            ->whereColumn('employee_jobs.guid', 'users.guid')
                            ->whereNull('date_deleted');
                    })->get();

        //$users->update(['acctlock'=>true, 'last_sync_at' => $new_sync_at]);
        foreach( $users as $user) {
            $user->acctlock = true;
            $user->last_sync_at = $new_sync_at;
            $user->save();
        }


    }

}
