<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use App\Models\Region;
use App\Models\Setting;
use App\Models\BusinessUnit;
use App\Models\DailyCampaign;
use Illuminate\Console\Command;
use App\Models\ScheduleJobAudit;
use App\Models\DailyCampaignView;
use Illuminate\Support\Facades\DB;
use App\Models\DailyCampaignSummary;
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

        try {
            
            $this->task = ScheduleJobAudit::Create([
                'job_name' => $this->signature,
                'start_time' => Carbon::now(),
                'status' => 'Processing',
            ]);

            $this->LogMessage( now() );
            $this->LogMessage("Task -- Update Daily Campaign Statistics and stored in the daily_campaigns table'");
            $this->storeDailyCampaign();
            $this->LogMessage( now() );

        } catch (\Exception $ex) {

            $this->info( $ex );

            // log message in system
            if ($this->task) {
                $this->task->status = 'Error';
                $this->task->end_time = Carbon::now();
                $this->task->message .= $ex->getMessage() . PHP_EOL;
                $this->task->save();
            }
          
            // send out email notification
            $notify = new \App\MicrosoftGraph\SendEmailNotification();
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

    protected function storeDailyCampaign() 
    {

        $as_of_date = today()->format('Y-m-d');
        $campaign_year = Setting::challenge_page_campaign_year();
        
        $this->LogMessage("(This process run for Campaign Year : " . $campaign_year . ")");
        $this->LogMessage("");

        $setting = Setting::first();
        $challenge_finalize = DailyCampaignSummary::where('campaign_year', $campaign_year)
                                        ->where('as_of_date', '>=', $setting->challenge_final_date)
                                        ->first();

        $campaign_finalize = DailyCampaignSummary::where('campaign_year', $campaign_year)
                                        ->where('as_of_date', '>=', $setting->campaign_final_date)
                                        ->first();


        if ((today() >= $setting->campaign_start_date && today() <= $setting->campaign_end_date) ||
            (today() >= $setting->challenge_start_date && today() <= $setting->challenge_end_date) ||
            (today() >= $setting->campaign_final_date && (!($campaign_finalize))) ||
            (today() >= $setting->challenge_final_date && (!($challenge_finalize)))
            ) {

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

            // Step 1A
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
                $campaign_year,
                $prior_year,
            ];

            $sql = DailyCampaignView::dynamicSqlForChallengePage();     // Shared sql
            $challenges = DB::select($sql, $parameters);

            $donor_count = 0;
            $total_dollar = 0;

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
                    // 'rank' => $row->rank,

                    'eligible_employee_count' => $row->ee_count,
                    'donors'  => $row->donors,
                    "dollars" => $row->dollars,
                ]);

                $donor_count += $row->donors;
                $total_dollar += $row->dollars;    

            }                          
            $this->LogMessage('Total rows created count : ' . sizeof($challenges)  );

            // Step 1B -- Update Daily Campaign Summary 
            $this->LogMessage("");
            $this->LogMessage("Updating daily campaign summary for campaign year " . $campaign_year); 
            $this->LogMessage("                 Donor Count  : " . $donor_count); 
            $this->LogMessage("                 Total Amount : " . $total_dollar); 
            
            DailyCampaignSummary::updateOrCreate([
                    'campaign_year' => $campaign_year,
            ],[
                'as_of_date' => $as_of_date,
                
                'donors' => $donor_count,
                'dollars' => $total_dollar,

                'created_by_id' => null,
                'updated_by_id' => null,
            ]);

            if (today() >= $setting->challenge_final_date && (!($challenge_finalize))) {

                // Step 1C -- Update Challenge History when final date reached
                $this->LogMessage("");
                $this->LogMessage("Updating Historical challenge page for campaign year " . $campaign_year); 

                // $this->LogMessage("                 Donor Count  : " . $donor_count); 
                // $this->LogMessage("                 Total Amount : " . $total_dollar); 

                $rows = DailyCampaign::where('campaign_year', $campaign_year)
                                ->where('as_of_date', $as_of_date)
                                ->where('daily_type', 0)
                                ->get();

                // Clean up Old data 
                HistoricalChallengePage::where('year', $campaign_year)->delete();

                $donor_count = 0;
                $total_dollar = 0;

                foreach ($rows as $row) {
                    HistoricalChallengePage::create([
                        'business_unit_code' => $row->business_unit,
                        'organization_name' => $row->business_unit_name,
                        'participation_rate' => $row->participation_rate,
                        'previous_participation_rate' => $row->previous_participation_rate,
                        'change' => $row->change_rate, 
                        'donors' => $row->donors,
                        'dollars' => $row->dollars,
                        'year' => $row->campaign_year, 
                    ]);

                    $donor_count += $row->donors;
                    $total_dollar += $row->dollars;
                }

                $this->LogMessage('Total rows created/updated count : ' . sizeof($rows)  );

               
            }

            // Step 2
            $this->LogMessage("");
            $this->LogMessage("Updating daily campaign by regions");
            $group_by_org_region = Region::leftJoin('daily_campaign_view', 'daily_campaign_view.tgb_reg_district', 'regions.code') 
                            ->where( function($query) use($campaign_year) {
                                    $query->where('daily_campaign_view.campaign_year', $campaign_year)
                                          ->orWhereNull('daily_campaign_view.campaign_year');
                            }) 
                            ->select(
                                DB::raw('regions.code as code'), 
                                DB::raw('regions.name as name'),
                                DB::raw("SUM(daily_campaign_view.donors) as donors"),
                                DB::raw("SUM(daily_campaign_view.dollars) as dollars"),
                                'daily_campaign_view.campaign_year',
                                // 'daily_campaign_view.organization_code',
                            )
                            ->groupBy('regions.code', 'regions.name', 'daily_campaign_view.campaign_year')
                            ->orderBy('regions.code')
                            ->get();

            foreach( $group_by_org_region as $row)  {

                $ee_count = 0;
                if ($row->code) {
                    $ee_count = EligibleEmployeeDetail::where('year', $campaign_year)
                                    ->where('as_of_date', '=', function ($query) use($as_of_date, $campaign_year) {
                                        $query->selectRaw('max(as_of_date)')
                                              ->from('eligible_employee_details as E1')
                                              ->where('E1.year', $campaign_year)
                                              ->where('E1.as_of_date', '<=', $as_of_date );
                                    })
                                    ->where('tgb_reg_district', $row->code)
                                    ->count();
                }

                DailyCampaign::create([
                    "campaign_year" => $campaign_year,
                    "as_of_date" => $as_of_date,
                    "daily_type" => 1,          // Region
                    'business_unit' => null, 
                    'business_unit_name' => null,
                    'region_code' => $row->code, 
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

            // Only generate between campaign start and end date range 
            if (today() <= $setting->campaign_final_date) {
                // Step 3
                $this->LogMessage("");
                $this->LogMessage("Updating daily campaign by departments");
                $group_by_org_dept = BusinessUnit::leftJoin('daily_campaign_view', 'daily_campaign_view.business_unit_code', 'business_units.code') 
                                ->where( function($query) use($campaign_year) {
                                        $query->where('daily_campaign_view.campaign_year', $campaign_year)
                                            ->orWhereNull('daily_campaign_view.campaign_year');
                                }) 
                                ->select(
                                    'business_units.code', 
                                    'business_units.name',
                                    'daily_campaign_view.deptid', 
                                    'daily_campaign_view.dept_name', 
                                    DB::raw("SUM(daily_campaign_view.donors) as donors"),
                                    DB::raw("SUM(daily_campaign_view.dollars) as dollars"),
                                    'daily_campaign_view.campaign_year',
                                )
                                ->groupBy('business_units.code', 'business_units.name', 'deptid', 'dept_name',
                                        'daily_campaign_view.campaign_year')
                                ->orderBy('business_units.code')
                                ->orderBy('deptid')
                                ->orderBy('dept_name')
                                ->get();

                foreach( $group_by_org_dept as $row)  {

                    $ee_count = 0;
                    if ($row->code) {
                        $ee_count = EligibleEmployeeDetail::where('year', $campaign_year)
                                        ->where('as_of_date', '=', function ($query) use($as_of_date, $campaign_year) {
                                            $query->selectRaw('max(as_of_date)')
                                                ->from('eligible_employee_details as E1')
                                                ->where('E1.year', $campaign_year)
                                                ->where('E1.as_of_date', '<=', $as_of_date );
                                        })
                                        ->where('business_unit', $row->code)
                                        ->where('deptid', $row->deptid)
                                        ->count();
                    }

                    DailyCampaign::create([
                        "campaign_year" => $campaign_year,
                        "as_of_date" => $as_of_date,
                        "daily_type" => 2,          // Department
                        'business_unit' => $row->code,
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
            }

        } else {

            $this->LogMessage( "The Current setting for daily campaign update : " );   
            $this->LogMessage( "Campaign Start Date : " . $setting->campaign_start_date->format('Y-m-d') );   
            $this->LogMessage( "Campaign End Date   : " . $setting->campaign_end_date->format('Y-m-d') );   
            $this->LogMessage( "Campaign Final Date   : " . $setting->campaign_final_date->format('Y-m-d') );   
            $this->LogMessage( "" );   
            $this->LogMessage( "The Current setting for challenge update : " );   
            $this->LogMessage( "Challenge Start Date : " . $setting->challenge_start_date->format('Y-m-d') );   
            $this->LogMessage( "Challenge End Date   : " . $setting->challenge_end_date->format('Y-m-d') );   
            $this->LogMessage( "Challenge Final Date   : " . $setting->challenge_final_date->format('Y-m-d') );   
            $this->LogMessage( "" );   

            $this->LogMessage( "No daily campaign will be captured for today " . today()->format('Y-m-d') );   
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
