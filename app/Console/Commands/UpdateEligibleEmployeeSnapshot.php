<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use App\Models\City;
use App\Models\Setting;
use App\Models\EmployeeJob;
use App\Models\BusinessUnit;
use App\Models\Organization;
use Illuminate\Console\Command;
use App\Models\ScheduleJobAudit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use App\Models\EligibleEmployeeByBU;
use App\Models\EligibleEmployeeDetail;
use App\Jobs\OrgPartipationTrackersExportJob;

class UpdateEligibleEmployeeSnapshot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "command:UpdateEligibleEmployeeSnapshot {--date=}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To take a snapshot of the Eligible Employee, optional --date YYYY-MM-DD to collect for the current as specified date';

    protected $task;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->message = '';
        $this->status = 'Completed';
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->task = ScheduleJobAudit::Create([
            'job_name' => $this->signature,
            'start_time' => Carbon::now(),
            'status' => 'Processing',
        ]);

        try {

            $this->LogMessage( now() );
            $this->LogMessage("Task -- Capture a snapshot of Eligible Employee with region, business unit, department");
            $this->storeEligibleEmployeeDetail();
            $this->LogMessage( now() );

            $this->LogMessage( now() );
            $this->LogMessage("Task -- Generate the Organization Participation Tractor Report at the designated time");
            $this->generateOrgPartipationTractorReport();
            $this->LogMessage( now() );

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
        
        // Update the Task Audit log
        $this->task->end_time = Carbon::now();
        $this->task->status = $this->status;
        $this->task->message = $this->message;
        $this->task->save();
    
        return 0;
    }


    protected function StoreEligibleEmployeeDetail() 
    {

        $setting = Setting::first();

        // $as_of_date = today();
        $as_of_date = $this->option('date') ? Carbon::parse($this->option('date')) : today();
        
        $year = $as_of_date->year;

        $this->LogMessage( "" );   
        $this->LogMessage( "Note: The business rule for collecting the eligible employee data on Sep 1st and Oct 15th every year" );   
        $this->LogMessage( "" );   
        $this->LogMessage( "As of date           : " . $as_of_date->format('Y-m-d') );           
        $this->LogMessage( "" );   

        // Important Note: Only collect the eligible employee on Sep 1 and Oct 15 yearly.

        if ( $as_of_date && 
                ( $this->option('date')  ||
                 ($as_of_date->month ==  9 && $as_of_date->day ==  1) ||
                 ($as_of_date->month == 10 && $as_of_date->day == 15)
                ) ) {

            $sql = EmployeeJob::where( function($query) {
                                $query->where('employee_jobs.empl_rcd', '=', function($q) {
                                        $q->from('employee_jobs as J2') 
                                            ->whereColumn('J2.emplid', 'employee_jobs.emplid')
                                            ->whereNull('date_deleted')
                                            ->where('J2.empl_status', 'A')
                                            ->selectRaw('min(J2.empl_rcd)');
                                    })
                                    ->orWhereNull('employee_jobs.empl_rcd');
                            })
                            ->where('employee_jobs.empl_status', 'A')
                            ->whereNull('date_deleted');

            $n = 0;
            $row_count = 0;

            // $all_bus = BusinessUnit::current()->orderBy('name')->pluck('name','code');

            $sql->chunk(1000, function($chuck) use($as_of_date, $year, &$row_count, &$n) {
                $this->LogMessage( "Capture snapshot of eligible employees batch (1000) - " . ++$n );

                if ($n == 1) {
                    EligibleEmployeeDetail::where('year', $year)
                        ->where('as_of_date', $as_of_date )
                        ->delete();   
                }

                foreach($chuck as $row)  {

                    // Special Rule -- To split GCPE employees from business unit BC022 
                    $bu = BusinessUnit::where('code', $row->business_unit)->with('organization')->first();
                    $business_unit_code = $bu->linked_bu_code;
                    if ($row->business_unit == 'BC022' && str_starts_with($row->dept_name, 'GCPE')) {
                        $business_unit_code  = 'BGCPE';
                    }
                    $linked_bu = BusinessUnit::where('code', $business_unit_code)->first();
                    
                    $city = City::where('city', trim( $row->office_city )  )->first();
                    $tgb_reg_district = $city ? $city->TGB_REG_DISTRICT : $row->tgb_reg_district;

                    $row_count++;

                    EligibleEmployeeDetail::create([
                        'year' => $year,
                        'as_of_date' => $as_of_date,

                        'organization_code' => 'GOV',
                        'emplid' => $row->emplid,
                        'empl_status' => $row->empl_status,
                        'name' => $row->name, 

                        'business_unit' => $business_unit_code,
                        'business_unit_name' => $linked_bu ? $linked_bu->name : null,

                        'deptid' => $row->deptid,
                        'dept_name' => $row->dept_name,

                        'tgb_reg_district' => $tgb_reg_district,
                        
                        'office_address1' => $row->office_address1,
                        'office_address2' => $row->office_address2,
                        'office_city' => $row->office_city,
                        'office_stateprovince' => $row->office_stateprovince,
                        'office_postal' => $row->office_postal,
                        'office_country' => $row->office_country,

                        'organization_name' => $row->organization_name,

                        'employee_job_id' => $row->id,

                    ]);
                }

            });  

            // Store latest as a summary for online page performance 
            $this->LogMessage("Task -- Year EE summary by business_unit");
            $this->storeEligibleEmployeeSummary($year, $as_of_date);
            
            $this->LogMessage('');
            $this->LogMessage('Total detail row created count : ' . $row_count  );

        } else {

            $this->LogMessage( "" );   
            $this->LogMessage( "No eligible employee data will be captured for today " . today()->format('Y-m-d') );   
            $this->LogMessage( "" );   
            
        }

    }

    protected function storeEligibleEmployeeSummary($campaign_year, $as_of_date) 
    {
        $eff_rec = EligibleEmployeeDetail::where('year', $campaign_year)
                                        ->where('as_of_date', '<=', $as_of_date )
                                        ->selectRaw('max(as_of_date) as eff_date' )
                                        ->distinct()
                                        ->first();
        $eff_date = $eff_rec ? $eff_rec->eff_date : null;

        $group_by_bu = EligibleEmployeeDetail::where('year', $campaign_year)
                            ->where('as_of_date', $eff_date )
                            ->select( 'year', 'business_unit', 'business_unit_name', 'organization_code',
                                        DB::raw("count(*) as ee_count"),       
                            )
                            ->groupBy('organization_code', 'business_unit', 'business_unit_name')
                            ->orderBy('organization_code')
                            ->orderBy('business_unit')
                            ->get();

        // dd( $group_by_bu->toSql(), $group_by_bu->count() );
            
        EligibleEmployeeByBU::where('campaign_year', $campaign_year )
                            ->where('organization_code', 'GOV')
                            ->delete();

        foreach($group_by_bu as $row ) {

            EligibleEmployeeByBU::create([
                'campaign_year' => $row->year,
                'as_of_date' => $as_of_date->format('Y-m-d'),
                'organization_code' => $row->organization_code,

                'business_unit_code' => $row->business_unit,
                'business_unit_name' => $row->business_unit_name,
                'ee_count' => $row->ee_count,

            ]);
        }

        $business_units = BusinessUnit::whereNotExists(function ($query) use ($campaign_year, $as_of_date) {
                                $query->select(DB::raw(1))
                                    ->from('eligible_employee_by_bus')
                                    ->whereColumn('eligible_employee_by_bus.business_unit_code', 'business_units.code');
                        })
                        ->where('status', 'A')
                        ->with('organization')
                        ->get();

        foreach($business_units as $row ) {

            EligibleEmployeeByBU::create([
                'campaign_year' => $campaign_year,
                'as_of_date' => $as_of_date->format('Y-m-d'),
                'organization_code' => $row->organization ? $row->organization->code : 'GOV',

                'business_unit_code' => $row->code,
                'business_unit_name' => $row->name,
                'ee_count' => 0,

            ]);

        }

    }

    protected function generateOrgPartipationTractorReport() {
        // See the function export2csv in OrgPartipationTractorReportController (should be same logic and criteria) 

        $setting = Setting::first();

        // $as_of_date = today();
        $as_of_date = $this->option('date') ? Carbon::parse($this->option('date')) : today();
        
        $year = $as_of_date->year;

        $this->LogMessage( "" );   
        $this->LogMessage( "Note: The business rule for generating the Organization Participation Tractor Report on Sep 1st and Oct 15th every year" );   
        $this->LogMessage( "" );   
        $this->LogMessage( "As of date           : " . $as_of_date->format('Y-m-d') );           
        $this->LogMessage( "" );   

        if ( $as_of_date && 
                ( $this->option('date')  ||
                ($as_of_date->month ==  9 && $as_of_date->day ==  1) ||
                ($as_of_date->month == 10 && $as_of_date->day == 15)
                ) ) {

            $campaign_year = today()->year;

            $EE_BUs = EligibleEmployeeByBU::where('organization_code', 'GOV')
                                ->where('campaign_year', $campaign_year)
                                ->where('ee_count', '>', 0)
                                ->orderBy('business_unit_code')->get();

            $submitted_at = now();

            foreach($EE_BUs as $index => $row) {

                // For Testing purpose
                // if ($row->business_unit_code <> 'BC003') {
                //     continue;
                // }

                $as_of_date = $row->as_of_date;
                $bu = $row->business_unit_code;

                $filters = [];
                $filters['as_of_date'] = $row->as_of_date;
                $filters['business_unit_code'] = $bu;
                $filters['year'] = $row->campaign_year;
                $filters['title'] = $row->business_unit_name . ' ('  . $row->business_unit_code . ')';

                $filename = 'OrgPartipationTracker_'.  $row->campaign_year . '_' . $bu . '_' . $as_of_date->format('Y-m-d') .".xlsx";

                // Submit a Job
                $history = \App\Models\ProcessHistory::create([
                    'batch_id' => 0,
                    'process_name' => 'OrgPartipationTractor',
                    'parameters' => json_encode( $filters ),
                    'status'  => 'Queued',
                    'submitted_at' => $submitted_at,
                    'original_filename' => $filename,
                    'filename' => $filename,
                    'total_count' => 0,
                    'done_count' => 0,
                    'created_by_id' => 999,
                    'updated_by_id' => 999,
                ]);
        
                // Submit a job 
                $batch = Bus::batch([
                    new OrgPartipationTrackersExportJob($history->id, $filename, $filters),
                ])->dispatch();

                // dd ($batch->id);
                $history->batch_id = $batch->id;
                $history->save();

                $this->LogMessage( ($index + 1) . " Generate Organization Partipation Tractor Report for business unit " . $bu );

            }

        } else {

            $this->LogMessage( "" );   
            $this->LogMessage( "No Organization Partipation Tractor Report will be processed for today " . today()->format('Y-m-d') );   
            $this->LogMessage( "" );   

        }

    }

    protected function LogMessage($text)
    {

        $this->info( $text );

        // write to log message
        $this->message .= $text . PHP_EOL;

        $this->task->message = $this->message;
        // $this->task->save();

    }

}
