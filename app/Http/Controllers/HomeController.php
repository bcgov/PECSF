<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Pledge;
use App\Models\EmployeeJob;
use App\Models\Announcement;
use App\Models\BusinessUnit;
use App\Models\CampaignYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\HistoricalChallengePage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {

        $campaignYear = CampaignYear::where('calendar_year', '<=', today()->year + 1 )
                            ->orderBy('calendar_year', 'desc')
                            ->first();

        $announcement = new Announcement;
        if ($request->session()->has('has-announcement') ) {
            $announcement = Announcement::first();
        }

        return view('home', compact('campaignYear','announcement'));

    }
}
