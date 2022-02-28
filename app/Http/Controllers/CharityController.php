<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePledgeRequest;
use App\Http\Requests\DonateStep3Request;
use App\Http\Requests\DontateStep1Request;
use App\Http\Requests\DontateStep2Request;
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

    public function saveAmount(DontateStep2Request $request)
    {
        $request->session()->put('amount', $request->validated());

        return redirect()->route('donate.distribution');
    }

    public function distribution()
    {
        if (!Session::has('charities')) {
            return redirect()->route('donate');
        }
        $selectedCharities = Session::get('charities');

        $amount = Session::get('amount')['amount'];
        $freq = Session::get('amount')['frequency'];

        $annual_amount = $amount;
        $onetime = $amount;
        $weekly = 0;
        if ($freq == 'bi-weekly') {
            $annual_amount = $amount * 26;
            $weekly = $amount;
            $onetime = 0;
        }

        $selectedCharities = Session::get('charities');

        $individualAmount = round($amount / count($selectedCharities['id']), 2);
        $individualPercent = round(100 / count($selectedCharities['id']), 2);

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

            $charity['amount-distribution'] = round($amount * $charity['percentage-distribution'] / 100, 2);

            $totalPercent += $charity['percentage-distribution'];
            $totalAmount += $charity['amount-distribution'];
            array_push($charities, $charity);
        }
        $lastIndex = count($charities) - 1;
        $charities[$lastIndex]['percentage-distribution'] = $charities[$lastIndex]['percentage-distribution'] + (100 - $totalPercent);
        $charities[$lastIndex]['amount-distribution'] = $charities[$lastIndex]['amount-distribution'] + ($amount - $totalAmount);
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
        return view($view, compact('charities', 'individualAmount', 'individualPercent', 'total', 'annual_amount', 'onetime', 'weekly', 'frequency', 'multiplier'));
    }

    public function saveDistribution(DonateStep3Request $request) {
        $input = $request->validated();
        $selectedCharities = Session::get('charities');
        $totalAmount = Session::get('amount')['amount'];

        if (array_key_exists('distributionByPercent', $input)) {
            // Correct $input['amount']
            foreach($input['percent'] as $index => $a) {
                $input['amount'][$index] = $totalAmount * $a / 100; 
            }
        } else {
            // Correct $input['percent']
            foreach($input['amount'] as $index => $a) {
                $input['percent'][$index] = round(100 * $a / $totalAmount, 2); 
            }
        }
        foreach($input['percent'] as $charityId => $percentageAmount) {
            $selectedCharities['percentage-distribution'][array_search($charityId, $selectedCharities['id'])] = $percentageAmount;
        }
        foreach($input['amount'] as $charityId => $amount) {
            $selectedCharities['amount-distribution'][array_search($charityId, $selectedCharities['id'])] = $amount;
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
        Session::put('amount', $forPDF['amount']);

        $selectedCharities = Session::get('charities');

        $amount = Session::get('amount')['amount'];
        $freq = Session::get('amount')['frequency'];

        $annual_amount = $amount;
        $onetime = $amount;
        $weekly = 0;
        if ($freq == 'bi-weekly') {
            $annual_amount = $amount * 26;
            $weekly = $amount;
            $onetime = 0;
        }

        $selectedCharities = Session::get('charities');

        $individualAmount = round($amount / count($selectedCharities['id']), 2);
        $individualPercent = round(100 / count($selectedCharities['id']), 2);

        $charities = [];

        $_charities = Charity::whereIn('id', $selectedCharities['id'])
            ->get(['id', 'charity_name as text']);
        $total = 0;
        // To remove rounding error calculate total and all remaning amount/percent should be added to last.
        $totalPercent = 0;
        $totalAmount = 0;

        foreach ($_charities as $charity) {
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

            $charity['amount-distribution'] = round($amount * $charity['percentage-distribution'] / 100, 2);

            $totalPercent += $charity['percentage-distribution'];
            $totalAmount += $charity['amount-distribution'];
            array_push($charities, $charity);
        }
        $lastIndex = count($charities) - 1;
        $charities[$lastIndex]['percentage-distribution'] = $charities[$lastIndex]['percentage-distribution'] + (100 - $totalPercent);
        $charities[$lastIndex]['amount-distribution'] = $charities[$lastIndex]['amount-distribution'] + ($amount - $totalAmount);
        if ($freq == 'bi-weekly') {
            foreach ($charities as $key => $value) {
                $total += $value['amount-distribution'] * 26;
                $total = round($total);
            }
        }

        $frequency = $freq == 'bi-weekly' ? "bi-weekly" : 'one time';
        $multiplier = $freq == 'bi-weekly' ? 26 : 1;
        
        $pdf = PDF::loadView('donate.pdf', compact('charities', 'individualAmount', 'individualPercent', 'total', 'annual_amount', 'onetime', 'weekly', 'frequency', 'multiplier'));
        return $pdf->download('Donation Summary.pdf');        
    }

    public function confirmDonation(CreatePledgeRequest $request)
    {
        $input = $request->validated();
        $multiplier = $input['frequency'] === 'one time' ? 1 : 26;
        DB::beginTransaction();
        $pledge = Pledge::create([
            'amount' => $input['annualAmount'] / $multiplier,
            'user_id' => Auth::id(),
            'frequency' => $input['frequency'],
            'goal_amount' => $input['annualAmount']
        ]);
        foreach ($input['charityAmount'] as $id => $amount) {
            PledgeCharity::create([
                'charity_id' => $id,
                'pledge_id' => $pledge->id,
                'additional' => $input['charityAdditional'][$id],
                'amount' => $amount / $multiplier,
                /* 'cheque_pending' => $multiplier, */
                'goal_amount' => $amount
            ]);
        }
        DB::commit();

        $forPDF = [
            'charities' => $request->session()->get('charities'),
            'amount' => $request->session()->get('amount'),
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
