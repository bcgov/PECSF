<?php

namespace App\Http\Controllers\Admin;

use App\Models\BankDepositForm;
use App\Models\BankDepositFormOrganizations;
use App\Models\BankDepositFormAttachments;
use App\Models\BusinessUnit;
use App\Models\Department;
use App\Models\Region;
use App\Models\User;
use App\Models\FSPool;
use App\Models\Pledge;
use App\Models\City;
use App\Models\Charity;
use App\Models\CampaignYear;
use App\Models\Organization;

use Illuminate\Http\Request;
use App\Models\PledgeCharity;
use Illuminate\Support\Facades\Session;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CampaignPledgeRequest;

class EventSubmissionQueueController extends Controller
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

    public function status(Request $request){

        $form = BankDepositForm::where("id",$request->submission_id)->first();
        if($request->status == 1){
            if($form->event_type == "Gaming")
            {
                $count = BankDepositForm::where("event_type","Gaming")->count() + 1;
                $zeroes = 3 - strlen($count);
                $id = "G".date("y");
                for($i = 0; $i<$zeroes;$i++){
                    $id.= "0";
                }
                $id .= $count;
                BankDepositForm::where("id",$request->submission_id)->update(['approved' => $request->status,'pecsf_id' => $id]);
            }

            else if($form->event_type == "Fundraiser")
            {
                $count = BankDepositForm::where("event_type","Fundraiser")->count() + 1;
                $zeroes = 3 - strlen($count);
                $id = "G".date("y");
                for($i = 0; $i<$zeroes;$i++){
                    $id.= "0";
                }
                $id .= $count;
                BankDepositForm::where("id",$request->submission_id)->update(['approved' => $request->status,'pecsf_id' => $id]);
            }
            else{

                BankDepositForm::where("id",$request->submission_id)->update(['approved' => $request->status]);
                $year =  intval(date("Y")) + 1;
                do{
                    $campaign_year = CampaignYear::where('calendar_year', $year)->first();
                    $year--;

                    if($year == 2005){
                        break;
                    }
                }while(!$campaign_year->isOpen());

                if(empty($campaign_year) || !$campaign_year->isOpen()){
                    $campaign_year = CampaignYear::where('calendar_year', intval(date("Y")))->first();
                }

                $gov_organization = Organization::where('code', 'GOV')->first();
                $is_GOV = ($form->organization_code == $gov_organization->code);

                if($is_GOV){
                    $existing = BankDepositForm::where("organization_code","=","GOV")
                        ->where("event-type","=","Cash One-time Donation")
                        ->where("form_submitter_id","=",$form->form_submitter_id)
                        ->get();

                    if(!empty($existing))
                    {
                        BankDepositForm::where("id",$request->submission_id)->update(['bc_gov_id' => "S".$form->bc_gov_id]);
                    }
                }

                $pay_period_amount = $form->deposit_amount;
                $one_time_amount =  $form->deposit_amount;
                $pay_period_annual_amt = $form->deposit_amount;




                // Create a new Pledge
                $form_organization = Organization::where('code', $form->organization_code)->first();
                $form_user = User::where('id', $form->form_submitter_id)->first();
                $pledge = Pledge::Create([
                    'organization_id' => $form_organization->id,
                    'user_id' =>    $form->form_submitter_id,
                    "pecsf_id" =>   $form->pecsf_id,
                    "first_name" => $form_user->name,
                    "last_name" =>  "",
                    "city" =>       $form->employment_city,
                    'campaign_year_id' => $campaign_year->id,
                    'type' => $form->regional_pool_id ? "P" : "C",
                    'f_s_pool_id' => empty($form->regional_pool_id) ? 0 : $form->regional_pool_id,
                    'one_time_amount' => $form->deposit_amount,
                    'pay_period_amount' => 0,
                    'goal_amount' => $form->deposit_amount,
                    'created_by_id' => $form_user->id,
                    'updated_by_id' => Auth::id(),
                ]);

                $message_text = 'Pledge with Transaction ID ' . $pledge->id . ' have been created successfully';


                $pledge->charities()->delete();

                $pledgeCharities = BankDepositFormOrganizations::where("bank_deposit_form_id" , $form->id)->get();

                if ( empty($form->regional_pool_id) )
                {
                    foreach( ['one-time'] as $frequency) {

                        $one_time_sum = 0;
                        $one_time_goal_sum = 0;
                        $pay_period_sum = 0;
                        $pay_period_goal_sum = 0;

                        $last_key = array_key_last($pledgeCharities->toArray());
                        foreach($pledgeCharities as $key => $charity) {
                            $percent = $charity->donation_percent;
                            $charity = Charity::where("id",$charity->vendor_id)->first();


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
                                    'charity_id' => $charity->id,
                                    'pledge_id' => $pledge->id,
                                    'frequency' => 'one-time',
                                    'additional' => $charity->specific_community_or_initiative,
                                    'percentage' => $percent,
                                    'amount' => round($form->deposit_amount * ($charity->donation_percent / 100)),
                                    'goal_amount' => round($form->deposit_amount * ($charity->donation_percent / 100)),
                                ]);
                            }
                        }
                    }
                }

            }
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
     $submissions = BankDepositForm::selectRaw("*,bank_deposit_forms.id as bank_deposit_form_id")
         ->join("users","bank_deposit_forms.form_submitter_id","=","users.id")
         ->where("approved","!=",1)
         ->get();
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
        $regions = Region::where("status","=","A")->get();
        $departments = Department::all();
        $campaign_year = CampaignYear::where('calendar_year', '<=', today()->year + 1 )->orderBy('calendar_year', 'desc')
            ->first();
        $current_user = User::where('id', Auth::id() )->first();
        $cities = City::all();
        $organizations = [];
        $selected_charities = [];

        // load the view and pass
        return view('admin-pledge.submission-queue.index',compact('selected_charities','organizations','cities','pools','regional_pool_id','business_units','regions','departments','campaign_year','submissions','current_user'));
    }
    /**
     * Display a listing of pledge details.
     *
     * @return \Illuminate\Http\Response
     */
    public function details(Request $request){
        $submissions = BankDepositForm::selectRaw("*,bank_deposit_forms.id as bank_deposit_form_id ")
            ->where("bank_deposit_forms.id","=",$request->form_id)
            ->join("users","bank_deposit_forms.form_submitter_id","=","users.id")
            ->get();
        foreach($submissions as $index => $submission){
            $submissions[$index]["charities"] = BankDepositFormOrganizations::where("bank_deposit_form_id","=",$request->form_id)->get();
            $submissions[$index]["attachments"] = BankDepositFormAttachments::where("bank_deposit_form_id","=",$request->form_id)->get();
        }

        $existing = [];
        if($submissions[0]->organization_code == "GOV"){
            $existing = BankDepositForm::where("organization_code","=","GOV")
                ->where("event_type","=","Cash One-time Donation")
                ->where("form_submitter_id","=",$submissions[0]->form_submitter_id)
                ->get();

            if(count($existing) > 0)
            {
                $submissions[0]->bc_gov_id = "S".$submissions[0]->bc_gov_id;
            }
        }
        $existing = [];

        if($submissions[0]->organization_code == "RET"){
            $existing = BankDepositForm::where("organization_code","=","RET")
                ->orderBy("pecsf_id","desc")
                ->whereNotNull("pecsf_id")
                ->get();

            if(count($existing) > 0)
            {
                $submissions[0]->pecsf_id = "R".substr(date("Y"),2,2).(intval(str_replace("R","",$existing[0]->bc_gov_id)) +1);
            }
            else{
                $submissions[0]->pecsf_id = "R".substr(date("Y"),2,2)."001";

            }
        }
        $existing = [];

        if($submissions[0]->event_type == "Gaming")
        {
            $existing = BankDepositForm::where("event_type","=","Gaming")
                ->where("pecsf_id","LIKE","G%")
                ->orderBy("pecsf_id","desc")
                ->get();

            if(count($existing) > 0)
            {
                $submissions[0]->pecsf_id = "G".substr(date("Y"),2,2).(intval(str_replace("G","",$existing[0]->pecsf_id)) + 1);
            }
            else{
                $submissions[0]->pecsf_id = "G".substr(date("Y"),2,2)."001";
            }
        }
        $existing = [];

        if($submissions[0]->event_type == "Fundraiser")
        {
            $existing = BankDepositForm::where("event_type","=","Fundraiser")
                ->where("pecsf_id","LIKE","F%")
                ->orderBy("pecsf_id","desc")
                ->get();

            if(count($existing) > 0)
            {
                $submissions[0]->pecsf_id = "F".substr(date("Y"),2,2).(intval(str_replace("F","",$existing[0]->pecsf_id)) + 1);
            }
            else{
                $submissions[0]->pecsf_id = "F".substr(date("Y"),2,2)."001";
            }
        }

        echo json_encode($submissions);
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
