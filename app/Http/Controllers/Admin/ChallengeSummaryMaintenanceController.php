<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Models\DailyCampaignSummary;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ChallengeSummaryMaintenanceRequest;

class ChallengeSummaryMaintenanceController extends Controller
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
            $summaries = DailyCampaignSummary::all();

            return Datatables::of($summaries)
                ->addColumn('action', function ($summary) {
                return '<a class="btn btn-info btn-sm  show-bu" data-id="'. $summary->id .'" >Show</a>' .
                       '<a class="btn btn-primary btn-sm ml-2 edit-bu" data-id="'. $summary->id .'" >Edit</a>' .
                       '<a class="btn btn-danger btn-sm ml-2 delete-bu" data-id="'. $summary->id .
                       '" data-code="'. $summary->campaign_year . '">Delete</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        // business-units --> challenge-summary

        return view('admin-campaign.challenge-summary.index');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ChallengeSummaryMaintenanceRequest $request)
    {

        if ($request->ajax()) {
            $summary = DailyCampaignSummary::Create([
                'campaign_year' => $request->campaign_year,
                'as_of_date' => $request->as_of_date,
                'donors' => $request->donors,
                'dollars' => $request->dollars,

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

            $summary = DailyCampaignSummary::where('id', $id)->first();
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
            $summary = DailyCampaignSummary::where('id', $id)->first();
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
    public function update(ChallengeSummaryMaintenanceRequest $request, $id)
    {
        if ($request->ajax()) {
            $summary = DailyCampaignSummary::where('id', $id)->first();
            $summary->fill( $request->all() );
            $summary->save();

            return response()->json($summary);
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
            $summary = DailyCampaignSummary::where('id', $id)->first();

            $summary->updated_by_id = Auth::Id();
            $summary->save();
            
            $summary->delete();

            return response()->noContent();

        } else {
            abort(404);
        }
    }

}
