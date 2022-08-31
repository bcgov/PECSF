<?php

namespace App\Http\Controllers;

use App\Http\Requests\VolunteerRegistrationRequest;
use App\Models\BankDepositForm;
use App\Models\BusinessUnit;
use App\Models\CampaignYear;
use App\Models\FSPool;
use App\Models\Organization;
use App\Models\Region;
use App\Models\Department;
use App\Models\User;
use App\Models\Pledge;
use App\Models\Volunteer;
use App\Models\City;
use Illuminate\Support\Facades\Auth;


use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use Illuminate\Http\Request;
use App\MicrosoftGraph\TokenCache;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Yajra\Datatables\Datatables;

class VolunteeringController extends Controller
{
    public function index() {
        $organizations = Organization::where('status' ,"=", "A")->get();
        $user = User::find(Auth::id());
        $totalPledgedDataTillNow = Pledge::where('user_id', Auth::id())->sum('goal_amount');
        $cities = City::all();
        $is_registered = !empty(Volunteer::where("user_id","=",Auth::id())->get()) ? true : false;
        return view('volunteering.index', compact('organizations', 'user', 'totalPledgedDataTillNow','cities','is_registered'));
    }

    public function training(){
        $organizations = Organization::where('status' ,"=", "A")->get();
        $user = User::find(Auth::id());
        $totalPledgedDataTillNow = Pledge::where('user_id', Auth::id())->sum('goal_amount');
        $cities = City::all();
        return view('volunteering.training', compact('organizations', 'user', 'totalPledgedDataTillNow','cities'));
    }

    public function profile(){
        $organizations = Organization::where('status' ,"=", "A")->get();
        $user = User::find(Auth::id());
        $totalPledgedDataTillNow = Pledge::where('user_id', Auth::id())->sum('goal_amount');
        $cities = City::all();
        $is_registered = !empty(Volunteer::where("user_id","=",Auth::id())->get()) ? Volunteer::where("user_id","=",Auth::id())->join("organizations","volunteers.organization_id","organizations.id")->first() : false;
        return view('volunteering.profile', compact('organizations', 'user', 'totalPledgedDataTillNow','cities','is_registered'));
    }

    public function edit(){
        $organizations = Organization::where('status' ,"=", "A")->get();
        $user = User::find(Auth::id());
        $cities = City::all();
        $is_registered = !empty(Volunteer::where("user_id","=",Auth::id())->get()) ? Volunteer::where("user_id","=",Auth::id())->join("organizations","volunteers.organization_id","organizations.id")->first() : false;
        return view('volunteering.edit', compact('organizations', 'user', 'cities','is_registered'));
    }

    public function store(VolunteerRegistrationRequest $request) {
        $form = Volunteer::Create(
            [
                'user_id' => Auth::id(),
                'address_type' =>  $request->address_type,
                'new_address' =>  $request->new_address.", ".$request->city.", ".$request->province.", ".$request->postal_code,
                'no_of_years' => $request->no_of_years,
                'preferred_role' => $request->preferred_role,
                'organization_id' => $request->organization_id,
            ]
        );
        return redirect()->route('volunteering.index');
    }

    public function bank_deposit_form(Request $request) {
        if($request->ajax())
        {
            $validator = Validator::make(request()->all(), [
                'organization_code'         => 'required',
            ],[
                'organization_code' => 'The Organization Code is required.',
            ]);
            $validator->validate();
        }
        else{
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

            if(empty($current_user)){
                redirect("login");
            }

            return view('volunteering.forms',compact('campaign_year','current_user','pools','regional_pool_id','business_units','regions','departments'));
        }
    }

}
