<?php

namespace App\Http\Controllers\Admin;

use App\Models\Charity;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CRACharityRequest;

class CRACharityController extends Controller
{
    //
    //
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
     public function index(Request $request) {

        if($request->ajax()) {

            $charities = Charity::orderBy('charity_name');

            return Datatables::of($charities)
                ->addColumn('effdt', function ($charity) {
                    return $charity->effective_date_of_status->format('d/m/Y');
                })
                ->addColumn('action', function ($charity) {
                return '<a class="btn btn-info btn-sm ml-2 mt-1 show-charity" data-id="'. $charity->id .'" >Show</a>' . 
                       '<a class="btn btn-primary btn-sm ml-2 mt-1 edit-charity" data-id="'. $charity->id .'" >Edit</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
        }

        return view('admin-campaign.charities.index');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CRACharityRequest $request)
    {

     
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {

        if ($request->ajax()) {

            $charity = Charity::where('id', $id)->first();

            $charity->effdt = $charity->effective_date_of_status ? $charity->effective_date_of_status->format('d/m/Y') : '';
            $charity->created_by_name = $charity->created_by ? $charity->created_by->name : '';
            $charity->updated_by_name = $charity->updated_by ? $charity->updated_by->name : '';
            $charity->formatted_created_at = $charity->created_at ? $charity->created_at->format('Y-m-d H:i:s') : '';
            $charity->formatted_updated_at = $charity->updated_at ? $charity->updated_at->format('Y-m-d H:i:s') : '';
            
            return response()->json($charity);
        }
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if ($request->ajax()) {
            
            $charity = Charity::where('id', $id)->first();
            $charity->effdt = $charity->effective_date_of_status ? $charity->effective_date_of_status->format('d/m/Y') : '';

            return response()->json($charity);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CRACharityRequest $request, $id)
    {

        // return ($request->validated);
        if ($request->ajax()) {
            $charity = Charity::where('id', $id)->first();

            $charity->fill( $request->validated() );

            $charity->use_alt_address = $request->exists('use_alt_address') ? true : false;
            $charity->updated_by_id = Auth::id();
            
            $charity->save();
        
            return response()->json($charity);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $charity = Charity::where('id', $id);
        // $charity->delete();

        // return response()->noContent();
    }

}
