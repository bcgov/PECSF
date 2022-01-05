<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    public function index() {
        $charities = [
            ['name'=> "BC Pension Corporation", 'total_donation' => 85023, 'total_donors' => 1050],
            ['name'=> "BC Securities Commission", 'total_donation' => 76201, 'total_donors' => 860],
            ['name'=> "Community Living BC", 'total_donation' => 63212, 'total_donors' => 822],
            ['name'=> "Destination BC Corporatio", 'total_donation' => 51212, 'total_donors' => 700],
            ['name'=> "Elections BC", 'total_donation' => 50900, 'total_donors' => 650],
            ['name'=> "Emergency Management BC", 'total_donation' => 49021, 'total_donors' => 649],
            ['name'=> "Environmental Assessment Office", 'total_donation' => 43000, 'total_donors' => 575],
            ['name'=> "Forest Practices Board", 'total_donation' => 43000, 'total_donors' => 566],
            ['name'=> "Government Communications & Public Engagement", 'total_donation' => 43000, 'total_donors' => 509],
            ['name'=> "Legislative Assembly", 'total_donation' => 43000, 'total_donors' => 504],
        ];
        return view('challenge.index', compact('charities'));
    }
}
