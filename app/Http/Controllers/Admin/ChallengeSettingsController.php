<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Http\Request;

use App\Models\DailyCampaign;
use App\Http\Controllers\Controller;
use App\Models\DailyCampaignSummary;
use Illuminate\Support\Facades\Auth;
use App\Models\HistoricalChallengePage;
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
            'campaign_final_date'       => 'required|date|after_or_equal:campaign_end_date',
        ],[

        ]);

        //run validation which will redirect on failure
        $validator->validate();

        $setting = Setting::first();

        $challenge_end_date = Carbon::create( $request->challenge_end_date );               

        $campaign_year = Setting::challenge_page_campaign_year($challenge_end_date);
        $last_process_date = DailyCampaign::where('campaign_year', $campaign_year)
                                ->where('daily_type',  0)
                                ->max('as_of_date');

        // Update the daily campaign summary if the challenge_end_date was changed when backdate 
        if ($last_process_date && $setting->challenge_end_date->format('Y-m-d') != $challenge_end_date->format('Y-m-d')) {

            // $campaign_year = Setting::challenge_page_campaign_year($challenge_end_date);
            // 
            // $last_process_date = DailyCampaign::where('campaign_year', $campaign_year)
            //                             ->where('daily_type',  0)
            //                             ->max('as_of_date');

            $as_of_date = min( $last_process_date,  $request->challenge_end_date, today()->format('Y-m-d') );

            $summary = DailyCampaign::where('campaign_year', $campaign_year)
                            ->where('as_of_date', $as_of_date)
                            ->where('daily_type',  0)
                            ->selectRaw( 'sum(donors) as total_donors, sum(dollars) as total_dollars')
                            ->first();

            DailyCampaignSummary::updateOrCreate([
                'campaign_year' => $campaign_year,
            ],[
                'as_of_date' => $as_of_date,

                'donors' => $summary->total_donors,
                'dollars' => $summary->total_dollars,

                'updated_by_id' => Auth::id(),
            ]);
        }

        $setting->challenge_start_date = $request->challenge_start_date;
        $setting->challenge_end_date   = $request->challenge_end_date;
        $setting->challenge_final_date = $request->challenge_final_date;

        $setting->campaign_start_date =  $request->campaign_start_date;
        $setting->campaign_end_date   =  $request->campaign_end_date;
        $setting->campaign_final_date =  $request->campaign_final_date;
        $setting->save();
        
        return response()->noContent();
    
    }

    public function finalizeChallengeData(Request $request){
        
       
        $validator = Validator::make(request()->all(), [],[]);

        $validator->after(function ($validator) use($request) {

            $setting = Setting::first();
            $as_of_date = Carbon::parse($request->challenge_final_date);
            $campaign_year = Setting::challenge_page_campaign_year( $as_of_date );   
    
            if (!($setting->challenge_start_date->format('Y-m-d') == $request->challenge_start_date &&
                    $setting->challenge_end_date->format('Y-m-d')   == $request->challenge_end_date &&
                    $setting->challenge_final_date->format('Y-m-d') == $request->challenge_final_date &&
                    $setting->campaign_start_date->format('Y-m-d') ==  $request->campaign_start_date &&
                    $setting->campaign_end_date->format('Y-m-d')   ==  $request->campaign_end_date &&
                    $setting->campaign_final_date->format('Y-m-d') ==  $request->campaign_final_date)) {

                $validator->errors()->add('challenge_final_date', 'You must save the latest setting first.');
            } 
           
            if ( $request->challenge_final_date > today() ) {
                $validator->errors()->add('challenge_final_date', 'The final date is later than today.');
            }

            $challenge = DailyCampaign::where('campaign_year', $campaign_year)
                                    ->where('as_of_date', $request->challenge_final_date)
                                    ->first();
            if (!($challenge)) {
                $validator->errors()->add('challenge_final_date', 'No Challenge Data for date ' . $request->challenge_final_date . 
                        ' yet.');
            }

            if ($setting->challenge_processed_final_date && 
                $setting->challenge_processed_final_date->format('Y-m-d')  == $request->challenge_final_date) {
                    $validator->errors()->add('challenge_final_date', 'The current finalized data is already for date ' .
                                    $request->challenge_final_date);
            }

        });

        //run validation which will redirect on failure
        $validator->validate();

        // Finalize -- Proceed to Copy to the historial data 
        $setting = Setting::first();
        
        $as_of_date = $setting->challenge_final_date;
        $campaign_year = Setting::challenge_page_campaign_year( $as_of_date );   

        DailyCampaign::finalize_challenge_page_data($campaign_year, $as_of_date);

        $setting->challenge_processed_final_date = $as_of_date;
        $setting->save();

        return response()->noContent();
    
    }

}
