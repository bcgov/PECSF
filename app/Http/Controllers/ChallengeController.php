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

        $campaign_year = $request->year ? $request->year : today()->year;

        $history = HistoricalChallengePage::select('year')
                                ->where('year', '<', $campaign_year)
                                ->orderBy('year', 'desc')
                                ->first(); 

        $prior_year = $history ? $history->year : $campaign_year - 1;                                

        if($request->ajax()) {

            if ( $campaign_year == today()->year ) {

                // Use Dynamic data during the challenge period
                if ( today() >= $setting->challenge_start_date && today() < $setting->challenge_end_date ) {

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
                        ];

                        $sql = <<<SQL
                            select 1 as current, business_unit_code, organization_name,
                                    -- 0 as participation_rate, 
                                    case when (select ee_count from eligible_employee_by_bus where eligible_employee_by_bus.campaign_year = ?
                                        and eligible_employee_by_bus.organization_code = 'GOV' 
                                        and eligible_employee_by_bus.business_unit_code = A.business_unit_code
                                        ) > 0 then 
                                            A.donors / (select ee_count from eligible_employee_by_bus where eligible_employee_by_bus.campaign_year = ?
                                                and eligible_employee_by_bus.organization_code = 'GOV' 
                                                and eligible_employee_by_bus.business_unit_code = A.business_unit_code
                                            ) * 100 
                                        else 0 end as participation_rate,
                                    -- 0 as previous_participation_rate, 
                                    (select participation_rate from historical_challenge_pages where year = ?
                                        and historical_challenge_pages.organization_name = A.organization_name
                                    ) as previous_participation_rate,
                                    (A.donors / (select ee_count from eligible_employee_by_bus where eligible_employee_by_bus.campaign_year = ?
                                        and eligible_employee_by_bus.organization_code = 'GOV' 
                                        and eligible_employee_by_bus.business_unit_code = A.business_unit_code
                                    ) * 100) - COALESCE((select participation_rate from historical_challenge_pages where year = ?
                                        and historical_challenge_pages.organization_name = A.organization_name
                                        ),0)
                                    as 'change_rate', 
                                    A.donors, A.dollars, (@row_number:=@row_number + 1) AS rank
                                    ,(select ee_count from eligible_employee_by_bus where eligible_employee_by_bus.campaign_year = ?
                                        and eligible_employee_by_bus.organization_code = 'GOV' 
                                        and eligible_employee_by_bus.business_unit_code = A.business_unit_code
                                    ) as ee_count
                            from 
                                (select business_unit_code, name as organization_name, sum(donors) as donors, sum(dollars) as dollars 
                                from business_units  
                                left outer join daily_campaign_view on business_units.code = daily_campaign_view.business_unit_code
                                where (daily_campaign_view.campaign_year = ? or daily_campaign_view .campaign_year is null) 
                                group by business_unit_code, name
                                order by sum(donors) desc) 
                                as A, (SELECT @row_number:=0) AS temp
                            where (select ee_count from eligible_employee_by_bus where eligible_employee_by_bus.campaign_year = ?
                                                and eligible_employee_by_bus.organization_code = 'GOV' 
                                                and eligible_employee_by_bus.business_unit_code = A.business_unit_code
                                            ) >= 5
                            order by A.donors / (select ee_count from eligible_employee_by_bus where eligible_employee_by_bus.campaign_year = ?
                                                and eligible_employee_by_bus.organization_code = 'GOV' 
                                                and eligible_employee_by_bus.business_unit_code = A.business_unit_code
                                            ) * 100 desc
                                        
                        SQL;


                        $challenges = DB::select($sql, $parameters);

                } else {

                        $parameters = [
                            $campaign_year,
                            today()->format('Y-m-d'),
                        ];

                        $sql = <<<SQL
                            select 0 as current, business_unit_name as organization_name, participation_rate, previous_participation_rate, change_rate, 
                                        donors, dollars, (@row_number:=@row_number + 1) AS rank,
                                        eligible_employee_count as ee_count
                            from daily_campaigns, (SELECT @row_number:=0) AS temp
                            where daily_type = 0
                            and campaign_year = ?
                            and as_of_date = (select max(as_of_date) from daily_campaigns D1
                                                                where D1.campaign_year = daily_campaigns.campaign_year
                                                                and D1.daily_type = daily_campaigns.daily_type
                                                                and D1.as_of_date <= ?
                                                                )     
                            and eligible_employee_count >= 5
                            order by participation_rate desc;     
                        SQL;

                        $challenges = DB::select($sql, $parameters);
                }
            
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
                       and round((case when participation_rate > 0 then 
                                            donors / (participation_rate / 100) 
                                else donors end),2) >= 5
                     order by participation_rate desc;     
                SQL;
                
                $challenges = DB::select($sql, $parameters);

            }

            if ($request->organization_name) {
                $challenges = array_filter($challenges, function($v, $k) use($request) {
                    return str_contains($v->organization_name, $request->organization_name);
                }, ARRAY_FILTER_USE_BOTH);
            }

            return Datatables::of($challenges)

                    // ->addColumn('current_', function ($special_campaign) {
                    //     return '<button class="btn btn-info btn-sm  show-bu" data-id="'. $special_campaign->id .'" >Show</button>' .
                    //         '<button class="btn btn-primary btn-sm ml-2 edit-bu" data-id="'. $special_campaign->id .'" >Edit</button>' .
                    //         '<button class="btn btn-danger btn-sm ml-2 delete-bu" data-id="'. $special_campaign->id .
                    //         '" data-name="'. $special_campaign->name . '">Delete</button>';
                    // })
            // ->rawColumns(['action'])
                    ->make(true);
        }

        $year_options = HistoricalChallengePage::select('year')->distinct()->orderBy('year', 'desc')->pluck('year')->toArray();
        $year = $year_options ? $year_options[0] : null;

        if ( today() >= $setting->challenge_start_date ) {
            array_unshift($year_options , strval( today()->year ) );
            $year = today()->year;
        }

        return view('challenge.index', compact('year_options', 'year'));
    }


    public function daily_campaign(Request $request){

        $campaign_year = today()->year;

        $date_options = \App\Models\DailyCampaign::select('as_of_date')
                        ->crossJoin('settings')
                        ->where('campaign_year', $campaign_year)
                        ->whereBetweenColumns('as_of_date',['settings.campaign_start_date', 'settings.campaign_end_date'])
                        // ->where('as_of_date', '<>', today() )
                        ->distinct()
                        ->orderBy('as_of_date', 'desc')
                        ->pluck('as_of_date');

        return view('challenge.daily_campaign', compact('date_options'));
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
                         'daily_campaign_update_region_'. $as_of_date .'.xlsx');
                break;
            case 'organization':
                return \Maatwebsite\Excel\Facades\Excel::download(new DailyCampaignByBUExport($campaign_year, $as_of_date),
                         'daily_campaign_update_by_org_'. $as_of_date .'.xlsx');
                break;
            case 'department':
                return \Maatwebsite\Excel\Facades\Excel::download(new DailyCampaignByDeptExport($campaign_year, $as_of_date),
                         'daily_campaign_update_by_dept_'. $as_of_date .'.xlsx');
                break;
        } 

    }


}
