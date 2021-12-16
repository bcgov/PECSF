<?php

namespace App\Http\Controllers;

use App\Models\Pledge;
use App\Models\PledgeCharity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonationController extends Controller {

    public function index() {
        $pledges = Pledge::with('charities')
            ->with('charities.charity')
            ->where('user_id', Auth::id())
            ->whereYear('created_at', 2021)
            ->get();
        return view('donations.index', compact('pledges'));
    }
    
}

?>