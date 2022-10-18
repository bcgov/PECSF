<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\FSPool;
use App\Models\Charity;
use App\Models\PayCalendar;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Models\SpecialCampaign;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\SpecialCampaignPledge;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\SpecialCampaignRequest;

class SpecialCampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return redirect()->route('special-campaign.create');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // forget the session for banner text
        // session()->forget('special-campaign-banner-text');


        //
        // $pool_option = 'P';
        // $pools = FSPool::current()->get()->sortBy(function($pool, $key) {
        //     return $pool->region->name;
        // });

        // Sepcial Campaign 
        $special_campaigns = SpecialCampaign::where('start_date', '<=', today())
                                                ->where('end_date', '>=', today())
                                                ->orderBy('start_date')
                                                ->get();

        // $regional_pool_id = $pools->count() > 0 ? $pools->first()->id : null;
        $special_campaign_id = $special_campaigns->count() > 0 ? $special_campaigns->first()->id : null;

        // Self service 
        $yearcd = Carbon::now()->format('Y');
        $amount_options =  [
                            6 => '$6',
                            12 => '$12',
                            20 => '$20',
                            50 => '$50',
                            '' => 'Custom',
                        ];
    
        $one_time_amount = 20;
        $one_time_amount_custom = null;

        // $edit_pecsf_allow = true;

        return view('special-campaign.wizard', compact('special_campaign_id', 'special_campaigns', 'yearcd',
                                        'amount_options', 'one_time_amount','one_time_amount_custom'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SpecialCampaignRequest $request)
    {
        //
        // Common data for summary and final submission
        $user = User::where('id', Auth::Id() )->first();

        // $pool_option = $request->pool_option;
        $one_time_amount = $request->one_time_amount ? $request->one_time_amount : $request->one_time_amount_custom ;
        
        // Calculate the deduct pay from 
        $current = PayCalendar::whereRaw(" ( date(SYSDATE()) between pay_begin_dt and pay_end_dt) ")->first();

        $check_dt = '';
        if ($current) {
            $period = PayCalendar::where('check_dt', '>=',  $current->check_dt )->skip(2)->take(1)->orderBy('check_dt')->first();
            $check_dt = $period ? $period->check_dt : null;
        }

        if ($request->ajax()) {

            // Generate Summary Page 
            if ($request->step == 2)  {

                // $organization = Organization::where('id', $request->organization_id)->first() ?? null;
                // $campaign_year = CampaignYear::where('id', $request->campaign_year_id)->first();

                $special_campaign = SpecialCampaign::where('id', $request->special_campaign_id)->first();
                $in_support_of = $special_campaign ? $special_campaign->charity->charity_name : '';
                $special_campaign_name = $special_campaign ? $special_campaign->name : '';

                return view('special-campaign.partials.summary', compact('user', 'one_time_amount',
                        'in_support_of', 'special_campaign_name', 'check_dt', 'request'))->render();
            }
            return response()->noContent();
        }
       
        
        /* Final submission -- form submission (non-ajax call) */
        $organization_id = $user->organization_id;

        // Make sure that there is no pledge transaction setup yet 
        $message_text = '';

        // Create a new Pledge
        $last_seqno = SpecialCampaignPledge::where('organization_id', $organization_id)
                        ->where('user_id', $user->id)
                        ->where('yearcd', $request->yearcd)
                        ->max('seqno');

        $seqno = $last_seqno ? ($last_seqno + 1) : 1;
//  dd([$request, $seqno] );

        $pledge = SpecialCampaignPledge::Create([
            'organization_id' => $organization_id,
            'user_id' => $user->id,
            'yearcd'  => $request->yearcd,
            'seqno'   => $seqno,
            'special_campaign_id' => $request->special_campaign_id,
            'one_time_amount' => $one_time_amount ?? 0,
            'deduct_pay_from' => $check_dt,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id,
        ]);

        $message_text = 'Pledge with Transaction ID ' . $pledge->id . ' have been created successfully';

        Session::flash('pledge_id', $pledge->id ); 
        return redirect()->route('special-campaign.thank-you')
                ->with('success', $message_text);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        //
        $pledge = SpecialCampaignPledge::where('id', $id)->first();

        // Make sure this transaction is for the current logged user 
        if (!$pledge) {
            return abort(404);
        } elseif  (!($pledge->user_id == Auth::id())) {
            return abort(403);
        }
     
         // Sepcial Campaign 
         $special_campaigns = SpecialCampaign::where('start_date', '<=', today())
                                ->where('end_date', '>=', today())
                                ->orderBy('start_date')
                                ->get();

        // $regional_pool_id = $pools->count() > 0 ? $pools->first()->id : null;
        $special_campaign_id = $pledge->special_campaign_id;

        $yearcd = $pledge->yearcd;
        
        $one_time_amount = $pledge->one_time_amount ?? 0;

        $amount_options =  [
            6 => '$6',
            12 => '$12',
            20 => '$20',
            50 => '$50',
            '' => 'Custom',
        ];

       
        if (in_array($pledge->one_time_amount, [6, 12, 20, 50]))  {
            $one_time_amount = $pledge->one_time_amount ?? null;
            $one_time_amount_custom = null;
        } else {
            $one_time_amount = null;
            $one_time_amount_custom = $pledge->one_time_amount;
        }
       
        return view('special-campaign.wizard', compact('pledge', 'special_campaigns', 'special_campaign_id',  'yearcd',
                    'amount_options', 'one_time_amount','one_time_amount_custom'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $pledge = SpecialCampaignPledge::where('id', $id)->first();

        // Make sure this transaction is for the current logged user 
        if (!$pledge) {
            return abort(404);
        } elseif  (!($pledge->user_id == Auth::id())) {
            return abort(403);
        }

        // Common data for summary and final submission
        $user = User::where('id', Auth::Id() )->first();


        $one_time_amount = $request->one_time_amount ? $request->one_time_amount : $request->one_time_amount_custom ;

        /* Final submission -- form submission (non-ajax call) */
        $organization_id = $user->organization_id;

        $pledge->special_campaign_id  = $request->special_campaign_id;
        $pledge->one_time_amount = $one_time_amount ?? 0;
        $pledge->updated_by_id = $user->id;
        $pledge->save();

       Session::flash('pledge_id', $pledge->id ); 
       return redirect()->route('special-campaign.thank-you')
                    ->with(['success' => 'Pledge with Transaction ID ' . $pledge->id . ' have been updated successfully'
                           ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function thankYou()
    {
        $pledge_id = session()->get('pledge_id');

        if ($pledge_id) {
            return view('special-campaign.thankyou', compact('pledge_id') );
        } else {
            return abort(403);
        }

    }


    // public function searchCharities(Request $request)
    // {

    //     $charities = Charity::where("charity_status","=","Registered");

    //     if($request->province != "")
    //     {
    //         $charities->where("province","=",$request->province);
    //     }

    //     if($request->category != "")
    //     {
    //         $charities->where("category_code","=",$request->category);
    //     }

    //     if($request->keyword != "")
    //     {
    //         $charities->where("charity_name","LIKE","%".$request->keyword."%");
    //     }

    //     $charities = $charities->paginate(7);
    //     $total = $charities->total();
    //     $selected_charity_id = $request->selected_charity_id;

    //     return view('special-campaign.partials.search-charity-result', compact('charities','total','selected_charity_id'))->render();
    // }

    public function summary(Request $request, $id) {

        $pledge = SpecialCampaignPledge::where('id', $id)->first();

        // Make sure this transaction is for the current logged user 
        if (!$pledge) {
            return abort(404);
        } elseif  (!($pledge->user_id == Auth::id())) {
            return abort(403);
        }

        $user = User::where('id', $pledge->user_id )->first();

        $one_time_amount = $pledge->one_time_amount;

        $special_campaign = SpecialCampaign::where('id', $pledge->special_campaign_id)->first();
        $in_support_of = $special_campaign ? $special_campaign->charity->charity_name : '';
        $special_campaign_name = $special_campaign ? $special_campaign->name : '';

        $check_dt = $pledge->deduct_pay_from;

        // download PDF file with download method
        if(isset($request->download_pdf)){
            // view()->share('donations.index',compact('pledges', 'currentYear', 'totalPledgedDataTillNow', 'campaignYear',
            //     'pledge', 'pledges_by_yearcd'));
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('special-campaign.partials.pdf', compact('user', 'one_time_amount', 'in_support_of', 'special_campaign_name', 'check_dt'));
            return $pdf->download('Donation Summary.pdf');
        } else {
            return view('special-campaign.partials.pdf', compact('user', 'one_time_amount', 'in_support_of', 'special_campaign_name', 'deduct_pay_from'));
        }
     
    }

    // public function regionalPoolDetail($id)
    // {
    //     $pool = FSPool::where('id', $id)->first();
    //     $charities = $pool ? $pool->charities : [];

    //     return view('special-campaign.partials.pool-detail', compact('charities') )->render();
    // }

    public function dismissSpecialCampaignBanner() {

        // forget the session for banner text
        session()->forget('special-campaign-banner-text');

        return response()->noContent();
    }



}
