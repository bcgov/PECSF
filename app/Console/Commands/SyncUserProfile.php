<?php

namespace App\Console\Commands;

use DateTime;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\EmployeeJob;
use App\Models\Organization;
use App\Models\JobSchedAudit;
use Illuminate\Console\Command;
use App\Models\ScheduleJobAudit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
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

    const MAX_CREATE_COUNT = 1000;
    const MAX_UPDATE_COUNT = 5000;

    /* Source Type is HCM */
    protected const SOURCE_TYPE = 'HCM';
    protected $task;
    protected $created_count;
    protected $updated_count;
    protected $locked_count;
    protected $message;
    protected $status;

    protected $last_refresh_time;
    

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
        $this->status = 'Completed';

        $this->last_refresh_time = time();

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        try {

            $this->task = ScheduleJobAudit::Create([
                'job_name' => $this->signature,
                'start_time' => Carbon::now(),
                'status' => 'Processing',
            ]);

            $this->LogMessage( now() );        
            $this->LogMessage("Update/Create - User Profile");
            $this->SyncUserProfile();
            $this->LogMessage( now() );     
            
            if  ($this->created_count > self::MAX_CREATE_COUNT)  {
                $this->LogMessage( '' );
                $this->LogMessage( '*NOTE: more than ' . self::MAX_CREATE_COUNT . ' new row found, only the first ' . self::MAX_CREATE_COUNT . ' lines detail were shown in the log');
                $this->LogMessage( '' );
            }

            if  ($this->updated_count > self::MAX_UPDATE_COUNT)  {
                $this->LogMessage( '' );
                $this->LogMessage( '*NOTE: more than ' . self::MAX_UPDATE_COUNT . ' changes found, only the first ' . self::MAX_UPDATE_COUNT . ' lines detail were shown in the log');
                $this->LogMessage( '' );
            }

        } catch (\Exception $ex) {

            // log message in system
            if ($this->task) {
                $this->task->status = 'Error';
                $this->task->end_time = Carbon::now();
                $this->task->message .= $ex->getMessage() . PHP_EOL;
                $this->task->save();
            }

            // send out email notification
            $notify = new \App\SharedLibraries\SendEmailNotification();
            $notify->job_id =  $this->task ? $this->task->id : null;
            $notify->job_name =  $this->signature;
            $notify->error_message = $ex->getMessage();
            $notify->send(); 

            // write message to the log  
            throw new Exception($ex);

        }

        $this->LogMessage( 'Total new created row(s) : ' . $this->created_count );
        $this->LogMessage( '' );
        $this->LogMessage( 'Total Updated row(s) : ' . $this->updated_count );
        $this->LogMessage( 'Total locked row(s) : ' . $this->locked_count );

        // Update the Task Audit log
        $this->task->end_time = Carbon::now();
        $this->task->status = $this->status;
        $this->task->message = $this->message;
        $this->task->save();

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
            ->where(function ($query)  {
                $query->whereNull('date_deleted')
                      ->orWhereBetween('date_deleted',['2021-09-01','2022-12-31']);    // To create 2022 donatiion in Greenfield for testing purpose (User Profile required)
            })
            ->orderBy('guid','asc')                // 
            ->orderBy('job_indicator','desc')      // Job indicator -- Secondary, then Primary 
            ->select(['id', 'emplid', 'empl_rcd', 'email', 'guid', 'idir', 
                'first_name', 'last_name', 'name', 'appointment_status', 'empl_ctg', 'job_indicator',
                'date_updated', 'date_deleted']);

        // Step 1 : Create and Update User Profile
        $this->LogMessage( now() );
        $this->LogMessage('Step 1 - Create or Update User Profile' );

        $organization = Organization::where('code', 'GOV')->first();
        $password = Hash::make(env('SYNC_USER_PROFILE_SECRET'));

        $prev_guid = '';

        $sql->chunk(1000, function($chuck) use($new_sync_at, $organization, $password, &$n, &$prev_guid) {

            $this->LogMessage( "batch (1000) - " . ++$n );

            // foreach ($employees as $employee) {
            foreach($chuck as $employee) {

                if ($prev_guid == $employee->guid) {
                    continue;
                }

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

                    $acctlock = $user->acctlock;
                    if (App::environment('prod')) {
                        $acctlock = $employee->date_deleted ? 1 : 0;
                    }

                    if ( (strtolower(trim($user->idir)) == strtolower(trim($employee->idir))) &&
                         (trim($user->name) == ($employee->first_name . ' ' . $employee->last_name)) &&
                         (trim($user->email) == $target_email ) && 
                         ($user->source_type == self::SOURCE_TYPE) &&   
                         ($user->emplid == $employee->emplid) &&
                         ($user->acctlock == $acctlock)  
                        ) {
                            // reach here mean No Differece found -- no action required
                    } else {                

                            $user->source_type = self::SOURCE_TYPE;
                            $user->emplid = $employee->emplid;
                            $user->email  = $target_email;
                            $user->idir = $employee->idir;
                            $user->name = $employee->first_name . ' ' . $employee->last_name;
                            $user->last_sync_at = $new_sync_at;
                            $user->acctlock = $acctlock;  
                            $user->save();

                            $this->updated_count += 1;

                            if  ($this->updated_count <= self::MAX_UPDATE_COUNT)  {
                                $this->LogMessage('(UPDATED) => emplid | ' . $user->emplid . ' | ' . $user->id . ' | ' . $user->name . ' | ' . $user->guid . ' | ' . $user->source_type . ' | ' . $user->email  . ' | ' . $user->idir );
                                $changes = $user->getChanges();
                                unset($changes["updated_at"]);
                                $this->LogMessage('  summary => '. json_encode( $changes ) );
                            }

                    }

                } else {

                        // Always lock users on lower regions
                        $acctlock = 1;
                        if (App::environment('prod')) {
                            $acctlock = $employee->date_deleted ? 1 : 0;
                        }

                        $user = User::updateOrCreate([
                            'email' => $target_email,     // key
                        ],[ 
                            'name' => $employee->first_name . ' ' . $employee->last_name,
                            'guid' => $employee->guid,
                            'idir' => $employee->idir,
                            'source_type' => self::SOURCE_TYPE,    
                            'password' => $password,
                            'acctlock' => $acctlock,
                            'last_sync_at' => $new_sync_at,
                            'organization_id' => $organization->id,
                            'emplid' => $employee->emplid,
                        ]);

                        $this->created_count += 1;

                        if  ($this->created_count <= self::MAX_CREATE_COUNT)  {
                            $this->LogMessage( '(CREATED) => id | ' . $user->id . ' | ' . $user->name . ' | ' . $user->guid . ' | ' . $user->source_type . ' | ' . $user->email  . ' | ' . $user->idir );
                        }

                }
            
                //
                $prev_guid = $employee->guid;

            }

        });


        // Step 2 : Lock Inactivate User account
        $this->LogMessage( now() );        
        $this->LogMessage('Step 2 - Lock Out Inactivate User account');

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
                $user->acctlock = 1;
                $user->last_sync_at = $new_sync_at;
                $user->save();

                $this->locked_count += 1;

                $this->LogMessage('(LOCKED) => - id | ' . $user->id . ' | ' . $user->name . ' | ' . $user->guid . ' | ' . $user->source_type . ' | ' . $user->email  . ' | ' . $user->idir );
                $changes = $user->getChanges();
                unset($changes["updated_at"]);
                $this->LogMessage('  summary => '. json_encode( $changes ) );
            }

        }

    }

    protected function LogMessage($text) 
    {

        $this->info( $text );

        // write to log message 
        $this->message .= $text . PHP_EOL;

        if (time() - $this->last_refresh_time > 5) {
            $this->task->message = $this->message;
            // $this->task->save();
    
            $this->last_refresh_time = time();
        }

    }

}
