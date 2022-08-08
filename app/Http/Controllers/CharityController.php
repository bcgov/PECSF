<?php

namespace App\Http\Controllers;

use PDF;
use App\Models\FSPool;
use App\Models\Pledge;
use App\Models\Charity;
use App\Models\CampaignYear;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Models\PledgeCharity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\DonateStep0Request;
use App\Http\Requests\DonateStep1Request;
use App\Http\Requests\DonateStep2Request;
use App\Http\Requests\DonateStep3Request;
use App\Http\Requests\CreatePledgeRequest;
use App\Http\Requests\DonateStep1aRequest;

class CharityController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth','campaign']);
    }

    public function index(Request $request)
    {
        if (!$request->ajax()) {
            return response()->setStatusCode(400);
        }

        $response = Charity::where('charity_name', 'LIKE', '%'.$request->search.'%')->paginate(10)->through(function ($charity, $key) {
            return [
                'id' => $charity->id,
                'text' => $charity->charity_name,
            ];
        });

        return $this->send($response, 'Charity listed successfully');
    }

    public function start(Request $request)
    {

        // Only allow when the campaign pledge period is opened
        $campaignYear = CampaignYear::where('calendar_year', '<=', today()->year + 1 )
                            ->orderBy('calendar_year', 'desc')->first();
        if ( !$campaignYear->isOpen() ) {
    //        return redirect()->route('donations.list');
        }

        $pool_option = "C";
        if (Session::has('pool_option')) {
            $pool_option = Session::get('pool_option');
        } else {

            // reload the existig pledge
            $errors = session('errors');
            if (!$errors) {

                $campaignYear = CampaignYear::where('calendar_year', '<=', today()->year + 1 )->orderBy('calendar_year', 'desc')
                                    ->first();
                $pledge = Pledge::where('user_id', Auth::id())
                                ->whereHas('campaign_year', function($q){
                                    $q->where('calendar_year','=', today()->year + 1 );
                                })->first();

                if ( $campaignYear->isOpen() && $pledge )  {

                    if ($pledge->fund_supported_pool) {
                        $pool_option = 'P';
                    } else {
                        $pool_option = 'C';

                        // $_ids = $pledge->charities->pluck(['charity_id'])->unique()->toArray();

                        // $_charities = Charity::whereIn('id', $_ids )
                        //                 ->get(['id', 'charity_name as text']);

                        // foreach ($_charities as $charity) {
                        //     $pledge_charity = $pledge->charities->where('charity_id', $charity->id)->first();

                        //     $charity['additional'] = '';
                        //     if ($pledge_charity) {
                        //         $charity['additional'] = $pledge_charity->additional ?? '';
                        //     }

                        //     array_push($selected_charities, $charity);
                        // }
                    }
                }
            }
        }

        return view('donate.start', compact('pool_option'));
    }

    public function savePoolOption(DonateStep0Request $request)
    {
        if ($request->has('cancel')) {
            session()->forget('pool_option');
            session()->forget('regional_pool_id');
            session()->forget('charities');
            return redirect()->route('donations.list');
        }

        Session()->put('pool_option', $request->pool_option);
        if ($request->pool_option == 'P') {
            return redirect()->route('donate.regional-pool');
        }
        return redirect()->route('donate.select-charities');
    }


    public function regionalPool(Request $request)
    {
        $pools = FSPool::where('start_date', '=', function ($query) {
                        $query->selectRaw('max(start_date)')
                                ->from('f_s_pools as A')
                                ->whereColumn('A.region_id', 'f_s_pools.region_id')
                                ->where('A.start_date', '<=', today());
                    })
                    ->where('status', 'A')
                    ->get();

        $regional_pool_id = $pools->count() > 0 ? $pools->first()->id : null;
        if (Session::has('regional_pool_id')) {
            $regional_pool_id = Session::get('regional_pool_id');

        } else {

            // reload the existig regional pool id
            $errors = session('errors');
            if (!$errors) {

                $pledge = Pledge::where('user_id', Auth::id())
                                ->whereHas('campaign_year', function($q){
                                $q->where('calendar_year','=', today()->year + 1 );
                            })->first();

                if ($pledge) {
                    $regional_pool_id = $pledge->f_s_pool_id;
                }
            }
        }

        return view('donate.regional-pool', compact('regional_pool_id', 'pools'));

    }

    public function regionalPoolDetail($id)
    {
        $pool = FSPool::where('id', $id)->first();
        $charities = $pool ? $pool->charities : [];

        return view('donate.partials.pool-detail', compact('charities') )->render();
    }

    public function saveRegionalPool(DonateStep1aRequest $request)
    {
        // if ($request->has('cancel')) {
        //     session()->forget('pool_option');
        //     // session()->forget('charities');
        //     return redirect()->route('donations.list');
        // }

        Session()->put('regional_pool_id', $request->regional_pool_id);
        // if ($request->pool_option == 'P') {
        //     return redirect()->route('donate.regional-pool');
        // }
        return redirect()->route('donate.amount');
    }

    public function select(Request $request)
    {

        /*
        $charities = [];
        if (Session::has('charities')) {
            $selectedCharities = Session::get('charities');

            $_charities = Charity::whereIn('id', $selectedCharities['id'])
                ->get(['id', 'charity_name as text']);

            foreach ($_charities as $charity) {
                $charity['additional'] = $selectedCharities['additional'][array_search($charity['id'], $selectedCharities['id'])];
                if (!$charity['additional']) {
                    $charity['additional'] = '';
                }

                array_push($charities, $charity);
            }
        }

        return view('donate.select', compact('charities'));
        */

        $terms = explode(" ", $request->get("title") );
        $charities=Charity::when($request->has("title"),function($q)use($request){

            $searchValues = preg_split('/\s+/', $request->get("title"), -1, PREG_SPLIT_NO_EMPTY);

            if ($request->get("designation_code")) {
                $q->where('designation_code', $request->get("designation_code"));
            }
            if ($request->get("category_code")) {
                $q->where('category_code', $request->get("category_code"));
            }
            if ($request->get("province")) {
                $q->where('province', $request->get("province"));
            }

            foreach ($searchValues as $term) {
                $q->whereRaw("LOWER(charity_name) LIKE '%" . strtolower($term) . "%'");
            }
            return $q->orderby('charity_name','asc');

        })->where('charity_status','Registered')->paginate(10);

        $designation_list = Charity::DESIGNATION_LIST;
        $category_list = Charity::CATEGORY_LIST;
        $province_list = Charity::PROVINCE_LIST;

        $selected_charities = [];
        //Session::forget("charities");
        if (Session::has('charities')) {
            $selectedCharities = Session::get('charities');

            $_charities = Charity::whereIn('id', $selectedCharities['id'])
                ->get(['id', 'charity_name as text']);

            if(!empty($_charities)){
                foreach ($_charities as $charity) {
                    $charity['additional'] = $selectedCharities['additional'][array_search($charity['id'], $selectedCharities['id'])];
                    if (!$charity['additional']) {
                        $charity['additional'] = '';
                    }

                    array_push($selected_charities, $charity);
                }
            }
        } else {

            // reload the existig pledge
            $errors = session('errors');

            if (!$errors) {

                $campaignYear = CampaignYear::where('calendar_year', '<=', today()->year + 1 )->orderBy('calendar_year', 'desc')
                ->first();
                $pledge = Pledge::where('user_id', Auth::id())
                                ->whereHas('campaign_year', function($q){
                                    $q->where('calendar_year','=', today()->year + 1 );
                                })->first();

                if ( $campaignYear->isOpen() && $pledge && count($pledge->charities) > 0 )  {

                    $_ids = $pledge->charities->pluck(['charity_id'])->toArray();

                    $_charities = Charity::whereIn('id', $_ids )
                                    ->get(['id', 'charity_name as text']);

                    foreach ($_charities as $charity) {
                        $pledge_charity = $pledge->charities->where('charity_id', $charity->id)->first();

                        $charity['additional'] = '';
                        if ($pledge_charity) {
                            $charity['additional'] = $pledge_charity->additional ?? '';
                        }

                        array_push($selected_charities, $charity);
                    }
                }
            }
        }


        if($request->ajax()){
            return view('donate.partials.charity-pagination', compact('charities','terms','designation_list','category_list','province_list','selected_charities') );
        }

        $multiple = true;
        $organizations = [];
        return view('donate.select', compact('organizations','multiple','charities','terms','designation_list','category_list','province_list','selected_charities'));
    }

    // public function edit(Request $request, $id = null) {
    //     // TODO: (JP) Reload the charity if exists

    //     $campaignYear = CampaignYear::where('calendar_year', '<=', today()->year + 1 )->orderBy('calendar_year', 'desc')
    //             ->first();
    //     $cy_pledges = Pledge::where('user_id', Auth::id())->onlyCampaignYear( today()->year + 1 )
    //                         ->get();

    //     if ( $campaignYear->isOpen() && count($cy_pledges) > 0 )  {
    //         $selectedCharities = ['id' => [], 'additional' => [] ];
    //         foreach ($cy_pledges as $pledge) {
    //             foreach ($pledge->charities as $charity) {
    //                 if (!(in_array($charity->charity_id , $selectedCharities['id']))) {
    //                     array_push($selectedCharities['id'], $charity->charity_id);
    //                     array_push($selectedCharities['additional'], $charity->additional);
    //                 }
    //             }
    //         }

    //         Session()->put('charities', $selectedCharities);
    //         //dd( [$cy_pledges, $campaignYear, $request, $selectedCharities]);
    //             // load onto the session
    //             return redirect()->route('donate');

    //     }

    //     return redirect()->route('donations.list');

    // }

    public function show(Request $request)
    {
        $charity = Charity::where('id', $request->charity_id)->first();

        if($request->ajax()){
            return view('donate.partials.charity', compact('charity') );
        }
    }

    public function remove()
    {
        $id = request()->charity_id;
        if (Session::has('charities')) {
            $selectedCharities = Session::get('charities');

            foreach ($selectedCharities['id'] as $key => $value) {
                if ($value == $id) {
                    unset($selectedCharities['id'][$key]);
                    unset($selectedCharities['additional'][$key]);
                }
            }
        }
        Session()->remove('charaties');
        Session()->put('charities', $selectedCharities);

        return $id;
    }

    public function saveCharities(DonateStep1Request $request)
    {
        // if ($request->has('cancel')) {
        //     session()->forget('charities');
        //     return redirect()->route('donations.list');
        // }

        Session()->put('charities', $request->validated());

        return redirect()->route('donate.amount');
    }

    public function amount()
    {
        $preselectedAmountOneTime = 20;
        $preselectedAmountBiWeekly = 20;

        $preselectedData = [
            'frequency' => 'bi-weekly',
            'one-time-amount' => 20,
            'bi-weekly-amount' => 50,
        ];

        if (Session::has('amount-step')) {
            $preselectedData = Session::get('amount-step');
            $preselectedAmountOneTime = $preselectedData['one-time-amount'];
            $preselectedAmountBiWeekly = $preselectedData['bi-weekly-amount'];
        }

        $amounts = [
            'bi-weekly' => [
                [
                    'amount' => 6,
                    'text' => '$6',
                    'selected' => ($preselectedAmountBiWeekly == 6) ? true : false,
                ],
                [
                    'amount' => 12,
                    'text' => '$12',
                    'selected' => ($preselectedAmountBiWeekly == 12) ? true : false,
                ],
                [
                    'amount' => 20,
                    'text' => '$20',
                    'selected' => ($preselectedAmountBiWeekly == 20) ? true : false,
                ],
                [
                    'amount' => 50,
                    'text' => '$50',
                    'selected' => ($preselectedAmountBiWeekly == 50) ? true : false,
                ],
                [
                    'amount' => '',
                    'text' => 'Custom',
                    'selected' => (!in_array($preselectedAmountBiWeekly, [6, 12, 20, 50])) ? true : false,
                ],
            ],
            'one-time' => [
                [
                    'amount' => 6,
                    'text' => '$6',
                    'selected' => ($preselectedAmountOneTime == 6) ? true : false,
                ],
                [
                    'amount' => 12,
                    'text' => '$12',
                    'selected' => ($preselectedAmountOneTime == 12) ? true : false,
                ],
                [
                    'amount' => 20,
                    'text' => '$20',
                    'selected' => ($preselectedAmountOneTime == 20) ? true : false,
                ],
                [
                    'amount' => 50,
                    'text' => '$50',
                    'selected' => ($preselectedAmountOneTime == 50) ? true : false,
                ],
                [
                    'amount' => '',
                    'text' => 'Custom',
                    'selected' => (!in_array($preselectedAmountOneTime, [6, 12, 20, 50])) ? true : false,
                ],
            ],
        ];

        $isCustomAmountOneTime = (!in_array($preselectedAmountOneTime, [6, 12, 20, 50])) ? true : false;
        $isCustomAmountBiWeekly = (!in_array($preselectedAmountBiWeekly, [6, 12, 20, 50])) ? true : false;
        $pool_option = Session::get('pool_option');
        $regional_pool_id = Session::has('pool_option') ? Session::get('regional_pool_id') : '';

        return view('donate.amount', compact('amounts', 'preselectedData', 'isCustomAmountOneTime', 'isCustomAmountBiWeekly','pool_option', 'regional_pool_id'));
    }

    public function saveAmount(DonateStep2Request $request)
    {
        $request->session()->put('amount-step', $request->validated());

        $pool_option = Session::get('pool_option');

        if ($pool_option == 'P') {
            return redirect()->route('donate.summary');
        }

        return redirect()->route('donate.distribution');
    }

    public function distribution($getData = false,$request=[]) {

        $pool_option = Session::get('pool_option');

        if(empty($request->download_pdf))
        {
            $request = new \stdClass();
            $request->download_pdf = false;
        }


        if ($pool_option == 'C') {
            if (!Session::has('charities')) {
                return redirect()->route('donate');
            }
            $selectedCharities = Session::get('charities');
            $frequency = Session::get('amount-step')['frequency'];

            $oneTimeAmount = ($frequency === 'one-time' || $frequency === 'both') ? Session::get('amount-step')['one-time-amount'] : 0;
            $biWeeklyAmount = ($frequency === 'bi-weekly' || $frequency === 'both') ? Session::get('amount-step')['bi-weekly-amount'] : 0;

            $annualBiWeeklyAmount = $biWeeklyAmount * 26;
            $annualOneTimeAmount = $oneTimeAmount;

            $oneTimeAmountPerCharity = round($oneTimeAmount / count($selectedCharities['id']), 2);
            $biWeeklyAmountPerCharity = round($biWeeklyAmount / count($selectedCharities['id']), 2);

            $oneTimePercentPerCharity = round(100 / count($selectedCharities['id']), 2);
            $biWeeklyPercentPerCharity = round(100 / count($selectedCharities['id']), 2);

            $charities = [];

            $charitiesDB = Charity::whereIn('id', $selectedCharities['id'])
                ->get(['id', 'charity_name as text']);

            $calculatedTotalPercentOneTime = 0;
            $calculatedTotalAmountOneTime = 0;
            $calculatedTotalPercentBiWeekly = 0;
            $calculatedTotalAmountBiWeekly = 0;
            $grandTotal = 0;
            foreach ($charitiesDB as $charity) {
                $charity = $charity->toArray();
                $charity['additional'] = $selectedCharities['additional'][array_search($charity['id'], $selectedCharities['id'])];
                if (!$charity['additional']) {
                    $charity['additional'] = '';
                }

                $charity['one-time-amount-distribution'] = $oneTimeAmountPerCharity;
                $charity['one-time-percentage-distribution'] = $oneTimePercentPerCharity;

                $charity['bi-weekly-amount-distribution'] = $biWeeklyAmountPerCharity;
                $charity['bi-weekly-percentage-distribution'] = $biWeeklyPercentPerCharity;

                // Override from session
                foreach (['one-time-amount-distribution', 'one-time-percentage-distribution', 'bi-weekly-amount-distribution', 'bi-weekly-percentage-distribution'] as $key) {
                    if (isset($selectedCharities[$key])) {
                        $charity[$key] = $selectedCharities[$key][array_search($charity['id'], $selectedCharities['id'])];
                    }
                }

                $charity['one-time-amount-distribution'] = round($oneTimeAmount * $charity['one-time-percentage-distribution'] / 100, 2);
                $charity['bi-weekly-amount-distribution'] = round($biWeeklyAmount * $charity['bi-weekly-percentage-distribution'] / 100, 2);

                $calculatedTotalPercentOneTime += $charity['one-time-percentage-distribution'];
                $calculatedTotalAmountOneTime += $charity['one-time-amount-distribution'];
                $calculatedTotalPercentBiWeekly += $charity['bi-weekly-percentage-distribution'];
                $calculatedTotalAmountBiWeekly += $charity['bi-weekly-amount-distribution'];

                $grandTotal += $charity['one-time-amount-distribution'];
                $grandTotal += ($charity['bi-weekly-amount-distribution'] * 26);
                array_push($charities, $charity);

            }
            // Correct Rounding Error for Total for last Charity
            $lastIndex = count($charities) - 1;
            $charities[$lastIndex]['one-time-percentage-distribution'] = $charities[$lastIndex]['one-time-percentage-distribution'] + (100 - $calculatedTotalPercentOneTime);
            $charities[$lastIndex]['one-time-amount-distribution'] = $charities[$lastIndex]['one-time-amount-distribution'] + ($oneTimeAmount - $calculatedTotalAmountOneTime);
            $charities[$lastIndex]['bi-weekly-percentage-distribution'] = $charities[$lastIndex]['bi-weekly-percentage-distribution'] + (100 - $calculatedTotalPercentBiWeekly);
            $charities[$lastIndex]['bi-weekly-amount-distribution'] = $charities[$lastIndex]['bi-weekly-amount-distribution'] + ($biWeeklyAmount - $calculatedTotalAmountBiWeekly);
        } else {
            if (!Session::has('regional_pool_id')) {
                return redirect()->route('donate');
            }

            $pool_id = Session()->get('regional_pool_id');
            $pool = FSPool::where('id', $pool_id)->first();
            $pool_charities = $pool ? $pool->charities : [];

            $frequency = Session::get('amount-step')['frequency'];

            $oneTimeAmount = ($frequency === 'one-time' || $frequency === 'both') ? Session::get('amount-step')['one-time-amount'] : 0;
            $biWeeklyAmount = ($frequency === 'bi-weekly' || $frequency === 'both') ? Session::get('amount-step')['bi-weekly-amount'] : 0;

            $annualBiWeeklyAmount = $biWeeklyAmount * 26;
            $annualOneTimeAmount = $oneTimeAmount;

            $calculatedTotalPercentOneTime = 0;
            $calculatedTotalAmountOneTime = 0;
            $calculatedTotalPercentBiWeekly = 0;
            $calculatedTotalAmountBiWeekly = 0;
            $grandTotal = 0;

            $charities = [];

            foreach ($pool_charities as $key => $pool_charity) {
                $charity = $pool_charity->charity->toArray();
                $charity['text'] = $pool_charity->charity->charity_name;
                $charity['additional'] = '';

                $percentage = $pool_charity->percentage;

                if ($key === count($pool_charities) - 1  ) {

                    $charity['one-time-amount-distribution'] = $oneTimeAmount - $calculatedTotalAmountOneTime ;
                    $charity['one-time-percentage-distribution'] = $pool_charity->percentage;

                    $charity['bi-weekly-amount-distribution'] =  $biWeeklyAmount - $calculatedTotalAmountBiWeekly ;
                    $charity['bi-weekly-percentage-distribution'] = $pool_charity->percentage;

                    $calculatedTotalPercentOneTime += $charity['one-time-percentage-distribution'];
                    $calculatedTotalAmountOneTime += $charity['one-time-amount-distribution'];
                    $calculatedTotalPercentBiWeekly += $charity['bi-weekly-percentage-distribution'];
                    $calculatedTotalAmountBiWeekly += $charity['bi-weekly-amount-distribution'];

                    $grandTotal += $charity['one-time-amount-distribution'];
                    $grandTotal += ($charity['bi-weekly-amount-distribution'] * 26);
                    // $grandTotal += ($charity['bi-weekly-amount-distribution'] );

                    array_push($charities, $charity);

                } else {

                    $charity['one-time-amount-distribution'] = round(($pool_charity->percentage * $annualOneTimeAmount) / 100 , 2);
                    $charity['one-time-percentage-distribution'] = $pool_charity->percentage;

                    $charity['bi-weekly-amount-distribution'] = round(($pool_charity->percentage * $annualBiWeeklyAmount) / 100 / 26 , 2);
                    $charity['bi-weekly-percentage-distribution'] = $pool_charity->percentage;

                    $calculatedTotalPercentOneTime += $charity['one-time-percentage-distribution'];
                    $calculatedTotalAmountOneTime += $charity['one-time-amount-distribution'];
                    $calculatedTotalPercentBiWeekly += $charity['bi-weekly-percentage-distribution'];
                    $calculatedTotalAmountBiWeekly += $charity['bi-weekly-amount-distribution'];

                    $grandTotal += $charity['one-time-amount-distribution'];
                    $grandTotal += ($charity['bi-weekly-amount-distribution'] * 26);
                    // $grandTotal += ($charity['bi-weekly-amount-distribution'] );
                    array_push($charities, $charity);

                };
            }
        }

        /* if ($freq == 'bi-weekly') {
            foreach ($charities as $key => $value) {
                $total += $value['amount-distribution'] * 26;
                $total = round($total);
            }
        } */
        $view = 'distribution';
        if (request()->is('donate/summary')) {
            $view = 'summary';
        }

        $view = 'donate.'.$view;


        $pool_option = Session::get('pool_option');
        $regional_pool_id = Session::get('regional_pool_id');

        // $multiplier = $freq == 'bi-weekly' ? 26 : 1;
        $multiplier = 1;
        // $total = "Yet To Calculated";
        $weekly = "ToDelete";

        $viewData = compact('charities', 'calculatedTotalPercentOneTime', 'calculatedTotalPercentBiWeekly', 'calculatedTotalAmountOneTime', 'calculatedTotalAmountBiWeekly', 'grandTotal', 'annualOneTimeAmount', 'annualBiWeeklyAmount', 'oneTimeAmount',
            'weekly', 'frequency', 'multiplier', 'pool_option', 'regional_pool_id');
        if ($getData) {
            return $viewData;
        }
        else if($request->download_pdf){
            $date = date("Y-m-d");
            view()->share('donations.index',compact('date','charities', 'calculatedTotalPercentOneTime', 'calculatedTotalPercentBiWeekly', 'calculatedTotalAmountOneTime', 'calculatedTotalAmountBiWeekly', 'grandTotal', 'annualOneTimeAmount', 'annualBiWeeklyAmount', 'oneTimeAmount',
                'weekly', 'frequency', 'multiplier', 'pool_option', 'regional_pool_id'));
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('donations.partials.summary', compact('date','charities', 'calculatedTotalPercentOneTime', 'calculatedTotalPercentBiWeekly', 'calculatedTotalAmountOneTime', 'calculatedTotalAmountBiWeekly', 'grandTotal', 'annualOneTimeAmount', 'annualBiWeeklyAmount', 'oneTimeAmount',
                'weekly', 'frequency', 'multiplier', 'pool_option', 'regional_pool_id'));
            return $pdf->download('Donation Summary.pdf');
        }
        else{
            return view($view, $viewData);
        }


    }

    public function distributionOld()
    {
        if (!Session::has('charities')) {
            return redirect()->route('donate');
        }
        $selectedCharities = Session::get('charities');

        $oneTimeAmount = Session::get('amount-step')['one-time-amount'];
        $biWeeklyAmount = Session::get('amount-step')['bi-weekly-amount'];
        $freq = Session::get('amount-step')['frequency'];

        $annual_amount_one_time = $oneTimeAmount;
        $onetime = $oneTimeAmount;
        $weekly = 0;

        $annual_amount_bi_wwekly = $biWeeklyAmount * 26;
        $weekly = $biWeeklyAmount;

        $selectedCharities = Session::get('charities');

        $individualAmountOneTime = round($oneTimeAmount / count($selectedCharities['id']), 2);
        $individualAmountBiWeekly = round($biWeeklyAmount / count($selectedCharities['id']), 2);
        $individualPercentOneTime = round(100 / count($selectedCharities['id']), 2);

        $charities = [];

        $_charities = Charity::whereIn('id', $selectedCharities['id'])
            ->get(['id', 'charity_name as text']);
        $total = 0;
        // To remove rounding error calculate total and all remaning amount/percent should be added to last.
        $totalPercent = 0;
        $totalAmount = 0;
        foreach ($_charities as $charity) {
            $charity = $charity->toArray();
            $charity['additional'] = $selectedCharities['additional'][array_search($charity['id'], $selectedCharities['id'])];
            if (!$charity['additional']) {
                $charity['additional'] = '';
            }

            $charity['percentage-distribution'] = $individualPercent;
            $charity['amount-distribution'] = $individualAmount;
            if (isset($selectedCharities['percentage-distribution'])) {
                $charity['percentage-distribution'] = $selectedCharities['percentage-distribution'][array_search($charity['id'], $selectedCharities['id'])];
            }

            if (isset($selectedCharities['amount-distribution'])) {
                $charity['amount-distribution'] = $selectedCharities['amount-distribution'][array_search($charity['id'], $selectedCharities['id'])];
            }

            $charity['amount-distribution'] = round($oneTimeAmount * $charity['percentage-distribution'] / 100, 2);

            $totalPercent += $charity['percentage-distribution'];
            $totalAmount += $charity['amount-distribution'];
            array_push($charities, $charity);
        }
        $lastIndex = count($charities) - 1;
        $charities[$lastIndex]['percentage-distribution'] = $charities[$lastIndex]['percentage-distribution'] + (100 - $totalPercent);
        $charities[$lastIndex]['amount-distribution'] = $charities[$lastIndex]['amount-distribution'] + ($oneTimeAmount - $totalAmount);
        if ($freq == 'bi-weekly') {
            foreach ($charities as $key => $value) {
                $total += $value['amount-distribution'] * 26;
                $total = round($total);
            }
        }
        $view = 'distribution';
        if (request()->is('donate/summary')) {
            $view = 'summary';
        }

        $view = 'donate.'.$view;


        $frequency = $freq == 'bi-weekly' ? "bi-weekly" : 'one time';
        $multiplier = $freq == 'bi-weekly' ? 26 : 1;
        return view($view, compact('charities', 'individualAmount', 'individualPercent', 'total', 'annual_amount_one_time', 'onetime', 'weekly', 'frequency', 'multiplier'));
    }

    public function saveDistribution(DonateStep3Request $request) {
        $input = $request->validated();
        if (!Session::has('charities')) {
            $campaignYear = CampaignYear::where('calendar_year', '<=', today()->year + 1 )->orderBy('calendar_year', 'desc')
                ->first();
            $pledge = Pledge::where('user_id', Auth::id())
                ->whereHas('campaign_year', function($q){
                    $q->where('calendar_year','=', today()->year + 1 );
                })->first();
            if ( $campaignYear->isOpen() && $pledge && count($pledge->charities) > 0 ) {
                $_ids = $pledge->charities->pluck(['charity_id'])->toArray();

                $charities = Charity::whereIn('id', $_ids)
                    ->get(['id', 'charity_name as text']);
            }
            if(empty($charities)){
                return redirect()->route('donate');
            }
        }else{
            $selectedCharities = Session::get('charities');
        }

        $preselectedData = Session::get('amount-step');
        $totalAmountOneTime = $preselectedData['one-time-amount'];
        $totalAmountBiWeekly = $preselectedData['bi-weekly-amount'];
        $frequency = $preselectedData['frequency'];

        if ($frequency === 'one-time' || $frequency === 'both') {
            if (array_key_exists('distributionByPercentOneTime', $input)) {
                // Correct $input['amount']
                foreach($input['oneTimePercent'] as $index => $a) {
                    $input['oneTimeAmount'][$index] = $totalAmountOneTime * $a / 100;
                }
            } else {
                // Correct $input['percent']
                foreach($input['oneTimeAmount'] as $index => $a) {
                    $input['oneTimePercent'][$index] = round(100 * $a / $totalAmountOneTime, 2);
                }
            }

            //
            foreach($input['oneTimePercent'] as $charityId => $percentageAmount) {
                $selectedCharities['one-time-percentage-distribution'][array_search($charityId, $selectedCharities['id'])] = $percentageAmount;
            }
            foreach($input['oneTimeAmount'] as $charityId => $amount) {
                $selectedCharities['one-time-amount-distribution'][array_search($charityId, $selectedCharities['id'])] = $amount;
            }
        }
        if ($frequency === 'bi-weekly' || $frequency === 'both') {
            if (array_key_exists('distributionByPercentBiWeekly', $input)) {
                // Correct $input['amount']
                foreach($input['biWeeklyPercent'] as $index => $a) {
                    $input['biWeeklyAmount'][$index] = $totalAmountBiWeekly * $a / 100;
                }
            } else {
                // Correct $input['percent']
                foreach($input['biWeeklyAmount'] as $index => $a) {
                    $input['biWeeklyPercent'][$index] = round(100 * $a / $totalAmountBiWeekly, 2);
                }
            }
            foreach($input['biWeeklyPercent'] as $charityId => $percentageAmount) {
                $selectedCharities['bi-weekly-percentage-distribution'][array_search($charityId, $selectedCharities['id'])] = $percentageAmount;
            }
            foreach($input['biWeeklyAmount'] as $charityId => $amount) {
                $selectedCharities['bi-weekly-amount-distribution'][array_search($charityId, $selectedCharities['id'])] = $amount;
            }
        }



        session()->put('charities', $selectedCharities);
        return redirect()->route('donate.summary');
    }

    public function summary(Request $request) {
        // Logic to show/calculate amount is already done in distribution fn, just view is different
        return $this->distribution(false,$request);
    }

    public function savePDF() {

        view()->share('employee',$data);
        $pdf = PDF::loadView('pdf_view', $data);
        // download PDF file with download method
        return $pdf->download('pdf_file.pdf');

       //  return view ('donate.pdf', $compact);
        $pdf = PDF::loadView('donate.pdf', $compact);
        return $pdf->download('Donation Summary.pdf');
    }

    public function confirmDonation(CreatePledgeRequest $request)
    {
        $input = $request->validated();
        DB::beginTransaction();
        $frequency = $input['frequency'];
        $multiplier = $frequency === 'OneTime' ? 1 : 26;

        // REMARK: always assign 'GOV' for the organization
        $organization = Organization::where('code', 'GOV')->first();
        $campaignYear = CampaignYear::where('calendar_year', today()->year + 1 )
                ->first();

        // dd(   [
        //         $input,
        //         $frequency,   // on-time

        //         $input['annualBiWeeklyAmount'],
        //         $input['annualOneTimeAmount'],
        //         // $input['annual'+$frequency+'Amount'],
        //         $multiplier,
        //         $input['pool_option'],
        //         // $input['regional_pool_id'],
        //         ]
        // );


        $pledge = Pledge::updateOrCreate([
            'organization_id' => $organization->id,
            'user_id' => Auth::id(),
            'campaign_year_id' => $campaignYear->id
        ],[
            'type' => $input['pool_option'],
            'f_s_pool_id' => $input['regional_pool_id'] ?? '',
            'one_time_amount' => $input['annualOneTimeAmount'],
            'pay_period_amount' => $input['annualBiWeeklyAmount'] / $multiplier,

            // 'amount' => $frequency === 'both' ? $input['annualBiWeeklyAmount'] / $multiplier + $input['annualOneTimeAmount']
            //     : ($frequency === 'one-time'  ? $input['annualOneTimeAmount']  : $input['annualBiWeeklyAmount'] ),
            // 'frequency' => $frequency === 'both' ? 'both' : ($frequency === 'BiWeekly' ? 'bi-weekly' : 'one time'),
            'goal_amount' => $frequency === 'both' ? $input['annualBiWeeklyAmount'] + $input['annualOneTimeAmount']
                : ($frequency === 'one-time'  ? $input['annualOneTimeAmount']  : $input['annualBiWeeklyAmount'] ),
        ]);

        $pledge->charities()->delete();

        if ($input['pool_option'] == 'C' ) {
            foreach(['OneTime', 'BiWeekly'] as $frequency) {
                if ($frequency === 'OneTime' && ($input['frequency'] !== 'one-time' && $input['frequency'] !== 'both')) {
                    continue;
                }
                if ($frequency === 'BiWeekly' && ($input['frequency'] !== 'bi-weekly' && $input['frequency'] !== 'both')) {
                    continue;
                }

                foreach ($input['charity'.$frequency.'Amount'] as $id => $amount) {
                    if ($amount <= 0) {
                        continue;
                    }
                    PledgeCharity::create([
                        'charity_id' => $id,
                        'pledge_id' => $pledge->id,
                        'additional' => $input['charityAdditional'][$id],
                        'percentage' => $input['charity'.$frequency.'Percentage'][$id],
                        'amount' => $amount,
                        'frequency' => $frequency === 'BiWeekly' ? 'bi-weekly' : 'one-time',
                        /* 'cheque_pending' => $multiplier, */
                        'goal_amount' => $frequency === 'BiWeekly' ? $amount * $multiplier : $amount,
                    ]);
                }
            }
        }

        DB::commit();

        $forPDF = [
            'charities' => $request->session()->get('charities'),
            'amount-step' => $request->session()->get('amount-step'),
            'request' => $request->validated()
        ];

        $request->session()->put('forPDF', $forPDF);
        $request->session()->forget(['pool_option', 'charities', 'amount']);


            return redirect()->route('donate.save.thank-you');


    }

    public function thankYou()
    {
        return view('donate.thankyou');
    }
}
