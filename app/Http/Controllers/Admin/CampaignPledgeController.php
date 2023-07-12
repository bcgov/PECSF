<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use App\Models\User;
use App\Models\FSPool;
use App\Models\Pledge;
use App\Models\Charity;
use App\Models\Donation;
use App\Models\CampaignYear;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Models\PledgeCharity;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\Models\NonGovPledgeHistory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CampaignPledgeRequest;

class CampaignPledgeController extends Controller
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
        // data.organization_id = $('#organization_id').val();
        // data.pecsf_id = $('#pecsf_id').val();
        // data.name = $('#name').val();
        // data.pecsf_city = $('#pecsf_city').val();
        // data.campaign_year_id = $('#campaign_year_id').val();
        // data.one_time_amt_from = $('#one_time_amt_from').val();
        // data.one_time_amt_to = $('#one_time_amt_to').val();
        // data.pay_period_amt_from = $('#pay_period_amt_from').val();
        // data.pay_period_amt_to = $('#pay_period_amt_to').val();

        if($request->ajax()) {

            // store the filter 
            $filter = $request->except("draw", "columns", "order", "start", "length", "search", "_");
            session(['admin_pledge_campaign_filter' => $filter]);

            $pledges = Pledge::with('organization', 'campaign_year', 'user', 'user.primary_job', 'fund_supported_pool', 'fund_supported_pool.region',
                            'distinct_charities', 'distinct_charities.charity')
                            // ->leftJoin('users', 'users.id', '=', 'pledges.user_id')
                            ->leftJoin('employee_jobs', 'employee_jobs.emplid', '=', 'pledges.emplid')
                            ->where( function($query) {
                                $query->where('employee_jobs.empl_rcd', '=', function($q) {
                                        $q->from('employee_jobs as J2') 
                                            ->whereColumn('J2.emplid', 'employee_jobs.emplid')
                                            ->selectRaw('min(J2.empl_rcd)');
                                    })
                                    ->orWhereNull('employee_jobs.empl_rcd');
                            })
                            ->when($request->tran_id, function($query) use($request) {
                                return $query->where('pledges.id', 'like', $request->tran_id);
                            })
                            ->when( $request->organization_id, function($query) use($request) {
                                $query->where('pledges.organization_id', $request->organization_id);
                            })
                            ->when( $request->pecsf_id, function($query) use($request) {
                                $query->where('pledges.pecsf_id', 'like', '%'. $request->pecsf_id .'%');
                            })
                            ->when( $request->emplid, function($query) use($request) {
                                $query->where('employee_jobs.emplid', 'like', '%'. $request->emplid .'%');
                            })
                            ->when( $request->name, function($query) use($request) {
                                $query->where('pledges.first_name', 'like', '%' . $request->name . '%')
                                      ->orWhere('pledges.first_name', 'like', '%' . $request->name . '%')
                                      ->orWhere('employee_jobs.name', 'like', '%' . $request->name . '%');
                            })
                            ->when( $request->city, function($query) use($request) {
                                $query->where( function($q) use($request) {
                                    return $q->where('employee_jobs.city', 'like', '%'. $request->city .'%')
                                             ->orWhere('pledges.city', 'like', '%'. $request->city .'%');
                                });
                            })
                            ->when( $request->campaign_year_id, function($query) use($request) {
                                $query->where('campaign_year_id', $request->campaign_year_id);
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
                            ->select('pledges.*');

            $gov = Organization::where('code', 'GOV')->first();

            return Datatables::of($pledges)
                ->addColumn('description', function($pledge) {
                    $text =  $pledge->type == 'P' ? $pledge->fund_supported_pool->region->name : 
                                       $pledge->distinct_charities()->count() . ' charities'  ;
                    //   $title = implode(', ',  $pledge->distinct_charities()->pluck('charity.charity_name')->toArray());
                    return "<span>" . $text . '</span>' ;
                })
                ->addColumn('action', function ($pledge) use($gov) {
                    $delete = ($pledge->organization_id != $gov->id && $pledge->ods_export_status == null )  ? 
                                    '<a class="btn btn-danger btn-sm ml-2 delete-pledge" data-id="'.
                                    $pledge->id . '" data-code="'. $pledge->id . '">Delete</a>' : '';
                    $edit = ($pledge->ods_export_status == null )  ? 
                            '<a class="btn btn-primary btn-sm ml-2" href="' . route('admin-pledge.campaign.edit',$pledge->id) . '">Edit</a>' : '';
                    return  '<a class="btn btn-info btn-sm" href="' . route('admin-pledge.campaign.show',$pledge->id) . '">Show</a>' .
                        $edit . 
                        // '<a class="btn btn-primary btn-sm ml-2" href="' . route('admin-pledge.donate-now.edit',$pledge->id) . '">Edit</a>'
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

        // restore filter if required 
        $filter = null;
        if (str_contains( url()->previous(), 'admin-pledge/campaign')) {
            $filter = session('admin_pledge_campaign_filter');
        }

        // get all the record 
        //$campaign_years = CampaignYear::orderBy('calendar_year', 'desc')->paginate(10);
        $organizations = Organization::where('status', 'A')->orderBy('name')->get();
        $campaign_years = CampaignYear::orderBy('calendar_year', 'desc')->get();
        $cities = City::orderBy('city')->get();

        // load the view and pass 
        return view('admin-pledge.campaign.index', compact('organizations', 'campaign_years','cities', 'filter'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $pool_option = 'P';
        $fspools = FSPool::current()->get()->sortBy(function($pool, $key) {
            return $pool->region->name;
        });

        $organizations = Organization::where('status', 'A')->orderBy('name')->get();
        $campaignYears = CampaignYear::where('calendar_year', '>=', today()->year )->orderBy('calendar_year')->get();
        $cities = City::orderBy('city')->get();

        $pay_period_amount = 0;
        $one_time_amount = 0;
        $pay_period_amount_other = null;
        $one_time_amount_other = null;

        $edit_pecsf_allow = true;

        return view('admin-pledge.campaign.wizard', compact('pool_option', 'fspools', 'organizations','campaignYears',
                    'edit_pecsf_allow',
                    'cities', 'pay_period_amount','one_time_amount','pay_period_amount_other', 'one_time_amount_other'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CampaignPledgeRequest $request)
    {
        //

        if ($request->ajax()) {

            // Generate Summary Page 
            if ($request->step == 3)  {

                $pool_option = $request->pool_option;


                $user = User::where('id', $request->user_id)->first() ?? null;
                $organization = Organization::where('id', $request->organization_id)->first() ?? null;
                $campaign_year = CampaignYear::where('id', $request->campaign_year_id)->first();

                $pool  = FSPool::current()->where('id', $request->pool_id)->first() ?? null;
                $charities = Charity::whereIn('id', $request->charities)->get() ?? [];

                $pay_period_amount = $request->pay_period_amount;
                $pay_period_total_amount = $request->pay_period_amount > 0 ? 
                            $request->pay_period_amount * $campaign_year->number_of_periods :
                            $request->pay_period_amount_other * $campaign_year->number_of_periods;
                $one_time_amount = $request->one_time_amount > 0 ? 
                            $request->one_time_amount : $request->one_time_amount_other;

                //
                $selected_charities =[];
                for ($i=0; $i < count($request->charities); $i++) {
                    $charity = Charity::where('id', $request->charities[$i])->first();
                    $charity['additional'] = $request->additional[$i];
                    $charity['percentage'] = $request->percentages[$i];
                    array_push($selected_charities, $charity);
                }                

                return view('admin-pledge.campaign.partials.summary', compact('user', 'organization', 'campaign_year',
                            'pool_option', 'pool', 'charities', 'selected_charities', 
                            'pay_period_amount', 'pay_period_total_amount', 'one_time_amount',
                             'request'))->render();

            }
            
            return response()->noContent();

        }
       
        
        /* Final submission -- form submission (non-ajax call) */
        $user = User::where('id', $request->user_id)->first();

        $campaign_year = CampaignYear::where('id', $request->campaign_year_id)->first();
        $gov_organization = Organization::where('code', 'GOV')->first();
        $is_GOV = ($request->organization_id == $gov_organization->id);

        $pay_period_amount = $request->pay_period_amount  ? 
                    $request->pay_period_amount : $request->pay_period_amount_other ;
        $one_time_amount = $request->one_time_amount ? 
                    $request->one_time_amount : $request->one_time_amount_other;
        $pay_period_annual_amt = $pay_period_amount * $campaign_year->number_of_periods;

        // Pool
        $pool = FSPool::where('id', $request->pool_id)->first();

        // Make sure that there is no pledge transaction setup yet 
        $message_text = '';
        $pledge = Pledge::where('organization_id', $request->organization_id)
                            ->when( $is_GOV, function($query) use($user) {
                                // ->where('user_id', $request->user_id)
                                $query->where('emplid', $user->emplid);                                
                            })
                            ->when( !($is_GOV), function($query) use($request) {
                                $query->where('pecsf_id', $request->pecsf_id);                                
                            })
                            ->where('campaign_year_id', $request->campaign_year_id)
                            ->first();
        if ($pledge) {
            // Update the esiting one 
            $pledge->type = $request->pool_option;
            $pledge->region_id = $request->pool_option == 'P' ? $pool->region_id : null;
            $pledge->f_s_pool_id = $request->pool_option == 'P' ? $request->pool_id : 0;
            $pledge->one_time_amount = $one_time_amount ?? 0;
            $pledge->pay_period_amount = $pay_period_amount ?? 0;
            $pledge->goal_amount = $pay_period_annual_amt + $one_time_amount;
            $pledge->updated_by_id = Auth::id();
            $pledge->save();

            $message_text = 'Pledge with Transaction ID ' . $pledge->id . ' have been updated successfully';

        } else {
            // Create a new Pledge

            $pledge = Pledge::Create([
                'organization_id' => $request->organization_id,
                'emplid' =>     $is_GOV ? $user->emplid : null,
                'user_id' =>    $is_GOV ? $request->user_id : 0,

                "pecsf_id" =>   $is_GOV ? null : $request->pecsf_id,
                "first_name" => $is_GOV ? null : $request->pecsf_first_name,
                "last_name" =>  $is_GOV ? null : $request->pecsf_last_name,
                "city" =>       $is_GOV ? null : $request->pecsf_city,

                'campaign_year_id' => $request->campaign_year_id,
                'type' => $request->pool_option,
                'region_id' => $request->pool_option == 'P' ? $pool->region_id : null,
                'f_s_pool_id' => $request->pool_option == 'P' ? $request->pool_id : 0,
                'one_time_amount' => $one_time_amount ?? 0,
                'pay_period_amount' => $pay_period_amount ?? 0,
                'goal_amount' => $pay_period_annual_amt + $one_time_amount,
                'created_by_id' => Auth::id(),
                'updated_by_id' => Auth::id(),
            ]);

            $message_text = 'Pledge with Transaction ID ' . $pledge->id . ' have been created successfully';
        }

        // $pledge->charities()->delete();
        foreach($pledge->charities as $pledge_charity) {
            $pledge_charity->delete();
        }

        if ( $request->pool_option == 'C' ) 
        {
            foreach( ['one-time', 'bi-weekly'] as $frequency) {

                $one_time_sum = 0;
                $one_time_goal_sum = 0;
                $pay_period_sum = 0;
                $pay_period_goal_sum = 0;

                $last_key = array_key_last($request->charities);
                foreach($request->charities as $key => $charity) {
                    $percent = $request->percentages[$key];

                    $new_one_time = round( $percent * $one_time_amount /100, 2);
                    $new_one_time_goal = round( $percent * $one_time_amount /100, 2);
                    $new_pay_period = round( $percent * $pay_period_amount /100, 2);
                    $new_pay_period_goal = round( $percent * $pay_period_annual_amt /100, 2);

                    if ($key == $last_key) {
                        $new_one_time = round($one_time_amount - $one_time_sum, 2);
                        $new_one_time_goal = round($one_time_amount - $one_time_goal_sum, 2);
                        $new_pay_period = round($pay_period_amount - $pay_period_sum, 2);
                        $new_pay_period_goal = round($pay_period_annual_amt - $pay_period_goal_sum, 2);
                    }

                    // One-Time 
                    if ($frequency == 'one-time' && $one_time_amount) {
                        PledgeCharity::create([
                            'charity_id' => $charity,
                            'pledge_id' => $pledge->id,
                            'frequency' => 'one-time',
                            'additional' => $request->additional[$key],
                            'percentage' => $request->percentages[$key],
                            'amount' => $new_one_time,
                            'goal_amount' => $new_one_time_goal,
                        ]);
                    }

                    // Bi-weekly
                    if ($frequency == 'bi-weekly' && $pay_period_amount) {
                        
                        PledgeCharity::create([
                            'charity_id' => $charity,
                            'pledge_id' => $pledge->id,
                            'frequency' => 'bi-weekly',
                            'additional' => $request->additional[$key],
                            'percentage' => $request->percentages[$key],
                            'amount' => $new_pay_period,
                            'goal_amount' => $new_pay_period_goal,
                        ]);
                    }

                    $one_time_sum += $new_one_time;
                    $one_time_goal_sum += $new_one_time_goal;
                    $pay_period_sum += $new_pay_period; 
                    $pay_period_goal_sum += $new_pay_period_goal; 

                }
            }
            
        }

        // return response()->noContent();
        return redirect()->route('admin-pledge.campaign.index')
                ->with('success', $message_text);
        
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

        $pledge = Pledge::where('id', $id)->first();

        $selected_charities =[];

        $sql = PledgeCharity::selectRaw("charity_id, additional, percentage,
                    sum(case when frequency = 'one-time' then goal_amount else 0 end) as one_time_amount,
                    sum(case when frequency = 'bi-weekly' then goal_amount else 0 end) pay_period_amount")
                ->where('pledge_id', $id)
                ->groupBy(['charity_id', 'additional', 'percentage'])
                ;

        $pledges_charities = $sql->get();

        $pool_charities= null;
        if ($pledge->type =='P') {
            $pool_charities = FSPool::asOfDate($pledge->created_at)->where('region_id', $pledge->region_id)->first()->charities;
        }

    
        
        return view('admin-pledge.campaign.show', compact('pledge', 'pledges_charities', 'pool_charities'));

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
        $pledge = Pledge::where('id', $id)
                        ->whereNull('ods_export_status')->first();

        if (!($pledge)) {
            return abort(404);      // 404 Not Found
        }
     
        $fspools = FSPool::current()->get()->sortBy(function($pool, $key) {
            return $pool->region->name;
        });

        $organization = Organization::where('id', $pledge->organization_id)->first();
        $organizations = Organization::where('status', 'A')->orderBy('name')->get();
        $campaignYears = CampaignYear::where('calendar_year', '>=', today()->year )->orderBy('calendar_year')->get();
        $cities = City::orderBy('city')->get();

        $pool_option = $pledge->type;
        $pay_period_amount = $pledge->pay_period_amount ?? 0;
        $one_time_amount = $pledge->one_time_amount ?? 0;

        $amt_choices = [0,6,12,20,50];

        $pay_period_amount_other = in_array($pay_period_amount, $amt_choices) ? '' :   $pay_period_amount;
        $one_time_amount_other = in_array($one_time_amount, $amt_choices) ? '' :   $one_time_amount;

        // For Non-Government 
        $edit_pecsf_allow = false;
        
        if ($pledge->pecsf_id) {
            $count = Donation::where('org_code', $organization->code)
                                ->where('pecsf_id', $pledge->pecsf_id)
                                ->where('yearcd', $pledge->campaign_year->calendar_year)
                                ->count();
            if ($count == 0) {
                $edit_pecsf_allow = true;
            }
        }

        return view('admin-pledge.campaign.wizard', compact('edit_pecsf_allow', 'pledge', 'pool_option', 'fspools', 'organization', 'organizations','campaignYears',
                    'cities', 'pay_period_amount','one_time_amount','pay_period_amount_other','one_time_amount_other'));
    
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pledge  $pledge
     * @return \Illuminate\Http\Response
     */
    public function update(CampaignPledgeRequest $request, $id)
    {
        
        // dd([$request, $id]);

        $pledge = Pledge::where('id', $id)->first();
        $campaign_year = $pledge->campaign_year;

        $gov_organization = Organization::where('code', 'GOV')->first();
        $is_GOV = ($request->organization_id == $gov_organization->id);


        $pay_period_amount = $request->pay_period_amount ? 
                    $request->pay_period_amount : $request->pay_period_amount_other ;
        $one_time_amount = $request->one_time_amount ? 
                    $request->one_time_amount : $request->one_time_amount_other;
        $pay_period_annual_amt = $pay_period_amount * $campaign_year->number_of_periods;

        if (!$is_GOV) {
            $pledge->organization_id = $request->organization_id;
            $pledge->pecsf_id   =   $request->pecsf_id;
            $pledge->first_name = $request->pecsf_first_name;
            $pledge->last_name  = $request->pecsf_last_name;
            $pledge->city       = $request->pecsf_city;
        }

        $pool = FSPool::where('id', $request->pool_id)->first();

        $pledge->type = $request->pool_option;
        $pledge->region_id = $request->pool_option == 'P' ? $pool->region_id : null;
        $pledge->f_s_pool_id = $request->pool_option == 'P' ? $request->pool_id : 0;
        $pledge->one_time_amount = $one_time_amount ?? 0;
        $pledge->pay_period_amount = $pay_period_amount ?? 0;
        $pledge->goal_amount = $pay_period_annual_amt + $one_time_amount;
        $pledge->updated_by_id = Auth::id();
        $pledge->save();

        // $pledge->charities()->delete();
        foreach($pledge->charities as $pledge_charity) {
            $pledge_charity->delete();
        }

        if ( $request->pool_option == 'C' ) 
        {
            foreach( ['one-time', 'bi-weekly'] as $frequency) {

                $one_time_sum = 0;
                $one_time_goal_sum = 0;
                $pay_period_sum = 0;
                $pay_period_goal_sum = 0;

                $last_key = array_key_last($request->charities);
                foreach($request->charities as $key => $charity) {

                    $percent = $request->percentages[$key];

                    $new_one_time = round( $percent * $one_time_amount /100, 2);
                    $new_one_time_goal = round( $percent * $one_time_amount /100, 2);
                    $new_pay_period = round( $percent * $pay_period_amount /100, 2);
                    $new_pay_period_goal = round( $percent * $pay_period_annual_amt /100, 2);

                    if ($key == $last_key) {
                        $new_one_time = round($one_time_amount - $one_time_sum, 2);
                        $new_one_time_goal = round($one_time_amount - $one_time_goal_sum, 2);
                        $new_pay_period = round($pay_period_amount - $pay_period_sum, 2);
                        $new_pay_period_goal = round($pay_period_annual_amt - $pay_period_goal_sum, 2);
                    }

                    // One-Time 
                    if ($frequency == 'one-time' && $one_time_amount) {
                        PledgeCharity::create([
                            'charity_id' => $charity,
                            'pledge_id' => $pledge->id,
                            'frequency' => 'one-time',
                            'additional' => $request->additional[$key],
                            'percentage' => $request->percentages[$key],
                            'amount' => $new_one_time,
                            'goal_amount' => $new_one_time_goal,
                        ]);
                    }

                    // Bi-weekly
                    if ($frequency == 'bi-weekly' && $pay_period_amount) {
                        
                        PledgeCharity::create([
                            'charity_id' => $charity,
                            'pledge_id' => $pledge->id,
                            'frequency' => 'bi-weekly',
                            'additional' => $request->additional[$key],
                            'percentage' => $request->percentages[$key],
                            'amount' => $new_pay_period,
                            'goal_amount' => $new_pay_period_goal,
                        ]);
                    }

                    $one_time_sum += $new_one_time;
                    $one_time_goal_sum += $new_one_time_goal;
                    $pay_period_sum += $new_pay_period; 
                    $pay_period_goal_sum += $new_pay_period_goal; 

                }
            }
            
        }

       return redirect()->route('admin-pledge.campaign.index')
                ->with('success','Pledge with Transaction ID ' . $pledge->id . ' have been updated successfully');

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
        $pledge = Pledge::where('id', $id)->first();

        $gov = Organization::where('code', 'GOV')->first();

        if ($pledge->organization_id == $gov->id) {
            return response()->json(['error' => "You are not allowed to delete this pledge " . $pledge->id . " which was created for 'Gov' organization."], 422); 
        }       

        // Check whether there is transactions exists
        $sql = Donation::whereExists(function ($query) use($id) {
                    $query->select(DB::raw(1))
                          ->from('pledges')
                          ->Join('organizations', 'pledges.organization_id', 'organizations.id')
                          ->Join('campaign_years', 'pledges.campaign_year_id', 'campaign_years.id')
                          ->whereColumn('donations.pecsf_id', 'pledges.pecsf_id')
                          ->whereColumn('donations.org_code', 'organizations.code')
                          ->whereColumn('donations.yearcd', 'campaign_years.calendar_year')
                          ->where('pledges.id', $id);
                  });

        if ($sql->count() > 0) {
            return response()->json(['error' => "You are not allowed to delete this pledge " . $pledge->id . ', has donation transactions loaded.'], 422); 
        }       
        
        // Delete the pledge and pledge charities
        if ($pledge->type == 'C' ) {
            // $pledge->charities()->delete();
            foreach($pledge->charities as $pledge_charity) {
                $pledge_charity->delete();
            }
        }
        $pledge->updated_by_id = Auth::Id();
        $pledge->save();
        $pledge->delete();

        return response()->noContent();
        


    }


    public function getUsers(Request $request)
    {

        $term = trim($request->q);

        $users = User::where('users.organization_id', $request->org_id)
             ->when($term, function($query) use($term) { 
                return $query->where( function($q) use($term) {
                      $q->whereRaw( "lower(users.name) like '%".$term."%'")
                       //   ->orWhereRaw( "lower(users.email) like '%".$term."%'")
                        ->orWhere( "users.emplid", 'like', '%'.$term.'%');
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
                    'department' =>  $user->primary_job->dept_name ? $user->primary_job->dept_name . ' ('. $user->primary_job->deptid . ')' : '',               
                    'business_unit' => $user->primary_job->bus_unit->name ? $user->primary_job->bus_unit->name . ' ('.$user->primary_job->bus_unit->code . ')' : '',                                        
                    'region' => $user->primary_job->region->name ? $user->primary_job->region->name . ' (' . $user->primary_job->region->code . ')' : '',                    
                    'organization' => $user->primary_job->organization_name ?? '',
            ];
        }

        return response()->json($formatted_users);

    }    

    public function getCampaignPledgeID(Request $request) {

        if($request->ajax()) {

            $gov = Organization::where('code', 'GOV')->first();
       
            $pledge = Pledge::where('campaign_year_id', $request->campaign_year_id)
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
        }
    }

    public function getNonGovUserDetail(Request $request) {
     
        if($request->ajax()) {

            // Get Region from Org
            $org = Organization::where('id', $request->org_id)->first();
            $pecsf_bu = $org ? ($org->business_unit ? $org->business_unit->name : '') : ''; 
            $formatted_result = (object) [
                'pecsf_bu' => $pecsf_bu,
            ];

            // Search for the Non-Gov History
            $pledge = Pledge::join('campaign_years', 'pledges.campaign_year_id', 'campaign_years.id')
                            ->where('pledges.organization_id', $request->org_id )
                            ->where('pledges.pecsf_id', $request->pecsf_id)
                            ->orderBy('pledges.id', 'desc')
                            ->orderBy('campaign_years.calendar_year', 'desc')
                            ->first();

            if ($pledge) {
                $formatted_result->first_name = $pledge->first_name;
                $formatted_result->last_name = $pledge->last_name;
                $formatted_result->city = $pledge->city;

            } else {
                // Search Non-Gov History
                $history = NonGovPledgeHistory::leftJoin('organizations', 'non_gov_pledge_histories.org_code', 'organizations.code')
                                ->where('organizations.id', $request->org_id )
                                ->where('non_gov_pledge_histories.pecsf_id', $request->pecsf_id)
                                ->orderBy('non_gov_pledge_histories.yearcd', 'desc')
                                ->first();

                if ($history) {
                    $formatted_result->first_name = $history->first_name;
                    $formatted_result->last_name = $history->last_name;
                    $formatted_result->city = $history->city;
                }
            }
                
            if (isset($formatted_result->city)) {
                $city = City::where('city', trim($formatted_result->city) )->first();
                $formatted_result->pecsf_region = $city ? ($city->region ? $city->region->name : '') : '';
            }

            return json_encode( $formatted_result );
            
            // return response()->noContent();    

        }
    }

}
