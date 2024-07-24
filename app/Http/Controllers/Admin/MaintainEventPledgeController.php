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

            // store the filter
            $filter = $request->except("draw", "columns", "order", "start", "length", "search", "_");
            session(['admin_pledge_event_pledge_filter' => $filter]);

            $pledges = BankDepositForm::with('region', 'bu', 'campaign_year','form_submitted_by')
                            ->leftJoin('employee_jobs', 'employee_jobs.emplid', '=', 'bank_deposit_forms.bc_gov_id')
                            ->where( function($query) {
                                $query->where('employee_jobs.empl_rcd', '=', function($q) {
                                        $q->from('employee_jobs as J2')
                                            ->whereColumn('J2.emplid', 'employee_jobs.emplid')
                                            ->selectRaw('min(J2.empl_rcd)');
                                    })
                                    ->orWhereNull('employee_jobs.empl_rcd');
                            })
                ->whereIn("approved",[1,2])
                            ->when($request->tran_id, function($query) use($request) {
                                return $query->where('bank_deposit_forms.id', 'like', $request->tran_id);
                            })
                            ->when( $request->organization_code, function($query) use($request) {
                                $query->where('bank_deposit_forms.organization_code', $request->organization_code);
                            })
                            ->when( $request->pecsf_id, function($query) use($request) {
                                $query->where('bank_deposit_forms.pecsf_id', 'like', '%'. $request->pecsf_id .'%');
                            })
                            ->when( $request->emplid, function($query) use($request) {
                                $query->where( function($q) use($request) {
                                    return $q->where('bank_deposit_forms.bc_gov_id', 'like', '%'. $request->emplid .'%')
                                             ->orWhere('employee_jobs.name', 'like', '%' . $request->emplid . '%');
                                });
                            })
                            ->when( $request->description, function($query) use($request) {
                                $query->where('bank_deposit_forms.description', 'like', '%'. $request->description .'%');
                            })
                            ->when( $request->employment_city, function($query) use($request) {
                                $query->where( function($q) use($request) {
                                    return $q->where('employee_jobs.city', 'like', '%'. $request->employment_city .'%')
                                             ->orWhere('bank_deposit_forms.employment_city', 'like', '%'. $request->employment_city .'%');
                                });
                            })
                            ->when( $request->campaign_year_id, function($query) use($request) {
                                $query->where('bank_deposit_forms.campaign_year_id', $request->campaign_year_id);
                            })
                            ->when( $request->event_type, function($query) use($request) {
                                $query->where('bank_deposit_forms.event_type', $request->event_type);
                            })
                            ->when( $request->sub_type, function($query) use($request) {
                                $query->where('bank_deposit_forms.sub_type', $request->sub_type);
                            })
                            ->when( $request->approved, function($query) use($request) {
                                $query->where('bank_deposit_forms.approved', $request->approved);
                            })
                            ->select('bank_deposit_forms.*');

            return Datatables::of($pledges)
                ->addColumn('action', function ($pledge) {
                    return  '<a href="#" class="more-info-pledge fas fa-info-circle fa-2x bottom-right" data-id="' . $pledge->id . '"></a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

         // restore filter if required
         $filter = null;
         if (str_contains( url()->previous(), 'admin-pledge/create') ||
             str_contains( url()->previous(), 'admin-pledge/submission-queue') ||
             str_contains( url()->previous(), 'admin-pledge/maintain-event') ||
             str_contains( url()->previous(), 'admin-pledge/status')) {
             $filter = session('admin_pledge_event_pledge_filter');
         }

        $organizations = Organization::where('status', 'A')->orderBy('name')->get();
        $campaign_years = CampaignYear::orderBy('calendar_year', 'desc')->get();
        $cities = City::orderBy('city')->get();

        $event_types = ['Cash One-Time Donation', 'Cheque One-Time Donation', 'Fundraiser', 'Gaming'];
        $sub_types = ['Auction', 'Entertainment', 'Food', 'Other', 'Sports', '50/50 Draw'];
        $status_list =  ["1" => "Approved", "2" => "Locked"];

        $default_campaign_year = CampaignYear::defaultCampaignYear();

        return view('admin-pledge.event.index',compact(
                         'filter', 'organizations', 'campaign_years', 'cities', 'event_types', 'sub_types', 'default_campaign_year', 'status_list'));

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

            $pledge = BankDepositForm::where('id', $id)->first();

            $pool_charities= null;
            if ($pledge->regional_pool_id) {
                $pool_charities = FSPool::where('id', $pledge->regional_pool_id)->first()->charities;
            }
            
            return view('admin-pledge.event.partials.detail-modal',
                                compact('pledge', 'pool_charities') )->render();

        }

    }


    public function create(){
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
        // $business_units = BusinessUnit::where("status","=","A")->whereColumn("code","linked_bu_code")->groupBy("linked_bu_code")->orderBy("name")->get();
        $business_units = BusinessUnit::where("status","=","A")
            // ->whereColumn("code","linked_bu_code")
            ->selectRaw("business_units.id, business_units.code, business_units.name, (select code from organizations where organizations.bu_code = business_units.code limit 1 ) as org_code ")
            // ->groupBy("linked_bu_code")
            ->orderBy("name")->get();
        $regions = Region::where("status","=","A")->get();
        $departments = Department::all();
        $campaign_year = CampaignYear::where('start_date', '<=', today() )->orderBy('calendar_year', 'desc')->first();
        $current_user = User::where('id', Auth::id() )->first();
        $cities = City::all()->sortBy("city");
        $organizations = [];
        $selected_charities = [];
        if(empty($current_user)){
            redirect("login");
        }
        $fund_support_pool_list = FSPool::current()->where('status', 'A')->with('region')->get()->sortBy(function($pool, $key) {
            return $pool->region->name;
        });
        return view('admin-pledge.event.create', compact('fund_support_pool_list','selected_charities','organizations','pools','cities', 'regional_pool_id', 'business_units','regions','departments','campaign_year','current_user'));
    }

}
