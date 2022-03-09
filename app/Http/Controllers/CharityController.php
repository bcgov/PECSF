<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePledgeRequest;
use App\Http\Requests\DonateStep3Request;
use App\Http\Requests\DontateStep1Request;
use App\Http\Requests\DonateStep2Request;
use App\Models\Charity;
use App\Models\Pledge;
use App\Models\PledgeCharity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PDF;
class CharityController extends Controller
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

    public function select()
    {
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

    public function saveCharities(DontateStep1Request $request)
    {
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
        return view('donate.amount', compact('amounts', 'preselectedData', 'isCustomAmountOneTime', 'isCustomAmountBiWeekly'));
    }

    public function saveAmount(DonateStep2Request $request)
    {
        $request->session()->put('amount-step', $request->validated());
        return redirect()->route('donate.distribution');
    }

    public function distribution($getData = false) {
        if (!Session::has('charities')) {
            return redirect()->route('donate');
        }
        $selectedCharities = Session::get('charities');

        $oneTimeAmount = Session::get('amount-step')['one-time-amount'];
        $biWeeklyAmount = Session::get('amount-step')['bi-weekly-amount'];
        $frequency = Session::get('amount-step')['frequency'];

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


        // $multiplier = $freq == 'bi-weekly' ? 26 : 1;
        $multiplier = 1;
        // $total = "Yet To Calculated";
        $weekly = "ToDelete";
        $viewData = compact('charities', 'calculatedTotalPercentOneTime', 'calculatedTotalPercentBiWeekly', 'calculatedTotalAmountOneTime', 'calculatedTotalAmountBiWeekly', 'grandTotal', 'annualOneTimeAmount', 'annualBiWeeklyAmount', 'oneTimeAmount', 'weekly', 'frequency', 'multiplier');
        if ($getData) {
            return $viewData;
        }
        return view($view, $viewData);
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
        $selectedCharities = Session::get('charities');
        $preselectedData = Session::get('amount-step');

        $totalAmountOneTime = $preselectedData['one-time-amount'];
        $totalAmountBiWeekly = $preselectedData['bi-weekly-amount'];

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
        // 
        foreach($input['oneTimePercent'] as $charityId => $percentageAmount) {
            $selectedCharities['one-time-percentage-distribution'][array_search($charityId, $selectedCharities['id'])] = $percentageAmount;
        }
        foreach($input['oneTimeAmount'] as $charityId => $amount) {
            $selectedCharities['one-time-amount-distribution'][array_search($charityId, $selectedCharities['id'])] = $amount;
        }

        foreach($input['biWeeklyPercent'] as $charityId => $percentageAmount) {
            $selectedCharities['bi-weekly-percentage-distribution'][array_search($charityId, $selectedCharities['id'])] = $percentageAmount;
        }
        foreach($input['biWeeklyAmount'] as $charityId => $amount) {
            $selectedCharities['bi-weekly-amount-distribution'][array_search($charityId, $selectedCharities['id'])] = $amount;
        }
        
        session()->put('charities', $selectedCharities);
        return redirect()->route('donate.summary');
    }

    public function summary() {
        // Logic to show/calculate amount is already done in distribution fn, just view is different
        return $this->distribution();
    }

    public function savePDF() {

        $forPDF = Session::get('forPDF');

        Session::put('charities', $forPDF['charities']);
        Session::put('amount-step', $forPDF['amount-step']);
        $compact = $this->distribution(true);
        
       //  return view ('donate.pdf', $compact);
        $pdf = PDF::loadView('donate.pdf', $compact);
        return $pdf->download('Donation Summary.pdf');        
    }

    public function confirmDonation(CreatePledgeRequest $request)
    {
        $input = $request->validated();
        DB::beginTransaction();
        foreach(['OneTime', 'BiWeekly'] as $frequency) {
            if ($frequency === 'OneTime' && ($input['frequency'] !== 'one-time' && $input['frequency'] !== 'both')) {
                continue;
            }
            if ($frequency === 'BiWeekly' && ($input['frequency'] !== 'bi-weekly' && $input['frequency'] !== 'both')) {
                continue;
            }
            $multiplier = $frequency === 'OneTime' ? 1 : 26;
            
            $pledge = Pledge::create([
                'amount' => $input['annual'.$frequency.'Amount'] / $multiplier,
                'user_id' => Auth::id(),
                'frequency' => $frequency,
                'goal_amount' => $input['annual'.$frequency.'Amount']
            ]);

            foreach ($input['charity'.$frequency.'Amount'] as $id => $amount) {
                PledgeCharity::create([
                    'charity_id' => $id,
                    'pledge_id' => $pledge->id,
                    'additional' => $input['charityAdditional'][$id],
                    'amount' => $amount,
                    /* 'cheque_pending' => $multiplier, */
                    'goal_amount' => $amount * $multiplier
                ]);
            }
    }

        DB::commit();

        $forPDF = [
            'charities' => $request->session()->get('charities'),
            'amount-step' => $request->session()->get('amount-step'),
            'request' => $request->validated()
        ];

        $request->session()->put('forPDF', $forPDF);
        
        $request->session()->forget(['charities', 'amount']);
        return redirect()->route('donate.save.thank-you');
    }

    public function thankYou()
    {
        return view('donate.thankyou');
    }
}
