<?php

namespace App\Http\Controllers\Admin;

use App\Models\BusinessUnit;
use App\Models\CampaignYear;
use App\Models\Organization;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Models\EligibleEmployeeByBU;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\EligibleEmployeeSummaryMaintenanceRequest;

class EligibleEmployeeSummaryMaintenanceController extends Controller
{
    //
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

            // $columns = ["code","name","status","effdt","linked_bu_code", "created_at"];
            $summaries = EligibleEmployeeByBU::when( $request->filter_campaign_year, function($query) use($request) {
                                $query->where('eligible_employee_by_bus.campaign_year', $request->filter_campaign_year);
                            })
                            ->when( $request->filter_organization_code, function($query) use($request) {
                                $query->where('eligible_employee_by_bus.organization_code', $request->filter_organization_code);
                            })
                            ->when( $request->filter_business_unit, function($query) use($request) {
                                $query->where('eligible_employee_by_bus.business_unit_code', 'like', '%' . $request->filter_business_unit . '%')
                                    ->orWhere('eligible_employee_by_bus.business_unit_name', 'like', '%' . $request->filter_business_unit . '%');
                            });

            return Datatables::of($summaries)
                ->addColumn('action', function ($summary) {
                    $action = '<a class="btn btn-info btn-sm  show-bu" data-id="'. $summary->id .'" >Show</a>';
                    if ($summary->organization_code <> 'GOV') {
                        $action .= '<a class="btn btn-primary btn-sm ml-2 edit-bu" data-id="'. $summary->id .'" >Edit</a>' .
                                    '<a class="btn btn-danger btn-sm ml-2 delete-bu" data-id="'. $summary->id .
                                    '" data-year="'. $summary->campaign_year . '" data-code="'. $summary->business_unit_code . '">Delete</a>';
                    }
                    return $action;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        // business-units --> eligible-employee-summary
        $campaign_years = range( today()->year, 2023);

        $organizations = Organization::where('status', 'A')->orderBy('name')
                                       ->with('business_unit')
                                       ->get();

        $business_units = BusinessUnit::where('status', 'A')->orderBy('name')
                                        ->get();

        return view('admin-campaign.eligible-employee-summary.index', compact('campaign_years', 'organizations', 'business_units'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EligibleEmployeeSummaryMaintenanceRequest $request)
    {

        if ($request->ajax()) {

            $organization = Organization::where('code', $request->organization_code)->with('business_unit')->first();

            if ($organization == 'GOV') {
                return response()->json([
                    'organization_code' => "Invalid organization code.",
                ], 422);
            }

            $year = $request->campaign_year;
            $as_of_date = $year < ($year . '-10-15') ? ($year . '-09-01') : ($year . '-10-15');

            $summary = EligibleEmployeeByBU::Create([
                'campaign_year' => $request->campaign_year,
                'as_of_date' => $as_of_date,
                'organization_code' => $request->organization_code,
                'business_unit_code' => $organization->business_unit->code,
                'business_unit_name' => $organization->business_unit->name,
                'ee_count' => $request->ee_count,
                'notes' => $request->notes,
                
                'created_by_id' => Auth::id(),
                'updated_by_id' => Auth::id(),
            ]);

            return response()->json($summary);
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

            $summary = EligibleEmployeeByBU::where('id', $id)->first();
            $summary->created_by_name = $summary->created_by ? $summary->created_by->name : '';
            $summary->updated_by_name = $summary->updated_by ? $summary->updated_by->name : '';
            $summary->formatted_created_at = $summary->created_at->format('Y-m-d H:i:s');
            $summary->formatted_updated_at = $summary->updated_at->format('Y-m-d H:i:s');

            $summary->donors = number_format($summary->donors);
            $summary->dollars  = number_format($summary->dollars);
            // $region->updated_at = date_timezone_set($region->updated_at, timezone_open('America/Vancouver'));
            unset($summary->created_by );
            unset($summary->updated_by );
            return response()->json($summary);
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
            $summary = EligibleEmployeeByBU::where('id', $id)->first();
            return response()->json($summary);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EligibleEmployeeSummaryMaintenanceRequest $request, $id)
    {
        if ($request->ajax()) {

            $summary = EligibleEmployeeByBU::where('id', $id)->first();

            if ($summary && $summary->organization_code <> 'GOV') {
                // $summary->fill( $request->all() );
                $summary->ee_count = $request->ee_count;
                $summary->notes = $request->notes;
                $summary->updated_by_id = Auth::id();
                $summary->save();

                return response()->json($summary);
            } else {
                return response()->json([
                    'organization_code' => "Invalid organization found.",
                ], 422);
            }
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

            $organization = Organization::where('code', $request->organization_code)->with('business_unit')->first();

            if ($organization == 'GOV') {
                return response()->json([
                    'organization_code' => "Invalid organization found.",
                ], 422);
            }

            $summary = EligibleEmployeeByBU::where('id', $id)->first();

            $summary->updated_by_id = Auth::Id();
            $summary->save();
            
            $summary->delete();

            return response()->noContent();

        } else {
            abort(404);
        }
    }

}
