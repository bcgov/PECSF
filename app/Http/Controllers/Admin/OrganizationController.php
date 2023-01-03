<?php

namespace App\Http\Controllers\Admin;

use App\Models\Organization;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\OrganizationRequest;

class OrganizationController extends Controller
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
    public function index(Request $request) {

        if($request->ajax()) {

            $orgs = Organization::select(['id', 'code', 'name', 'status', 'effdt']);

            return Datatables::of($orgs)
                ->addColumn('action', function ($org) {
                return '<a class="btn btn-info btn-sm  show-organization" data-id="'. $org->id .'" >Show</a>' . 
                       '<a class="btn btn-primary btn-sm ml-2 edit-organization" data-id="'. $org->id .'" >Edit</a>' . 
                       '<a class="btn btn-danger btn-sm ml-2 delete-organization" data-id="'. $org->id .
                       '" data-code="'. $org->code . '">Delete</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
        }

        return view('admin-campaign.organizations.index');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrganizationRequest $request)
    {

        if ($request->ajax()) {
            $org = Organization::Create([
                'code' => $request->code,
                'name' => $request->name,
                'status' => $request->status,
                'effdt' => $request->effdt,
                'created_by_id' => Auth::id(),
                'updated_by_id' => Auth::id(),
            ]);

            return response()->json($org);
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

            $org = Organization::where('id', $id)->first();
            $org->created_by_name = $org->created_by ? $org->created_by->name : '';
            $org->updated_by_name = $org->updated_by ? $org->updated_by->name : '';
            $org->formatted_created_at = $org->created_at->format('Y-m-d H:i:s');
            $org->formatted_updated_at = $org->updated_at->format('Y-m-d H:i:s');
            unset($org->created_by );
            unset($org->updated_by );
            return response()->json($org);
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
            $org = Organization::where('id', $id)->first();
            return response()->json($org);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OrganizationRequest $request, $id)
    {
        if ($request->ajax()) {
            $org = Organization::where('id', $id)->first();
            $org->fill( $request->all() );
            $org->save();
        
            return response()->json($org);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if($request->ajax()) {
            $org = Organization::where('id', $id)->first();

            if ($org->hasPledge) {
                return response()->json([
                    'title'  => "Invalid delete!",
                    'message' => 'The Business unit "' .$org->code . ' - '. $org->name . '" cannot be deleted, it is being referenced on the pledge(s).'], 403);
            }

            // Delete the specified organization
            $org->updated_by_id = Auth::Id();
            $org->save();
            
            $org->delete();

            return response()->noContent();
        
        } else {
            abort(404);
        }
    }

}
