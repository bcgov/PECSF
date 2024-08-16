<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use App\Models\User;
use App\Models\FSPool;
use App\Models\Charity;
use App\Models\Donation;
use App\Models\PayCalendar;
use App\Models\CampaignYear;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Models\PledgeCharity;
use App\Models\DonateNowPledge;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\Models\NonGovPledgeHistory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\CampaignPledgeRequest;
use App\Http\Requests\DonateNowPledgeRequest;

class DonateNowPledgeController extends Controller
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
    public function index(Request $request)
    {

        if($request->ajax()) {

            // store the filter 
            $filter = $request->except("draw", "columns", "order", "start", "length", "search", "_");
            session(['admin_pledge_donate_now_filter' => $filter]);

            $pledges = DonateNowPledge::with('organization', 'campaign_year', 'user', 'user.primary_job', 'fund_supported_pool', 'fund_supported_pool.region',
                            'charity', 
                            'related_city', 'related_city.region',
                            'user.primary_job.city_by_office_city', 'user.primary_job.city_by_office_city.region')
                            // ->leftJoin('users', 'users.id', '=', 'donate_now_pledges.user_id')
                            ->leftJoin('employee_jobs', 'employee_jobs.emplid', '=', 'donate_now_pledges.emplid')
                            ->where( function($query) {
                                $query->where('employee_jobs.empl_rcd', '=', function($q) {
                                        $q->from('employee_jobs as J2') 
                                            ->whereColumn('J2.emplid', 'employee_jobs.emplid')
                                            ->selectRaw('min(J2.empl_rcd)');
                                    })
                                    ->orWhereNull('employee_jobs.empl_rcd');
                            })
                            ->when($request->tran_id, function($query) use($request) {
                                return $query->where('donate_now_pledges.id', 'like', $request->tran_id);
                            })
                            ->when( $request->organization_id, function($query) use($request) {
                                $query->where('donate_now_pledges.organization_id', $request->organization_id);
                            })
                            ->when( $request->pecsf_id, function($query) use($request) {
                                $query->where('donate_now_pledges.pecsf_id', 'like', '%'. $request->pecsf_id .'%');
                            })
                            ->when( $request->emplid, function($query) use($request) {
                                $query->where('employee_jobs.emplid', 'like', '%'. $request->emplid .'%');
                            })
                            ->when($request->seqno, function($query) use($request) {
                                return $query->where('donate_now_pledges.seqno', $request->seqno);
                            })
                            ->when( $request->name, function($query) use($request) {
                                $query->where('donate_now_pledges.first_name', 'like', '%' . $request->name . '%')
                                      ->orWhere('donate_now_pledges.first_name', 'like', '%' . $request->name . '%')
                                      ->orWhere('employee_jobs.name', 'like', '%' . $request->name . '%');
                            })
                            ->when( $request->city, function($query) use($request) {
                                $query->where( function($q) use($request) {
                                    return $q->where('employee_jobs.office_city', 'like', '%'. $request->city .'%')
                                             ->orWhere('donate_now_pledges.city', 'like', '%'. $request->city .'%');
                                });
                            })
                            ->when( $request->yearcd && $request->yearcd <> 'all', function($query) use($request) {
                                $query->where('donate_now_pledges.yearcd', $request->yearcd );
                            })
                            ->when( $request->cancelled == 'C', function($query) use($request) {
                                $query->whereNotNull('donate_now_pledges.cancelled');
                            })
                            ->when( $request->cancelled == 'N', function($query) use($request) {
                                $query->whereNull('donate_now_pledges.cancelled');
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
                            ->select('donate_now_pledges.*');

            $gov = Organization::where('code', 'GOV')->first();

            return Datatables::of($pledges)
                ->addColumn('description', function($pledge) {
                    // $text =  $pledge->type == 'P' ? $pledge->fund_supported_pool->region->name : 
                    //                    $pledge->distinct_charities()->count() . ' charities'  ;
                    // //   $title = implode(', ',  $pledge->distinct_charities()->pluck('charity.charity_name')->toArray());
                    $title =  $pledge->type == 'P' ? $pledge->fund_supported_pool->region->name : 
                                    $pledge->charity->charity_name  ;
                    $text =  $pledge->type == 'P' ? $pledge->fund_supported_pool->region->name : 
                                    ((strlen($pledge->charity->charity_name) > 50) ? substr($pledge->charity->charity_name, 0, 50) . '...' :
                                    $pledge->charity->charity_name);
                                                                          
                    return "<span title='". $title ."'>" . $text . '</span>' ;
                })
                ->addColumn('action', function ($pledge) use($gov) {
                    $delete = ($pledge->organization_id != $gov->id && $pledge->ods_export_status == null && $pledge->cancelled == null)  ? 
                                '<a class="btn btn-danger btn-sm ml-2 delete-pledge" data-id="'.
                             $pledge->id . '" data-code="'. $pledge->id . '">Delete</a>' : '';
                    $edit = ($pledge->cancelled == null)  ? 
                            '<a class="btn btn-primary btn-sm ml-2" href="' . route('admin-pledge.donate-now.edit',$pledge->id) . '">Edit</a>' : '';
                    return '<a class="btn btn-info btn-sm" href="' . route('admin-pledge.donate-now.show',$pledge->id) . '">Show</a>' .
                        $edit . 
                        // '<a class="btn btn-primary btn-sm ml-2" href="' . route('admin-pledge.donate-now.edit',$pledge->id) . '">Edit</a>'
                        $delete;

                })
                ->editColumn('created_at', function ($user) {
                    return $user->created_at->format('Y-m-d H:i:s'); // human readable format
                })
                ->editColumn('updated_at', function ($user) {
                    return $user->updated_at->format('Y-m-d H:i:s'); // human readable format
                })                        
                ->rawColumns(['action','description'])
                ->make(true);
        }

        // restore filter if required 
        $filter = null;
        if (str_contains( url()->previous(), 'admin-pledge/donate-now')) {
            $filter = session('admin_pledge_donate_now_filter');
        }
                
        // get all the record 
        //$campaign_years = CampaignYear::orderBy('calendar_year', 'desc')->paginate(10);
        $organizations = Organization::where('status', 'A')->orderBy('name')->get();
        $campaign_years = CampaignYear::orderBy('calendar_year', 'desc')->get();
        $cities = City::orderBy('city')->get();

        $deduct_pay_from = PayCalendar::nextDeductPayFrom();
        $yearcd = substr($deduct_pay_from,0,4);

        // load the view and pass 
        return view('admin-pledge.donate-now.index', compact('organizations', 'campaign_years','cities', 'filter', 'yearcd'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $pledge = new DonateNowPledge();

        $pledge->type = 'P';
        $pledge->yearcd = date('Y');
        // $pledge->one_time_amount = 20;
        $pledge->deduct_pay_from = PayCalendar::nextDeductPayFrom();

        $pool_option = 'P';
        $fspools = FSPool::current()->where('status', 'A')->get()->sortBy(function($pool, $key) {
            return $pool->region->name;
        });

        $organizations = Organization::where('status', 'A')->orderBy('name')->get();
        $campaignYears = CampaignYear::where('calendar_year', '>=', today()->year )->orderBy('calendar_year')->get();
        $cities = City::orderBy('city')->get();

        $is_new_pledge = true;
        
        $deduct_pay_from = PayCalendar::nextDeductPayFrom();
        $yearcd = substr($deduct_pay_from,0,4);

        return view('admin-pledge.donate-now.create-edit', compact('pledge', 'pool_option', 'fspools', 'organizations','campaignYears','cities',
                    'is_new_pledge', 'yearcd'
                    //  'deduct_pay_from', 'one_time_amount'
                    ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DonateNowPledgeRequest $request)
    {

        $gov_organization = Organization::where('code', 'GOV')->first();
        $user = User::where('id', $request->user_id )->first();

        // Create a new Pledge
        $last_seqno = DonateNowPledge::where('organization_id', $request->organization_id)
                        ->when( $request->organization_id == $gov_organization->id, function($query) use($user){
                            $query->where('emplid', $user->emplid)
                                  ->whereNull('pecsf_id');
                        })
                        ->when( $request->organization_id != $gov_organization->id, function($query) use($request){
                            $query->whereNull('emplid')
                                  ->where('pecsf_id', $request->pecsf_id);
                        })
                        ->where('yearcd', $request->yearcd)
                        ->max('seqno');

        $seqno = $last_seqno ? ($last_seqno + 1) : 1;
        //  dd([$request, $seqno] );
        $gov = Organization::where('code', 'GOV')->first();
        $pool = FSPool::where('id', $request->pool_id)->first();

        $pledge = DonateNowPledge::Create([
            'organization_id' => $request->organization_id,
            'emplid' => ($request->organization_id == $gov->id) ? $user->emplid : null,
            'user_id' => ($request->organization_id == $gov->id) ? $request->user_id : null,
            'pecsf_id' => (!($request->organization_id == $gov->id)) ? $request->pecsf_id : null,
            'first_name' => (!($request->organization_id == $gov->id)) ? $request->pecsf_first_name : null, 
            'last_name' => (!($request->organization_id == $gov->id)) ? $request->pecsf_last_name : null,
            'city' => (!($request->organization_id == $gov->id)) ? $request->pecsf_city : null,
            'yearcd'  => $request->yearcd,
            'seqno'   => $seqno,
            'type'    => $request->pool_option,
            'region_id' => ($request->pool_option == 'P' ? $pool->region_id : null),
            'f_s_pool_id' => ($request->pool_option == 'P' ? $request->pool_id : null),
            'charity_id' =>  ($request->pool_option == 'C' ? $request->charity_id : null),
            'one_time_amount' => $request->one_time_amount,
            'deduct_pay_from' => PayCalendar::nextDeductPayFrom(),
            'special_program' => $request->special_program,

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

        $pledge = DonateNowPledge::where('id', $id)->first();

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
        
        return view('admin-pledge.donate-now.show', compact('pledge'));

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
        $pledge = DonateNowPledge::where('id', $id)
                            ->whereNull('cancelled')
                            // ->whereNull('ods_export_status')
                            ->first();

        if (!($pledge)) {
            return abort(404);      // 404 Not Found
        }

        // Prepare for display
        $fspools = FSPool::current()->where('status','A')->get()->sortBy(function($pool, $key) {
            return $pool->region->name;
        });

        $organization = Organization::where('id', $pledge->organization_id)->first();
        $cities = City::orderBy('city')->get();

        $pool_option = $pledge->type;
        $is_new_pledge = false;
        
        return view('admin-pledge.donate-now.create-edit', compact('is_new_pledge', 
                                'pledge', 'pool_option', 'fspools', 'organization', 'cities'));
    
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pledge  $pledge
     * @return \Illuminate\Http\Response
     */
    public function update(DonateNowPledgeRequest $request, $id)
    {
        
        $pledge = DonateNowPledge::where('id', $id)->first();

        $gov_organization = Organization::where('code', 'GOV')->first();
        $is_GOV = ($request->organization_id == $gov_organization->id);
        $pool = FSPool::where('id', $request->pool_id)->first();

        // $pay_period_amount = $request->pay_period_amount ? 
        //             $request->pay_period_amount : $request->pay_period_amount_other ;
        // $one_time_amount = $request->one_time_amount ? 
        //             $request->one_time_amount : $request->one_time_amount_other;
        // $pay_period_annual_amt = $pay_period_amount * $campaign_year->number_of_periods;

        if (!$is_GOV) {
            $pledge->organization_id = $request->organization_id;
            $pledge->pecsf_id   = $request->pecsf_id;
            $pledge->first_name = $request->pecsf_first_name;
            $pledge->last_name  = $request->pecsf_last_name;
            $pledge->city       = $request->pecsf_city;
        }

        $pledge->type = $request->pool_option;
        $pledge->region_id = ($request->pool_option == 'P' ? $pool->region_id : null);
        $pledge->f_s_pool_id = ($request->pool_option == 'P' ? $request->pool_id : null);
        $pledge->charity_id  = ($request->pool_option == 'C' ? $request->charity_id : null);
        if (!($pledge->ods_export_status)) {
            // not allow to change after post to PeopleSoft
            $pledge->one_time_amount = $request->one_time_amount ?? 0;
        }
        $pledge->special_program = $request->special_program;
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
        $pledge = DonateNowPledge::where('id', $id)
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
    public function cancel(CampaignPledgeRequest $request, $id)
    {

        //
        $pledge = DonateNowPledge::where('id', $id)
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


    public function getUsers(Request $request)
    {

        if($request->ajax()) {
            $term = trim($request->q);

            $users = User::where('users.organization_id', $request->org_id)
                ->when($term, function($query) use($term) { 
                    return $query->where( function($q) use($term) {
                        $q->whereRaw( "lower(users.name) like '%".addslashes($term)."%'")
                        //   ->orWhereRaw( "lower(users.email) like '%".$term."%'")
                            ->orWhere( "users.emplid", 'like', '%'.addslashes($term).'%');
                });
                })
                ->with('primary_job')
                ->with('primary_job.region') 
                ->with('primary_job.bus_unit') 
                ->limit(50)
                ->orderby('users.name','asc')
                ->get();

            $formatted_users = [];
            foreach ($users as $user) {
                $formatted_users[] = ['id' => $user->id, 
                        'text' => $user->name . ' ('. $user->emplid .')',
                        'email' =>  $user->primary_job->email, 
                        'emplid' => $user->emplid,  
                        'first_name' =>  $user->primary_job->first_name ?? '', 
                        'last_name' =>  $user->primary_job->last_name ?? '', 
                        'department' =>  $user->primary_job->dept_name . ' ('. $user->primary_job->deptid . ')',               
                        'business_unit' => $user->primary_job->bus_unit->name . ' ('.$user->primary_job->bus_unit->code . ')' ,                                        
                        'region' => $user->primary_job->city_by_office_city->region->code ? $user->primary_job->city_by_office_city->region->name . ' (' . $user->primary_job->city_by_office_city->region->code . ')' : '',                    
                        'office_city' => $user->primary_job->office_city ?? '',
                        'organization' => $user->primary_job->organization_name ?? '',
                ];
            }

            return response()->json($formatted_users);

        } else {
            return redirect('/');
        }

    }    

    public function getCampaignPledgeID(Request $request) {

        if($request->ajax()) {

            $gov = Organization::where('code', 'GOV')->first();
       
            $pledge = DonateNowPledge::where('campaign_year_id', $request->campaign_year_id)
                            ->where('organization_id', $request->org_id)
                            ->when($request->org_id == $gov->id, function($q) use($request) {
                                    return $q->where('user_id', $request->user_id);
                            }) 
                            ->when($request->org_id != $gov->id, function($q) use($request) {
                                return $q->where('pecsf_id', $request->pecsf_id);
                            })
                            ->first(); 

            $result = (object) [ 'id' => ($pledge ? $pledge->id : null) ]; 
            return json_encode( $result );
        } else {
            return redirect('/');
        }
    }

    // public function getNonGovUserDetail(Request $request) {
     
    //     if($request->ajax()) {

    //         // Search for the Non-Gov History
    //         $pledge = DonateNowPledge::join('campaign_years', 'pledges.campaign_year_id', 'campaign_years.id')
    //                         ->where('pledges.organization_id', $request->org_id )
    //                         ->where('pledges.pecsf_id', $request->pecsf_id)
    //                         ->orderBy('campaign_years.calendar_year', 'desc')
    //                         ->first();

    //         if ($pledge) {
    //             $formatted_result = (object) [
    //                     'first_name' => $pledge->first_name,
    //                     'last_name' => $pledge->last_name,
    //                     'city' => $pledge->city,
    //                 ];

    //             return json_encode( $formatted_result );
    //         }
            

    //         // Search Non-Gov History
    //         $history = NonGovPledgeHistory::leftJoin('organizations', 'non_gov_pledge_histories.org_code', 'organizations.code')
    //                         ->where('organizations.id', $request->org_id )
    //                         ->where('non_gov_pledge_histories.pecsf_id', $request->pecsf_id)
    //                         ->orderBy('non_gov_pledge_histories.yearcd', 'desc')
    //                         ->first();

    //         if ($history) {
    //             $formatted_result = (object) [
    //                     'first_name' => $history->first_name,
    //                     'last_name' => $history->last_name,
    //                     'city' => $history->city,
    //                 ];

    //             return json_encode( $formatted_result );
    //         }
                
    //         return response()->noContent();    

    //     } else {
    //         return redirect('/');
    //     }
    // }

}
