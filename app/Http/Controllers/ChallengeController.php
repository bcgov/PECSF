<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Setting;
use App\Models\CampaignYear;
use Illuminate\Http\Request;
use App\Models\DailyCampaign;

use Yajra\Datatables\Datatables;
use App\Models\DailyCampaignView;
use Illuminate\Support\Facades\DB;
use App\Models\DailyCampaignSummary;
use App\Models\EligibleEmployeeByBU;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\HistoricalChallengePage;
use Illuminate\Support\Facades\Storage;
use App\Exports\DailyCampaignByBUExport;
use App\Exports\DailyCampaignByDeptExport;
use App\Exports\DailyCampaignByRegionExport;
use App\Exports\OrgPartipationTrackersExport;

class ChallengeController extends Controller
{

    public function __construct()
    {
         if(empty(Auth::id())){
             redirect("/login");
         }
    }

    public function index(Request $request) {

        $setting = Setting::first();

        $current_campaign_year = Setting::challenge_page_campaign_year();
        $campaign_year = $request->year ? $request->year : $current_campaign_year;

        session()->flash('_old_input.year', $campaign_year);

        $history = HistoricalChallengePage::select('year')
                                ->where('year', '<', $campaign_year)
                                ->orderBy('year', 'desc')
                                ->first(); 

        $prior_year = $history ? $history->year : $campaign_year - 1;                                

        $as_of_date = null;
        $dollar_total = 0;
        $donor_count = 0;

        $summary = DailyCampaignSummary::where('campaign_year', $campaign_year)->first(); 

        if ($summary) {
            $as_of_day = $summary->as_of_date;
        } else {
            $as_of_day = DailyCampaign::where('campaign_year', $campaign_year)
                            ->where('daily_type', 0)
                            ->where('as_of_date', '<=', $setting->challenge_end_date )
                            ->max('as_of_date');
        }
       
        // if (today() >= $setting->challenge_end_date &&
        //       ($setting->challenge_final_date == $setting->challenge_processed_final_date)) {
        //     $as_of_day = $setting->challenge_final_date;
        // }

        $finalized = false;
        $final_row = HistoricalChallengePage::select('year')
                                ->where('year', $current_campaign_year)
                                ->first(); 
        if ($final_row && $as_of_day >= $setting->challenge_final_date) {
            $finalized = true;
        }

        if($request->ajax()) {

            if (!($finalized)) {
            // if ($as_of_day != $setting->challenge_final_date ) {

                // Use Dynamic data during the challenge period
                // if ( today() >= $setting->challenge_start_date && today() < $setting->challenge_end_date ) {

                //         $parameters = [
                //             $campaign_year,
                //             $campaign_year,
                //             $prior_year,
                //             $campaign_year,
                //             $prior_year,
                //             $campaign_year,
                //             $campaign_year,
                //             $campaign_year,
                //             $campaign_year,
                //             $campaign_year,
                //             $campaign_year,
                //             $prior_year,
                //         ];

                //         $sql = <<<SQL
                //             select 1 as current, business_unit_code, organization_name,
                //                     -- 0 as participation_rate, 
                //                     case when (select ee_count from eligible_employee_by_bus where eligible_employee_by_bus.campaign_year = ?
                //                         and eligible_employee_by_bus.organization_code = 'GOV' 
                //                         and eligible_employee_by_bus.business_unit_code = A.business_unit_code
                //                         ) > 0 then 
                //                             A.donors / (select ee_count from eligible_employee_by_bus where eligible_employee_by_bus.campaign_year = ?
                //                                 and eligible_employee_by_bus.organization_code = 'GOV' 
                //                                 and eligible_employee_by_bus.business_unit_code = A.business_unit_code
                //                             ) * 100 
                //                         else 0 end as participation_rate,
                //                     -- 0 as previous_participation_rate, 
                //                     (select participation_rate from historical_challenge_pages where year = ?
                //                         -- and historical_challenge_pages.organization_name = A.organization_name
                //                         and historical_challenge_pages.business_unit_code = A.business_unit_code
                //                     ) as previous_participation_rate,
                //                     (A.donors / (select ee_count from eligible_employee_by_bus where eligible_employee_by_bus.campaign_year = ?
                //                         and eligible_employee_by_bus.organization_code = 'GOV' 
                //                         and eligible_employee_by_bus.business_unit_code = A.business_unit_code
                //                     ) * 100) - COALESCE((select participation_rate from historical_challenge_pages where year = ?
                //                         -- and historical_challenge_pages.organization_name = A.organization_name
                //                         and historical_challenge_pages.business_unit_code = A.business_unit_code
                //                         ),0)
                //                     as 'change_rate', 
                //                     A.donors, A.dollars, (@row_number:=@row_number + 1) AS rank
                //                     ,(select ee_count from eligible_employee_by_bus where eligible_employee_by_bus.campaign_year = ?
                //                         and eligible_employee_by_bus.organization_code = 'GOV' 
                //                         and eligible_employee_by_bus.business_unit_code = A.business_unit_code
                //                     ) as ee_count
                //             from 
                //                 (select business_units.code as business_unit_code, name as organization_name, sum(donors) as donors, sum(dollars) as dollars 
                //                 from business_units  
                //                 left outer join daily_campaign_view on business_units.code = daily_campaign_view.business_unit_code
                //                 where (daily_campaign_view.campaign_year = ? or daily_campaign_view.campaign_year is null) 
                //                 group by business_units.code, name
                //                 order by sum(donors) desc) 
                //                 as A, (SELECT @row_number:=0) AS temp
                //             where 1 = 1
                //               and ((select ee_count from eligible_employee_by_bus where eligible_employee_by_bus.campaign_year = ?
                //                                 and eligible_employee_by_bus.organization_code = 'GOV' 
                //                                 and eligible_employee_by_bus.business_unit_code = A.business_unit_code
                //                       ) is not null)
                //               and ((select ee_count from eligible_employee_by_bus where eligible_employee_by_bus.campaign_year = ?
                //                                 and eligible_employee_by_bus.organization_code = 'GOV' 
                //                                 and eligible_employee_by_bus.business_unit_code = A.business_unit_code
                //                       ) >= 5)
                //             order by A.donors / (select ee_count from eligible_employee_by_bus where eligible_employee_by_bus.campaign_year = ?
                //                                 and eligible_employee_by_bus.organization_code = 'GOV' 
                //                                 and eligible_employee_by_bus.business_unit_code = A.business_unit_code
                //                             ) * 100 desc, 
                //                             abs(A.donors / (select ee_count from eligible_employee_by_bus where eligible_employee_by_bus.campaign_year = ?
                //                         and eligible_employee_by_bus.organization_code = 'GOV' 
                //                         and eligible_employee_by_bus.business_unit_code = A.business_unit_code
                //                     ) * 100) - COALESCE((select participation_rate from historical_challenge_pages where year = ?
                //                         -- and historical_challenge_pages.organization_name = A.organization_name
                //                         and historical_challenge_pages.business_unit_code = A.business_unit_code
                //                         ),0)
                                        
                //         SQL;

                //         $challenges = DB::select($sql, $parameters);

                // } else {

                        $parameters = [
                            $campaign_year,
                            $as_of_day,
                        ];

                        $sql = <<<SQL
                            select 0 as current, business_unit_name as organization_name, participation_rate, previous_participation_rate, change_rate, 
                                        donors, dollars, (@row_number:=@row_number + 1) AS rank,
                                        eligible_employee_count as ee_count
                            from daily_campaigns, (SELECT @row_number:=0) AS temp
                            where campaign_year = ?
                        
                            and as_of_date = ?
                            and daily_type = 0     
                            and donors >= 5
                            and participation_rate > 0
                            order by participation_rate desc, abs(change_rate);     
                        SQL;

                        // $sql = <<<SQL
                        //     select 0 as current, business_unit_name as organization_name, participation_rate, previous_participation_rate, change_rate, 
                        //                 donors, dollars, rank,
                        //                 eligible_employee_count as ee_count
                        //     from daily_campaigns
                        //     where campaign_year = ?
                        //     and as_of_date = ?
                        //     and daily_type = 0     
                        //     and donors >= 5
                        //     order by rank ;
                        // SQL;

                        $challenges = DB::select($sql, $parameters);
                // }
            
            } else {

                // SELECT (@row_number:=@row_number + 1) AS row_num, Name, Country, Year  
                // FROM Person, (SELECT @row_number:=0) AS temp ORDER BY Year;  
                $parameters = [
                    $campaign_year,
                ];

                $sql = <<<SQL
                    select 0 as current, organization_name, participation_rate, previous_participation_rate, `change` as change_rate, 
                                donors, dollars, (@row_number:=@row_number + 1) AS rank,
                                round(case when participation_rate > 0 then 
                                            donors / (participation_rate / 100) 
                                else donors end) as ee_count
                      from historical_challenge_pages, (SELECT @row_number:=0) AS temp
                     where year = ?                      
                       and donors >= 5
                       and participation_rate > 0
                     order by participation_rate desc, `change` desc;     
                SQL;
                
                $challenges = DB::select($sql, $parameters);

            }

            // Charting (POC)
            if ($request->has('chart')) {
                $data = new \stdClass();
                $data->regions = [];
                $data->values = [];

                // Sort by organization name 
                usort($challenges, function ($item1, $item2) {
                    return $item1->organization_name <=> $item2->organization_name;
                });

                foreach ($challenges as $row) {
                    // Structure of data:
                    // {
                    //     regions: ["region 1","region 2","region 3"];
                    //     values: [ ['name': "region 1", "value" = 20],
                    //               ['name': "region 2", "value" = 40],
                    //               ['name': "region 3", "value" = 50],
                    //             ];
                    // }
                    array_push( $data->regions, $row->organization_name );
                    array_push( $data->values, [ 'name' => $row->organization_name , 
                                                 'value' => round($row->participation_rate,2),
                                                 'change' =>  round($row->change_rate,2) ] );
                }
    
                return json_encode($data);
            }


            if ($request->organization_name) {
                $challenges = array_filter($challenges, function($v, $k) use($request) {
                    return str_contains(strtolower($v->organization_name), strtolower($request->organization_name));
                }, ARRAY_FILTER_USE_BOTH);
            }

            // $summary 
            // $summary = DailyCampaignSummary::where('campaign_year', $campaign_year)
            //                 ->first();


            return Datatables::of($challenges)
                ->with([
                    'as_of_date' => $summary ? $summary->as_of_date->format('l, F jS Y ') : null,
                    'total_donors' => $summary ? number_format($summary->donors) : '0.00',
                    'total_dollars' => $summary ? number_format($summary->dollars) : '0.00',
                ])

                    // ->addColumn('current_', function ($special_campaign) {
                    //     return '<button class="btn btn-info btn-sm  show-bu" data-id="'. $special_campaign->id .'" >Show</button>' .
                    //         '<button class="btn btn-primary btn-sm ml-2 edit-bu" data-id="'. $special_campaign->id .'" >Edit</button>' .
                    //         '<button class="btn btn-danger btn-sm ml-2 delete-bu" data-id="'. $special_campaign->id .
                    //         '" data-name="'. $special_campaign->name . '">Delete</button>';
                    // })
            // ->rawColumns(['action'])
                    
                    ->make(true);
        }

        // // TODO - From daily summary  
        // $summary = DailyCampaignSummary::where('campaign_year', $campaign_year)
        //             ->first();


        $finalized_years = HistoricalChallengePage::select('year')->distinct()->orderBy('year', 'desc')->pluck('year')->toArray();

        $year_options = $finalized_years;
        if ($current_campaign_year == $finalized_years[0] && today() < $setting->challenge_final_date) {
            // not yet finalized
            array_shift($finalized_years);
        }

        if ($current_campaign_year > $year_options[0]) {
            $found = DailyCampaign::where('campaign_year', $current_campaign_year)
                                    ->where('daily_type', 0)
                                    ->first();
            if ($found) {
                array_unshift($year_options , $current_campaign_year );
            }
        }
        $year = $year_options ? $year_options[0] : null;
        // if ( today() >= $setting->challenge_start_date ) {
        //     array_unshift($year_options , strval( today()->year ) );
        //     $year = today()->year;
        // }
       
        // Avoid duplication 
        $year_options = array_unique($year_options);

        // Last update datetime of the current year
        // $last_update = null;
        // if ( $year == today()->year ) {
        //     $daily_campaign = DailyCampaign::where('campaign_year', $year )
        //                             ->orderBy('campaign_year', 'desc')
        //                             ->orderBy('as_of_date', 'desc')
        //                             ->first();
        //     $last_update = $daily_campaign->created_at;
        // } 

        if ($request->has('download')) {

            // $summary = DailyCampaignSummary::where('campaign_year', $campaign_year)->first(); 

            // $final_date = $as_of_day;
            // if ($summary) {
            //     $final_date = $summary->as_of_date;
            // }
            if (!(in_array( $campaign_year, $finalized_years))) {
                abort(404);
            }
            
            if (!($summary)) {
                
                $parameters = [
                    $campaign_year,
                    $as_of_day,
                ];

                $sql = <<<SQL
                    select business_unit_name as organization_name, donors, dollars
                      from daily_campaigns
                     where campaign_year = ?
                       and as_of_date = ?
                       and daily_type = 0     
                       and dollars > 0
                     order by organization_name;     
                SQL;
                
            } else {
                $parameters = [
                    $campaign_year,
                ];

                $sql = <<<SQL
                    select organization_name,donors, dollars
                      from historical_challenge_pages 
                     where year = ?                      
                       and dollars > 0
                     order by organization_name;     
                SQL;
            }

            $challenges = DB::select($sql, $parameters);

            $total_dollars = 0;
            $total_donors = 0;
            $other_dollars = 0;
            $other_donors = 0;
            $rows = []; 
            foreach( $challenges as $challenge) {
                $total_dollars += $challenge->dollars;
                $total_donors += $challenge->donors;

                if ($challenge->donors >= 5) {
                    $rows[] = $challenge;
                } else {
                    $other_dollars += $challenge->dollars;
                    $other_donors += $challenge->donors;
                }
            }
            $rows[] = (object) [ 'organization_name' => 'Other', 'dollars' => $other_dollars, 'donors' => $other_donors ];

            // Total Donors and Amount
            $total_dollars = $summary ? $summary->dollars : 0;
            $total_donors =  $summary ? $summary->donors : 0;



            // $summary = DailyCampaignSummary::where('campaign_year', $campaign_year)->first(); 

            // $final_date = $as_of_date;
            // if ($summary) {
            //     $final_date = $summary->as_of_date;
            // }
            
            // return view('challenge.partials.final_stats_pdf',  compact('campaign_year', 'as_of_day', 'rows', 'total_dollars', 'total_donors'));
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('challenge.partials.final_stats_pdf', 
                        compact('campaign_year', 'as_of_day', 'rows', 'total_dollars', 'total_donors')
                    )->setPaper('letter', 'portrait');
            return $pdf->download( $campaign_year . ' PECSF Campaign Final Statistics.pdf');
        }

        return view('challenge.index', compact('finalized_years', 'year_options', 'year'));
    }

    public function daily_campaign(Request $request){

        if (!(Setting::isCampaignPeriodActive())) {
            abort(404);
        }

        $campaign_year = Setting::challenge_page_campaign_year();

        $setting = Setting::first();

        $final_date_options = [];
        $date_options = [];
        $dept_date_options = [];

        $final_date_options = DailyCampaign::select('as_of_date')
                                    ->where('campaign_year', $campaign_year)
                                    ->where(function($query) use($setting) {
                                        return $query->where('as_of_date', '=', $setting->campaign_final_date->format('Y-m-d'));
                                    })
                                    // ->where('as_of_date', '<>', today() )
                                    ->distinct()
                                    ->orderBy('as_of_date', 'desc')
                                    ->pluck('as_of_date');

        $dept_date_options = DailyCampaign::select('as_of_date')
                                    ->where('campaign_year', $campaign_year)
                                    ->where('daily_type', 2)
                                    ->where(function($query) use($setting) {
                                        return $query->where('as_of_date', '=', $setting->campaign_end_date->format('Y-m-d'));
                                    })
                                    // ->where('as_of_date', '<>', today() )
                                    ->distinct()
                                    ->orderBy('as_of_date', 'desc')
                                    ->pluck('as_of_date');


        // dd( [ $final_date_options , $date_options , $dept_date_options ]);

        return view('challenge.daily_campaign', compact('final_date_options', 'date_options', 'dept_date_options'));
    }

    public function download(Request $request)
    {

        if (!(Setting::isCampaignPeriodActive())) {
            abort(404);
        }
       
        $campaign_year = Setting::challenge_page_campaign_year();
        $setting = Setting::first();

        $sort = $request->sort;
        $as_of_date = $request->start_date ?? today()->format('Y-m-d');

        if ( $as_of_date >= $setting->campaign_end_date->format('Y-m-d')) {
            if ($as_of_date < $setting->campaign_final_date->format('Y-m-d')) {
                $as_of_date = $setting->campaign_end_date->format('Y-m-d');
            } else {
                if ($sort == 'department')  {
                    $as_of_date = $setting->campaign_end_date->format('Y-m-d');
                } else {
                    $as_of_date = $setting->campaign_final_date->format('Y-m-d');
                }
            }
        }

        switch ($request->sort) {
            case 'region': 
                return \Maatwebsite\Excel\Facades\Excel::download(new DailyCampaignByRegionExport($campaign_year, $as_of_date),
                         'Daily_Campaign_Update_Region_'. $as_of_date .'.xlsx');
                break;
            case 'organization':
                return \Maatwebsite\Excel\Facades\Excel::download(new DailyCampaignByBUExport($campaign_year, $as_of_date),
                         'Daily_Campaign_Update_By_Org_'. $as_of_date .'.xlsx');
                break;
            case 'department':
                return \Maatwebsite\Excel\Facades\Excel::download(new DailyCampaignByDeptExport($campaign_year, $as_of_date),
                         'Daily_Campaign_Update_By_Dept_'. $as_of_date .'.xlsx');
                break;
        } 

    }

    public function org_participation_tracker(Request $request)
    {
        if (!(CampaignYear::isAnnualCampaignOpenNow())) {
            abort(404);
        }

        $year_options = CampaignYear::where('calendar_year', '>', 2023)->orderBy('calendar_year', 'desc')->pluck('calendar_year');

        $current_year = today()->year;
        $business_units = EligibleEmployeeByBU::where('organization_code', 'GOV')
                        ->where('campaign_year', $current_year)
                        ->where('ee_count', '>', 0)
                        ->orderBy('business_unit_name')
                        ->get();

        $user = User::where('id', Auth::id() )->first();
        $default_bu = $user ? $user->primary_job->bus_unit->code : null;


        return view('challenge.org_participation_tracker', compact('year_options', 'business_units','default_bu'));
    }

    public function org_participation_tracker_download(Request $request)
    {
        if (!(CampaignYear::isAnnualCampaignOpenNow())) {
            abort(404);
        }
        
        $as_of_date = today();
        $cy = $request->campaign_year;
        $bu =  $request->business_unit;

        $ee_by_bu = EligibleEmployeeByBU::where('organization_code', 'GOV')
                            ->where('campaign_year', $cy)
                            ->where('ee_count', '>', 0)
                            ->where('business_unit_code', $bu)
                            ->first();
        
        $start_date =  Carbon::createFromDate(null, 10, 15);  // Year defaults to current year

        if ( ($cy < today()->year || today() >= $start_date) && $ee_by_bu) {

            $filters = [];
            $filters['as_of_date'] = $ee_by_bu->as_of_date;
            $filters['business_unit_code'] = $bu;
            $filters['year'] = $cy;
            $filters['title'] = $ee_by_bu->business_unit_name . ' ('  . $ee_by_bu->business_unit_code . ')';

            $filename = 'OrgPartipationTracker_'.  $cy . '_' . $bu . '_' . $as_of_date->format('Y-m-d') .".xlsx";

            return \Maatwebsite\Excel\Facades\Excel::download(new OrgPartipationTrackersExport( null, $filters), $filename);  
    
        } else {

            return redirect()->route('challenge.org_participation_tracker')
                    ->with('message', 'The organization partipation tracter report for campaign Year ' . $cy . ' will be accessible starting from October 15, ' . $cy);
        }

    }
}
