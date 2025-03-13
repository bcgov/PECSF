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
use Binafy\LaravelUserMonitoring\Utills\Detector;
use Binafy\LaravelUserMonitoring\Utills\UserUtils;

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

    public function updateVisitMonitoringLog(Request $request) {

        if ($request->has('pagename')) {

            $detector = new Detector();

            // Store visit
            DB::table(config('user-monitoring.visit_monitoring.table'))->insert([
                'user_id' => UserUtils::getUserId(),
                'browser_name' => $detector->getBrowser(),
                'platform' => $detector->getDevice(),
                'device' => $detector->getDevice(),
                'ip' => $request->ip(),
                'user_guard' => UserUtils::getCurrentGuardName(),
                'page' => $request->pagename,             
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            return abort(404);
        }

        return response()->noContent();

    }

}
