<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\DailyCampaign;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

class ChallengePageDataReportController extends Controller
{
    
    /**
     * create a new instance of the class
     *
     * @return void
     */
    function __construct()
    {
         $this->middleware('permission:setting');
    }

    public function index(Request $request) {

        if($request->ajax()) {

            $campaign_year = $request->campaign_year;
            $as_of_date    = $request->as_of_date;

            $parameters = [
                $campaign_year,
                $as_of_date,
            ];

            $sql = <<<SQL

                select 0 as current, business_unit, business_unit_name as organization_name, participation_rate, previous_participation_rate, change_rate, 
                            donors, dollars, (@row_number:=@row_number + 1) AS rank,
                            eligible_employee_count as ee_count
                from daily_campaigns, (SELECT @row_number:=0) AS temp
                where campaign_year = ?
                and as_of_date = ?
                and daily_type = 0     
                order by participation_rate desc, abs(change_rate);     

            SQL;
            
            $challenges = DB::select($sql, $parameters);

            return Datatables::of($challenges)
                    ->make(true);
        }

        $year_options = DailyCampaign::where('daily_type', 0)
                            ->distinct('campaign_year')
                            ->orderBy('campaign_year', 'desc')
                            ->pluck('campaign_year');

        $default_year = $year_options ? $year_options->first() : null;

        $date_options = DailyCampaign::where('daily_type', 0)
                            ->where('campaign_year', $default_year)
                            ->distinct('as_of_date')
                            ->orderBy('as_of_date', 'desc')
                            ->pluck('as_of_date');

        return view('admin-report.challenge-page-data.index', compact('year_options', 'date_options'));
        
    }

    public function getDateOptions(Request $request) {

        if($request->ajax()) {

            $date_options = DailyCampaign::where('daily_type', 0)
                                ->where('campaign_year', $request->campaign_year)
                                ->distinct('as_of_date')
                                ->orderBy('as_of_date', 'desc')
                                ->pluck('as_of_date');

            return json_encode( $date_options );
        } else {
            return redirect('/');
        }

    }

}
