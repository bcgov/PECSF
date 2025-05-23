<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\FSPool;
use App\Models\Charity;
use App\Models\PayCalendar;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Models\DonateNowPledge;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\DonateNowRequest;
use Illuminate\Support\Facades\Session;
use App\Models\TransactionTimesMonitoring;
use Binafy\LaravelUserMonitoring\Utills\Detector;
use Binafy\LaravelUserMonitoring\Utills\UserUtils;


class DonateNowController extends Controller
{
    //
         /**
     * create a new instance of the class
     *
     * @return void
     */
    function __construct()
    {

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return redirect()->route('donate-now.create');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        session(['donate_now_pledge_transaction_start' => now()]);

        // Make sure the Annual camplaign is not started
        if (\App\Models\CampaignYear::isAnnualCampaignOpenNow() ) {
            return response("<h4>Invalid operation. Donate Now is not available during Annual Campaign Period. Click <a href='".
                         route('donations.list') ."'>here</a> to go back.</h4>");
            // abort(404);
        }

        //
        $pool_option = 'P';
        $pools = FSPool::current()->where('status', 'A')->with('region')->get()->sortBy(function($pool, $key) {
            return $pool->region->name;
        });

        $regional_pool_id = $pools->count() > 0 ? $pools->first()->id : null;


        // Self service
        $deduct_pay_from = PayCalendar::nextDeductPayFrom();
        $yearcd = substr($deduct_pay_from,0,4);

        $amount_options =  [
                            6 => '$6',
                            12 => '$12',
                            20 => '$20',
                            50 => '$50',
                            '' => 'Custom',
                        ];

        $one_time_amount = 20;
        $one_time_amount_custom = null;

        $edit_pecsf_allow = true;
        $organizations = [];

        $fund_support_pool_list = FSPool::current()->where('status', 'A')->with('region')->get()->sortBy(function($pool, $key) {
            return $pool->region->name;
        });

        return view('donate-now.wizard', compact('fund_support_pool_list','pool_option', 'pools', 'regional_pool_id', 'yearcd',
                    'edit_pecsf_allow', 'organizations',
                    'amount_options', 'one_time_amount','one_time_amount_custom'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DonateNowRequest $request)
    {
        // Common data for summary and final submission
        $user = User::where('id', Auth::Id() )->first();

        $pool_option = $request->pool_option;
        $one_time_amount = $request->one_time_amount ? $request->one_time_amount : $request->one_time_amount_custom ;

        // Calculate the deduct pay from
        $current = PayCalendar::whereRaw(" ( date(SYSDATE()) between pay_begin_dt and pay_end_dt) ")->first();

        $check_dt = PayCalendar::nextDeductPayFrom();
        // if ($current) {
        //     $period = PayCalendar::where('check_dt', '>=',  $current->check_dt )->skip(2)->take(1)->orderBy('check_dt')->first();
        //     $check_dt = $period ? $period->check_dt : null;
        // }

        if ($request->ajax()) {

            // Generate Summary Page
            if ($request->step == 3)  {

                // $organization = Organization::where('id', $request->organization_id)->first() ?? null;
                // $campaign_year = CampaignYear::where('id', $request->campaign_year_id)->first();

                $in_support_of = "";
                if ($request->pool_option == 'P')  {
                    $pool  = FSPool::current()->where('id', $request->pool_id)->first();
                    $in_support_of = $pool ? $pool->region->name : '';
                } else {
                    // $charity = Charity::where('id', $request->charity_id)->first();
                    $charity = Charity::where('id', $request->charities[0])->first();

                    $in_support_of = $charity ? $charity->charity_name : '';
                }

                return view('donate-now.partials.summary', compact('user', 'one_time_amount',
                        'in_support_of', 'check_dt', 'request'))->render();
            }
            return response()->noContent();
        }


        /* Final submission -- form submission (non-ajax call) */
        $organization_id = $user->organization_id;

        // Make sure that there is no pledge transaction setup yet
        $message_text = '';

        // Create a new Pledge
        $pool = FSPool::where('id', $request->pool_id)->first();

        $last_seqno = DonateNowPledge::where('organization_id', $organization_id)
                        // ->where('user_id', $user->id)
                        ->where('emplid', $user->emplid)
                        ->where('yearcd', $request->yearcd)
                        ->max('seqno');

        $seqno = $last_seqno ? ($last_seqno + 1) : 1;
//  dd([$request, $seqno] );

        $pledge = DonateNowPledge::Create([
            'organization_id' => $organization_id,
            'emplid'  => $user->emplid,
            'user_id' => $user->id,
            'yearcd'  => $request->yearcd,
            'seqno'   => $seqno,
            'type'    => $pool_option,
            'region_id' => ($pool_option == 'P' ? $pool->region_id : null),
            'f_s_pool_id' => ($pool_option == 'P' ? $request->pool_id : null),
            // 'charity_id' =>  ($pool_option == 'C' ? $request->charity_id : null),
            'charity_id' =>  ($pool_option == 'C' ? $request->charities[0] : null),
            'one_time_amount' => $one_time_amount ?? 0,
            'deduct_pay_from' => $check_dt,
            // 'special_program' => $request->special_program,
            'special_program' => ($pool_option == 'C' ? $request->additional[0] : null),

            'created_by_id' => $user->id,
            'updated_by_id' => $user->id,
        ]);

        $message_text = 'Pledge with Transaction ID ' . $pledge->id . ' have been created successfully';


        // When the transaction is completed, write to transaction Tines Monitoring
        $start_time = session('donate_now_pledge_transaction_start') ? session('donate_now_pledge_transaction_start') : now();
        $end_time = now();
        $duration = $start_time->diffInSeconds($end_time);
        
        $detector = new Detector;

        TransactionTimesMonitoring::create([
            'user_id' => UserUtils::getUserId(),
            'action_type' => 'create',
            'table_name' => 'donate_now_pledges',
            'tran_id' => $pledge->id,
            'browser_name' => $detector->getBrowser(),
            'platform' => $detector->getDevice(),
            'device' => $detector->getDevice(),
            'ip' => request()->ip(),
            'user_guard' => UserUtils::getCurrentGuardName(),
            'page' => request()->path(),

            'start_time' => $start_time,
            'end_time' => $end_time,
            'duration' => $start_time->diffInSeconds($end_time),
        ]);


        Session::flash('pledge_id', $pledge->id );
        return redirect()->route('donate-now.thank-you')
                ->with('success', $message_text);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pledge  $pledge
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pledge  $pledge
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    // public function edit(Request $request, $id)
    {
        //
        // $pledge = DonateNowPledge::where('id', $id)->first();

        // // Make sure this transaction is for the current logged user
        // if (!$pledge) {
        //     return abort(404);
        // } elseif  (!($pledge->user_id == Auth::id())) {
        //     return abort(403);
        // }

        // $pools = FSPool::current()->get()->sortBy(function($pool, $key) {
        //     return $pool->region->name;
        // });

        // $pool_option = $pledge->type;
        // $yearcd = $pledge->yearcd;
        // $regional_pool_id = $pledge->f_s_pool_id;

        // $one_time_amount = $pledge->one_time_amount ?? 0;

        // $amount_options =  [
        //     6 => '$6',
        //     12 => '$12',
        //     20 => '$20',
        //     50 => '$50',
        //     '' => 'Custom',
        // ];


        // if (in_array($pledge->one_time_amount, [6, 12, 20, 50]))  {
        //     $one_time_amount = $pledge->one_time_amount ?? null;
        //     $one_time_amount_custom = null;
        // } else {
        //     $one_time_amount = null;
        //     $one_time_amount_custom = $pledge->one_time_amount;
        // }

        // return view('donate-now.wizard', compact('pledge', 'pool_option', 'pools', 'regional_pool_id', 'yearcd',
        //             'amount_options', 'one_time_amount','one_time_amount_custom'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pledge  $pledge
     * @return \Illuminate\Http\Response
     */
    public function update(DonateNowRequest $request, $id)
    {

//  dd([$request, $id]);

    //     $pledge = DonateNowPledge::where('id', $id)->first();

    //     // Make sure this transaction is for the current logged user
    //     if (!$pledge) {
    //         return abort(404);
    //     } elseif  (!($pledge->user_id == Auth::id())) {
    //         return abort(403);
    //     }

    //     // Common data for summary and final submission
    //     $user = User::where('id', Auth::Id() )->first();

    //     $pool_option = $request->pool_option;
    //     $one_time_amount = $request->one_time_amount ? $request->one_time_amount : $request->one_time_amount_custom ;

    //     /* Final submission -- form submission (non-ajax call) */
    //     $organization_id = $user->organization_id;

    //     $pledge->type  = $pool_option;
    //     $pledge->f_s_pool_id  = ($pool_option == 'P' ? $request->pool_id : null);
    //     $pledge->charity_id  = ($pool_option == 'C' ? $request->charity_id : null);
    //     $pledge->one_time_amount = $one_time_amount ?? 0;
    //     $pledge->special_program = $request->special_program;
    //     $pledge->updated_by_id = $user->id;
    //     $pledge->save();

    //    Session::flash('pledge_id', $pledge->id );
    //    return redirect()->route('donate-now.thank-you')
    //                 ->with(['success' => 'Pledge with Transaction ID ' . $pledge->id . ' have been updated successfully'
    //                        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pledge  $pledge
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

    }


    public function thankYou()
    {
        $pledge_id = session()->get('pledge_id');

        if ($pledge_id) {
            return view('donate-now.thankyou', compact('pledge_id') );
        } else {
            return abort(403);
        }

    }


    public function searchCharities(Request $request)
    {

        $charities = Charity::where("charity_status","=","Registered");

        if($request->province != "")
        {
            $charities->where("province","=",$request->province);
        }

        if($request->category != "")
        {
            $charities->where("category_code","=",$request->category);
        }

        if($request->keyword != "")
        {
            $charities->where("charity_name","LIKE","%".$request->keyword."%");
        }

        $charities = $charities->paginate(7);
        $total = $charities->total();
        $selected_charity_id = $request->selected_charity_id;

        return view('donate-now.partials.search-charity-result', compact('charities','total','selected_charity_id'))->render();
    }

    public function summary(Request $request, $id) {

        $pledge = DonateNowPledge::where('id', $id)->first();

        // Make sure this transaction is for the current logged user
        if (!$pledge) {
            return abort(404);
        } elseif  (!($pledge->user_id == Auth::id())) {
            return abort(403);
        }

        $user = User::where('id', $pledge->user_id )->first();

        $one_time_amount = $pledge->one_time_amount;
        $in_support_of = "";
        if ($pledge->type == 'P')  {
            $pool  = FSPool::current()->selectRaw("regions.*,f_s_pools.*")->where('f_s_pools.id', $pledge->f_s_pool_id)->join("regions","f_s_pools.region_id","regions.id")->first();
            $pool['text'] = $pool->name;
            $pool['additional'] = $pool->notes;
            $pool['one-time-percentage-distribution'] = 100;
            $pool['one-time-amount-distribution'] = $one_time_amount;
            $in_support_of = $pool ? $pool->region->name : '';

            $pool_charities = $pool ? $pool->charities : [];
            $oneTimeAmount = $pledge->one_time_amount;

            $annualOneTimeAmount = $oneTimeAmount;

            $calculatedTotalPercentOneTime = 0;
            $calculatedTotalAmountOneTime = 0;
            $calculatedTotalPercentBiWeekly = 0;
            $calculatedTotalAmountBiWeekly = 0;
            $grandTotal = 0;
            $charities = [];
            $number_of_periods = 1;
            foreach ($pool_charities as $key => $pool_charity) {
                $charity = $pool_charity->charity->toArray();
                $charity['text'] = $pool_charity->charity->charity_name;
                $charity['additional'] = $pool_charity->name;

                $percentage = $pool_charity->percentage;

                if ($key === count($pool_charities) - 1  ) {

                    $charity['one-time-amount-distribution'] = $oneTimeAmount - $calculatedTotalAmountOneTime ;
                    $charity['one-time-percentage-distribution'] = $pool_charity->percentage;
                    $calculatedTotalPercentOneTime += $charity['one-time-percentage-distribution'];
                    $calculatedTotalAmountOneTime += $charity['one-time-amount-distribution'];
                    $grandTotal += $charity['one-time-amount-distribution'];
                   // $grandTotal += ($charity['bi-weekly-amount-distribution'] );

                    array_push($charities, $charity);

                } else {
                    $charity['one-time-amount-distribution'] = round(($pool_charity->percentage * $annualOneTimeAmount) / 100 , 2);
                    $charity['one-time-percentage-distribution'] = $pool_charity->percentage;
                    $calculatedTotalPercentOneTime += $charity['one-time-percentage-distribution'];
                    $calculatedTotalAmountOneTime += $charity['one-time-amount-distribution'];
                    $grandTotal += $charity['one-time-amount-distribution'];
                    // $grandTotal += ($charity['bi-weekly-amount-distribution'] );
                    array_push($charities, $charity);

                };
            }

        } else {
            $charity = Charity::where('id', $pledge->charity_id)->first();
            $charity['text'] = $charity->charity_name;
            $charity['additional'] = $pledge->special_program;
            $charity['one-time-percentage-distribution'] = 100;
            $charity['one-time-amount-distribution'] = $one_time_amount;
            $in_support_of = $charity ? $charity->charity_name : '';
        }

        // download PDF file with download method
        $fsp_name = $pledge->type == 'P' ? $in_support_of : false;
        if(isset($request->download_pdf)){
            // view()->share('donations.index',compact('pledges', 'currentYear', 'totalPledgedDataTillNow', 'campaignYear',
            //     'pledge', 'pledges_by_yearcd'));
            $date = date("Y-m-d");
            $charities = !empty($charities) ? $charities : [$charity];
            $annualOneTimeAmount = $one_time_amount;

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('donate-now.partials.pdf', compact('fsp_name','annualOneTimeAmount','charities','date','user', 'one_time_amount', 'in_support_of'));
            return $pdf->download('Donate Now Summary - '.date("Y-m-d").'.pdf');
        } else {
            return view('donate-now.partials.pdf', compact('fsp_name','user', 'one_time_amount', 'in_support_of'));
        }

    }

    public function regionalPoolDetail(Request $request, $id)
    {
        if ($request->ajax()) {
            $pool = FSPool::where('id', $id)->first();
            $charities = $pool ? $pool->charities : [];
            $charities = $charities->sortBy('charity.charity_name');
            return view('donate-now.partials.pool-detail', compact('charities') )->render();
        } else {
            return redirect('/');
        }
    }

}

