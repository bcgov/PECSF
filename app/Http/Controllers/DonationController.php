<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Pledge;
use App\Models\CampaignYear;
use Illuminate\Http\Request;
use App\Models\PledgeCharity;
use App\Models\PledgeHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;


class DonationController extends Controller {

    public function index() {
        $currentYear = Carbon::now()->format('Y');
        $pledges = Pledge::with('charities')
            ->with('charities.charity')
            ->where('user_id', Auth::id())
            ->whereYear('created_at', $currentYear)
            ->get();
        $totalPledgedDataTillNow = "$".Pledge::where('user_id', Auth::id())->sum('goal_amount');

        // Campaign Year
        $campaignYear = CampaignYear::where('calendar_year', '<=', today()->year + 1 )
                            ->orderBy('calendar_year', 'desc')
                            ->first();
        $pledge = Pledge::where('user_id', Auth::id())
                         ->whereHas('campaign_year', function($q){
                             $q->where('calendar_year','=', today()->year + 1 );
                         })->first();

        $user = User::where('id', Auth::id() )->first();

        // Pledge Histoy data (source Greenfield) 
        $sql_1 = Pledge::selectRaw("pledges.*, 'Annual' as donation_type, 'One-Time' as frequency")->where('user_id', $user->id)
                    // ->where('type', 'P')
                    ->where('one_time_amount', '<>', 0)
                    ->with(['campaign_year']);
        $old_pledges = Pledge::selectRaw("pledges.*, 'Annual' as donation_type, 'Bi-Weekly' as frequency")->where('user_id', $user->id)
                        ->where('pay_period_amount', '<>', 0)
                        ->with(['campaign_year'])
                        ->unionAll($sql_1)
                        ->get();

        $old_pledges_by_yearcd = $old_pledges->sortByDesc('yearcd')->groupBy('campaign_year.calendar_year');     
        
        // Pledge Histoy data (source BI) 
        $old_bi_pool_pledges = PledgeHistory::where('GUID', $user->guid)
                    ->where('campaign_type', '=', 'Annual')
                    ->leftJoin('regions', 'regions.id', '=', 'pledge_histories.region_id')
                    ->selectRaw("yearcd, campaign_year_id, campaign_type, source, case when source = 'Pool' then regions.name else name1 end, 
                                                            case when source = 'Pool' then '' else name2 end, frequency, max(pledge) as pledge")
                    ->groupBy(['yearcd', 'campaign_year_id', 'campaign_type', 'source', 'frequency']);

        $old_bi_pledges = PledgeHistory::where('GUID', $user->guid)
                        ->whereNotIn('campaign_type', ['Annual','Event'])
                        //->where('source','Non-Pool')
                        ->leftJoin('charities', 'charities.id', '=', 'pledge_histories.charity_id')
                        ->selectRaw('yearcd, campaign_year_id, campaign_type, source, name1, name2, frequency, pledge')
                        ->unionAll($old_bi_pool_pledges)
                        ->get();               

        // Limit to last 3 years pledge history                            
        // $last_3_yearcd = PledgeHistory::where('GUID', $user->guid)->orderByDesc('yearcd')->groupBy('yearcd')->pluck('yearcd')->take(3);
        // $old_bi_pledges_by_yearcd = $old_bi_pledges->whereIn('yearcd', $last_3_yearcd)->sortByDesc('yearcd')->groupBy('yearcd');
        $old_bi_pledges_by_yearcd = $old_bi_pledges->sortByDesc('yearcd')->groupBy('yearcd');

        // Calculate pledge totally amount
        $totalPledgedDataTillNow = 0;
        foreach ($old_pledges as $old_pledge)
        {
            if ($old_pledge->type == 'P') {
                $amount =  $old_pledge->frequency == 'One-Time' ?  $old_pledge->one_time_amount :
                                    ($old_pledge->pay_period_amount * $old_pledge->campaign_year->number_of_periods); 
                $totalPledgedDataTillNow += $amount;                                    
            } else {
                foreach($old_pledge->charities->where('frequency', strtolower($old_pledge->frequency)) as $pledge_charity)
                {
                    $amount =  $pledge_charity->frequency == 'one-time' ?  $pledge_charity->amount :
                                    ($pledge_charity->amount * $old_pledge->campaign_year->number_of_periods); 
                    $totalPledgedDataTillNow += $amount;                                    
                }
            }                                    
        }  
        foreach ($old_bi_pledges as $old_pledge)
        {

            $amount =  $old_pledge->frequency == 'One-Time' ?  $old_pledge->pledge :
                                    ($old_pledge->pledge * $old_pledge->campaign_year->number_of_periods); 
            $totalPledgedDataTillNow += $amount;
        }  
        
        //dd([ $old_bi_pledges,  $totalPledgedDataTillNow]);

        return view('donations.index', compact('pledges', 'currentYear', 'totalPledgedDataTillNow', 'campaignYear',
                    'pledge', 'old_bi_pledges_by_yearcd', 'old_pledges_by_yearcd'));
    }

    public function oldPledgeDetail(Request $request) 
    {

        if ($request->source == 'history') {

            $user = User::where('id', Auth::id() )->first();

            $old_pledges = PledgeHistory::where('GUID', $user->guid)
                            ->where('campaign_type', 'Annual')
                            ->where('yearcd', $request->yearcd)
                            ->where('frequency', $request->frequency)
                            ->orderBy('name1')
                            ->get();

            $campaign_year = CampaignYear::where('calendar_year', $request->yearcd)->first();        

            $year = $request->yearcd;
            $pledge_amt = $old_pledges->first()->pledge;
            $number_of_periods = $campaign_year->number_of_periods;
            $total_amount = $pledge_amt * $number_of_periods;
            $pool_name = $old_pledges->first()->source == "Pool" ? $old_pledges->first()->region->name : '';

            return view('donations.partials.bi-pledge-detail-modal', 
                    compact('year', 'pool_name', 'pledge_amt', 'number_of_periods', 'total_amount', 'old_pledges') )->render();

            
        } else {


            if ($request->type == 'Annual') {

                $pledge = Pledge::where('id', $request->id)->first();

                $year = $pledge->campaign_year->calendar_year; 
                $frequency = $request->frequency;
                $pledge_amt = $request->frequency == 'One-Time' ? $pledge->one_time_amount : $pledge->pay_period_amount ;
                $number_of_periods = $pledge->campaign_year->number_of_periods;
                $total_amount = $request->frequency == 'One-Time' ? $pledge->one_time_amount : $pledge->pay_period_amount * $number_of_periods; 

                return view('donations.partials.pledge-detail-modal', 
                        compact('year', 'frequency', 'pledge_amt', 'number_of_periods', 'total_amount', 'pledge') )->render();

            }

        }

        
    }
    
}

?>