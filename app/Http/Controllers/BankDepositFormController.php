<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\FSPool;
use App\Models\Region;
use App\Models\BusinessUnit;
use App\Models\Department;
use App\Models\CampaignYear;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BankDepositFormController extends Controller
{
    public function index() {$pools = FSPool::where('start_date', '=', function ($query) {
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
        return view('volunteering.forms',compact('campaign_year','current_user','pools','regional_pool_id','business_units','regions','departments'));
    }

    public function store(Request $request) {
        $validator = Validator::make(request()->all(), [
            'organization_name.*' => 'required',
            'organization_code'         => 'required',
            'form_submitter'         => 'required',
            'campaign_year'         => 'required',
            'event_type'         => 'required',
            'sub_type'         => 'required',
            'deposit_date'         => 'required',
            'deposit_amount'         => 'required',
            'employment_city'         => 'required',
            'region'         => 'required',
            'business_unit'         => 'required',
            'department'         => 'required',
            'address_1'         => 'required',
            'address_2'         => 'required',
            'city'         => 'required',
            'province'         => 'required',
            'postal_code'         => 'required',
            'charity_selection' => 'required',
            'description' => 'required',
            'attachments.*' => 'required',
            'organization_name.*' => 'required',
            'vendor_id.*' => 'required',
            'donation_percent.*' => 'required',
            'specific_community_or_initiative_errors.*' => 'required'
        ],[
            'organization_code' => 'The Organization Code is required.',
            'organization_name.required' => "The Organization Name is Required."
        ]);
        $validator->after(function ($validator) use($request) {
            if(empty(request('organization_name'))){
                $validator->errors()->add('organization_name.0','The Organization name is required.');
            };
            if(empty(request('vendor_id'))){
                $validator->errors()->add('vendor_id.0','The Vendor Id is required.');
            };
            if(empty(request('donation_percent'))){
                $validator->errors()->add('donation_percent.0','The Donation Percent is required.');
            };
            if(empty(request('specific_community_or_initiative_errors'))){
                $validator->errors()->add('specific_community_or_initiative.0','This Field is required.');
            };
            if(empty(request('atttachment'))){
                $validator->errors()->add('attachment.0','Atleast one attachment is required.');
            };
        });
        $validator->validate();
    }



}
