<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\FSPool;
use App\Models\Pledge;
use App\Models\Charity;
use App\Models\City;
use App\Models\CampaignYear;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Models\PledgeCharity;
use App\Models\PledgeHistory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\PledgeHistorySummary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\AnnualCampaignRequest;


class AnnualCampaignController extends Controller
{

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
        return redirect()->route('annual-campaign.create');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( Pledge $duplicate_pledge = null )
    {
        // redirect with session data when using duplicating function
        $duplicate_pledge = session()->has('new_pledge') ? session()->get('new_pledge') : $duplicate_pledge;

        // Only allow when the campaign pledge period is opened
        $campaign_year = CampaignYear::where('calendar_year', '<=', today()->year + 1 )
                            ->orderBy('calendar_year', 'desc')->first();
        if ( !$campaign_year->isOpen() ) {
            return redirect()->route('donations.list');
        }

        // 1) Initial the default values on the wizard
        $step = 1;
        $pool_option = 'P';

        // -- For Fund Support Pool page
        $fspools = FSPool::current()->where('status', 'A')->with('region')->get()->sortBy(function($pool, $key) {
            return $pool->region->name;
        });
        $regional_pool_id = $fspools->count() > 0 ? $fspools->first()->id : null;

        // -- For charities page
        $designation_list = Charity::DESIGNATION_LIST;
        $category_list = Charity::CATEGORY_LIST;
        $province_list = Charity::PROVINCE_LIST;

        $fund_support_pool_list = FSPool::current()->where('status', 'A')->with('region')->get()->sortBy(function($pool, $key) {
                                    return $pool->region->name;
                                  });
        $selected_charities = [];

        // Default Amount values
        $frequency ='bi-weekly';
        $preselectedAmountOneTime = 20;
        $preselectedAmountBiWeekly = 20;

        $preselectedData = [
            'frequency' => 'bi-weekly',
            'one-time-amount' => 20,
            'bi-weekly-amount' => 50,
        ];

        // Distribution page -- Keep the last selected charities (to determine any changes)
        $last_selected_charities = [];
        $last_one_time_amount = null;
        $last_bi_weekly_amount = null;


        $calculatedTotalPercentOneTime = 0;
        $calculatedTotalAmountOneTime = 0;
        $calculatedTotalPercentBiWeekly = 0;
        $calculatedTotalAmountBiWeekly = 0;
        $annualBiWeeklyAmount = 0;
        $annualOneTimeAmount = 0;
        $grandTotal = 0;
        $oneTimeAmount = 0;
        $oneTimeAmountEntered = 0;
        $biWeeklyAmountEntered = 0;

        // 2) Check whether the existing record entered, then reloading the data
        $organization = Organization::where('code', 'GOV')->first();
        $user = User::where('id', Auth::id())->first();

        $pledge = Pledge::where('organization_id', $user->organization_id ? $user->organization_id : $organization->id )
                            // ->where('user_id', Auth::id())
                            ->where('emplid', $user->emplid)
                            ->where('campaign_year_id', $campaign_year->id)
                            ->first();

        $is_duplicate = ($duplicate_pledge && !($pledge)) ? true : false;

        if ($pledge || $duplicate_pledge ) {

            $step = 1;

            if ($is_duplicate) {
                $pledge = $duplicate_pledge;

                $step = 3;  // Display summary page as a default if no error found

                // Validate Pool
                if ($pledge->type == 'P') {
                    // Find the default selected FS Pool ID
                    $pos = $fspools->search(function ($item, $key) use($pledge){
                        return $item->region_id ==  $pledge->region_id;
                    });
                    if ($pos >= 0) {
                        $regional_pool_id = $fspools[$pos]->id;
                        $step = 3;
                    } else {
                        $regional_pool_id = null;
                        $step = 1;
                    }
                } else {
                    // Find any inactive charity
                    // $charity_ids = $pledge->charities->pluck('charity_id');
                    $charity_ids = $pledge->charities->unique('charity_id')->pluck('charity_id');
                    $count = Charity::whereIn('id', $charity_ids)->where('charity_status', 'Registered')->count();
                    if ($count < $charity_ids->count()) {
                        $step = 1;
                    }
                }
                // dd( [ $fspools, $pledge->region_id,  $pledge->f_s_pool_id, $regional_pool_id , $pos, $fspools[$pos]] );

            } else {
                $regional_pool_id = $pledge->type == 'P' ? $pledge->f_s_pool_id : $regional_pool_id;
            }

            $pool_option = $pledge->type;
            // $regional_pool_id = $pledge->type == 'P' ? $pledge->f_s_pool_id : $regional_pool_id;
            $preselectedAmountOneTime = $pledge->one_time_amount > 0 ? $pledge->one_time_amount : $preselectedAmountOneTime;
            $preselectedAmountBiWeekly = $pledge->pay_period_amount > 0 ? $pledge->pay_period_amount : $preselectedAmountBiWeekly;

            $frequency = 'both';
            if ($pledge->one_time_amount > 0 && $pledge->pay_period_amount == 0) {
                $frequency = 'one-time';
            } if ($pledge->one_time_amount == 0 && $pledge->pay_period_amount > 0) {
                $frequency = 'bi-weekly';
            }

            $preselectedData = [
                'frequency' => $frequency,
                'one-time-amount' => $preselectedAmountOneTime,
                'bi-weekly-amount' => $preselectedAmountBiWeekly,
            ];

            // These fields for Distribution page
            $oneTimeAmount = $pledge->one_time_amount;
            $biWeeklyAmount =  $pledge->pay_period_amount;
            $last_one_time_amount = $pledge->one_time_amount;
            $last_bi_weekly_amount = $pledge->pay_period_amount;

            $annualBiWeeklyAmount = $biWeeklyAmount * $campaign_year->number_of_periods;  // 26;
            $annualOneTimeAmount = $oneTimeAmount;

            $calculatedTotalPercentOneTime = 0;
            $calculatedTotalAmountOneTime = 0;
            $calculatedTotalPercentBiWeekly = 0;
            $calculatedTotalAmountBiWeekly = 0;
            $grandTotal = 0;

            // $multiplier = $frequency == 'one-time' ? 1 : $campaign_year->number_of_periods; // 26;
            $selected_charities = [];
            $last_distribitions = [];


            // reload charity if the  pool_option is charities
            if ($pool_option == 'C') {

                if ($pledge && count($pledge->charities) > 0 )  {

                    $_ids = $pledge->charities->pluck(['charity_id'])->toArray();
                    $last_selected_charities = $_ids;

                    $_charities = Charity::whereIn('charities.id', $_ids )
                                    ->join('f_s_pool_charities', 'charities.id', 'f_s_pool_charities.charity_id')
                                    ->get(['charities.id', 'charity_name as text', 'f_s_pool_charities.name as program_name']);

                    foreach ($_charities as $charity) {
                        $pledge_charity = $pledge->charities->where('charity_id', $charity->id)->first();

                        $charity['additional'] = '';
                        if ($pledge_charity) {
                            $charity['additional'] = $pledge_charity->additional ?? '';
                        }

                        $charity['one-time-amount-distribution'] = 0;
                        $charity['one-time-percentage-distribution'] = 0;
                        $charity['bi-weekly-amount-distribution'] = 0;
                        $charity['bi-weekly-percentage-distribution'] = 0;

                        // Get One-Time Distribution if exists
                        if ($pledge->one_time_amount > 0) {
                            $pledge_one_time = $pledge->charities->where('charity_id', $charity->id)->where('frequency','one-time')
                                            ->first();

                            $charity['one-time-amount-distribution'] = $pledge_one_time->amount;
                            $charity['one-time-percentage-distribution'] = $pledge_one_time->percentage;
                        }

                        // Get BiWeekly Distribution if exists
                        if ($pledge->pay_period_amount > 0) {
                            $pledge_biweekly = $pledge->charities->where('charity_id', $charity->id)->where('frequency','bi-weekly')
                                            ->first();

                            $charity['bi-weekly-amount-distribution'] = $pledge_biweekly->amount;
                            $charity['bi-weekly-percentage-distribution'] = $pledge_biweekly->percentage;
                        }

                        // Keep for comparing purpose


                        $calculatedTotalPercentOneTime += $charity['one-time-percentage-distribution'];
                        $calculatedTotalAmountOneTime += $charity['one-time-amount-distribution'];
                        $calculatedTotalPercentBiWeekly += $charity['bi-weekly-percentage-distribution'];
                        $calculatedTotalAmountBiWeekly += $charity['bi-weekly-amount-distribution'];

                        $grandTotal += $charity['one-time-amount-distribution'];
                        $grandTotal += ($charity['bi-weekly-amount-distribution'] * $campaign_year->number_of_periods);  // 26

                        array_push($selected_charities, $charity);
                    }
                }

            }

        }


        // 3) Set the amount initial value
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

        $multiple = true;
        $organizations = [];

        $fsp_name = false;
// dd([ $is_duplicate, $duplicate_pledge, $pool_option, $pledge, $fspools, $regional_pool_id, $campaign_year ]);
        return view('annual-campaign.wizard', compact('fsp_name','step', 'pool_option',
                        'fspools', 'regional_pool_id',
                        'campaign_year',

                        'organizations', 'multiple', 'selected_charities',
                        'designation_list', 'category_list', 'province_list', 'fund_support_pool_list',
                        'amounts', 'preselectedData', 'isCustomAmountOneTime', 'isCustomAmountBiWeekly',

                        'calculatedTotalPercentOneTime', 'calculatedTotalPercentBiWeekly', 'calculatedTotalAmountOneTime', 'calculatedTotalAmountBiWeekly', 'grandTotal', 'annualOneTimeAmount', 'annualBiWeeklyAmount', 'oneTimeAmount',
                        'oneTimeAmountEntered', 'biWeeklyAmountEntered',
                         'frequency', // 'multiplier',
                         'last_selected_charities',
                         'last_one_time_amount', 'last_bi_weekly_amount',
                         'is_duplicate',
                    ));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AnnualCampaignRequest $request)
    {
        //
        if ($request->ajax()) {

            // Generate Distribution page or Summary Page
            if ($request->step == 3 and $request->pool_option == 'C') {
                return $this->distribution($request);
            }
            if (($request->step == 3 and $request->pool_option == 'P') or
                ($request->step == 4 and $request->pool_option == 'C')) {
                return $this->summary($request);
            }

            return response()->noContent();

        }


        /* Final submission -- form submission (non-ajax call) */
        $input = $request->validated();

        DB::beginTransaction();
        $frequency = $input['frequency'];
        $number_of_periods = $input['number_of_periods'];

        // REMARK: always assign 'GOV' for the organization
        $user = User::where('id', Auth::id() )->first();
        $organization = Organization::where('code', 'GOV')->first();
        $pool = FSPool::where('id', $request->regional_pool_id)->first();

        $business_unit =  $user->primary_job ? $user->primary_job->business_unit : null;
        // $tgb_reg_district =  $user->primary_job ? $user->primary_job->tgb_reg_district : null;
        $office_city = $user->primary_job ? $user->primary_job->office_city : null;
        $city = City::where('city', trim( $office_city )  )->first();
        $tgb_reg_district = $city ? $city->TGB_REG_DISTRICT : $user->primary_job->tgb_reg_district ;

        $old_pledge = Pledge::where('organization_id', $user->organization_id ? $user->organization_id : $organization->id)
                            ->where('emplid', $user->emplid)
                            ->where('campaign_year_id', $request->campaign_year_id)
                            ->first();
        $created_by_id = $old_pledge ? $old_pledge->created_by_id :  Auth::id();                           


        $pledge = Pledge::updateOrCreate([
            'organization_id' => $user->organization_id ? $user->organization_id : $organization->id,
            // 'user_id' => Auth::id(),
            'emplid' => $user->emplid,
            'campaign_year_id' => $request->campaign_year_id,
        ],[
            'user_id' => Auth::id(),

            'business_unit' => $business_unit,
            'tgb_reg_district' => $tgb_reg_district,
            'city' => $office_city,

            'type' => $input['pool_option'],
            'region_id' => $input['pool_option'] == 'P' ? $pool->region_id : null,
            'f_s_pool_id' => $input['pool_option'] == 'P' ? $input['regional_pool_id'] : 0,
            'one_time_amount' => $input['annualOneTimeAmount'],
            'pay_period_amount' => $input['annualBiWeeklyAmount'] / $number_of_periods,
            'goal_amount' => $frequency === 'both' ? $input['annualBiWeeklyAmount'] + $input['annualOneTimeAmount']
                                 : ($frequency === 'one-time'  ? $input['annualOneTimeAmount']  : $input['annualBiWeeklyAmount'] ),
            'created_by_id' => $created_by_id,
            'updated_by_id' => Auth::id(),
        ]);

        // $pledge->charities()->delete();
        foreach ($pledge->charities as $pledge_charity) {
            $pledge_charity->delete();
        }

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
                        'goal_amount' => $frequency === 'BiWeekly' ? $amount * $number_of_periods : $amount,
                    ]);
                }
            }
        }
        DB::commit();

        $message_text = 'Pledge with Transaction ID ' . $pledge->id . ' have been created successfully';

        Session::flash('pledge_id', $pledge->id );
        return redirect()->route('annual-campaign.thank-you')
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


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pledge  $pledge
     * @return \Illuminate\Http\Response
     */
    public function update(AnnualCampaignRequest $request, $id)
    {

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
            return view('annual-campaign.thankyou', compact('pledge_id') );
        } else {
            return abort(403);
        }

    }


    public function distribution($request=[]) {

        $last_selected_charities = json_decode($request->last_selected_charities) ?? [];
        $last_one_time_amount = $request->last_one_time_amount ?? null;
        $last_bi_weekly_amount = $request->last_bi_weekly_amount ?? null;

        // charities change ?
        $charities_changed = false;
        if ( count(array_diff($last_selected_charities, $request->charities)) > 0 ||
             count(array_diff( $request->charities, $last_selected_charities)) > 0 ) {

            $charities_changed = true;
        }
        $last_selected_charities = $request->charities;

        // To recalculate the distributions
        $selectedCharities = $request->charities;
        $frequency =  $request->frequency;

        $oneTimeAmount = ($frequency === 'one-time' || $frequency === 'both') ?
                            ($request->one_time_amount ? $request->one_time_amount : $request->one_time_amount_custom) : 0 ;
        $biWeeklyAmount = ($frequency === 'bi-weekly' || $frequency === 'both') ?
                            ($request->bi_weekly_amount ? $request->bi_weekly_amount : $request->bi_weekly_amount_custom) : 0 ;

        // Amount Changed ?
        $onetime_amount_changed = false;
        $biweekly_amount_changed = false;
        if ($last_one_time_amount != $oneTimeAmount) {
            $onetime_amount_changed = true;
        }
        if ($last_bi_weekly_amount != $biWeeklyAmount) {
            $biweekly_amount_changed = true;
        }
        // assign the current selection to store for comparison
        $last_one_time_amount = $oneTimeAmount;
        $last_bi_weekly_amount = $biWeeklyAmount;

        $oneTimePercent = $request->oneTimePercent ? array_sum($request->oneTimePercent) : 100;
        $biWeeklyPercent = $request->biWeeklyPercent ? array_sum($request->biWeeklyPercent) : 100;

        if ($charities_changed) {
            $oneTimePercent = 100;
            $biWeeklyPercent = 100;
        } else {

            if ($onetime_amount_changed ) {
                $oneTimeAmount = $oneTimeAmount ;
                $oneTimePercent = 100;
            } else {
                $oneTimeAmount = $request->oneTimeAmount ? array_sum($request->oneTimeAmount) : null;
            }
            if ($biweekly_amount_changed ) {
                $biWeeklyAmount = $biWeeklyAmount;
                $biWeeklyPercent = 100;
            } else {
                $biWeeklyAmount = $request->biWeeklyAmount ? array_sum($request->biWeeklyAmount) : null;
            }

        }

        // Keep user entered expected-total
        $oneTimeAmountEntered = $oneTimeAmount;
        $biWeeklyAmountEntered = $biWeeklyAmount;

        $annualBiWeeklyAmount = $biWeeklyAmount * $request->number_of_periods; //  26;
        $annualOneTimeAmount = $oneTimeAmount;

// dd([ $onetime_amount_changed, $biweekly_amount_changed, $oneTimeAmount, $biWeeklyAmount, $request]);

        $selected_charities = [];

        $calculatedTotalPercentOneTime = 0;
        $calculatedTotalAmountOneTime = 0;
        $calculatedTotalPercentBiWeekly = 0;
        $calculatedTotalAmountBiWeekly = 0;
        $grandTotal = 0;

        foreach ($selectedCharities as $index => $selected_charity) {

            $charity = Charity::where('id', $selected_charity)
                            ->select(['id', 'charity_name as text'])->first();

            $charity = Charity::where('charities.id', $selected_charity )
                            ->join('f_s_pool_charities', 'charities.id', 'f_s_pool_charities.charity_id')
                            ->select(['charities.id', 'charity_name as text', 'f_s_pool_charities.name as program_name'])->first();     

            $charity['additional'] = $request->additional[$index] ?? '';

            if ($index == count($selectedCharities) - 1 ) {

                if($charities_changed || $onetime_amount_changed) {
                    $charity['one-time-amount-distribution'] = $oneTimeAmount - $calculatedTotalAmountOneTime ;
                    $charity['one-time-percentage-distribution'] = $oneTimePercent - $calculatedTotalPercentOneTime;
                } else {
                    $charity['one-time-amount-distribution'] = $request->has('oneTimeAmount') ? $request->oneTimeAmount[$selected_charity] : 0;
                    $charity['one-time-percentage-distribution'] = $request->has('oneTimePercent') ? $request->oneTimePercent[$selected_charity] :0 ;
                }

                if($charities_changed || $biweekly_amount_changed) {
                    $charity['bi-weekly-amount-distribution'] =  $biWeeklyAmount - $calculatedTotalAmountBiWeekly ;
                    $charity['bi-weekly-percentage-distribution'] = $biWeeklyPercent - $calculatedTotalPercentBiWeekly;
                } else {
                    $charity['bi-weekly-amount-distribution'] = $request->has('biWeeklyAmount') ? $request->biWeeklyAmount[$selected_charity] : 0;
                    $charity['bi-weekly-percentage-distribution'] = $request->has('biWeeklyPercent') ? $request->biWeeklyPercent[$selected_charity] : 0;
                }

                $calculatedTotalPercentOneTime += $charity['one-time-percentage-distribution'];
                $calculatedTotalAmountOneTime += $charity['one-time-amount-distribution'];
                $calculatedTotalPercentBiWeekly += $charity['bi-weekly-percentage-distribution'];
                $calculatedTotalAmountBiWeekly += $charity['bi-weekly-amount-distribution'];

                $grandTotal += $charity['one-time-amount-distribution'];
                $grandTotal += ($charity['bi-weekly-amount-distribution'] * $request->number_of_periods);  // 26
                array_push($selected_charities, $charity);

            } else {

                // percentage or amount based on input
                $oneTimeAmountPerCharity = 0;
                $oneTimePercentPerCharity = 0;
                $biWeeklyAmountPerCharity = 0;
                $biWeeklyPercentPerCharity =0;

                if ($charities_changed) {

                    $oneTimeAmountPerCharity = round($oneTimeAmount / count($selectedCharities), 2);
                    $oneTimePercentPerCharity = round(100 / count($selectedCharities), 2);
                    $biWeeklyAmountPerCharity = round($biWeeklyAmount / count($selectedCharities), 2);
                    $biWeeklyPercentPerCharity = round(100 / count($selectedCharities), 2);

                } else {

                    if ($frequency === 'one-time' || $frequency === 'both') {
                        if ($request->has('oneTimePercent')) {
                            if($onetime_amount_changed) {
                                // $oneTimeAmountPerCharity =  round(( $request->oneTimePercent[$selected_charity] * $oneTimeAmount) / 100 , 2);
                                // $oneTimePercentPerCharity = $request->oneTimePercent[$selected_charity];
                                $oneTimeAmountPerCharity = round($oneTimeAmount / count($selectedCharities), 2);
                                $oneTimePercentPerCharity = round(100 / count($selectedCharities), 2);
                            } else {
                                $oneTimeAmountPerCharity =  $request->oneTimeAmount[$selected_charity];
                                $oneTimePercentPerCharity = $request->oneTimePercent[$selected_charity];
                            }
                        } else {
                            $oneTimeAmountPerCharity = round($oneTimeAmount / count($selectedCharities), 2);
                            $oneTimePercentPerCharity = round(100 / count($selectedCharities), 2);
                        }
                    }

                    if ($frequency === 'bi-weekly' || $frequency === 'both') {
                        if ($request->has('biWeeklyPercent')) {
                            if ($biweekly_amount_changed) {
                                // $biWeeklyAmountPerCharity = round(( $request->biWeeklyPercent[$selected_charity] * $biWeeklyAmount) / 100 , 2);
                                // $biWeeklyPercentPerCharity = $request->biWeeklyPercent[$selected_charity];
                                $biWeeklyAmountPerCharity = round($biWeeklyAmount / count($selectedCharities), 2);
                                $biWeeklyPercentPerCharity = round(100 / count($selectedCharities), 2);
                            } else {
                                $biWeeklyAmountPerCharity = $request->biWeeklyAmount[$selected_charity];
                                $biWeeklyPercentPerCharity = $request->biWeeklyPercent[$selected_charity];
                            }
                        } else {
                            $biWeeklyAmountPerCharity = round($biWeeklyAmount / count($selectedCharities), 2);
                            $biWeeklyPercentPerCharity = round(100 / count($selectedCharities), 2);
                        }
                    }

                }

                $charity['one-time-amount-distribution'] = $oneTimeAmountPerCharity;
                $charity['one-time-percentage-distribution'] = $oneTimePercentPerCharity;
                $charity['bi-weekly-amount-distribution'] = $biWeeklyAmountPerCharity;
                $charity['bi-weekly-percentage-distribution'] = $biWeeklyPercentPerCharity;

                // accumulated total
                $calculatedTotalPercentOneTime += $charity['one-time-percentage-distribution'];
                $calculatedTotalAmountOneTime += $charity['one-time-amount-distribution'];
                $calculatedTotalPercentBiWeekly += $charity['bi-weekly-percentage-distribution'];
                $calculatedTotalAmountBiWeekly += $charity['bi-weekly-amount-distribution'];

                $grandTotal += $charity['one-time-amount-distribution'];
                $grandTotal += ($charity['bi-weekly-amount-distribution'] * $request->number_of_periods); //26
                array_push($selected_charities, $charity);
            }
        }

        $pool_option = $request->pool_option;
        $regional_pool_id = $request->regional_pool_id;

        $viewData = compact('selected_charities', 'calculatedTotalPercentOneTime', 'calculatedTotalPercentBiWeekly', 'calculatedTotalAmountOneTime', 'calculatedTotalAmountBiWeekly', 'grandTotal', 'annualOneTimeAmount', 'annualBiWeeklyAmount', 'oneTimeAmount',
             'oneTimeAmountEntered', 'biWeeklyAmountEntered',
             'frequency', 'pool_option', 'regional_pool_id',
             'last_selected_charities',
             'last_one_time_amount', 'last_bi_weekly_amount',
            );

        return view('annual-campaign.partials.distribution', $viewData)->render();

    }


    public function summary($request=[], $id=null) {

        $pool_option = $request->pool_option;
        $frequency =  $request->frequency;
        $number_of_periods = $request->number_of_periods;

        $oneTimeAmount = ($frequency === 'one-time' || $frequency === 'both') ?
                            ($request->one_time_amount ? $request->one_time_amount : $request->one_time_amount_custom) : 0 ;
        $biWeeklyAmount = ($frequency === 'bi-weekly' || $frequency === 'both') ?
                            ($request->bi_weekly_amount ? $request->bi_weekly_amount : $request->bi_weekly_amount_custom) : 0 ;

        if ($pool_option == 'C') {

            $selectedCharities = $request->charities;
            $frequency =  $request->frequency;

            $annualBiWeeklyAmount = $biWeeklyAmount * $number_of_periods; // 26;
            $annualOneTimeAmount = $oneTimeAmount;

            $charities = [];

            $calculatedTotalPercentOneTime = 0;
            $calculatedTotalAmountOneTime = 0;
            $calculatedTotalPercentBiWeekly = 0;
            $calculatedTotalAmountBiWeekly = 0;
            $grandTotal = 0;
            // foreach ($charitiesDB as $charity) {
            foreach ($selectedCharities as $index => $selected_charity) {

                $charity = Charity::where('id', $selected_charity)
                                ->select(['id', 'charity_name as text'])->first();

                // $charity = $charity->toArray();
                $charity['additional'] = $request->additional[$index] ?? '';

                $charity['one-time-amount-distribution'] = ($frequency == 'one-time' || $frequency == 'both') ?  $request->oneTimeAmount[$selected_charity] : 0;
                $charity['one-time-percentage-distribution'] = ($frequency == 'one-time' || $frequency == 'both') ?  $request->oneTimePercent[$selected_charity] : 0;
                $charity['bi-weekly-amount-distribution'] = ($frequency == 'bi-weekly' || $frequency == 'both') ? $request->biWeeklyAmount[$selected_charity] : 0;
                $charity['bi-weekly-percentage-distribution'] = ($frequency == 'bi-weekly' || $frequency == 'both') ?  $request->biWeeklyPercent[$selected_charity] : 0;

                $calculatedTotalPercentOneTime += $charity['one-time-percentage-distribution'];
                $calculatedTotalAmountOneTime += $charity['one-time-amount-distribution'];
                $calculatedTotalPercentBiWeekly += $charity['bi-weekly-percentage-distribution'];
                $calculatedTotalAmountBiWeekly += $charity['bi-weekly-amount-distribution'];

                $grandTotal += $charity['one-time-amount-distribution'];
                $grandTotal += ($charity['bi-weekly-amount-distribution'] * $number_of_periods); //26
                array_push($charities, $charity);

            }

        } else {

            $pool_id = $request->regional_pool_id;
            $pool = FSPool::where('id', $pool_id)->first();
            $pool_charities = $pool ? $pool->charities : [];

            $frequency = $request->frequency;

            $annualBiWeeklyAmount = $biWeeklyAmount * $number_of_periods;  // 26
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
                    $grandTotal += ($charity['bi-weekly-amount-distribution'] * $number_of_periods); //26
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
                    $grandTotal += ($charity['bi-weekly-amount-distribution'] * $number_of_periods);
                    // $grandTotal += ($charity['bi-weekly-amount-distribution'] );
                    array_push($charities, $charity);

                };
            }
        }

        $pool_option = $request->pool_option;
        $regional_pool_id = $request->regional_pool_id;
        $fsp_name = false;

        $viewData = compact('fsp_name','charities', 'calculatedTotalPercentOneTime', 'calculatedTotalPercentBiWeekly', 'calculatedTotalAmountOneTime', 'calculatedTotalAmountBiWeekly', 'grandTotal', 'annualOneTimeAmount', 'annualBiWeeklyAmount', 'oneTimeAmount',
             'frequency', 'number_of_periods', 'pool_option', 'regional_pool_id');
        return view('annual-campaign.partials.summary', $viewData)->render();

    }


    public function summaryPdf(Request $request, $id) {

        $pledge = Pledge::select("pledges.*")->with("campaign_year")->where('pledges.id', $id)->first();


        // Make sure this transaction is for the current logged user
        if (!$pledge) {
            return abort(404);
        } elseif  (!($pledge->user_id == Auth::id())) {
            return abort(403);
        }

        $number_of_periods = $pledge->campaign_year->number_of_periods;
        $frequency = $pledge->frequency;

        if ($pledge->type == 'C') {
            $fsp_name = false;
            $selectedCharities = $pledge->distinct_charities; //Session::get('charities');

            $oneTimeAmount = ($frequency === 'one-time' || $frequency === 'both') ? $pledge->one_time_amount : 0;
            $biWeeklyAmount = ($frequency === 'bi-weekly' || $frequency === 'both') ? $pledge->pay_period_amount : 0;

            $annualBiWeeklyAmount = $biWeeklyAmount * $number_of_periods; //26
            $annualOneTimeAmount = $oneTimeAmount;

            $charities = [];

            // $charitiesDB = Charity::whereIn('id', $selectedCharities)
            //     ->get(['id', 'charity_name as text']);

            $calculatedTotalPercentOneTime = 0;
            $calculatedTotalAmountOneTime = 0;
            $calculatedTotalPercentBiWeekly = 0;
            $calculatedTotalAmountBiWeekly = 0;
            $grandTotal = 0;

            foreach ($selectedCharities as $selected_charity) {

                $charity = Charity::where('id', $selected_charity->charity->id)
                                 ->select(['id', 'charity_name as text'])->first();
                $charity['additional'] = $selected_charity->additional;

                $one_time_pledge = $pledge->charities->where('charity_id', $selected_charity->charity_id)->where('frequency', 'one-time')->first();
                $biweekly_pledge = $pledge->charities->where('charity_id', $selected_charity->charity_id)->where('frequency', 'bi-weekly')->first();

                $charity['one-time-amount-distribution'] = $one_time_pledge ? $one_time_pledge->amount : 0;
                $charity['one-time-percentage-distribution'] = $one_time_pledge ? $one_time_pledge->percentage : 0;
                $charity['bi-weekly-amount-distribution'] = $biweekly_pledge ? $biweekly_pledge->amount : 0;
                $charity['bi-weekly-percentage-distribution'] = $biweekly_pledge ? $biweekly_pledge->percentage : 0;

                $calculatedTotalPercentOneTime += $charity['one-time-percentage-distribution'];
                $calculatedTotalAmountOneTime += $charity['one-time-amount-distribution'];
                $calculatedTotalPercentBiWeekly += $charity['bi-weekly-percentage-distribution'];
                $calculatedTotalAmountBiWeekly += $charity['bi-weekly-amount-distribution'];

                $grandTotal += $charity['one-time-amount-distribution'];
                $grandTotal += ($charity['bi-weekly-amount-distribution'] * $number_of_periods); //26
                array_push($charities, $charity);

            }

        } else {
            $fsp_name = $pledge->region->name;
            $pool_id = $pledge->f_s_pool_id;
            $pool = FSPool::where('id', $pool_id)->first();
            $pool_charities = $pool ? $pool->charities : [];

            $oneTimeAmount = ($frequency === 'one-time' || $frequency === 'both') ? $pledge->one_time_amount : 0;
            $biWeeklyAmount = ($frequency === 'bi-weekly' || $frequency === 'both') ? $pledge->pay_period_amount : 0;

            $annualBiWeeklyAmount = $biWeeklyAmount * $number_of_periods; //26
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
                    $grandTotal += ($charity['bi-weekly-amount-distribution'] * $number_of_periods); //26
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
                    $grandTotal += ($charity['bi-weekly-amount-distribution'] * $number_of_periods); //26
                    // $grandTotal += ($charity['bi-weekly-amount-distribution'] );
                    array_push($charities, $charity);

                };
            }
        }

        $pool_option = $pledge->pool_option;
        $regional_pool_id = $pledge->regional_pool_id;


        if($request->download_pdf){
            $date = date("Y-m-d");
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('annual-campaign.partials.pdf', compact('fsp_name','date','charities', 'calculatedTotalPercentOneTime', 'calculatedTotalPercentBiWeekly', 'calculatedTotalAmountOneTime', 'calculatedTotalAmountBiWeekly', 'grandTotal', 'annualOneTimeAmount', 'annualBiWeeklyAmount', 'oneTimeAmount',
                 'frequency', 'number_of_periods', 'pool_option', 'regional_pool_id', 'biWeeklyAmount'));
            return $pdf->download('Annual Campaign Summary - '.(intval($pledge->campaign_year->calendar_year) - 1).'.pdf');
        }

    }

    public function regionalPoolDetail(Request $request, $id)
    {

        if ($request->ajax()) {
            $pool = FSPool::where('id', $id)->first();
            $charities = $pool ? $pool->charities : [];

            return view('annual-campaign.partials.pool-detail', compact('charities') )->render();
        } else {
            return redirect('/');
        }

    }

    public function validDuplicate(Request $request, $id)
    {

        $msg = '';

        if ($request->source == 'GF') {

            $pledge = Pledge::where('id', $id)->first();

            // checking -- fund support pool
            if ($pledge->type == 'P') {

                // -- For Fund Support Pool page
                $fspools = FSPool::current()->where('status', 'A')->with('region')->get()->sortBy(function($pool, $key) {
                    return $pool->region->name;
                });

                // Find the default selected FS Pool ID
                $pos = $fspools->search(function ($item, $key) use($pledge){
                    return $item->region_id ==  $pledge->region_id;
                });
                if (!($pos >= 0)) {
                    $msg = "The Regional Pool you've selected is not available in the current campaign. Click here to see available regional pools, or alternatively you can select a different years choices from your donor history";
                }

            } else {
                // check charity
                // $charity_ids = $pledge->charities->pluck('charity_id');
                $charity_ids = $pledge->charities->unique('charity_id')->pluck('charity_id');

                $count = Charity::whereIn('id', $charity_ids)->where('charity_status', 'Registered')->count();

                if ($count < $charity_ids->count()) {
                    $msg = "One or more charity(ies) you've selected is not available in the current campaign. Click 'continue' to see available charities, or alternatively you can select a different years choices from your donor history";
                }

            }

        } else {

            $hist_pledge = PledgeHistorySummary::where('pledge_history_id', $id)
                                ->first();

            if ($hist_pledge->source == 'P') {
                $hist_region = $hist_pledge->region_by_code();

                // -- For Fund Support Pool page
                $fspools = FSPool::current()->where('status', 'A')->with('region')->get()->sortBy(function($pool, $key) {
                    return $pool->region->name;
                });

                // Find the default selected FS Pool ID
                $pos = $fspools->search(function ($item, $key) use($hist_region){
                    return $item->region_id ==  $hist_region->id;
                });
                if (!($pos >= 0)) {
                    $msg = "The Regional Pool you've selected is not available in the current campaign. Click 'Continue' to see available regional pools, or alternatively you can select a different years choices from your donor history";
                }

            } else {

                // 'Bi-Weekly'
                $bi_weekly_pledges = PledgeHistory::where('emplid', $hist_pledge->emplid)
                                ->where('yearcd', $hist_pledge->yearcd)
                                ->where('campaign_type', $hist_pledge->campaign_type)
                                ->where('frequency', 'Bi-Weekly')
                                ->orderBy('source')
                                ->get();

                // One-Time
                $one_time_pledges = PledgeHistory::where('emplid', $hist_pledge->emplid)
                                ->where('yearcd', $hist_pledge->yearcd)
                                ->where('campaign_type', $hist_pledge->campaign_type)
                                ->where('frequency', 'One-Time')
                                ->orderBy('source')
                                ->get();

                foreach( $bi_weekly_pledges as $index => $bi_weekly_pledge) {
                    $charity = Charity::where('id', $bi_weekly_pledge->charity->id)->where('charity_status', 'Registered')->first();
                    if (!$charity) {
                        $msg = "One or more charity(ies) you've selected is not available in the current campaign. Click here to see available charities, or alternatively you can select a different years choices from your donor history";
                        break;
                    }
                }

                foreach( $one_time_pledges as $index => $one_time_pledge) {
                    $charity = Charity::where('id', $one_time_pledge->charity->id)->where('charity_status', 'Registered')->first();
                    if (!$charity) {
                        $msg = "One or more charity(ies) you've selected is not available in the current campaign. Click here to see available charities, or alternatively you can select a different years choices from your donor history";
                        break;
                    }
                }

            }

        }

        return response()->json([
            'message' => $msg,
        ], 200);

    }


    public function duplicate(Request $request)
    {

        $user = User::where('id', Auth::id() )->first();

        // check whether the current annual campaign pledge exists or not
        $current_pledge = Pledge::join('campaign_years', 'campaign_years.id', 'campaign_year_id')
                                    ->where('campaign_years.calendar_year',  today()->year + 1 )
                                    // ->where('user_id', Auth::id() )
                                    ->where('emplid', $user->emplid )
                                    ->first();
        if ($current_pledge) {
            return redirect()->route('donations.list')->with('error','The Annual Campaign pledge have already created, no duplication allowed!');
            // return abort(409);       // Conflict (pledge already exists)
        }

        // Find the pledge
        $hist_pledge = null;
        if ($request->source == 'GF') {
            $hist_pledge = Pledge::where('id', $request->id)->first();
        } else {
            $hist_pledge = PledgeHistorySummary::where('pledge_history_id', $request->id)->first();
        }

// dd([$hist_pledge, $request]);
        if (!$hist_pledge) {
            return redirect()->route('donations.list')->with('error','The history record not found!');
        }
        if (!($hist_pledge->emplid == $user->emplid)) {
            return redirect()->route('donations.list')->with('error','This is not your history record!');
            // return abort(403);      // 403 Forbidden
        }
        if (!($hist_pledge->is_annual_campaign) && ($request->source == 'BI')) {
            return redirect()->route('donations.list')->with('error','The is not an Annual Campaign pledge!');
            // return abort(404);      // 404 Not Found
        }

        if(!empty($hist_pledge))
        {
            $campaignYear = CampaignYear::where('calendar_year', '<=', today()->year + 1 )
                                ->orderBy('calendar_year', 'desc')->first();
            $new_pledge = new Pledge();

            // Clone the new pledge from the specify pledge history
            if ($request->source == 'GF') {
                $pledge = Pledge::where('id', $request->id)->first();

                $new_pledge = $pledge->replicate()->fill([
                    'campaign_year_id' => $campaignYear->id,
                ]);

                // replicate the relationship
                $row = 0;
                foreach($pledge->charities as $index => $old_charity)
                {
                    $new_pledge_charity = new PledgeCharity();

                    $new_pledge_charity->charity_id =  $old_charity->charity_id;
                    $new_pledge_charity->additional =  $old_charity->additional;
                    $new_pledge_charity->percentage =  $old_charity->percentage;
                    $new_pledge_charity->amount =      $old_charity->amount;
                    $new_pledge_charity->frequency =   $old_charity->frequency;
                    $new_pledge_charity->goal_amount = $old_charity->goal_amount;

                    $new_pledge->charities[$row] = $new_pledge_charity;
                    $row += 1;
                }

            } else {

                $user = User::where('id', Auth::id())->first();
// dd([$hist_pledge, $hist_pledge->fund_supported_pool()->id ]);
                $new_pledge->user_id = $user->id;
                $new_pledge->organization_id = $user->organization_id;
                $new_pledge->campaign_year_id = $campaignYear->id;
                $new_pledge->type  = $hist_pledge->source;
                $new_pledge->region_id = $hist_pledge->source == 'P' ? $hist_pledge->region_by_code()->id : 0;
                $new_pledge->f_s_pool_id = $hist_pledge->source == 'P' ? $hist_pledge->fund_supported_pool()->id : 0;
                $new_pledge->pay_period_amount = 0;
                $new_pledge->one_time_amount = 0;

                // 'Bi-Weekly'
                $bi_weekly_pledges = PledgeHistory::where('emplid', $hist_pledge->emplid)
                                ->where('yearcd', $hist_pledge->yearcd)
                                ->where('campaign_type', $hist_pledge->campaign_type)
                                ->where('frequency', 'Bi-Weekly')
                                ->orderBy('source')
                                ->get();

                // One-Time
                $one_time_pledges = PledgeHistory::where('emplid', $hist_pledge->emplid)
                                ->where('yearcd', $hist_pledge->yearcd)
                                ->where('campaign_type', $hist_pledge->campaign_type)
                                ->where('frequency', 'One-Time')
                                ->orderBy('source')
                                ->get();

                if ($hist_pledge->source == 'P') {
                    // $new_pledge->bi_weekly_pledges
                    if ( count($bi_weekly_pledges) ) {
                        $new_pledge->pay_period_amount = round(($bi_weekly_pledges->first()->pledge / 26), 2);
                    }
                    if ( count($one_time_pledges) ) {
                        $new_pledge->one_time_amount = $one_time_pledges->first()->pledge;
                    }

                    $new_pledge->goal_amount = ($new_pledge->pay_period_amount * $campaignYear->number_of_periods) +
                                                $new_pledge->one_time_amount;

                } else {

                    $row = 0;
                    $total_amount = 0;
                    foreach( $bi_weekly_pledges as $index => $bi_weekly_pledge) {
                        // if ( $index == 0 ) {
                        //     $new_pledge->pay_period_amount = round(($bi_weekly_pledge->pledge / 26),2);
                        // }

                        if ($bi_weekly_pledge->charity) {
                            $new_pledge_charity = new PledgeCharity();

                            $new_pledge_charity->charity_id = $bi_weekly_pledge->charity->id;
                            $new_pledge_charity->additional = $bi_weekly_pledge->name2;
                            $new_pledge_charity->percentage = $bi_weekly_pledge->percent;
                            $new_pledge_charity->amount = round(($bi_weekly_pledge->amount / 26),2);
                            $new_pledge_charity->frequency = 'bi-weekly'; // : 'one-time',
                            $new_pledge_charity->goal_amount = $new_pledge_charity->amount * $campaignYear->number_of_periods;

                            $new_pledge->charities[$row] = $new_pledge_charity;
                            $row += 1;

                            $total_amount += $new_pledge_charity->amount;
                        }

                    }
                    $new_pledge->pay_period_amount = $total_amount;

                    foreach( $one_time_pledges as $index => $one_time_pledge) {
                        if ( $index == 0 ) {
                            $new_pledge->one_time_amount = $one_time_pledge->pledge;
                        }

                        if ($one_time_pledge->charity) {
                            $new_pledge_charity = new PledgeCharity();

                            $new_pledge_charity->charity_id = $one_time_pledge->charity->id;
                            $new_pledge_charity->additional = $one_time_pledge->name2;
                            $new_pledge_charity->percentage = $one_time_pledge->percent;
                            $new_pledge_charity->amount = $one_time_pledge->amount;
                            $new_pledge_charity->frequency = 'one-time';
                            $new_pledge_charity->goal_amount =  $one_time_pledge->amount;


                            $new_pledge->charities[$row] = $new_pledge_charity;
                            $row += 1;
                        }


                    }

                    $new_pledge->goal_amount = ($new_pledge->pay_period_amount * $campaignYear->number_of_periods) +
                                                $new_pledge->one_time_amount;

                }

                // dd([ $bi_weekly_pledges, $one_time_pledges, $new_pledge ]);

            }

            // return $this->create($new_pledge);
            return redirect()->route('annual-campaign.create')->with(['new_pledge' => $new_pledge]);

        }
        else{
            return abort(404);      // 404 Not Found
        }
    }

}
