<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\EmployeeJob;
use App\Models\PledgeHistory;
use Illuminate\Console\Command;
use App\Models\ScheduleJobAudit;
use Illuminate\Support\Facades\Http;

class ImportEmployeeJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ImportEmployeeJob';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import the Employee Information from BI';

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

        $this->info( now() );     
        $this->info("Update/Create - Employee Job Information");
        $this->UpdateEmployeeJob();
        $this->info( now() );     

        // Update the Task Audit log
        $task->end_time = Carbon::now();
        $task->status = 'Completed';
        $task->save();

        return 0;

    }

    protected function UpdateEmployeeJob() 
    {

        // Get the latest success job's start time 
        $last_job = ScheduleJobAudit::where('job_name', $this->signature)
            ->where('status','Completed')
            ->orderBy('end_time', 'desc')->first();
        $last_start_time = $last_job ? $last_job->start_time : '2000-01-01' ; 

        $filter = 'date_updated gt \''.$last_start_time.'\' or date_deleted gt \''.$last_start_time.'\'';

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
            ->get(env('ODS_INBOUND_REPORT_EMPLOYEE_DEMO_BI_ENDPOINT').'?$count=true&$top=1'.'&$filter='.$filter);
                
        $row_count = json_decode($response->body())->{'@odata.count'};
        
        $size = 10000;
        for ($i = 0; $i <= $row_count / $size ; $i++) {

            $top  = $size;
            $skip = $size * $i;

            // Loading pledge history data
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
                ->get(env('ODS_INBOUND_REPORT_EMPLOYEE_DEMO_BI_ENDPOINT') .'?$top='.$top.'&$skip='.$skip.'&$filter='.$filter) ;

            $this->info( 'Total Count = '. $row_count .' $i = '. $i .' $top = '. $top .' $skip '. $skip);
            // Loading pledge history data
            // $response = Http::withHeaders(['Content-Type' => 'application/json'])
            //     ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
            //     ->get(env('ODS_INBOUND_REPORT_PLEDGE_HISTORY_BI_ENDPOINT'));

            if ($response->successful()) {
                $data = json_decode($response->body())->value; 
                $batches = array_chunk($data, 1000);

                $organization = \App\Models\Organization::where('code', 'GOV')->first();
                $business_units = \App\Models\BusinessUnit::pluck('id','code')->toArray();
                $regions = \App\Models\Region::pluck('id','code')->toArray();

                foreach ($batches as $key => $batch) {
                    $this->info( '    -- each batch (1000) $key - '. $key );
                    foreach ($batch as $row) {

                        // $regional_district = RegionalDistrict::where('tgb_reg_district', $row->tgb_reg_district)->first();
                        // $business_unit = \App\Models\BusinessUnit::where('code', $row->BUSINESS_UNIT)->first();
                        // $region = \App\Models\Region::where('code', $row->tgb_reg_district)->first(); 
                        
                        EmployeeJob::updateOrCreate([
                            'emplid' => $row->EMPLID,
                            'empl_rcd' => $row->EMPL_RCD,
                        ],[                            
                            'organization_id' => $organization ? $organization->id : null,
                            'effdt' => $row->EFFDT,
                            'effseq' => $row->EFFSEQ,
                            'empl_status' => $row->EMPL_STATUS,
                            'empl_ctg' => $row->EMPL_CTG,
                            'empl_class' => $row->EMPL_CLASS,
                            'job_indicator' => $row->job_indicator,
                            'position_number' => $row->position_number,
                            'position_title' => $row->position_title,
                            'appointment_status' => $row->appointment_status,
                            'first_name' => $row->first_name,
                            'last_name' => $row->last_name,
                            'name' => $row->name,
                            'email' => $row->email,
                            'guid' => $row->GUID,
                            'idir' => $row->IDIR,

                            'business_unit' => $row->BUSINESS_UNIT,
                            'business_unit_id' => array_key_exists( $row->BUSINESS_UNIT , $business_units) ? $business_units[$row->BUSINESS_UNIT] : null,
                            'deptid' => $row->DEPTID,
                            'dept_name' => $row->dept_name,
                            'tgb_reg_district' => $row->tgb_reg_district,
                            'region_id' => array_key_exists( $row->tgb_reg_district , $regions) ? $regions[$row->tgb_reg_district] : null,
                            'city' => $row->city,
                            'stateprovince' => $row->stateprovince,
                            'country' => $row->country,

                            'organization' => $row->Organization,
                            'level1_program' => $row->level1_program,
                            'level2_division' => $row->level2_division,
                            'level3_branch' => $row->level3_branch,
                            'level4' => $row->level4,
                            'supervisor_emplid' => $row->supervisor_emplid,
                            'supervisor_name' => $row->supervisor_name,
                            'supervisor_email' => $row->supervisor_email,
                            'date_updated' => $row->date_updated,
                            'date_deleted' => $row->date_deleted,
                          
                            'created_by_id' => null,
                            'updated_by_id' => null,

                        ]);
                    }
                }
            } else {
                $this->info( $response->status() );
                $this->info( $response->body() );
            }

        }
    }



}
