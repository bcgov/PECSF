<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChallengeSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:setting');
    }

    public function index(Request $requests){

        $setting = Setting::first();

        $setting->challenge_start_date = $setting->challenge_start_date ?? today();
        $setting->challenge_end_date = $setting->challenge_end_date ?? today();
        $setting->challenge_final_date = $setting->challenge_final_date ?? today();

        $setting->campaign_start_date = $setting->campaign_start_date ?? today();
        $setting->campaign_end_date = $setting->campaign_end_date ?? today();
        $setting->campaign_final_date = $setting->campaign_final_date ?? today();

        return view('admin-campaign.challenge.index',compact('setting'));
    }

    public function store(Request $request){

        $validator = Validator::make(request()->all(), [
            'challenge_start_date'      => 'required|date',
            'challenge_end_date'        => 'required|date|after:challenge_start_date',
            'challenge_final_date'      => 'required|date|after_or_equal:challenge_end_date',
            'campaign_start_date'       => 'required|date',
            'campaign_end_date'         => 'required|date|after:campaign_start_date',
            // 'campaign_final_date'       => 'required|date|after_or_equal:campaign_end_date',
        ],[

        ]);

        //run validation which will redirect on failure
        $validator->validate();

        $setting = Setting::first();
    
        $setting->challenge_start_date = $request->challenge_start_date;
        $setting->challenge_end_date   = $request->challenge_end_date;
        $setting->challenge_final_date = $request->challenge_final_date;

        $setting->campaign_start_date =  $request->campaign_start_date;
        $setting->campaign_end_date   =  $request->campaign_end_date;
        // $setting->campaign_final_date =  $request->campaign_final_date;

        $setting->save();
        
        return response()->noContent();
    
    }
}
