<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use App\Models\Donation;
use App\Models\CampaignYear;
use App\Models\Organization;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;

class DonationDataController extends Controller
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

            $donations = Donation::with('organization', 'process_history', 'process_history.created_by', 'process_history.updated_by')
                            ->when( $request->org_code, function($query) use($request) {
                                $query->where('donations.org_code', $request->org_code);
                            })
                            ->when( $request->pecsf_id, function($query) use($request) {
                                $query->where('donations.pecsf_id', 'like', '%'. $request->pecsf_id .'%');
                            })
                            ->when( $request->name, function($query) use($request) {
                                // $query->where('first_name', 'like', '%' . $request->name . '%')
                                //       ->orWhere('first_name', 'like', '%' . $request->name . '%')
                                    $query->orWhere('donations.name', 'like', '%' . $request->name . '%');
                            })
                            // ->when( $request->city, function($query) use($request) {
                            //     $query->where( function($q) use($request) {
                            //         return $q->where('employee_jobs.city', 'like', '%'. $request->city .'%')
                            //                  ->orWhere('pledges.city', 'like', '%'. $request->city .'%');
                            //     });
                            // })
                            ->when( $request->yearcd, function($query) use($request) {
                                $query->where('donations.yearcd', $request->yearcd);
                            })
                            ->when( $request->source_type, function($query) use($request) {
                                $query->where('donations.source_type', $request->source_type);
                            })
                            ->when( $request->frequency, function($query) use($request) {
                                $query->where('donations.frequency', $request->frequency);
                            })
                            ->when( is_numeric($request->amount_from) || is_numeric($request->amount_to), function($query) use($request) {
                                $from = is_numeric($request->amount_from) ? $request->amount_from : 0;
                                $to = is_numeric($request->amount_to) ? $request->amount_to : 9999999;
                                return  $query->whereBetween('donations.amount',[ $from, $to]);
                            })
                            ->select('donations.*');


            // $gov = Organization::where('code', 'GOV')->first();

            return Datatables::of($donations)
                ->editColumn('created_at', function ($donation) {
                    return $donation->process_history->created_at->format('Y-m-d H:i:s'); // human readable format
                })
                ->editColumn('updated_at', function ($donation) {
                    return $donation->process_history->updated_at->format('Y-m-d H:i:s'); // human readable format
                })                        
                // ->rawColumns(['action','description'])
                ->make(true);

        }

        // get all the record 
        $organizations = Organization::where('status', 'A')->orderBy('name')->get();
        // $campaign_years = CampaignYear::orderBy('calendar_year')->get();
        // $cities = City::orderBy('city')->get();
        $years = Donation::orderBy('yearcd')->distinct('yearcd')->pluck('yearcd');
        $source_type_list = Donation::SOURCE_TYPE_LIST;
        $frequencies = Donation::orderBy('frequency')->distinct('frequency')->pluck('frequency');

        // load the view and pass 
        return view('admin-report.donation-data.index', compact('organizations', 'years', 'source_type_list', 'frequencies'));

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
