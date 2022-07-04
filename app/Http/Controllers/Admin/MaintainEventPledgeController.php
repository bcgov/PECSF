<?php

namespace App\Http\Controllers\Admin;

use App\Models\BankDepositForm;
use App\Models\BusinessUnit;
use App\Models\Department;
use App\Models\Region;
use App\Models\User;
use App\Models\FSPool;
use App\Models\Pledge;
use App\Models\Charity;
use App\Models\City;
use App\Models\CampaignYear;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Models\PledgeCharity;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CampaignPledgeRequest;

class MaintainEventPledgeController extends Controller
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

            $pledges = Pledge::with('organization', 'campaign_year', 'user', 'user.primary_job', 'fund_supported_pool', 'fund_supported_pool.region',
                            'distinct_charities', 'distinct_charities.charity')
                            ->select('pledges.*');

            return Datatables::of($pledges)
                ->addColumn('description', function($pledge) {
                    $text =  $pledge->type == 'P' ? $pledge->fund_supported_pool->region->name :
                                       $pledge->distinct_charities->count() . ' chartites'  ;
                    //   $title = implode(', ',  $pledge->distinct_charities->pluck('charity.charity_name')->toArray());
                    return "<span>" . $text . '</span>' ;
                })
                ->addColumn('action', function ($pledge) {
                    return '<a class="btn btn-info btn-sm" href="' . route('admin-pledge.campaign.show',$pledge->id) . '">Show</a>' .
                        '<a class="btn btn-primary btn-sm ml-2" href="' . route('admin-pledge.campaign.edit',$pledge->id) . '">Edit</a>';
            })->rawColumns(['action','description'])
            ->make(true);
        }

        // get all the record
        //$campaign_years = CampaignYear::orderBy('calendar_year', 'desc')->paginate(10);

        $pools = FSPool::where('start_date', '=', function ($query) {
            $query->selectRaw('max(start_date)')
                ->from('f_s_pools as A')
                ->whereColumn('A.region_id', 'f_s_pools.region_id')
                ->where('A.start_date', '<=', today());
        })
            ->where('status', 'A')
            ->get();
        $regional_pool_id = $pools->count() > 0 ? $pools->first()->id : null;
        $business_units = BusinessUnit::all();
        $regions = Region::all();
        $departments = Department::all();
        $campaign_year = CampaignYear::where('calendar_year', '<=', today()->year + 1 )->orderBy('calendar_year', 'desc')
            ->first();
        $current_user = User::where('id', Auth::id() )->first();
        $cities = City::all();
        if(isset($request->search_by) && !empty($request->begins_with)){
            if($request->search_by == "calendar_year"){
                $event_pledges = BankDepositForm::where("created_at","<=",$request->begins_with);
            }
            else if($request->search_by == "id"){
                $event_pledges = BankDepositForm::where($request->search_by,"=",$request->begins_with);
            }
            else{
                $event_pledges = BankDepositForm::where($request->search_by,"like",$request->begins_with."%");
            }
            $event_pledges = $event_pledges->orderBy("created_at","desc")->limit($request->limit)->get();
        }
        else{
            $event_pledges = BankDepositForm::orderBy("created_at","desc")->limit(30)->get();
        }

        $event_pledges = [];

        // load the view and pass
        return view('admin-pledge.event.index',compact('cities','current_user','campaign_year','departments','regions','business_units','regional_pool_id','pools','event_pledges','request'));

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

        $pay_period_amount = 20;
        $one_time_amount = 20;
        $pay_period_amount_other = null;
        $one_time_amount_other = null;

        return view('admin-pledge.campaign.wizard', compact('pool_option', 'fspools', 'organizations','campaignYears',
            'pay_period_amount','one_time_amount','pay_period_amount_other', 'one_time_amount_other'));
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
                $campaign_year = CampaignYear::where('id', $request->campaign_year_id)->first();

                $pool  = FSPool::current()->where('id', $request->pool_id)->first() ?? null;
                $charities = Charity::whereIn('id', $request->charities)->get() ?? [];

                $pay_period_amount = $request->pay_period_amount > 0 ?
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

                return view('admin-pledge.campaign.partials.summary', compact('user', 'campaign_year', 'pool_option', 'pool',
                            'charities', 'selected_charities', 'pay_period_amount','one_time_amount' ))->render();

            }

            return response()->noContent();

        }

        /* Final submission -- form submission (non-ajax call) */
        $campaign_year = CampaignYear::where('id', $request->campaign_year_id)->first();

        $pay_period_amount = $request->pay_period_amount  ?
                    $request->pay_period_amount : $request->pay_period_amount_other ;
        $one_time_amount = $request->one_time_amount ?
                    $request->one_time_amount : $request->one_time_amount_other;
        $pay_period_annual_amt = $pay_period_amount * $campaign_year->number_of_periods;

        $pledge = Pledge::Create([
            'organization_id' => $request->organization_id,
            'user_id' => $request->user_id,
            'campaign_year_id' => $request->campaign_year_id,
            'type' => $request->pool_option,
            'f_s_pool_id' => $request->pool_option == 'P' ? $request->pool_id : 0,
            'one_time_amount' => $one_time_amount,
            'pay_period_amount' => $pay_period_amount ,
            'goal_amount' => $pay_period_annual_amt + $one_time_amount,
            'created_by_id' => Auth::id(),
            'updated_by_id' => Auth::id(),
        ]);

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
                ->with('success','Pledge with Transaction ID ' . $pledge->id . ' have been created successfully');

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

        $organizations = Organization::where('status', 'A')->orderBy('name')->get();
        $campaignYears = CampaignYear::where('calendar_year', '>=', today()->year )->orderBy('calendar_year')->get();

        $pool_option = $pledge->type;
        $pay_period_amount = $pledge->pay_period_amount ?? 0;
        $one_time_amount = $pledge->one_time_amount ?? 0;

        $amt_choices = [0,6,12,20,50];

        $pay_period_amount_other = in_array($pay_period_amount, $amt_choices) ? '' :   $pay_period_amount;
        $one_time_amount_other = in_array($one_time_amount, $amt_choices) ? '' :   $one_time_amount;


        return view('admin-pledge.campaign.wizard', compact('pledge', 'pool_option', 'fspools', 'organizations','campaignYears',
                    'pay_period_amount','one_time_amount','pay_period_amount_other','one_time_amount_other'));

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
        $pledge->one_time_amount = $one_time_amount;
        $pledge->pay_period_amount = $pay_period_amount;
        $pledge->goal_amount = $pay_period_annual_amt + $one_time_amount;
        $pledge->created_by_id = Auth::id();
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
    public function destroy(Pledge $pledge)
    {
        //
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
