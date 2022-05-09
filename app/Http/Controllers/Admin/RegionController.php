<?php

namespace App\Http\Controllers\Admin;

use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegionRequest;
use Illuminate\Support\Facades\Auth;

class RegionController extends Controller
{
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

            $regions = Region::select(['id', 'code', 'name', 'status', 'effdt', 'notes']);

            return Datatables::of($regions)
                ->addColumn('action', function ($region) {
                return '<a class="btn btn-info btn-sm  show-region" data-id="'. $region->id .'" >Show</a>' . 
                       '<a class="btn btn-primary btn-sm ml-2 edit-region" data-id="'. $region->id .'" >Edit</a>' . 
                       '<a class="btn btn-danger btn-sm ml-2 delete-region" data-id="'. $region->id .
                       '" data-code="'. $region->code . '">Delete</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
        }

        return view('admin-campaign.regions.index');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegionRequest $request)
    {

        if ($request->ajax()) {
            $region = Region::Create([
                'code' => $request->code,
                'name' => $request->name,
                'status' => $request->status,
                'effdt' => $request->effdt,
                'notes' => $request->notes,
                'created_by_id' => Auth::id(),
                'updated_by_id' => Auth::id(),
            ]);

            return response()->json($region);
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

            $region = Region::where('id', $id)->first();
            $region->created_by_name = $region->created_by ? $region->created_by->name : '';
            $region->updated_by_name = $region->updated_by ? $region->updated_by->name : '';
            $region->formatted_created_at = $region->created_at->format('Y-m-d H:i:s');
            $region->formatted_updated_at = $region->updated_at->format('Y-m-d H:i:s');
            // $region->updated_at = date_timezone_set($region->updated_at, timezone_open('America/Vancouver')); 
            unset($region->created_by );
            unset($region->updated_by );
            return response()->json($region);
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
            $region = Region::where('id', $id)->first();
            return response()->json($region);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RegionRequest $request, $id)
    {
        if ($request->ajax()) {
            $region = Region::where('id', $id)->first();
            $region->fill( $request->all() );
            $region->save();
        
            return response()->json($region);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $region = Region::where('id', $id);
        $region->delete();

        return response()->noContent();
    }


}
