<?php

namespace App\Http\Controllers;

use App\Models\Pledge;
use App\Models\PledgeCharity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CampaignYear;

class DonationController extends Controller {

    public function index() {
        $currentYear = Carbon::now()->format('Y');
        $pledges = Pledge::with('charities')
            ->with('charities.charity')
            ->where('user_id', Auth::id())
            ->whereYear('created_at', $currentYear)
            ->get();
        $totalPledgedDataTillNow = "$".Pledge::where('user_id', Auth::id())->sum('goal_amount');

        $campaignYear = CampaignYear::where('calendar_year', today()->year + 1 )->first();
        if (!($campaignYear && $campaignYear->isOpen()) ) {
            $campaignYear = CampaignYear::where('calendar_year', today()->year )->first();
        }

        $cyPledges = Pledge::where('user_id', Auth::id())
            ->onlyCampaignYear( $campaignYear->calendar_year )
            ->get();

        return view('donations.index', compact('pledges', 'currentYear', 'totalPledgedDataTillNow', 'campaignYear','cyPledges'));
    }
    
}

?>