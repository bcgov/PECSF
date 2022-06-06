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

        // $campaignYear = CampaignYear::where('calendar_year', today()->year + 1 )->first();
        // if (!($campaignYear && $campaignYear->isOpen()) ) {
        //     $campaignYear = CampaignYear::where('calendar_year', today()->year )->first();
        // }

        // $cyPledges = Pledge::where('user_id', Auth::id())
        //     ->onlyCampaignYear( $campaignYear->calendar_year )
        //     ->get();

        $campaignYear = CampaignYear::where('calendar_year', '<=', today()->year + 1 )
                            ->orderBy('calendar_year', 'desc')
                            ->first();
        $pledge = Pledge::where('user_id', Auth::id())
                         ->whereHas('campaign_year', function($q){
                             $q->where('calendar_year','=', today()->year + 1 );
                         })->first();

        $user = User::where('id', Auth::id() )->first();

        // Pledge Histoy data (source Greenfield) 
        $sql_1 = Pledge::selectRaw("pledges.*, 'One-Time' as frequency")->where('user_id', $user->id)
                    ->where('type', 'P')
                    ->where('one_time_amount', '<>', 0)
                    ->with(['campaign_year']);
        $sql_2 = Pledge::selectRaw("pledges.*, 'Bi-Weekly' as frequency")->where('user_id', $user->id)
                    ->where('type', 'P')
                    ->where('pay_period_amount', '<>', 0)
                    ->with(['campaign_year']);
        $sql_3 = Pledge::selectRaw("pledges.*, 'One-Time' as frequency")->where('user_id', $user->id)
                    ->where('type', 'C')
                    ->where('one_time_amount', '<>', 0)
                    ->with('campaign_year','charities');
                    // ->with(['campaign_year','charities'=> function($q) { 
                    //     $q->where('pledge_charities.frequency', '=', 'one-time');
                    // }]);
        $old_pledges = Pledge::selectRaw("pledges.*, 'Bi-Weekly' as frequency")->where('user_id', $user->id)
                    ->where('type', 'C')
                    ->where('pay_period_amount', '<>', 0)
                    ->with('campaign_year','charities')
                    // ->with(['campaign_year','charities' => function($q){
                    //         $q->where('pledge_charities.frequency', '=', 'bi-weekly');
                    // }])
                    ->unionAll($sql_3)
                    ->unionAll($sql_2)
                    ->unionAll($sql_1)
                    ->get();
        $old_pledges_by_yearcd = $old_pledges->sortByDesc('yearcd')->groupBy('campaign_year.calendar_year');     
        
        // Pledge Histoy data (source BI) 
        $old_bi_pledges = PledgeHistory::where('GUID', $user->guid)->orderByDesc('yearcd')
                            ->orderBy('campaign_type')->orderBy('source')->orderBy('tgb_reg_district')
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
                                    ($old_pledge->amount * $old_pledge->campaign_year->number_of_periods); 
            $totalPledgedDataTillNow += $amount;
        }  
        
        //dd([ $old_bi_pledges,  $totalPledgedDataTillNow]);

        return view('donations.index', compact('pledges', 'currentYear', 'totalPledgedDataTillNow', 'campaignYear',
                    'pledge', 'old_bi_pledges_by_yearcd', 'old_pledges_by_yearcd'));
    }

    
}

?>