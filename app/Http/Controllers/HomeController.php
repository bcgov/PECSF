<?php

namespace App\Http\Controllers;

use App\Models\BusinessUnit;
use App\Models\CampaignYear;
use App\Models\EmployeeJob;
use App\Models\HistoricalChallengePage;
use App\Models\Pledge;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        return view('home');
    }
}
