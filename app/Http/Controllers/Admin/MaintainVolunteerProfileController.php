<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\User;
use App\Models\BusinessUnit;
use App\Models\CampaignYear;
use App\Models\Organization;
use App\Models\VolunteerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\MaintainVolunteerProfileRequest;
use Yajra\Datatables\Datatables;


class MaintainVolunteerProfileController extends Controller
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
     */
    public function index(Request $request)
    {
        //
        if($request->ajax()) {

            // store the filter 
            $filter = $request->except("draw", "columns", "order", "start", "length", "search", "_");
            session(['admin_volunteering_profile' => $filter]);

            $profiles = VolunteerProfile::with('organization', 'primary_job', 'business_unit',
                            // 'related_city', 'related_city.region',
                            'employee_city', 'employee_region'
                            // 'primary_job.city_by_office_city', 'primary_job.city_by_office_city.region'
                            )
                            // ->leftJoin('users', 'users.id', '=', 'volunteer_profiles.user_id')
                            ->leftJoin('employee_jobs', 'employee_jobs.emplid', '=', 'volunteer_profiles.emplid')
                            ->where( function($query) {
                                $query->where('employee_jobs.empl_rcd', '=', function($q) {
                                        $q->from('employee_jobs as J2') 
                                            ->whereColumn('J2.emplid', 'employee_jobs.emplid')
                                            ->selectRaw('min(J2.empl_rcd)');
                                    })
                                    ->orWhereNull('employee_jobs.empl_rcd');
                            })
                            ->when($request->tran_id, function($query) use($request) {
                                return $query->where('volunteer_profiles.id', 'like', $request->tran_id);
                            })
                            ->when( $request->organization_code, function($query) use($request) {
                                $query->where('volunteer_profiles.organization_code', $request->organization_code);
                            })
                            ->when( $request->pecsf_id, function($query) use($request) {
                                $query->where('volunteer_profiles.pecsf_id', 'like', '%'. $request->pecsf_id .'%');
                            })
                            ->when( $request->emplid, function($query) use($request) {
                                $query->where('employee_jobs.emplid', 'like', '%'. $request->emplid .'%');
                            })
                            ->when( $request->name, function($query) use($request) {
                                $query->where('volunteer_profiles.first_name', 'like', '%' . $request->name . '%')
                                      ->orWhere('volunteer_profiles.first_name', 'like', '%' . $request->name . '%')
                                      ->orWhere('employee_jobs.name', 'like', '%' . $request->name . '%');
                            })
                            ->when( $request->campaign_year && $request->campaign_year <> 'all', function($query) use($request) {
                                $query->where('volunteer_profiles.campaign_year', $request->campaign_year );
                            })
                            ->when( $request->business_unit_code, function($query) use($request) {
                                $query->where('volunteer_profiles.business_unit_code', $request->business_unit_code );
                            })
                            ->when( $request->no_of_years, function($query) use($request) {
                                $query->where('volunteer_profiles.no_of_years', $request->no_of_years );
                            })
                            ->when( $request->city, function($query) use($request) {
                                $query->where( function($q) use($request) {
                                    return $q->where('volunteer_profiles.employee_city_name', 'like', '%'. $request->city .'%');
                                });
                            })
                            ->when( $request->preferred_role, function($query) use($request) {
                                $query->where('volunteer_profiles.preferred_role', $request->preferred_role );
                            })
                            ->select('volunteer_profiles.*');

            $gov = Organization::where('code', 'GOV')->first();

            return Datatables::of($profiles)
                // ->addColumn('description', function($profile) {
                //     // $text =  $profile->type == 'P' ? $profile->fund_supported_pool->region->name : 
                //     //                    $profile->distinct_charities()->count() . ' charities'  ;
                //     // //   $title = implode(', ',  $profile->distinct_charities()->pluck('charity.charity_name')->toArray());
                //     $title =  $profile->type == 'P' ? $profile->fund_supported_pool->region->name : 
                //                     $profile->charity->charity_name  ;
                //     $text =  $profile->type == 'P' ? $profile->fund_supported_pool->region->name : 
                //                     ((strlen($profile->charity->charity_name) > 50) ? substr($profile->charity->charity_name, 0, 50) . '...' :
                //                     $profile->charity->charity_name);
                                                                          
                //     return "<span title='". $title ."'>" . $text . '</span>' ;
                // })
                ->addColumn('action', function ($profile) use($gov) {
                    $delete = ($profile->organization_id != $gov->id && $profile->ods_export_status == null && $profile->cancelled == null)  ? 
                                '<a class="btn btn-danger btn-sm ml-2 delete-profile" data-id="'.
                             $profile->id . '" data-code="'. $profile->fullname . '">Delete</a>' : '';
                    $edit = '<a class="btn btn-primary btn-sm ml-2" href="' . route('admin-volunteering.profile.edit',$profile->id) . '">Edit</a>';
                    return '<a class="btn btn-info btn-sm" href="' . route('admin-volunteering.profile.show',$profile->id) . '">Show</a>' .
                        $edit . 
                        // '<a class="btn btn-primary btn-sm ml-2" href="' . route('admin-volunteering.profile.edit',$profile->id) . '">Edit</a>'
                        $delete;

                })
                ->addColumn('preferred_role_name', function ($profile) use($gov) {
                    return VolunteerProfile::ROLE_LIST[$profile->preferred_role];
                })    
                ->editColumn('created_at', function ($user) {
                    return $user->created_at->format('Y-m-d H:i:s'); // human readable format
                })
                ->editColumn('updated_at', function ($user) {
                    return $user->updated_at->format('Y-m-d H:i:s'); // human readable format
                })                        
                ->rawColumns(['action'])
                ->make(true);
        }

        // restore filter if required 
        $filter = null;
        if (str_contains( url()->previous(), 'admin-volunteering/profile')) {
            $filter = session('admin_volunteering_profile');
        }
                
        // get all the record 
        //$campaign_years = CampaignYear::orderBy('calendar_year', 'desc')->paginate(10);
        $organizations = Organization::where('status', 'A')->orderBy('name')->get();
        $business_units = BusinessUnit::where("status","A")
                                ->whereIn('code', function($query) {
                                    $query->select('linked_bu_code')
                                    ->from("business_units");                               
                                })
                                ->orderBy("name")->get();
        $year_list = range(2024, today()->year);

        $role_list = VolunteerProfile::ROLE_LIST;        
        $cities = City::orderBy('city')->get();

        // load the view and pass 
        return view('admin-volunteering.profile.index', compact('organizations', 'year_list', 'business_units', 'cities', 'role_list', 'filter'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $profile = new VolunteerProfile();

        $profile->address_type = 'S';
        $profile->campaign_year = date('Y');

        $organizations = Organization::where('status', 'A')->orderBy('name')->get();

        $business_units = BusinessUnit::where("status","A")
                                ->whereIn('code', function($query) {
                                    $query->select('linked_bu_code')
                                    ->from("business_units");                               
                                })
                                ->orderBy("name")->get();
        $cities = City::orderBy('city')->get();
        $campaignYears = range(2024, today()->year);

        $role_list = VolunteerProfile::ROLE_LIST;
        $province_list = VolunteerProfile::PROVINCE_LIST;

        $is_new_profile = true;
        $is_renew = false;

        $campaign_year = today()->year;

        return view('admin-volunteering.profile.create-edit', compact('profile', 'organizations','campaignYears',
                    'business_units', 'cities', 'role_list', 'province_list', 'is_new_profile', 'is_renew', 'campaign_year'
                    ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MaintainVolunteerProfileRequest $request)
    {

        $user = User::where('emplid', $request->emplid )->first();

        $organization = Organization::where('id', $request->organization_id)->first();
        $pecsf_city = City::where('city', $request->pecsf_city)->first();
        $city = City::where('id', $request->city)->first();

        $profile = VolunteerProfile::Create([
            'campaign_year' => $request->campaign_year,
            'organization_code' => $organization->code,
            'emplid' => ($organization->code == 'GOV') ? $request->emplid : null,
            'pecsf_id' => (!($organization->code == 'GOV')) ? $request->pecsf_id : null,
            'first_name' => (!($organization->code == 'GOV')) ? $request->pecsf_first_name : null, 
            'last_name' => (!($organization->code == 'GOV')) ? $request->pecsf_last_name : null,
            'employee_city_name' => ($organization->code == 'GOV') ?  ($user->primary_job ? $user->primary_job->office_city : null) : $request->pecsf_city,
            'employee_bu_code' => ($organization->code == 'GOV') ? ($user->primary_job ? $user->primary_job->business_unit : null) : $organization->bu_code,
            'employee_region_code' => ($organization->code == 'GOV') ? ($user->primary_job ? $user->primary_job->city_by_office_city->region->code : null) : $pecsf_city->TGB_REG_DISTRICT,

            'business_unit_code' => $request->business_unit_code,
            'no_of_years' => $request->no_of_years,
            'preferred_role' => $request->preferred_role,

            'address_type' =>  $request->address_type,
            'address' => ($request->address_type =="G") ? null : $request->address,
            'city' => ($request->address_type =="G") ? null : $city->city,
            'province' => ($request->address_type =="G") ? null : $request->province,
            'postal_code' => ($request->address_type =="G") ? null : $request->postal_code,
            'opt_out_recongnition' => $request->opt_out_recongnition ? 'Y' : 'N',

            'created_by_id' => Auth::Id(),
            'updated_by_id' => Auth::Id(),
        ]);


        Session::flash('success', 'Volunteer profile with Transaction ID ' . $profile->id . ' have been created successfully' ); 
        return response()->noContent();
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\profle  $profile
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        //
        $profile = VolunteerProfile::where("id",$id)->first();

        if (!$profile) { abort(404);}
  
        return view('admin-volunteering.profile.show', compact('profile'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ModelsVolunteerProfile  $profile
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    // public function edit(Request $request, $id)
    {
        //
        $profile = VolunteerProfile::where('id', $id)->first();

        if (!($profile)) {
            return abort(404);      // 404 Not Found
        }

        // Prepare for display
        $organizations = Organization::where('status', 'A')->orderBy('name')->get();

        $business_units = BusinessUnit::where("status","A")
                            ->whereIn('code', function($query) {
                                $query->select('linked_bu_code')
                                ->from("business_units");                               
                            })
                            ->orderBy("name")->get();
        $cities = City::orderBy('city')->get();
        $campaignYears = range(2024, today()->year);

        $role_list = VolunteerProfile::ROLE_LIST;
        $province_list = VolunteerProfile::PROVINCE_LIST;

        $is_new_profile = false;

        $is_renew = $profile->isRenewProfile;

        return view('admin-volunteering.profile.create-edit', compact('profile', 'organizations', 'campaignYears',
                                'business_units', 'cities', 'role_list', 'province_list', 'is_new_profile', 'is_renew'
                    ));
            
    
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ModelsVolunteerProfile  $profile
     * @return \Illuminate\Http\Response
     */
    public function update(MaintainVolunteerProfileRequest $request, $id)
    {
        
        $profile = VolunteerProfile::where('id', $id)->first();

        if (!($profile)) {
            return abort(404);      // 404 Not Found
        }

        $org = Organization::where('id', $request->organization_id)->first();
        $pecsf_city = City::where('city', $request->pecsf_city)->first();
        $city = City::where('id', $request->city)->first();

        if ($org->code <> 'GOV') {
            $profile->first_name = $request->pecsf_first_name;
            $profile->last_name  = $request->pecsf_last_name;
            $profile->employee_city_name = $request->pecsf_city;
            $profile->employee_bu_code = $org->bu_code;
            $profile->employee_region_code = $pecsf_city->TGB_REG_DISTRICT;
        }

        $profile->business_unit_code = $request->business_unit_code;
        $profile->no_of_years = $profile->isRenewProfile ? 1 : $request->no_of_years;
        $profile->preferred_role = $request->preferred_role;

        $profile->address_type = $request->address_type;
        $profile->address = ($request->address_type =="G") ? null : $request->address;
        $profile->city = ($request->address_type =="G") ? null : $city->city;
        $profile->province = ($request->address_type =="G") ? null : $request->province;
        $profile->postal_code = ($request->address_type =="G") ? null : $request->postal_code;
        $profile->opt_out_recongnition = $request->opt_out_recongnition ? 'Y' : 'N';

        $profile->updated_by_id = Auth::id();
        $profile->save();

        Session::flash('success', 'The volunteer profile with Transaction ID ' . $profile->id . ' have been updated successfully' ); 
        return response()->noContent();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\VolunteerProfile  $profile
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //
        $profile = VolunteerProfile::where('id', $id)->first();

        if (!($profile)) {
            return abort(404);      // 404 Not Found
        }

        // $gov = Organization::where('code', 'GOV')->first();

        // if ($profile->organization_id == $gov->id) {
        //     return response()->json(['error' => "You are not allowed to delete this pledge " . $profile->id . " which was created for 'Gov' organization."], 422); 
        // }       
        
        // Delete the pledge
        $profile->updated_by_id = Auth::Id();
        $profile->save();

        $profile->delete();

        return response()->noContent();
        
    }


    public function getUsers(Request $request)
    {

        if($request->ajax()) {
            $term = trim($request->q);

            $users = User::where('users.organization_id', $request->org_id)
                ->when($term, function($query) use($term) { 
                    return $query->where( function($q) use($term) {
                        $q->whereRaw( "lower(users.name) like '%".addslashes($term)."%'")
                        //   ->orWhereRaw( "lower(users.email) like '%".$term."%'")
                            ->orWhere( "users.emplid", 'like', '%'.addslashes($term).'%');
                });
                })
                ->with('primary_job')
                ->with('primary_job.region') 
                ->with('primary_job.bus_unit') 
                ->limit(50)
                ->orderby('users.name','asc')
                ->selectRaw("users.*, (select count(*) from volunteer_profiles where organization_code = 'GOV' 
                                                and volunteer_profiles.emplid = users.emplid
                                                and volunteer_profiles.campaign_year < ". today()->year .") as volunteer_profile_count")
                ->get();

            $formatted_users = [];
            foreach ($users as $user) {
                $formatted_users[] = ['id' => $user->emplid, 
                        'text' => $user->name . ' ('. $user->emplid .')',
                        'email' =>  $user->primary_job->email, 
                        // 'user_id' => $user->id,
                        'emplid' => $user->emplid,  
                        'first_name' =>  $user->primary_job->first_name ?? '', 
                        'last_name' =>  $user->primary_job->last_name ?? '', 
                        'department' =>  $user->primary_job->dept_name . ' ('. $user->primary_job->deptid . ')',               
                        'business_unit' => $user->primary_job->bus_unit->name . ' ('.$user->primary_job->bus_unit->code . ')' ,                                        
                        'business_unit_code' => $user->primary_job->bus_unit->code,                                        
                        'region' => $user->primary_job->city_by_office_city->region->code ? $user->primary_job->city_by_office_city->region->name . ' (' . $user->primary_job->city_by_office_city->region->code . ')' : '',                    
                        'office_city' => $user->primary_job->office_city ?? '',
                        'organization' => $user->primary_job->organization_name ?? '',
                        'full_address'  => $user->primary_job->office_full_address ?? '',
                        'profile_count' => $user->volunteer_profile_count,
                ];
            }

            return response()->json($formatted_users);

        } else {
            return redirect('/');
        }

    }    

}
