<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\User;
use App\Models\EmployeeJob;
use App\Models\BusinessUnit;
use App\Models\CampaignYear;
use Illuminate\Http\Request;
use App\Models\VolunteerProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\VolunteerProfileRequest;

class VolunteerProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $user = User::where('id', Auth::id())->first();
        $profile = VolunteerProfile::where("campaign_year", today()->year)
                                   ->where("organization_code", 'GOV')
                                   ->where("emplid", $user->emplid)
                                   ->first();

        if ($profile) {
            return redirect()->route('volunteering.profile.show',$profile->id);
        } else {
            if (CampaignYear::isVolunteerRegistrationOpenNow()) {
                return redirect()->route('volunteering.profile.create');
            } else {
                // No Volunteer History and was closed for registration  
                abort(403);
            }
        }
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        if (!CampaignYear::isVolunteerRegistrationOpenNow()) {
            abort(403);
        }

        $user = User::where('id', Auth::id())->first();
        $profile = VolunteerProfile::where("campaign_year", today()->year)
                                   ->where("organization_code", 'GOV')
                                   ->where("emplid", $user->emplid)
                                   ->first();

        $business_units = BusinessUnit::where("status","A")->orderBy("name")->get();
        $cities = City::orderBy('city')->select('city','id')->get();

        $role_list = VolunteerProfile::ROLE_LIST;
        $province_list = VolunteerProfile::PROVINCE_LIST;

        $registered_in_past = VolunteerProfile::where("campaign_year", '<', today()->year)
                                ->where("organization_code", 'GOV')
                                ->where("emplid", $user->emplid)
                                ->orderBy("campaign_year", 'desc')
                                ->first();
        $is_renew = $registered_in_past ? true : false;

        if (!$profile && $registered_in_past) {
            $profile = $registered_in_past->replicate();
            $profile->id = null;
            $profile->campaign_year = today()->year;
            $profile->no_of_years = 1;
        }

        return view('volunteer-profile.wizard', compact('profile','user', 'cities', 'business_units', 'role_list', 'province_list', 'is_renew'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VolunteerProfileRequest $request)
    {

        if ($request->ajax()) {

            // Generate Summary Page
            if ($request->step == 2)  {

                $user = User::where('id', Auth::id())->first();
                $job = $user->primary_job;

                $business_unit = BusinessUnit::where('code', $request->business_unit_code)->first();
                $role_name = VolunteerProfile::ROLE_LIST[$request->preferred_role];

                if ($request->address_type == 'G') { 
                    $address = $job->office_full_address;
                } else {
                    $city = City::where('id', $request->city)->first();            
                    $province = $request->province ? VolunteerProfile::PROVINCE_LIST[$request->province] : '';
                    $address = $request->address .', '. $city->city .', '. $province .', '. $request->postal_code;
                }
                
                $is_renew = $request->is_renew == 'Y' ? true : false;

                return view('volunteer-profile.partials.summary', compact('request', 'user', 'business_unit', 
                        'address', 'role_name', 'is_renew'))->render();
            }
            return response()->noContent();
        }


        /* Final submission -- form submission (non-ajax call) */
        // $profile = VolunteerProfile::where("user_id",Auth::id())->first();
        $user = User::where('id', Auth::id())->first();
        $city = City::where('id', $request->city)->first();
        // $job = $user->primary_job;

        $registered_in_past = VolunteerProfile::where("campaign_year", '<', today()->year)
                                ->where("organization_code", 'GOV')
                                ->where("emplid", $user->emplid)
                                ->orderBy("campaign_year", 'desc')
                                ->first();
        $is_renew = $registered_in_past ? true : false;
        

        $profile = VolunteerProfile::Create(
            [
                'campaign_year' => today()->year,
                // 'user_id' => Auth::id(),
                'organization_code' => 'GOV',
                'emplid' => $user->emplid,

                'employee_city_name'   => $user->primary_job ? $user->primary_job->office_city : null,
                'employee_bu_code'     =>  $user->primary_job ? $user->primary_job->business_unit : null,
                'employee_region_code' => $user->primary_job ? $user->primary_job->city_by_office_city->region->code : null,
    
                'business_unit_code' => $request->business_unit_code,
                'no_of_years' => $is_renew ? 1 : $request->no_of_years,
                'preferred_role' => $request->preferred_role,

                'address_type' => $request->address_type,
                'address' => ($request->address_type =="G") ? null : $request->address,
                'city' => ($request->address_type =="G") ? null : $city->city,
                'province' => ($request->address_type =="G") ? null : $request->province,
                'postal_code' => ($request->address_type =="G") ? null : $request->postal_code,
                'opt_out_recongnition' => $request->opt_out_recongnition ? 'Y' : 'N',

                'created_by_id'  => Auth::id(),
                'updated_by_id' => Auth::id(),
            ]
        );

        $message_text = 'Your volunteer profile have been created successfully';

        Session::flash('profile_id', $profile->id );
        return redirect()->route('volunteering.profile.thank-you')->with('success', $message_text);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        //  
        $profile = VolunteerProfile::where("id",$id)->first();
        $user = User::where('id', Auth::id())->first();

        if (!$profile) { abort(404);}
        if ($profile->emplid <> $user->emplid ) { abort(403); }

        $allow_edit = (CampaignYear::isVolunteerRegistrationOpenNow() && (today()->year == $profile->campaign_year)) ? true : false;

        return view('volunteer-profile.show', compact('profile','user', 'allow_edit'));
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        //
        if (!CampaignYear::isVolunteerRegistrationOpenNow()) {
            abort(403);
        }

        $campaign_year = today()->year;

        $user = User::where('id', Auth::id())->first();
        $profile = VolunteerProfile::where("campaign_year", today()->year)
                                ->where("organization_code", 'GOV')
                                ->where("emplid", $user->emplid)
                                ->first();

        if (!$profile) { abort(404);}
        if ($profile->emplid <> $user->emplid ) { abort(403); }

        // $profile = VolunteerProfile::where("user_id",Auth::id())->first();
        // $user = User::where('id', Auth::id())->first();

        $business_units = BusinessUnit::where("status","A")->orderBy("name")->get();
        $cities = City::orderBy('city')->select('city','id')->get();

        $role_list = VolunteerProfile::ROLE_LIST;
        $province_list = VolunteerProfile::PROVINCE_LIST;

        $is_renew = $profile->is_renew_profile;

        return view('volunteer-profile.wizard', compact('profile','user', 'business_units', 'cities', 'role_list', 'province_list', 'is_renew'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VolunteerProfileRequest $request, string $id)
    {

        $user = User::where('id', Auth::id())->first();
        $profile = VolunteerProfile::where("id",$id)->first();

        if (!$profile) { abort(404);}
        if ($profile->emplid <> $user->emplid ) { abort(403); }

        $city = City::where('id', $request->city)->first();

        // Updating 
        $profile->employee_city_name   = $user->primary_job ? $user->primary_job->office_city : null;
        $profile->employee_bu_code     = $user->primary_job ? $user->primary_job->business_unit : null;
        $profile->employee_region_code = $user->primary_job ? $user->primary_job->city_by_office_city->region->code : null;


        $profile->address_type = $request->address_type;
        $profile->business_unit_code = $request->business_unit_code;
        $profile->no_of_years = $profile->is_renew_profile ? 1 : $request->no_of_years;
        $profile->preferred_role = $request->preferred_role;
        $profile->address = ($request->address_type =="G") ? null : $request->address;
        $profile->city = ($request->address_type =="G") ? null : $city->city;
        $profile->province = ($request->address_type =="G") ? null : $request->province;
        $profile->postal_code = ($request->address_type =="G") ? null : $request->postal_code;
        $profile->opt_out_recongnition  = $request->opt_out_recongnition ? 'Y' : 'N';

        $profile->updated_by_id = Auth::id();
        $profile->save();

        $message_text = 'Your volunteer profile have been updated successfully';

        Session::flash('profile_id', $profile->id );
        return redirect()->route('volunteering.profile.thank-you')->with('success', $message_text);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function thankYou()
    {

        $profile_id = session()->get('profile_id');

        if ($profile_id) {
            $profile = VolunteerProfile::where("id", $profile_id)->first();
            return view('volunteer-profile.thankyou', compact('profile') );
        } else {
            return abort(403);
        }

    }

}
