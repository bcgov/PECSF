<?php

namespace App\Http\Controllers;

use App\Models\Charity;
use App\Models\Pledge;
use App\Models\PledgeCharity;
use App\Models\PledgeExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PledgeCharityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return Charity::has('pledges')->with('pledges')->get()->flatten();
        // return PledgeCharity::with('pledge')->get()->flatten();
/*         SELECT charities.*, SUM(amount) as CHQAMOUNT
FROM pledge_charities, charities
WHERE pledge_charities.charity_id = charities.id
GROUP BY charities.id
 *//* 
        return PledgeCharity::select('charities.*', DB::raw('sum(amount) as CHQAMOUNT'))
            ->groupBy('charity_id')
            ->join('charities','charity_id', '=', 'charities.id')
            ->toSql(); */
        return PledgeExport::all();
            
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PledgeCharity  $pledgeCharity
     * @return \Illuminate\Http\Response
     */
    public function show(PledgeCharity $pledgeCharity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PledgeCharity  $pledgeCharity
     * @return \Illuminate\Http\Response
     */
    public function edit(PledgeCharity $pledgeCharity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PledgeCharity  $pledgeCharity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PledgeCharity $pledgeCharity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PledgeCharity  $pledgeCharity
     * @return \Illuminate\Http\Response
     */
    public function destroy(PledgeCharity $pledgeCharity)
    {
        //
    }
}
