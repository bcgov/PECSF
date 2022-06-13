<?php

namespace App\Http\Controllers\Admin;

use App\Models\BusinessUnit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use App\Http\Requests\BusinessUnitRequest;
use Illuminate\Support\Facades\Auth;



class BusinessUnitController extends Controller
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

            $columns = ["code","name","status","created_at"];
            $business_units = BusinessUnit::orderBy($columns[$request->input("order")[0]['column']],$request->input("order")[0]['dir']);

            return Datatables::of($business_units)
                ->addColumn('action', function ($business_unit) {
                return '<a class="btn btn-info btn-sm  show-bu" data-id="'. $business_unit->id .'" >Show</a>' .
                       '<a class="btn btn-primary btn-sm ml-2 edit-bu" data-id="'. $business_unit->id .'" >Edit</a>' .
                       '<a class="btn btn-danger btn-sm ml-2 delete-bu" data-id="'. $business_unit->id .
                       '" data-code="'. $business_unit->code . '">Delete</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
        }

        return view('admin-campaign.business-units.index');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BusinessUnitRequest $request)
    {

        if ($request->ajax()) {
            $business_unit = BusinessUnit::Create([
                'code' => $request->code,
                'name' => $request->name,
                'status' => $request->status,
                'effdt' => $request->effdt,
                'notes' => $request->notes,
                'created_by_id' => Auth::id(),
                'updated_by_id' => Auth::id(),
            ]);

            return response()->json($business_unit);
        }

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

            $business_unit = BusinessUnit::where('id', $id)->first();
            $business_unit->created_by_name = $business_unit->created_by ? $business_unit->created_by->name : '';
            $business_unit->updated_by_name = $business_unit->updated_by ? $business_unit->updated_by->name : '';
            $business_unit->formatted_created_at = $business_unit->created_at->format('Y-m-d H:i:s');
            $business_unit->formatted_updated_at = $business_unit->updated_at->format('Y-m-d H:i:s');
            // $region->updated_at = date_timezone_set($region->updated_at, timezone_open('America/Vancouver'));
            unset($business_unit->created_by );
            unset($business_unit->updated_by );
            return response()->json($business_unit);
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
            $business_unit = BusinessUnit::where('id', $id)->first();
            return response()->json($business_unit);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BusinessUnitRequest $request, $id)
    {
        if ($request->ajax()) {
            $business_unit = BusinessUnit::where('id', $id)->first();
            $business_unit->fill( $request->all() );
            $business_unit->save();

            return response()->json($business_unit);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $business_unit = BusinessUnit::where('id', $id);
        $business_unit->delete();

        return response()->noContent();
    }
}
