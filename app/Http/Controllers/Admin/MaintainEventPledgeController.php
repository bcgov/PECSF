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
use Illuminate\Support\Facades\Session;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CampaignPledgeRequest;
use Carbon\Carbon;

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
                ->whereNull('A.deleted_at')
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
        if(!empty(request("search_by")) && !empty(request("begins_with"))){
            if(request("search_by") == "calendar_year"){
                $event_pledges = BankDepositForm::join("campaign_years","campaign_years.id","bank_deposit_forms.campaign_year_id");
                $event_pledges =  $event_pledges->where("calendar_year","=",request("begins_with"));
            }
            else if($request->search_by == "id"){
                $event_pledges = BankDepositForm::where($request->search_by,"=",$request->begins_with);
            }
            else if($request->search_by == "employee_id"){
                $event_pledges = BankDepositForm::where("bc_gov_id","like","%".$request->begins_with."%");
            }
            else if($request->search_by == "pecsf_id"){
                $event_pledges =  BankDepositForm::where("pecsf_id","like","%".$request->begins_with."%");
            }
            else{
                $event_pledges = BankDepositForm::where($request->search_by,"=",$request->begins_with);

            }

            if(!empty($request->event_type))
            {
                $event_pledges = $event_pledges->where("event_type","=", $request->event_type);
            }

            if(!empty($request->sub_type))
            {
                $event_pledges = $event_pledges->where("sub_type","=", $request->sub_type);
            }
            $event_pledges = $event_pledges->orderBy("bank_deposit_forms.created_at","desc")->limit(request("limit"))->get();
        }
        else{
            $event_pledges = BankDepositForm::orderBy("bank_deposit_forms.created_at","desc");
            if(!empty($request->event_type))
            {
                $event_pledges = $event_pledges->where("event_type","=", $request->event_type);
            }
            if(!empty($request->sub_type))
            {
                $event_pledges = $event_pledges->where("sub_type","=", $request->sub_type);
            }

            $event_pledges->where("approved","=",1);

            $event_pledges->join("users","form_submitter_id","users.id");
               $event_pledges = $event_pledges->limit(30)->get();
        }
        $charities=Charity::when($request->has("title"),function($q)use($request){

            $searchValues = preg_split('/\s+/', $request->get("title"), -1, PREG_SPLIT_NO_EMPTY);

            if ($request->get("designation_code")) {
                $q->where('designation_code', $request->get("designation_code"));
            }
            if ($request->get("category_code")) {
                $q->where('category_code', $request->get("category_code"));
            }
            if ($request->get("province")) {
                $q->where('province', $request->get("province"));
            }

            foreach ($searchValues as $term) {
                $q->whereRaw("LOWER(charity_name) LIKE '%" . strtolower($term) . "%'");
            }
            return $q->orderby('charity_name','asc');

        })->where('charity_status','Registered')->paginate(10);
        $designation_list = Charity::DESIGNATION_LIST;
        $category_list = Charity::CATEGORY_LIST;
        $province_list = Charity::PROVINCE_LIST;
        $terms = explode(" ", $request->get("title") );

        $selected_charities = [];
        if (Session::has('charities')) {
            $selectedCharities = Session::get('charities');

            $_charities = Charity::whereIn('id', $selectedCharities['id'])
                ->get(['id', 'charity_name as text']);

            foreach ($_charities as $charity) {
                $charity['additional'] = $selectedCharities['additional'][array_search($charity['id'], $selectedCharities['id'])];
                if (!$charity['additional']) {
                    $charity['additional'] = '';
                }

                array_push($selected_charities, $charity);
            }
        } else {

            // reload the existig pledge
            $errors = session('errors');

            if (!$errors) {

                $campaignYear = CampaignYear::where('calendar_year', '<=', today()->year + 1 )->orderBy('calendar_year', 'desc')
                    ->first();
                $pledge = Pledge::where('user_id', Auth::id())
                    ->whereHas('campaign_year', function($q){
                        $q->where('calendar_year','=', today()->year + 1 );
                    })->first();

                if ( $campaignYear->isOpen() && $pledge && count($pledge->charities) > 0 )  {

                    $_ids = $pledge->charities->pluck(['charity_id'])->toArray();

                    $_charities = Charity::whereIn('id', $_ids )
                        ->get(['id', 'charity_name as text']);

                    foreach ($_charities as $charity) {
                        $pledge_charity = $pledge->charities->where('charity_id', $charity->id)->first();

                        $charity['additional'] = '';
                        if ($pledge_charity) {
                            $charity['additional'] = $pledge_charity->additional ?? '';
                        }

                        array_push($selected_charities, $charity);
                    }
                }
            }
        }
        $multiple = 'false';
        // load the view and pass
        $fund_support_pool_list = FSPool::current()->where('status', 'A')->with('region')->get()->sortBy(function($pool, $key) {
            return $pool->region->name;
        });
        $organizations = [];
        return view('admin-pledge.event.index',compact('fund_support_pool_list','organizations','multiple','selected_charities','terms','charities','province_list','category_list','designation_list','cities','current_user','campaign_year','departments','regions','business_units','regional_pool_id','pools','event_pledges','request'));


    }


    public function createEvent(){
        $pools = FSPool::where('start_date', '=', function ($query) {
            $query->selectRaw('max(start_date)')
                ->from('f_s_pools as A')
                ->whereColumn('A.region_id', 'f_s_pools.region_id')
                ->whereNull('A.deleted_at')
                ->where('A.start_date', '<=', today());
        })
            ->where('status', 'A')
            ->get();
        $regional_pool_id = $pools->count() > 0 ? $pools->first()->id : null;
        $business_units = BusinessUnit::where("status","=","A")->whereColumn("code","linked_bu_code")->groupBy("linked_bu_code")->orderBy("name")->get();
        $regions = Region::where("status","=","A")->get();
        $departments = Department::all();
        $campaign_year = CampaignYear::where('calendar_year', '<=', today()->year + 1 )->orderBy('calendar_year', 'desc')
            ->first();
        $current_user = User::where('id', Auth::id() )->first();
        $cities = City::all();
        $organizations = [];
        $selected_charities = [];
        if(empty($current_user)){
            redirect("login");
        }
        $fund_support_pool_list = FSPool::current()->where('status', 'A')->with('region')->get()->sortBy(function($pool, $key) {
            return $pool->region->name;
        });
        return view('admin-pledge.create.index', compact('fund_support_pool_list','selected_charities','organizations','pools','cities', 'regional_pool_id', 'business_units','regions','departments','campaign_year','current_user'));
    }

}
