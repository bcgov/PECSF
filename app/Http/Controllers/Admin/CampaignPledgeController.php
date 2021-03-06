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

            $pledges = Pledge::with('organization', 'campaign_year', 'user', 'user.primary_job', 'fund_supported_pool', 'fund_supported_pool.region',
                            'distinct_charities', 'distinct_charities.charity')
                            ->leftJoin('users', 'users.id', '=', 'pledges.user_id')
                            ->leftJoin('employee_jobs', 'employee_jobs.id', '=', 'users.employee_job_id')
                            ->when( $request->organization_id, function($query) use($request) {
                                $query->where('organization_id', $request->organization_id);
                            })
                            ->when( $request->pecsf_id, function($query) use($request) {
                                $query->where('pecsf_id', 'like', '%'. $request->pecsf_id .'%');
                            })
                            ->when( $request->emplid, function($query) use($request) {
                                $query->where('employee_jobs.emplid', 'like', '%'. $request->emplid .'%');
                            })
                            ->when( $request->name, function($query) use($request) {
                                $query->where('first_name', 'like', '%' . $request->name . '%')
                                      ->orWhere('first_name', 'like', '%' . $request->name . '%')
                                      ->orWhere('name', 'like', '%' . $request->name . '%');
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
                    $delete = ($pledge->organization_id != $gov->id)  ? '<a class="btn btn-danger btn-sm ml-2 delete-pledge" data-id="'.
                             $pledge->id . '" data-code="'. $pledge->id . '">Delete</a>' : '';
                    return '<a class="btn btn-info btn-sm" href="' . route('admin-pledge.campaign.show',$pledge->id) . '">Show</a>' .
                        '<a class="btn btn-primary btn-sm ml-2" href="' . route('admin-pledge.campaign.edit',$pledge->id) . '">Edit</a>'
                        . $delete;

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
        $campaign_years = CampaignYear::orderBy('calendar_year')->get();
        $cities = City::orderBy('city')->get();

        // load the view and pass 
        return view('admin-pledge.campaign.index', compact('organizations', 'campaign_years','cities'));

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

        $pay_period_amount = 20;
        $one_time_amount = 20;
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

                $_charities = Charity::whereIn('id', $request->charities)->get() ?? [];

                foreach ($_charities as $key => $charity) {
                    $charity['additional'] = $request->additional[$key];
                    $charity['percentage'] = $request->percentages[$key];
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


        $campaign_year = CampaignYear::where('id', $request->campaign_year_id)->first();
        $gov_organization = Organization::where('code', 'GOV')->first();
        $is_GOV = ($request->organization_id == $gov_organization->id);

        $pay_period_amount = $request->pay_period_amount  ? 
                    $request->pay_period_amount : $request->pay_period_amount_other ;
        $one_time_amount = $request->one_time_amount ? 
                    $request->one_time_amount : $request->one_time_amount_other;
        $pay_period_annual_amt = $pay_period_amount * $campaign_year->number_of_periods;


        // Make sure that there is no pledge transaction setup yet 
        $message_text = '';
        $pledge = Pledge::where('organization_id', $request->organization_id)
                            ->where('user_id', $request->user_id)
                            ->where('campaign_year_id', $request->campaign_year_id)
                            ->first();
        if ($pledge) {
            // Update the esiting one 
            $pledge->type = $request->pool_option;
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

                'user_id' =>    $is_GOV ? $request->user_id : 0,

                "pecsf_id" =>   $is_GOV ? null : $request->pecsf_id,
                "first_name" => $is_GOV ? null : $request->pecsf_first_name,
                "last_name" =>  $is_GOV ? null : $request->pecsf_last_name,
                "city" =>       $is_GOV ? null : $request->pecsf_city,

                'campaign_year_id' => $request->campaign_year_id,
                'type' => $request->pool_option,
                'f_s_pool_id' => $request->pool_option == 'P' ? $request->pool_id : 0,
                'one_time_amount' => $one_time_amount ?? 0,
                'pay_period_amount' => $pay_period_amount ?? 0,
                'goal_amount' => $pay_period_annual_amt + $one_time_amount,
                'created_by_id' => Auth::id(),
                'updated_by_id' => Auth::id(),
            ]);

            $message_text = 'Pledge with Transaction ID ' . $pledge->id . ' have been created successfully';
        }

        $pledge->charities()->delete();

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
        
        return view('admin-pledge.campaign.show', compact('pledge', 'pledges_charities'));

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
        $pledge = Pledge::where('id', $id)->first();
     
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

        $pay_period_amount = $request->pay_period_amount ? 
                    $request->pay_period_amount : $request->pay_period_amount_other ;
        $one_time_amount = $request->one_time_amount ? 
                    $request->one_time_amount : $request->one_time_amount_other;
        $pay_period_annual_amt = $pay_period_amount * $campaign_year->number_of_periods;

        $pledge->type = $request->pool_option;
        $pledge->f_s_pool_id = $request->pool_option == 'P' ? $request->pool_id : 0;
        $pledge->one_time_amount = $one_time_amount ?? 0;
        $pledge->pay_period_amount = $pay_period_amount ?? 0;
        $pledge->goal_amount = $pay_period_annual_amt + $one_time_amount;
        $pledge->updated_by_id = Auth::id();
        $pledge->save();

        $pledge->charities()->delete();

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
        
        // Delete the pledge
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
                    'department' =>  $user->primary_job->dept_name . ' ('. $user->primary_job->deptid . ')',               
                    'business_unit' => $user->primary_job->bus_unit->name . ' ('.$user->primary_job->bus_unit->code . ')' ,                                        
                    'region' => $user->primary_job->region->name . ' (' . $user->primary_job->region->code . ')',                    
                    'organization' => $user->primary_job->organization_name ?? '',
            ];
        }

        return response()->json($formatted_users);

    }    
}
