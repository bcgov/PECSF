<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
    public function index()
    {
        $charities = [
            ['name'=> "BC Pension Corporation", 'total_donation' => 85023, 'total_donors' => 1050, 'participation_rate' => 94.3, 'final_participation_rate' => 87, 'change' => 7.3],
            ['name'=> "BC Securities Commission", 'total_donation' => 76201, 'total_donors' => 860, 'participation_rate' => 26.5, 'final_participation_rate' => 73.5, 'change' => -47],
            ['name'=> "Community Living BC", 'total_donation' => 63212, 'total_donors' => 822, 'participation_rate' => 58, 'final_participation_rate' => 42, 'change' => 16],
            ['name'=> "Destination BC Corporatio", 'total_donation' => 51212, 'total_donors' => 700, 'participation_rate' => 94.3, 'final_participation_rate' => 87, 'change' => 7.3],
            ['name'=> "Elections BC", 'total_donation' => 50900, 'total_donors' => 650, 'participation_rate' => 26.5, 'final_participation_rate' => 73.5, 'change' => -47],
            ['name'=> "Emergency Management BC", 'total_donation' => 49021, 'total_donors' => 649, 'participation_rate' => 58, 'final_participation_rate' => 42, 'change' => 16],
            ['name'=> "Environmental Assessment Office", 'total_donation' => 43000, 'total_donors' => 575, 'participation_rate' => 94.3, 'final_participation_rate' => 87, 'change' => 7.3],
            ['name'=> "Forest Practices Board", 'total_donation' => 43000, 'total_donors' => 566, 'participation_rate' => 26.5, 'final_participation_rate' => 73.5, 'change' => -47],
            ['name'=> "Government Communications & Public Engagement", 'total_donation' => 43000, 'total_donors' => 504, 'participation_rate' => 58, 'final_participation_rate' => 42, 'change' => 16],
            ['name'=> "Legislative Assembly", 'total_donation' => 43000, 'total_donors' => 504, 'participation_rate' => 94.3, 'final_participation_rate' => 87, 'change' => 7.3],
        ];
        
        return view('home' , compact('charities'));
    }
}
