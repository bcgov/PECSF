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
        return view('volunteering.index', compact('organizations', 'user', 'totalPledgedDataTillNow'));
    }

    public function training(){
        $organizations = Organization::where('status' ,"=", "A")->get();
        $user = User::find(Auth::id());
        $totalPledgedDataTillNow = Pledge::where('user_id', Auth::id())->sum('goal_amount');
        return view('volunteering.training', compact('organizations', 'user', 'totalPledgedDataTillNow'));
    }

    public function profile(){
        $organizations = Organization::where('status' ,"=", "A")->get();
        $user = User::find(Auth::id());
        $totalPledgedDataTillNow = Pledge::where('user_id', Auth::id())->sum('goal_amount');
        return view('volunteering.profile', compact('organizations', 'user', 'totalPledgedDataTillNow'));
    }

    public function store(VolunteerRegistrationRequest $request) {
        $validator = Validator::make(request()->all(), [
            'organization_code'         => 'required',
        ],[
            'organization_code' => 'The Organization Code is required.',
        ]);
        $validator->validate();

        $form = BankDepositForm::Create(
            [
                'organization_code' => $request->organization_code,
                'form_submitter_id' =>  $request->form_submitter_id,
                'event_type' =>  $request->event_type,
                'sub_type' => $request->sub_type,
                'deposit_date' => $request->deposit_date,
                'deposit_amount' => $request->deposit_amount,
                'description' => $request->description,
                'employment_city' => $request->employment_city,
                'region_id' => $request->region_id,
                'department_id' => $request->department_id,
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'address_city' => $request->address_city,
                'address_province' => $request->address_province,
                'address_postal_code' => $request->address_postal_code
            ]
        );

        foreach($request->organizations as $organization){
            $form->organizations()->create([
                'organization_name' => $organization->name,
                'vendor_id' => $organization->vendor_id,
                'donation_percent' => $organization->donation_percent,
                'specific_community_or_initiative' => $organization->specific_community_or_initiative
            ]);
        }

        $filepath = "/uploads";
        $form->attachments()->create([
            'local_path' => $filepath
        ]);

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
