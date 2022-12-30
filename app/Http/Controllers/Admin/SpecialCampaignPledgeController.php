<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use App\Models\User;
use App\Models\PayCalendar;
use App\Models\CampaignYear;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Models\SpecialCampaignPledge;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\SpecialCampaignPledgeRequest;
use App\Models\SpecialCampaign;

class SpecialCampaignPledgeController extends Controller
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
    public function index(Request $request)
    {

        if($request->ajax()) {

            $pledges = SpecialCampaignPledge::with('organization', 'campaign_year', 'user', 'user.primary_job', 
                            'special_campaign')
                            ->leftJoin('users', 'users.id', '=', 'special_campaign_pledges.user_id')
                            ->leftJoin('employee_jobs', 'employee_jobs.emplid', '=', 'users.emplid')
                            ->where( function($query) {
                                $query->where('employee_jobs.empl_rcd', '=', function($q) {
                                        $q->from('employee_jobs as J2') 
                                            ->whereColumn('J2.emplid', 'employee_jobs.emplid')
                                            ->selectRaw('min(J2.empl_rcd)');
                                    })
                                    ->orWhereNull('employee_jobs.empl_rcd');
                            })
                            ->when($request->tran_id, function($query) use($request) {
                                return $query->where('special_campaign_pledges.id', 'like', $request->tran_id);
                            })
                            ->when( $request->organization_id, function($query) use($request) {
                                $query->where('special_campaign_pledges.organization_id', $request->organization_id);
                            })
                            ->when( $request->pecsf_id, function($query) use($request) {
                                $query->where('special_campaign_pledges.pecsf_id', 'like', '%'. $request->pecsf_id .'%');
                            })
                            ->when( $request->emplid, function($query) use($request) {
                                $query->where('employee_jobs.emplid', 'like', '%'. $request->emplid .'%');
                            })
                            ->when($request->seqno, function($query) use($request) {
                                return $query->where('special_campaign_pledges.seqno', $request->seqno);
                            })
                            ->when( $request->name, function($query) use($request) {
                                $query->where('special_campaign_pledges.first_name', 'like', '%' . $request->name . '%')
                                      ->orWhere('special_campaign_pledges.first_name', 'like', '%' . $request->name . '%')
                                      ->orWhere('users.name', 'like', '%' . $request->name . '%');
                            })
                            ->when( $request->city, function($query) use($request) {
                                $query->where( function($q) use($request) {
                                    return $q->where('employee_jobs.city', 'like', '%'. $request->city .'%')
                                             ->orWhere('special_campaign_pledges.city', 'like', '%'. $request->city .'%');
                                });
                            })
                            ->when( $request->campaign_year_id, function($query) use($request) {
                                // $query->where('campaign_year_id', $request->campaign_year_id);
                                $query->where('yearcd', function($q) use($request){
                                            $q->select('calendar_year')
                                                    ->from('campaign_years')
                                                    ->where('campaign_years.id', $request->campaign_year_id);
                                });
                            })
                            ->when( $request->cancelled == 'C', function($query) use($request) {
                                $query->whereNotNull('special_campaign_pledges.cancelled');
                            })
                            ->when( $request->cancelled == 'N', function($query) use($request) {
                                $query->whereNull('special_campaign_pledges.cancelled');
                            })
                            ->when( $request->cancelled == 'N', function($query) use($request) {
                                $query->whereNull('special_campaign_pledges.cancelled');
                            })
                            ->when( $request->deduct_pay_from, function($query) use($request) {
                                $query->where('special_campaign_pledges.deduct_pay_from', $request->deduct_pay_from);
                            })
                            ->when( $request->special_campaign_name, function($query) use($request) {
                                $query->whereIn('special_campaign_pledges.special_campaign_id', function($q) use($request) {
                                    $q->select('id')
                                            ->from('special_campaigns')
                                            ->whereRaw("LOWER(special_campaigns.name) LIKE '%" . strtolower($request->special_campaign_name) . "%'");
                                });
                            })
                            ->when( is_numeric($request->one_time_amt_from) || is_numeric($request->one_time_amt_to), function($query) use($request) {
                                $from = is_numeric($request->one_time_amt_from) ? $request->one_time_amt_from : 0;
                                $to = is_numeric($request->one_time_amt_to) ? $request->one_time_amt_to : 9999999;
                                return  $query->whereBetween('one_time_amount',[ $from, $to]);
                            })
                            ->when( is_numeric($request->pay_period_amt_from) || is_numeric($request->pay_period_amt_to), function($query) use($request) {
                                $from = is_numeric($request->pay_period_amt_from) ? $request->pay_period_amt_from : 0;
                                $to = is_numeric($request->pay_period_amt_to) ? $request->pay_period_amt_to : 9999999;
                                return  $query->whereBetween('pay_period_amount',[ $from, $to]);
                            })
                            ->select('special_campaign_pledges.*');

            $gov = Organization::where('code', 'GOV')->first();

            return Datatables::of($pledges)
                ->addColumn('action', function ($pledge) use($gov) {
                    $delete = ($pledge->organization_id != $gov->id && $pledge->ods_export_status == null && $pledge->cancelled == null)  ? 
                                '<a class="btn btn-danger btn-sm ml-2 delete-pledge" data-id="'.
                             $pledge->id . '" data-code="'. $pledge->id . '">Delete</a>' : '';
                    $edit = ($pledge->ods_export_status == null && $pledge->cancelled == null)  ? 
                            '<a class="btn btn-primary btn-sm ml-2" href="' . route('admin-pledge.special-campaign.edit',$pledge->id) . '">Edit</a>' : '';
                    return '<a class="btn btn-info btn-sm" href="' . route('admin-pledge.special-campaign.show',$pledge->id) . '">Show</a>' .
                        $edit . 
                        // '<a class="btn btn-primary btn-sm ml-2" href="' . route('admin-pledge.special-campaign.edit',$pledge->id) . '">Edit</a>'
                        $delete;

                })
                ->editColumn('created_at', function ($user) {
                    return $user->created_at->format('Y-m-d H:m:s'); // human readable format
                })
                ->editColumn('updated_at', function ($user) {
                    return $user->updated_at->format('Y-m-d H:m:s'); // human readable format
                })                        
                ->rawColumns(['action','description'])
                ->make(true);
        }

        // get all the record 
        //$campaign_years = CampaignYear::orderBy('calendar_year', 'desc')->paginate(10);
        $organizations = Organization::where('status', 'A')->orderBy('name')->get();
        $campaign_years = CampaignYear::orderBy('calendar_year', 'desc')->get();
        $cities = City::orderBy('city')->get();

        // load the view and pass 
        return view('admin-pledge.special-campaign.index', compact('organizations', 'campaign_years','cities'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $pledge = new SpecialCampaignPledge();

        $pledge->type = 'P';
        $pledge->yearcd = date('Y');
        // $pledge->one_time_amount = 20;
        $pledge->deduct_pay_from = PayCalendar::nextDeductPayFrom();

        $organizations = Organization::where('status', 'A')->orderBy('name')->get();

        $special_campaigns = SpecialCampaign::orderBy('name')->get();

        // default the first special campaign
        $pledge->special_campaign_id = $special_campaigns->first()->id;

        $campaignYears = CampaignYear::where('calendar_year', '>=', today()->year )->orderBy('calendar_year')->get();
        $cities = City::orderBy('city')->get();

        $is_new_pledge = true;

        return view('admin-pledge.special-campaign.create-edit', compact('pledge', 'organizations', 'special_campaigns', 'campaignYears','cities',
                    'is_new_pledge',
                    //  'deduct_pay_from', 'one_time_amount'
                    ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SpecialCampaignPledgeRequest $request)
    {
        
        // Create a new Pledge
        $last_seqno = SpecialCampaignPledge::where('organization_id', $request->organization_id)
                        ->where('user_id', $request->user_id)
                        ->where('pecsf_id', $request->pecsf_id)
                        ->where('yearcd', $request->yearcd)
                        ->max('seqno');

        $seqno = $last_seqno ? ($last_seqno + 1) : 1;
        //  dd([$request, $seqno] );
        $gov = Organization::where('code', 'GOV')->first();

        $pledge = SpecialCampaignPledge::Create([
            'organization_id' => $request->organization_id,
            'user_id' => ($request->organization_id == $gov->id) ? $request->user_id : null,
            'pecsf_id' => (!($request->organization_id == $gov->id)) ? $request->pecsf_id : null,
            'first_name' => (!($request->organization_id == $gov->id)) ? $request->pecsf_first_name : null, 
            'last_name' => (!($request->organization_id == $gov->id)) ? $request->pecsf_last_name : null,
            'city' => (!($request->organization_id == $gov->id)) ? $request->pecsf_city : null,
            'yearcd'  => $request->yearcd,
            'seqno'   => $seqno,
            'special_campaign_id' => $request->special_campaign_id, 
            'one_time_amount' => $request->one_time_amount,
            'deduct_pay_from' => PayCalendar::nextDeductPayFrom(),

            'created_by_id' => Auth::Id(),
            'updated_by_id' => Auth::Id(),
        ]);


        Session::flash('success', 'Pledge with Transaction ID ' . $pledge->id . ' have been created successfully' ); 
        return response()->noContent();
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pledge  $pledge
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        //
        // dd(['show', $request, $id]);

        $pledge = SpecialCampaignPledge::where('id', $id)->first();

        if (!($pledge)) {
            return abort(404);      // 404 Not Found
        }

        $selected_charities =[];

        // $sql = PledgeCharity::selectRaw("charity_id, additional, percentage,
        //             sum(case when frequency = 'one-time' then goal_amount else 0 end) as one_time_amount")
        //         ->where('pledge_id', $id)
        //         ->groupBy(['charity_id', 'additional', 'percentage'])
        //         ;

        // $pledges_charities = $sql->get();
        
        return view('admin-pledge.special-campaign.show', compact('pledge'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pledge  $pledge
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    // public function edit(Request $request, $id)
    {
        //
        $pledge = SpecialCampaignPledge::where('id', $id)
                            ->whereNull('cancelled')
                            ->whereNull('ods_export_status')->first();

        if (!($pledge)) {
            return abort(404);      // 404 Not Found
        }

        $special_campaigns = SpecialCampaign::orderBy('name')->get();

        // $organization = Organization::where('id', $pledge->organization_id)->first();
        $cities = City::orderBy('city')->get();

        // $pool_option = $pledge->type;
        $is_new_pledge = false;
        
        return view('admin-pledge.special-campaign.create-edit', compact('is_new_pledge', 
                                'pledge', 'special_campaigns', 'cities'));
    
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pledge  $pledge
     * @return \Illuminate\Http\Response
     */
    public function update(SpecialCampaignPledgeRequest $request, $id)
    {
        
        $pledge = SpecialCampaignPledge::where('id', $id)->first();

        $gov_organization = Organization::where('code', 'GOV')->first();
        $is_GOV = ($request->organization_id == $gov_organization->id);


        if (!$is_GOV) {
            $pledge->organization_id = $request->organization_id;
            $pledge->pecsf_id   = $request->pecsf_id;
            $pledge->first_name = $request->pecsf_first_name;
            $pledge->last_name  = $request->pecsf_last_name;
            $pledge->city       = $request->pecsf_city;
        }

        $pledge->special_campaign_id = $request->special_campaign_id; 

        $pledge->one_time_amount = $request->one_time_amount ?? 0;
        $pledge->updated_by_id = Auth::id();
        $pledge->save();

        Session::flash('success', 'Pledge with Transaction ID ' . $pledge->id . ' have been updated successfully' ); 
        return response()->noContent();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pledge  $pledge
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //
        $pledge = SpecialCampaignPledge::where('id', $id)
                            ->whereNull('cancelled')
                            ->whereNull('ods_export_status')
                            ->first();

        if (!($pledge)) {
            return abort(404);      // 404 Not Found
        }

        $gov = Organization::where('code', 'GOV')->first();

        if ($pledge->organization_id == $gov->id) {
            return response()->json(['error' => "You are not allowed to delete this pledge " . $pledge->id . " which was created for 'Gov' organization."], 422); 
        }       
        
        // Delete the pledge
        $pledge->updated_by_id = Auth::Id();
        $pledge->save();

        $pledge->delete();

        return response()->noContent();
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pledge  $pledge
     * @return \Illuminate\Http\Response
     */
    public function cancel(SpecialCampaignPledgeRequest $request, $id)
    {

        //
        $pledge = SpecialCampaignPledge::where('id', $id)
                                    ->whereNull('cancelled')
                                    ->whereNull('ods_export_status')
                                    ->first();

        if (!($pledge)) {
            return abort(404);      // 404 Not Found
        }

        // $gov = Organization::where('code', 'GOV')->first();

        // if ($pledge->organization_id == $gov->id) {
        //     return response()->json(['error' => "You are not allowed to cancel this pledge " . $pledge->id . " which was created for 'Gov' organization."], 422); 
        // }       

        // Delete the pledge
        $pledge->cancelled = 'Y';
        $pledge->cancelled_by_id = Auth::Id();
        $pledge->cancelled_at = now();
        $pledge->save();

        Session::flash('success', 'Pledge with Transaction ID ' . $pledge->id . ' have been cancelled successfully' ); 
        return response()->noContent();

    }

}
