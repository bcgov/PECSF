<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\ElligibleEmployee;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

class EligibleEmployeeCountController extends Controller
{

    /**
     * create a new instance of the class
     *
     * @return void
     */
    function __construct()
    {
         $this->middleware('permission:setting');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        //
        if($request->ajax()) {

            $summaries = ElligibleEmployee::when( $request->year, function($query) use($request) {
                                    $query->where('elligible_employees.year', $request->year);
                                })->orderBy('business_unit');

           
            return Datatables::of($summaries)
                ->make(true);

        }

        // get all the record for select options 
        $years = ElligibleEmployee::distinct('year','as_of_date')->orderBy('year')->pluck('as_of_date','year');

        // load the view and pass data
        return view('admin-report.eligible-employee-count.index', compact('years'));

    }
}
