<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Pledge;
use App\Models\CampaignYear;
use Illuminate\Http\Request;
use App\Models\PledgeHistory;
use App\Models\BankDepositForm;
use App\Models\DonateNowPledge;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ViewPledgeHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\SpecialCampaignPledge;


class DonationController extends Controller {

    public function index(Request $request) {
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
        $current_pledge = Pledge::where('user_id', Auth::id())
                         ->whereHas('campaign_year', function($q){
                             $q->where('calendar_year','=', today()->year + 1 );
                         })->first();

        $user = User::where('id', Auth::id() )->first();
                        
        // NOTE: Must use the raw select statement in Laravel for querying this custom SQL view due to the performance issue
        // $all_pledges = DB::select( DB::raw("SELECT * FROM pledge_history_view WHERE (GUID = '" . $user->guid . 
        //                                         "' and GUID <> '') OR (source = 'GF' and user_id = " . Auth::id() . ") order by yearcd desc, donation_type desc;" ) );
        // $all_pledges = DB::select( DB::raw("SELECT * FROM pledge_history_view WHERE (emplid = '" . $user->emplid . 
        //                                         "' and emplid <> '') OR (source = 'GF' and user_id = " . Auth::id() . ") order by yearcd desc, donation_type desc;" ) );

        $all_pledges = ViewPledgeHistory::where( function($q) use($user) {
                $q->where('emplid', $user->emplid)
                  ->where('emplid', '<>', '');
        })
        ->orderBy('yearcd', 'desc')
        ->orderBy('donation_type')->get();

        $pledges_by_yearcd = collect( $all_pledges )->sortByDesc('yearcd')->groupBy('yearcd');

        $totalPledgedDataTillNow = 0;
        foreach ($all_pledges as $pledge) {
            $totalPledgedDataTillNow += $pledge->pledge;
        }

        // download PDF file with download method
        if(isset($request->download_pdf)){
            // view()->share('donations.index',compact('pledges', 'currentYear', 'totalPledgedDataTillNow', 'campaignYear',
            //     'pledge', 'pledges_by_yearcd'));
            $pdf = PDF::loadView('donations.partials.pdf', compact('pledges', 'currentYear', 'totalPledgedDataTillNow', 'campaignYear',
                'current_pledge', 'pledges_by_yearcd'));
            return $pdf->download('Donation Summary.pdf');
        }
        else{
            return view('donations.index', compact('pledges', 'currentYear', 'totalPledgedDataTillNow', 'campaignYear',
                'current_pledge', 'pledges_by_yearcd'));
        }

    }

    public function pledgeDetail(Request $request)
    {

        if ($request->source == 'BI') {

            $user = User::where('id', Auth::id() )->first();

            $donate_today_pledge = PledgeHistory::where('id', $request->id)->first();
            $old_pledges = PledgeHistory::where('emplid', $user->emplid)
                            ->where('campaign_type', $request->donation_type)
                            ->where('yearcd', $request->yearcd)
                            ->where('frequency', $request->frequency)
                            ->when( $request->donation_type == 'Donate Today', function($query) use($request) {
                                return $query->where('id', $request->id);
                            })
                            ->orderBy('name1')
                            ->get();

            $campaign_year = CampaignYear::where('calendar_year', $request->yearcd)->first();

            $type = $request->donation_type;
            $year = $request->yearcd;
            $frequency = $request->frequency;
            if ($request->frequency == 'One-Time') {
                $pledge_amt = $old_pledges->first()->pledge;    // Note: Donate Today -- per_pay_amt is always zero
            } else {                
                $pledge_amt = $old_pledges->first()->per_pay_amt;
            } 
            //  $request->frequency == 'One-Time' ? $pledge->one_time_amount : $pledge->pay_period_amount ;
            $number_of_periods = $request->frequency == 'One-Time' ? 1 : $campaign_year->number_of_periods;
            // $total_amount = $request->frequency == 'One-Time' ? $pledge_amt : $pledge_amt * $number_of_periods;
            $total_amount = $old_pledges->first()->pledge;
            $pool_name = $old_pledges->first()->source == "Pool" ? $old_pledges->first()->region->name : '';

            return view('donations.partials.bi-pledge-detail-modal',
                    compact('type', 'year', 'frequency', 'pool_name', 'pledge_amt', 'number_of_periods', 
                                'total_amount', 'old_pledges', 'donate_today_pledge') )->render();

        } else {


            if ($request->donation_type == 'Annual') {

                $pledge = Pledge::where('id', $request->id)->first();

                $year = $pledge->campaign_year->calendar_year;
                $frequency = $request->frequency;
                $pledge_amt = $request->frequency == 'One-Time' ? $pledge->one_time_amount : $pledge->pay_period_amount ;
                $number_of_periods = $pledge->campaign_year->number_of_periods;
                $total_amount = $request->frequency == 'One-Time' ? $pledge->one_time_amount : $pledge->pay_period_amount * $number_of_periods;

                return view('donations.partials.pledge-detail-modal',
                        compact('year', 'frequency', 'pledge_amt', 'number_of_periods', 'total_amount', 'pledge') )->render();

            } elseif ($request->donation_type == 'Donate Now') {

                $pledge = DonateNowPledge::where('id', $request->id)->first();
                
                $year = $request->yearcd;
                $frequency = $request->frequency;
                $pledge_amt = $pledge->one_time_amount;
                $total_amount = $pledge->one_time_amount;

                return view('donations.partials.donate-now-pledge-detail-modal',
                        compact('year', 'frequency', 'pledge_amt', 'total_amount', 'pledge') )->render();

            } elseif ($request->donation_type == 'Special Campaign') {
                // Special Campaign - Detail

                $pledge = SpecialCampaignPledge::where('id', $request->id)->first();
                
                $year = $request->yearcd;
                $frequency = $request->frequency;
                $pledge_amt = $pledge->one_time_amount;
                $total_amount = $pledge->one_time_amount;
                $check_dt = $pledge->deduct_pay_from;

                // $special_campaign = SpecialCampaign::where('id', $pledge->special_campaign_id)->first();
                $in_support_of = $pledge ? $pledge->special_campaign->charity->charity_name : '';
                $special_campaign_name = $pledge ? $pledge->special_campaign->name : '';

                return view('donations.partials.special-campaign-pledge-detail-modal',
                        compact('year', 'frequency', 'pledge_amt', 
                                 'in_support_of', 'special_campaign_name', 'check_dt',
                                    'total_amount', 'pledge') )->render();
                                    
            } elseif ($request->donation_type == 'Event') {
                // Event - Detail

                $pledge = BankDepositForm::where('id', $request->id)->first();

                $year = $pledge->created_at->format('Y');
                $frequency = $request->frequency;
                $pledge_amt = $pledge->deposit_amount;
                $total_amount = $pledge->deposit_amount;
                
                return view('donations.partials.event-detail-modal',
                        compact('year', 'frequency', 'pledge_amt', 'total_amount', 'pledge') )->render();

                return 'to be developed'                    ;
            }

        }
    }

}

?>
