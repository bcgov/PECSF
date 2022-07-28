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

    /* attributes for share in the command */
    protected $total_count;
    protected $created_count;
    protected $updated_count;
    protected $message;
    protected $status;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->total_count = 0;
        $this->created_count = 0;
        $this->updated_count = 0;
        $this->message = '';
        $this->status = 'Completed';

        $this->normal_run = true; 

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ini_set('memory_limit', '4096M');

        if (EmployeeJob::count() < 20000) {
            $this->normal_run = false;    // mean first load
        }

        $this->task = ScheduleJobAudit::Create([
            'job_name' => $this->signature,
            'start_time' => Carbon::now(),
            'status' => 'Processing',
        ]);

        $this->LogMessage( now() );
        $this->LogMessage("Update/Create - Employee Job Information");
        $this->UpdateEmployeeJob();
        $this->LogMessage( now() );

        $this->LogMessage( 'Total Row count     : ' . $this->total_count  );
        $this->LogMessage( '' );
        $this->LogMessage( 'Total Created count : ' . $this->created_count  );
        $this->LogMessage( 'Total Updated count : ' . $this->updated_count  );

        $this->task->end_time = Carbon::now();
        $this->task->status = $this->status;
        $this->task->message = $this->message;
        $this->task->save();

        return 0;

    }

    protected function UpdateEmployeeJob()
    {

        // Get the latest success job's start time
        $last_job = ScheduleJobAudit::where('job_name', $this->signature)
            ->where('status','Completed')
            ->orderBy('end_time', 'desc')->first();
        $last_start_time = $last_job ? $last_job->start_time : '2000-01-01' ;

        //$filter = 'date_updated gt \''.$last_start_time.'\' or date_deleted gt \''.$last_start_time.'\'';
        $filter = "";  // Disabled the filter due to process timimg issue
        $orderBy = 'EMPLID asc, EMPL_RCD asc, EFFDT desc, EFFSEQ desc, date_updated desc';

        try {
            // Validate the value...
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
                ->get(env('ODS_INBOUND_REPORT_EMPLOYEE_DEMO_BI_ENDPOINT').'?$count=true&$top=1'.
                                '&$filter='.$filter.'&$orderBy='.$orderBy);
        } catch (\Exception $ex) {

            // write to log message 
            $this->status = 'Error';
            $this->LogMessage( $ex->getMessage() );

            return 1;
        }

        $row_count = json_decode($response->body())->{'@odata.count'};
        $this->total_count = $row_count;

        $organization = \App\Models\Organization::where('code', 'GOV')->first();
        $business_units = \App\Models\BusinessUnit::pluck('id','code')->toArray();
        $regions = \App\Models\Region::pluck('id','code')->toArray();

        $size = 1000;

        // This is the previous $row for comparison purpose
        $last_row = new \stdClass;
        $last_row->EMPLID = null;
        $last_row->EMPL_RCD = null;

        for ($i = 0; $i <= $row_count / $size ; $i++) {

            $top  = $size;
            $skip = $size * $i;

            try {
                // Loading pledge history data
                $response = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
                    ->get(env('ODS_INBOUND_REPORT_EMPLOYEE_DEMO_BI_ENDPOINT') .'?$top='.$top.'&$skip='.$skip.
                                    '&$filter='.$filter.'&$orderBy='.$orderBy) ;

                $this->LogMessage( 'Total Count = '. $row_count .' $i = '. $i .' $top = '. $top .' $skip '. $skip);
                // Loading pledge history data
                // $response = Http::withHeaders(['Content-Type' => 'application/json'])
                //     ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
                //     ->get(env('ODS_INBOUND_REPORT_PLEDGE_HISTORY_BI_ENDPOINT'));
                                    
                if ($response->successful()) {
                    $data = json_decode($response->body())->value;
                    $batches = array_chunk($data, $size);

                    foreach ($batches as $key => $batch) {
                        $this->LogMessage( '    -- each batch ('.$size.') $key - '. $key );

                        foreach ($batch as $row) {

                            // $regional_district = RegionalDistrict::where('tgb_reg_district', $row->tgb_reg_district)->first();
                            // $business_unit = \App\Models\BusinessUnit::where('code', $row->BUSINESS_UNIT)->first();
                            // $region = \App\Models\Region::where('code', $row->tgb_reg_district)->first();

                            if (trim($row->EMPLID) == trim($last_row->EMPLID) && trim($row->EMPL_RCD) == trim($last_row->EMPL_RCD)) {
                                // SKIP when the same EMPLID and EMPL_RCD 
                                // echo 'skip = ' . $row->EMPLID . ' - ' . $row->EMPL_RCD . '|'.  $last_row->EMPLID . ' - ' . $last_row->EMPL_RCD .PHP_EOL;
                                continue;
                            }

                            $job = EmployeeJob::updateOrCreate([
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
                                'guid' => trim($row->GUID),
                                'idir' => trim($row->IDIR),

                                'business_unit' => $row->BUSINESS_UNIT,
                                'business_unit_id' => array_key_exists( $row->BUSINESS_UNIT , $business_units) ? $business_units[$row->BUSINESS_UNIT] : null,
                                'deptid' => $row->DEPTID,
                                'dept_name' => $row->dept_name,
                                'tgb_reg_district' => $row->tgb_reg_district,
                                'region_id' => array_key_exists( $row->tgb_reg_district , $regions) ? $regions[$row->tgb_reg_district] : null,
                                'city' => $row->city,
                                'stateprovince' => $row->stateprovince,
                                'country' => $row->country,

                                'organization' => trim($row->Organization),
                                'level1_program' => trim($row->level1_program),
                                'level2_division' => trim($row->level2_division),
                                'level3_branch' => trim($row->level3_branch),
                                'level4' => trim($row->level4),
                                'supervisor_emplid' => $row->supervisor_emplid,
                                'supervisor_name' => $row->supervisor_name,
                                'supervisor_email' => $row->supervisor_email,
                                'date_updated' => $row->date_updated ? (substr($row->date_updated,0,10).' '.substr($row->date_updated,11,8)) : null,
                                'date_deleted' => $row->date_deleted ? (substr($row->date_deleted,0,10).' '.substr($row->date_deleted,11,8)) : null,

                                'created_by_id' => null,
                                'updated_by_id' => null,

                            ]);

                            if ($job->wasRecentlyCreated) {

                                if ($this->normal_run) {
                                    $this->LogMessage('(CREATED) => emplid | ' . $job->emplid . ' | ' . $job->empl_rcd . ' | ' . $job->guid . ' | ' . $job->idir );
                                }                                    

                                $this->created_count += 1;

                            } elseif ($job->wasChanged() ) {

                                $this->LogMessage('(UPDATED) => emplid | ' . $job->emplid . ' | ' . $job->empl_rcd . ' | ' . $job->guid . ' | ' . $job->idir );
                                $changes = $job->getChanges();
                                unset($changes["updated_at"]);
                                $this->LogMessage('  summary => '. json_encode( $changes ) );

                                $this->updated_count += 1;
                            } else {
                                // No Action
                            }

                            // Keep the previous role
                            $last_row = $row;
                        }
                    }
                    
                } else {

                    // write to log message 
                    $this->status = 'Error';
                    $this->LogMessage( 'Status: ' . $response->status() . ' Response Body: ' .  $response->body() );

                }

            } catch (\Exception $ex) {

                // write to log message 
                $this->status = 'Error';
                $this->LogMessage( $ex->getMessage() );

                return 1;
            }

        }
    }

    protected function LogMessage($text) 
    {

        $this->info( $text );

        // write to log message 
        $this->message .= $text . PHP_EOL;

        $this->task->message = $this->message;
        $this->task->save();
        
    }

}
