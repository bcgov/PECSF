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

    /* Source Type is HCM */
    protected const SOURCE_TYPE = 'HCM';
    protected $created_count;
    protected $updated_count;
    protected $locked_count;
    protected $message;
    

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->created_count = 0;
        $this->updated_count = 0;
        $this->locked_count = 0;
        $this->message = '';

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
            'status' => 'Initiated',
        ]);


        $this->info("Update/Create - User Profile");
        $this->SyncUserProfile();
        $this->info( now() );        

        $this->message .= 'Total new created row(s) : ' . $this->created_count . PHP_EOL;
        $this->message .= 'Total Updated row(s) : ' . $this->updated_count . PHP_EOL;
        $this->message .= 'Total locked row(s) : ' . $this->locked_count . PHP_EOL;

        echo  $this->message;        
        
        // Update the Task Audit log
        $task->end_time = Carbon::now();
        $task->status = 'Completed';
        $task->message = $this->message;
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

        $sql = EmployeeJob::where('guid', '!=', '')
            // ->whereNotIn('email', ['', ' '])
            // ->where(function ($query) use ($last_start_time) {
            //     $query->where('date_updated', '>=', $last_start_time)
            //         ->orWhere('date_deleted', '>=', $last_start_time);
            // })
            //         ->orWhere('date_deleted', '>=', $last_start_time);
            ->whereNull('date_deleted')
            ->orderBy('guid','asc')                // 
            ->orderBy('job_indicator','desc')      // Job indicator -- Secondary, then Primary 
            ->select(['id', 'emplid', 'empl_rcd', 'email', 'guid', 'idir', 
                'first_name', 'last_name', 'name', 'appointment_status', 'empl_ctg', 'job_indicator',
                'date_updated', 'date_deleted']);


        // Step 1 : Create and Update User Profile
        $this->info( now() );
        $this->info('Step 1 - Create or Update User Profile' );

        $organization = Organization::where('code', 'GOV')->first();
        $password = Hash::make(env('SYNC_USER_PROFILE_SECRET'));

        $sql->chunk(1000, function($chuck) use($new_sync_at, $organization, $password, &$n) {

            $this->info( "batch (1000) - " . ++$n );

            // foreach ($employees as $employee) {
            foreach($chuck as $employee) {

                $target_email = trim($employee->guid . '@gov');

                // Check the user by GUID 
                $user = User::where(function ($query) {
                                $query->where('source_type', self::SOURCE_TYPE)
                                    ->orWhereNull('source_type');
                            })
                            ->where('id','>',999)
                            ->where('guid', $employee->guid)
                            ->first();
            
                if ($user) {

                    $acctlock = $employee->date_deleted ? true : false;

                    if ( (strtolower(trim($user->idir)) == strtolower(trim($employee->idir))) and
                         (trim($user->email) == $target_email ) and 
                         ($user->source_type == self::SOURCE_TYPE) and   
                         ($user->acctlock == $acctlock)  
                        ) {
                            // reach here mean No Differece found -- no action required
                    } else {                

                        try {
                            $user->source_type = self::SOURCE_TYPE;
                            $user->email  = $target_email;
                            $user->idir = $employee->idir;
                            $user->last_sync_at = $new_sync_at;
                            $user->save();

                            $this->updated_count += 1;

                        } catch(\Illuminate\Database\QueryException $ex){ 

                            $this->message .= 'Exception -- ' . $ex->getMessage() . PHP_EOL;
                            // Note any method of class PDOException can be called on $ex.
                        }

                    }

                } else {

                        $user = User::updateOrCreate([
                            'email' => $target_email,     // key
                        ],[ 
                            'name' => $employee->first_name . ' ' . $employee->last_name,
                            'guid' => $employee->guid,
                            'idir' => $employee->idir,
                            'source_type' => self::SOURCE_TYPE,    
                            'password' => $password,
                            'acctlock' => false,
                            'last_sync_at' => $new_sync_at,
                            'organization_id' => $organization->id,
                            'employee_job_id' => $employee->id,
                            'emplid' => $employee->emplid,
                        ]);

                        $this->created_count += 1;

                }
            
            }

        });


        // Step 2 : Lock Inactivate User account
        $this->info( now() );        
        $this->info('Step 2 - Lock Out Inactivate User account');

        $users = User::where(function ($query) {
                            $query->where('source_type', self::SOURCE_TYPE)
                                ->orWhereNull('source_type');
                        })
                    ->where('id','>',999)
                    ->where('organization_id', $organization->id)
                    ->where('acctlock', 0)
                    ->where(function ($query) {
                          $query->whereExists(function ($query) {
                                    $query->select(DB::raw(1))
                                        ->from('employee_jobs')
                                        ->whereColumn('employee_jobs.guid', 'users.guid')
                                        ->whereNotNull('date_deleted');
                                })
                                ->orWhereNotExists(function ($query) {
                                    $query->select(DB::raw(1))
                                        ->from('employee_jobs')
                                        ->whereColumn('employee_jobs.guid', 'users.guid');
                                });
                    })
                    ->get();

        //$users->update(['acctlock'=>true, 'last_sync_at' => $new_sync_at]);
        foreach( $users as $user) {

            // make sure no other job record is not deleted yet
            $unlocked_user = EmployeeJob::where('guid', $user->guid)
                                ->whereNull('date_deleted')
                                ->first();

            if (!($unlocked_user)) {
                $user->acctlock = true;
                $user->last_sync_at = $new_sync_at;
                $user->save();

                $this->locked_count += 1;
            }

        }

    }

}
