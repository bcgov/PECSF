<?php

namespace App\Http\Controllers\Admin;

use App\Models\PayCalendar;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

class PayCalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        if($request->ajax()) {

            $pay_calendars = PayCalendar::select(['id', 'pay_begin_dt', 'pay_end_dt', 'check_dt']);

            return Datatables::of($pay_calendars)
            //     ->addColumn('action', function ($org) {
            //     return '<a class="btn btn-info btn-sm  show-organization" data-id="'. $org->id .'" >Show</a>' . 
            //            '<a class="btn btn-primary btn-sm ml-2 edit-organization" data-id="'. $org->id .'" >Edit</a>' . 
            //            '<a class="btn btn-danger btn-sm ml-2 delete-organization" data-id="'. $org->id .
            //            '" data-code="'. $org->code . '">Delete</a>';
            // })
            // ->rawColumns(['action'])
            ->make(true);
        }

        return view('admin-campaign.pay-calendar.index');
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
