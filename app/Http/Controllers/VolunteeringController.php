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
use App\Models\EmployeeJob;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\SupplyOrderform;
use App\Models\Setting;


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
        $settings = Setting::first();
        $is_registered = !empty(Volunteer::where("user_id","=",Auth::id())->get()) ? Volunteer::where("user_id","=",Auth::id())->join("organizations","volunteers.organization_id","organizations.id")->where("volunteers.updated_at","<",Carbon::parse($settings->volunteer_start_date))->first() : false;
        if(!empty($is_registered)){
            if(is_array($is_registered->new_address))
            {
                $is_registered->province = $is_registered->new_address[2];
                $is_registered->city = $is_registered->new_address[1];
            }
            else{

                $is_registered->province = "";
                $is_registered->city = "";
            }
        }

        $global_address = EmployeeJob::where("emplid","=",$user->emplid)->first();
        $business_units = BusinessUnit::where("status","=","A")->orderBy("name")->get();
        return view('volunteering.index', compact('business_units','global_address','organizations', 'user', 'totalPledgedDataTillNow','cities','is_registered'));
    }

    public function training(){
        $organizations = Organization::where('status' ,"=", "A")->get();
        $user = User::find(Auth::id());
        $totalPledgedDataTillNow = Pledge::where('user_id', Auth::id())->sum('goal_amount');
        $cities = City::all();
        $global_address = EmployeeJob::where("emplid","=",$user->emplid)->first();

        if($global_address){
            $global_address =  $global_address->office_address1." ".$global_address->office_address2." ,".$global_address->office_city." ,".$global_address->stateprovince." ,".$global_address->country." ,".$global_address->postal;
            $province = "";
            $setcity = "";
        }
        return view('volunteering.training', compact('global_address','organizations', 'user', 'totalPledgedDataTillNow','cities'));
    }

    public function profile(){
        $organizations = Organization::where('status' ,"=", "A")->get();
        $user = User::find(Auth::id());
        $totalPledgedDataTillNow = Pledge::where('user_id', Auth::id())->sum('goal_amount');
        $cities = City::all();
        $is_registered = !empty(Volunteer::where("user_id","=",Auth::id())->get()) ? Volunteer::where("user_id","=",Auth::id())->join("organizations","volunteers.organization_id","organizations.id")->first() : false;
        $global_address = EmployeeJob::where("emplid","=",$user->emplid)->first();

        if($global_address){
            $global_address =  $global_address->office_address1." ".$global_address->office_address2." ,".$global_address->office_city." ,".$global_address->stateprovince." ,".$global_address->country." ,".$global_address->postal;
        }
        else{
            $global_address = "";
        }
        return view('volunteering.profile', compact('global_address','organizations', 'user', 'totalPledgedDataTillNow','cities','is_registered'));
    }

    public function edit(){
        $organizations = Organization::where('status' ,"=", "A")->get();
        $user = User::find(Auth::id());
        $cities = City::all();
        $is_registered = !empty(Volunteer::where("user_id","=",Auth::id())->get()) ? Volunteer::where("user_id","=",Auth::id())->join("organizations","volunteers.organization_id","organizations.id")->first() : false;
        $global_address = EmployeeJob::where("emplid","=",$user->emplid)->first();

        if($global_address){
            $province = "";
            $setcity = "";
            $global_address =  $global_address->office_address1." ".$global_address->office_address2." ,".$global_address->office_city." ,".$global_address->stateprovince." ,".$global_address->country." ,".$global_address->postal;
        }
        else{
            $global_address = "";
        }
        if($is_registered)
        {
            $province = substr(explode(",",$is_registered->new_address)[2],1,strlen(explode(",",$is_registered->new_address)[2]));;
            $setcity = substr(explode(",",$is_registered->new_address)[1],1,strlen(explode(",",$is_registered->new_address)[1]));
        }

        return view('volunteering.edit', compact('global_address','organizations', 'user', 'cities','is_registered','province','setcity'));
    }

    public function store(VolunteerRegistrationRequest $request) {
        $form = Volunteer::Create(
            [
                'user_id' => Auth::id(),
                'address_type' =>  $request->address_type,
                'new_address' =>  ($request->address_type=="Global") ? $request->global_address : $request->new_address.", ".$request->city.", ".$request->province.", ".$request->postal_code,
                'no_of_years' => $request->no_of_years,
                'preferred_role' => $request->preferred_role,
                'organization_id' => $request->organization_id,
            ]
        );

        return redirect()->route('profile');
    }

    public function update(Request $request){
        $validator = Validator::make(request()->all(), [
            'organization_id' => 'required',
            'no_of_years' => 'required',
            'preferred_role' => 'required',
            'address_type' => 'required'
        ]);

        $validator->after(function ($validator) use($request) {
            if ($request->address_type == "New") {
                if (empty($request->city)) {
                    $validator->errors()->add('city', 'A City is required.');
                }
                if (empty($request->province)) {
                    $validator->errors()->add('province', 'A Province is required.');
                }
                if (empty($request->postal_code)) {
                    $validator->errors()->add('postal_code', 'A Postal Code is required.');
                }
                if (empty($request->new_address)) {
                    $validator->errors()->add('new_address', 'A Street Address is required.');
                }
            }
        });


        $validator->validate();

        Volunteer::updateOrCreate(
            ["user_id" => Auth::id()],
        [
            'new_address'         => ($request->address_type=="Global") ? $request->global_address : $request->new_address.", ".$request->city.", ".$request->province.", ".$request->postal_code,
            'address_type'         => $request->address_type,
            'organization_id' => $request->organization_id,
            'no_of_years' => $request->no_of_years,
            'preferred_role' =>  $request->preferred_role,
            'updated_at' => Carbon::now()
        ]
        );


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

    public function supply_order_form(Request $request)
    {

        if($request->wantsJson()){
            $validator = Validator::make(request()->all(), [
                'calendars'         => 'required|integer',
                'posters'         => 'required|integer',
                'stickers'         => 'required|integer',
                'first_name'         => 'required',
                'last_name'         => 'required',
                'business_unit_id'         => 'required',
                'include_name'         => 'required',
                'address_type' => 'required',
                'date_required' => 'required|after:today',
             ],[
                'business_unit_id' => 'The Organization Code is required.',
                'deposit_date.before' => 'The deposit date must be the current date or a date before the current date.'
            ]);

            $validator->after(function ($validator) use($request) {
                $expression = '/^([a-zA-Z]\d[a-zA-Z])\ {0,1}(\d[a-zA-Z]\d)$/';
                if($request->address_type == "po")
                {
                    if(empty($request->po)){
                        $validator->errors()->add('po','Enter a Po Box');
                    }
                    if(empty($request->po_city)) {
                        $validator->errors()->add('po_city', 'Enter a City');
                    }
                    if(empty($request->po_postal_code)) {
                        $validator->errors()->add('po_postal_code', 'Enter a Postal Code');
                    }
                    if(empty($request->po_province)) {
                        $validator->errors()->add('po_province','Enter a Province');
                    }
                    if(!preg_match($expression, $request->po_postal_code)){
                        $validator->errors()->add('po_postal_code', 'Invalid Postal Code | Try L1L 1L1');
                    }
                }
                else{
                    if(empty($request->unit_suite_floor)) {
                        $validator->errors()->add('unit_suite_floor', 'Unit Suite Floor is Required');
                    }
                        if(empty($request->physical_address)) {
                            $validator->errors()->add('physical_address', 'Physical Address is required');
                        }
                    if(empty($request->city)) {
                        $validator->errors()->add('city', 'City is required');
                    }

                    if(empty($request->province)) {
                        $validator->errors()->add('province', 'Province is required');
                    }
                    if(empty($request->postal_code)){
                        $validator->errors()->add('postal_code', 'Postal Code is required');
                    }
                    if(!preg_match($expression, $request->postal_code)){
                        $validator->errors()->add('postal_code', 'Invalid Postal Code | Try L1L 1L1');
                    }
                }

            });

            $validator->validate();
            $form = SupplyOrderform::Create(
                [
                    'calendar'         => $request->calendars,
                    'posters'         => $request->posters,
                    'stickers'         => $request->stickers,
                    'first_name'         => $request->first_name,
                    'last_name'         => $request->last_name,
                    'business_unit_id'         => $request->business_unit_id,
                    'include_name'         => $request->include_name,
                    'unit_suite_floor'         => $request->address_type == "po" ? "" : $request->unit_suite_floor,
                    'physical_address'         =>  $request->address_type == "po" ? "" : $request->physical_address,
                    'city'         => $request->address_type == "po" ? $request->po_city : $request->city,
                    'province'         => $request->address_type == "po" ? $request->po_province : $request->province,
                    'postal_code' => $request->address_type == "po" ? $request->po_postal_code : $request->postal_code,
                    'po_box' => $request->po ? $request->po : "",
                    'comments' => empty($request->comments) ? "" : $request->comments,
                    'address_type' => $request->address_type,
                ]
            );
            return json_encode(array(route('supply_order_form')));
            }

        $r = true;
        $business_units = BusinessUnit::where("status","=","A")->orderBy("name")->get();


        return view('volunteering.supply',compact('business_units'));
    }

}
