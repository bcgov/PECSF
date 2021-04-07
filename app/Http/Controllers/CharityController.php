<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Charity;
use App\Http\Requests\DontateStep1Request;
use App\Http\Requests\DontateStep2Request;
use Illuminate\Support\Facades\Session;

class CharityController extends Controller
{
    public function index(Request $request) {
        if (!$request->ajax()) {
            return response()->setStatusCode(400);
        }

        $response = Charity::where('charity_name', 'LIKE' ,"%".$request->search."%")->paginate(10)->through(function ($charity, $key) {
            return [
                "id" => $charity->id,
                "text" => $charity->charity_name
            ];
        });
        return $this->send($response, "Charity listed successfully");
    }

    public function select() {
        $charities = [];
        if (Session::has('charities')) {
            $selectedCharities = Session::get('charities');

            $_charities = Charity::whereIn("id", $selectedCharities["id"])
                        ->get(["id", "charity_name as text"]);
            
            foreach ($_charities as $charity) {
                $charity['additional'] = $selectedCharities['additional'][array_search($charity['id'], $selectedCharities["id"])];
                if (!$charity['additional']) 
                    $charity['additional'] = '';
                
                array_push($charities, $charity);
            }

        }
        return view('donate.select', compact('charities'));
    }

    public function saveCharities(DontateStep1Request $request) {
        Session()->put("charities", $request->validated());
        return redirect()->route('donate.amount');
    }

    public function amount() {
        $preselectedAmount = 20;

        $preselectedData = [
            "frequency" => 'bi-weekly',
            "amount" => 20
        ];

        if (Session::has('amount')) {
            $preselectedData = Session::get('amount');
            $preselectedAmount = $preselectedData['amount'];
        }

        $amounts = [
            "bi-weekly" => [
                [
                    "amount" => 6,
                    "text" => "$6",
                    "selected" => ($preselectedAmount == 6) ? true : false
                ],
                [
                    "amount" => 12,
                    "text" => "$12",
                    "selected" => ($preselectedAmount == 12) ? true : false
                ],
                [
                    "amount" => 20,
                    "text" => "$20",
                    "selected" => ($preselectedAmount == 20) ? true : false
                ],
                [
                    "amount" => 50,
                    "text" => "$50",
                    "selected" => ($preselectedAmount == 50) ? true : false
                ],
                [
                    "amount" => '',
                    "text" => "Custom",
                    "selected" => (!in_array($preselectedAmount, [6, 12, 20, 50])) ? true : false
                ]
            ]
        ];

        $isCustomAmount = ($preselectedData["frequency"] == 'one-time' || !in_array($preselectedAmount, [6, 12, 20, 50])) ? true : false;


        return view('donate.amount', compact('amounts', 'preselectedData', 'isCustomAmount'));
    }

    public function saveAmount(DontateStep2Request $request) {
        $request->session()->put("amount", $request->validated());
        return redirect()->route('donate.distribution');
    }

    public function distribution() {
        if (!Session::has('charities')) {
            return redirect()->route('donate');
        }
        $selectedCharities = Session::get('charities');

        $amount = Session::get('amount')['amount'];
        $selectedCharities = Session::get('charities');

        $individualAmount = round($amount / count($selectedCharities['id']), 2);
        $individualPercent = round(100 / count($selectedCharities['id']), 2);

        $charities = [];

        $_charities = Charity::whereIn("id", $selectedCharities["id"])
            ->get(["id", "charity_name as text"]);

        foreach ($_charities as $charity) {
            $charity['additional'] = $selectedCharities['additional'][array_search($charity['id'], $selectedCharities["id"])];
            if (!$charity['additional'])
            $charity['additional'] = '';

            $charity['percentage-distribution'] = $individualPercent;
            $charity['amount-distribution'] = $individualAmount;

            if (isset($selectedCharities['percentage-distribution'])) {
                $charity['percentage-distribution'] = $selectedCharities['percentage-distribution'][array_search($charity['id'], $selectedCharities["id"])];
            }

            if (isset($selectedCharities['amount-distribution'])) {
                $charity['amount-distribution'] = $selectedCharities['amount-distribution'][array_search($charity['id'], $selectedCharities["id"])];
            }
            
            array_push($charities, $charity);
        }

        return view('donate.distribution',compact('charities', 'individualAmount', 'individualPercent'));
    }

    public function saveDistribution(Request $request) {
        
    }
}
