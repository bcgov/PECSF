<?php

namespace App\Http\Controllers;

use App\Models\Pledge;
use App\Models\PledgeCharity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonationController extends Controller {

    public function index() {
        $currentYear = Carbon::now()->format('Y');
        $pledges = Pledge::with('charities')
            ->with('charities.charity')
            ->where('user_id', Auth::id())
            ->whereYear('created_at', $currentYear)
            ->get();
        $totalPledgedDataTillNow = "$".Pledge::where('user_id', Auth::id())->sum('goal_amount');
        return view('donations.index', compact('pledges', 'currentYear', 'totalPledgedDataTillNow'));
    }
    
}

?>