<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\VolunteerRegistrationRequest;
use App\Models\BusinessUnit;
use App\Models\CampaignYear;
use App\Models\FSPool;
use App\Models\Organization;
use App\Models\Region;
use App\Models\Department;
use App\Models\User;
use App\Models\Pledge;
use App\Models\Volunteer;
use Illuminate\Support\Facades\Auth;


use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use Illuminate\Http\Request;
use App\MicrosoftGraph\TokenCache;


use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Yajra\Datatables\Datatables;

class BankDepositFormController extends Controller
{
    public function index() {
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
        return view('volunteering.forms',compact('campaign_year','current_user','pools','regional_pool_id','business_units','regions','departments'));
    }

    public function store(VolunteerRegistrationRequest $request) {
        $validator = Validator::make(request()->all(), [
            'organization_code'         => 'required',
            'start_date'        => ['required'],
            'charities.*'       => ['required'],
            'status.*'          => ['required', Rule::in(['A', 'I'])],
            'names.*'           => 'required|max:50',
            'descriptions.*'    => 'required',
            'percentages.*'     => 'required|numeric|min:0|max:100|between:0,100.00|regex:/^\d+(\.\d{1,2})?$/',
            'contact_names.*'   => 'required',
            'contact_emails.*'  => 'required|email',
            'images.*'          => 'required|mimes:jpg,jpeg,png,bmp|max:2048',
        ],[
            'charities.required' => 'The charity field is required.',
            'status.in' => 'The selected status is invalid.',
            'names.required' => 'The name field is required.',
            'descriptions.required' => 'The description field is required.',
            'percentages.required' => 'The Percentage field is required.',
            'percentages.max' => 'The Percentage must not be greater than 100.',
            'percentages.min' => 'The Percentage must be at least 0.',
            'percentages.numeric' => 'The Percentage must be a number.',
            'percentages.between' => 'The percentages.0 must be between 0 and 100.',
            'percentages.regex' => 'The percentages format is invalid.',
            'contact_names.required' => 'The Contact Name field is required.',
            'contact_titles.required' => 'The Contact Title field is required.',
            'contact_emails.required' => 'The Contact Email field is required.',
            'contact_emails.email' => 'The Email field is invalid.',
            'notes.required' => 'The Notes field is required.',
            'images.required' => 'Please upload an image',
            'images.max' => 'Sorry! Maximum allowed size for an image is 2MB',
        ]);
        $validator->validate();
    }



}
