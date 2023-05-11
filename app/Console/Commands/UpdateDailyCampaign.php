<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Setting;
use App\Models\DailyCampaign;
use Illuminate\Console\Command;
use App\Models\ScheduleJobAudit;
use App\Models\DailyCampaignView;
use Illuminate\Support\Facades\DB;
use App\Models\EligibleEmployeeDetail;
use App\Models\HistoricalChallengePage;

class UpdateDailyCampaign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:UpdateDailyCampaign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Daily Campaign Statistics and stored in the daily_campaigns table';

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

        $this->LogMessage( now() );
        $this->LogMessage("Task -- Update Daily Campaign Statistics and stored in the daily_campaigns table'");
        $this->storeDailyCampaign();
        $this->LogMessage( now() );

        // Update the Task Audit log
        $this->task->end_time = Carbon::now();
        $this->task->status = $this->status;
        $this->task->message = $this->message;
        $this->task->save();
    
        return 0;
    }

    protected function storeDailyCampaign() 
    {

        $setting = Setting::first();

        if (today() >= $setting->campaign_start_date && today() <= $setting->campaign_end_date) {

            $as_of_date = today()->format('Y-m-d');
            $campaign_year = today()->year;

            // check eligible employee data for the current campiagn year, run the process if required. 
            $ee_count = EligibleEmployeeDetail::where('year', $campaign_year)->count();
            if ($ee_count == 0) {
                $this->LogMessage("===============================");
                $this->LogMessage("Warning: No Eligible Employee Detail data created for campaign year " . $campaign_year);
                $this->LogMessage("         start to run the process command:TakeEligibleEmployeeSnapshot for capture eligible Employee Snapshot on ") . $as_of_date;
                $this->LogMessage("");                
                $this->call('command:UpdateEligibleEmployeeSnapshot');
                $this->LogMessage("===============================") ;   
                $this->LogMessage( "" );   
            }
    
            $this->LogMessage("Campaign Year : " . $campaign_year);
            $this->LogMessage("As of Date    : " . $as_of_date);
            $this->LogMessage("");

            // Clear Up the old data if exists
            DailyCampaign::where('campaign_year', $campaign_year)
                         ->where('as_of_date', $as_of_date)
                         ->delete();

            // Step 1
            $this->LogMessage("Updating daily campaign by business units");

            $campaign_year = today()->year;

            $history = HistoricalChallengePage::select('year')
                                    ->where('year', '<', $campaign_year)
                                    ->orderBy('year', 'desc')
                                    ->first(); 
    
            $prior_year = $history ? $history->year : $campaign_year - 1;        

            $parameters = [
                $campaign_year,
                $campaign_year,
                $prior_year,
                $campaign_year,
                $prior_year,
                $campaign_year,
                $campaign_year,
                $campaign_year,
            ];

            $sql = DailyCampaignView::dynamicSqlForChallengePage();     // Shared sql
            $challenges = DB::select($sql, $parameters);

            foreach( $challenges as $row)  {

                DailyCampaign::create([
                    "campaign_year" => $campaign_year,
                    "as_of_date" => $as_of_date,
                    "daily_type" => 0,      // Business Unit (Oragnization)
                    'business_unit' => $row->business_unit_code,
                    'business_unit_name' => $row->organization_name,
                    'region_code' => null,
                    'region_name' => null,
                    'deptid' => null,
                    'dept_name' => null,

                    'participation_rate' => round($row->participation_rate,2),
                    'previous_participation_rate' => $row->previous_participation_rate,
                    'change_rate' => round($row->change_rate,2),

                    'eligible_employee_count' => $row->ee_count,
                    'donors'  => $row->donors,
                    "dollars" => $row->dollars,
                ]);
            }                          
            $this->LogMessage('Total rows created count : ' . sizeof($challenges)  );

            // Step 2
            $this->LogMessage("");
            $this->LogMessage("Updating daily campaign by regions");
            $group_by_org_region = DailyCampaignView::where('campaign_year', $campaign_year)
                            ->leftJoin('regions', 'daily_campaign_view.tgb_reg_district', 'regions.code') 
                            ->select(
                                'daily_campaign_view.tgb_reg_district', 
                                'regions.name',
                                DB::raw("SUM(daily_campaign_view.donors) as donors"),
                                DB::raw("SUM(daily_campaign_view.dollars) as dollars"),
                                'daily_campaign_view.campaign_year',
                            )
                            ->groupBy('tgb_reg_district')
                            ->orderBy('tgb_reg_district')
                            ->get();

            foreach( $group_by_org_region as $row)  {

                $ee_count = 0;
                if ($row->organization_code == 'GOV') {
                    $ee_count = EligibleEmployeeDetail::where('year', $campaign_year)
                                    ->where('as_of_date', '=', function ($query) use($as_of_date, $campaign_year) {
                                        $query->selectRaw('max(as_of_date)')
                                              ->from('eligible_employee_details as E1')
                                              ->where('E1.year', $campaign_year)
                                              ->where('E1.as_of_date', '<=', $as_of_date );
                                    })
                                    ->where('tgb_reg_district', $row->tgb_reg_district)
                                    ->count();
                }

                DailyCampaign::create([
                    "campaign_year" => $campaign_year,
                    "as_of_date" => $as_of_date,
                    "daily_type" => 1,          // Region
                    'business_unit' => null, 
                    'business_unit_name' => null,
                    'region_code' => $row->tgb_reg_district, 
                    'region_name' => $row->name, 
                    'deptid' => null,
                    'dept_name' => null,

                    'participation_rate' => null,
                    'previous_participation_rate' => null,
                    'change_rate' => null,

                    'eligible_employee_count' => $ee_count,
                    'donors'  => $row->donors,
                    "dollars" => $row->dollars,
                ]);
            }   
            $this->LogMessage('Total rows created count : ' . $group_by_org_region->count()  );


            // Step 3
            $this->LogMessage("");
            $this->LogMessage("Updating daily campaign by departments");
            $group_by_org_dept = DailyCampaignView::where('campaign_year', $campaign_year)
                            ->leftJoin('business_units', 'daily_campaign_view.business_unit_code', 'business_units.code') 
                            ->select(
                                'daily_campaign_view.business_unit_code', 
                                'business_units.name',
                                'daily_campaign_view.deptid', 
                                'daily_campaign_view.dept_name', 
                                DB::raw("SUM(daily_campaign_view.donors) as donors"),
                                DB::raw("SUM(daily_campaign_view.dollars) as dollars"),
                                'daily_campaign_view.campaign_year',
                            )
                            ->groupBy('business_unit_code', 'deptid', 'dept_name')
                            ->orderBy('business_unit_code')
                            ->orderBy('deptid')
                            ->orderBy('dept_name')
                            ->get();

            foreach( $group_by_org_dept as $row)  {

                $ee_count = 0;
                if ($row->organization_code == 'GOV') {
                    $ee_count = EligibleEmployeeDetail::where('year', $campaign_year)
                                    ->where('as_of_date', '=', function ($query) use($as_of_date) {
                                        $query->selectRaw('max(as_of_date)')
                                              ->from('eligible_employee_details as E1')
                                              ->whereColumn('E1.year', 'eligible_employee_details.year')
                                              ->where('E1.as_of_date', '<=', $as_of_date );
                                    })
                                    ->where('business_unit', $row->business_unit_code)
                                    ->where('deptid', $row->deptid)
                                    ->count();
                }

                DailyCampaign::create([
                    "campaign_year" => $campaign_year,
                    "as_of_date" => $as_of_date,
                    "daily_type" => 2,          // Department
                    'business_unit' => $row->business_unit_code,
                    'business_unit_name' => $row->name,
                    'region_code' => null,
                    'region_name' => null,
                    'deptid' => $row->deptid, 
                    'dept_name' => $row->dept_name, 

                    'participation_rate' => null,
                    'previous_participation_rate' => null,
                    'change_rate' => null,

                    'eligible_employee_count' => $ee_count,
                    'donors'  => $row->donors,
                    "dollars" => $row->dollars,
                ]);
            }   
            $this->LogMessage('Total rows created count : ' . $group_by_org_dept->count()  );

        } else {

            $this->LogMessage( "The Current setting for daily campaign update " );   
            $this->LogMessage( "Campaign Start Date : " . $setting->campaign_start_date );   
            $this->LogMessage( "Campaign End Date   : " . $setting->campaign_end_date );   
            $this->LogMessage( "" );   
            $this->LogMessage( "No daily campaign will be captured for today " . today() );   

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
