<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Setting;
use App\Models\CampaignYear;
use Illuminate\Http\Request;
use App\Models\DailyCampaign;
use Yajra\Datatables\Datatables;

use App\Models\DailyCampaignView;
use Illuminate\Support\Facades\DB;
use App\Models\DailyCampaignSummary;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\HistoricalChallengePage;
use Illuminate\Support\Facades\Storage;
use App\Exports\DailyCampaignByBUExport;
use App\Exports\DailyCampaignByDeptExport;
use App\Exports\DailyCampaignByRegionExport;

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

        $as_of_day = DailyCampaign::where('campaign_year', $campaign_year)
                            ->where('daily_type', 0)
                            ->where('as_of_date', '<=', $setting->challenge_end_date )
                            ->max('as_of_date');
        
        if (today() >= $setting->challenge_end_date &&
              ($setting->challenge_final_date == $setting->challenge_processed_final_date)) {
            $as_of_day = $setting->challenge_final_date;
        }

        if($request->ajax()) {

            if ( $campaign_year == $current_campaign_year ) {
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
            $summary = DailyCampaignSummary::where('campaign_year', $campaign_year)
                            ->first();


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


        $year_options = HistoricalChallengePage::select('year')->distinct()->orderBy('year', 'desc')->pluck('year')->toArray();

        $year = $year_options ? $year_options[0] : null;
        if ( today() >= $setting->challenge_start_date ) {
            array_unshift($year_options , strval( today()->year ) );
            $year = today()->year;
        }
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

        return view('challenge.index', compact('year_options', 'year'));
    }

    public function daily_campaign(Request $request){

        $campaign_year = Setting::challenge_page_campaign_year();

        $setting = Setting::first();

        $final_date_options = [];
        $date_options = [];
        $dept_date_options = [];

        if (today() >= $setting->campaign_final_date) {

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

        } else {

            $date_options = DailyCampaign::select('as_of_date')
                            ->where('campaign_year', $campaign_year)
                            ->where(function($query) use($setting) {
                                return $query->WhereBetween('as_of_date',[$setting->campaign_start_date->format('Y-m-d'), $setting->campaign_end_date->format('Y-m-d')]);
                            })
                            // ->where('as_of_date', '<>', today() )
                            ->distinct()
                            ->orderBy('as_of_date', 'desc')
                            ->pluck('as_of_date');
        }

        // dd( [ $final_date_options , $date_options , $dept_date_options ]);

        return view('challenge.daily_campaign', compact('final_date_options', 'date_options', 'dept_date_options'));
    }

    public function download(Request $request)
    {
        
        $campaign_year = today()->year;

        $as_of_date = DailyCampaign::where('campaign_year', $campaign_year)    
                            ->where('as_of_date', '<=', today() ) 
                            ->max('as_of_date');
        $as_of_date = $request->start_date ? $request->start_date : $as_of_date;

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

}
