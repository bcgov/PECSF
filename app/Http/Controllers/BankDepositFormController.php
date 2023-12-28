<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\User;
use App\Models\FSPool;
use App\Models\Pledge;
use App\Models\Region;
use App\Models\Charity;
use App\Models\Department;
use App\Models\EmployeeJob;
use App\Models\BusinessUnit;
use App\Models\CampaignYear;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Models\ProcessHistory;

use App\Models\BankDepositForm;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\BankDepositFormAttachments;
use App\Models\BankDepositFormOrganizations;

class BankDepositFormController extends Controller
{


    /**
     * create a new instance of the class
     *
     * @return void
     */
    function __construct()
    {
        $this->doc_folder = "app/uploads/bank_deposit_form_attachments";
    }

    public function index(Request $request)
    {
        $pools = FSPool::select("f_s_pools.*")->where('start_date', '=', function ($query) {
            $query->selectRaw('max(start_date)')
                ->from('f_s_pools as A')
                ->whereColumn('A.region_id', 'f_s_pools.region_id')
                ->where('A.start_date', '<=', today());
        })
            ->join("regions","regions.id","=","f_s_pools.region_id")
            ->where('f_s_pools.status', 'A')
            ->orderBy("name","asc")
            ->get();
        $regional_pool_id = $pools->count() > 0 ? $pools->first()->id : null;
        // $business_units = BusinessUnit::where("status","=","A")->whereColumn("code","linked_bu_code")->groupBy("linked_bu_code")->orderBy("name")->get();
        $business_units = BusinessUnit::where("status","=","A")
                        ->whereColumn("code","linked_bu_code")
                        ->selectRaw("business_units.id, business_units.code, business_units.name, (select code from organizations where organizations.bu_code = business_units.code limit 1 ) as org_code ")
                        ->groupBy("linked_bu_code")->orderBy("name")->get();
        $regions = Region::where("status","=","A")->orderby("name", "asc")->get();
        $departments = Department::all();
        $campaign_year = CampaignYear::where('start_date', '<=', today() )->orderBy('calendar_year', 'desc')->first();
        $current_user = User::where('id', Auth::id() )->first();
        $organizations = [];// Charity::where("charity_status","=","Registered")->paginate(7);

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
                $q->whereRaw("LOWER(charity_name) LIKE '%" . strtolower(addslashes($term)) . "%'");
            }
            return $q->orderby('charity_name','asc');

        })->where('charity_status','Registered')->paginate(10);
        $cities = City::all()->sortBy("city");
        $designation_list = Charity::DESIGNATION_LIST;
        $category_list = Charity::CATEGORY_LIST;
        $province_list = Charity::PROVINCE_LIST;
        $terms = explode(" ", $request->get("title") );
        $multiple = 'false';
        $selected_charities = [];

        $skip_info_modal = (str_ends_with( strtolower(url()->previous()), 'bank_deposit_form'));

        $fund_support_pool_list = FSPool::current()->where('f_s_pools.status', 'A')->join("regions","regions.id","=","f_s_pools.region_id")->with('region')->orderBy("name",'asc')->get();

        return view('volunteering.forms',compact('fund_support_pool_list','organizations','selected_charities','multiple','charities','terms','province_list','category_list','designation_list','cities','campaign_year','current_user','pools','regional_pool_id','business_units','regions','departments'
                        ,'skip_info_modal'
                    ));
    }

    // public function ignoreRemovedFiles($request){
    //     if(!empty(request()->ignoreFiles))
    //     {
    //         $fields = $request['attachments'];
    //         $request['attachments'] = [];
    //         foreach( $fields as $index => $file )
    //         {
    //             if(!in_array($file->getClientOriginalName(),explode(",",request()->ignoreFiles)))
    //             {
    //                 $request['attachments'][] = $file;
    //             }
    //         }
    //     }
    //     return $request;
    // }

    public function store(Request $request) {
// dd($request->all());
        $bu_election_bc = BusinessUnit::where('code', 'BC015')->first();

        $validator = Validator::make($request->all(), [
            'organization_code'         => 'required',
            'form_submitter'         => 'required',
            'campaign_year'         => 'required',
            'event_type'         => 'required',
            //'sub_type'         => 'required',
            //'sub_type' => ['sometimes', 'boolean', 'default:false'], // Make it not required and set default to false
            'bc_gov_id'  => [ Rule::when( $request->organization_code == 'GOV', ['required_unless:event_type,Fundraiser,Gaming']),
                              Rule::when( $request->organization_code == 'GOV' 
                                            && (!($request->business_unit == ($bu_election_bc ? $bu_election_bc->id : null) && $request->bc_gov_id == '000000')),
                                    ['exists:users,emplid']),
                              Rule::when( $request->organization_code == 'GOV' && substr($request->event_type,0,1) == 'C', "numeric|digits:6"),
                            ], 
            'employee_name'  => [ Rule::when( $request->organization_code == 'GOV', ['required_unless:event_type,Fundraiser,Gaming']) ],                             
            'deposit_date'         => 'required|before:tomorrow',
            'deposit_amount'         => 'required|numeric|between:0.01,999999.99|regex:/^\d+(\.\d{1,2})?$/',

            'employment_city'         => 'required',
            // 'postal_code'         => ($request->event_type == "Fundraiser" || $request->event_type == "Gaming") ? " ":'postal_code:CA',
            'region'         => 'required',
            'business_unit'         => 'required',

            'address_1'    =>  'required_unless:event_type,Fundraiser,Gaming',
            'city'         =>  'required_unless:event_type,Fundraiser,Gaming',
            'province'     =>  'required_unless:event_type,Fundraiser,Gaming',
            'postal_code'  =>  'required_unless:event_type,Fundraiser,Gaming',

            'charity_selection' => ['required', Rule::in(['fsp', 'dc']) ],
            'regional_pool_id'       => ['required_if:charity_selection,fsp', Rule::when( $request->charity_selection == 'fsp', ['exists:f_s_pools,id']) ],

            'description' => 'required',
            // 'attachments.*' => 'required|mimes:pdf,xls,xlsx,csv,png,jpg,jpeg',
            'attachments' => "required|max:3",
        ],[

            'organization_code' => 'The Organization Code is required.',
            'attachments.required' => 'At least one attachment is required.',
            'deposit_date.before' => 'The deposit date must be the current date or a date before the current date.',

            'deposit_amount.regex' => 'Only two decimal places allowed.',
            'attachments.max' => 'The total number of attachments must not be greater than 5.',


         ]);
        $validator->after(function ($validator) use($request) {

            // if(strpos($request->deposit_amount,".") !== FALSE){
            //     $decimals = substr($request->deposit_amount,strpos($request->deposit_amount,".")+1,3);
            //     if(strlen($decimals) == 3)
            //     {
            //         $validator->errors()->add('deposit_amount','Only two decimal places allowed.');
            //     }
            // }
            // if($request->organization_code == "false")
            // {
            //     $validator->errors()->add('organization_code','An organization code is required.');
            // }

            if($request->event_type != "Gaming" && $request->event_type != "Fundraiser"){
            // if($request->organization_code != "GOV" && $request->organization_code != "RET")
            // {
            //         if(empty($request->pecsf_id))
            //         {
            //             $validator->errors()->add('pecsf_id','A PECSF ID is required.');
            //         }
            // }

                if($request->event_type != "Fundraiser" && $request->event_type!= "Gaming")
                {
                    if(empty($request->employee_name))
                    {
                        $validator->errors()->add('employee_name','The employee name field is required.');
                    }
                }


                if($request->organization_code == "GOV"){
                    if(empty($request->bc_gov_id))
                    {
                        $validator->errors()->add('bc_gov_id','An Employee ID is required.');
                    }
                    else if(!is_numeric($request->bc_gov_id))
                    {
                        $validator->errors()->add('bc_gov_id','The Employee ID must be a number.');
                    }
                    else if(strlen($request->bc_gov_id) != 6){
                        $validator->errors()->add('bc_gov_id','The Employee ID must be 6 digits.');
                    }

                    if($request->event_type == "Cash One-Time Donation" || $request->event_type == "Cheque One-Time Donation")
                    {
                        if(strlen($request->employee_name) < 1){
                            $validator->errors()->add('employee_name','Employee name required.');
                        }
                        else if(strpos($request->employee_name,",") == FALSE){
                            $validator->errors()->add('employee_name','Employee name must be your first and last name seperated by a comma. "Jack,Johnson".');
                        }
                        else{
                            $names = explode(",",$request->employee_name);

                            if(strlen($names[0]) < 1){
                                $validator->errors()->add('employee_name','First name before the comma must be more than 1 character.');
                            }
                            else if(strlen($names[1]) < 1){
                                $validator->errors()->add('employee_name','Last name after the comma must be more than 1 character.');
                            }
                            else if($names[1][0] == " "){
                                $validator->errors()->add('employee_name','Enter first and last name without any spaces. "Jack,Johnson".');
                            }
                        }
                    }

                }
            }

            if($request->charity_selection == "dc")
            {
            //     if(empty($request->regional_pool_id)){
            //         $validator->errors()->add('regional_pool_id','Select a Regional Pool.');
            //     }
            // }
            // else{
                $total = 0;
                $a = [];
                $reverse = count(is_array(request("donation_percent"))? request("donation_percent"):[]) - $request->org_count;
                if($request->org_count < 1){
                    $validator->errors()->add('charity','You need to Select a Charity.');
                }
                else{
                    for($i=(count(request("donation_percent")) -1);$i >= (count(request("donation_percent")) - $request->org_count);$i--){

                        $reverse++;
                        if(empty(request("id")[$i]))
                        {
                            $validator->errors()->add('organization_name.'.$i,'The Organization name is required.');
                        }
                        if(empty(request('vendor_id')[$i])){
                            $validator->errors()->add('vendor_id.'.$i,'The Vendor Id is required.');
                        }
                        if(empty(request('donation_percent')[$i])){
                            $validator->errors()->add('donation_percent.'.(((count(request("donation_percent")))+1) - $reverse),'The Donation Percent is required.');
                        }
                        else if(!is_numeric(request('donation_percent')[$i])){
                            $validator->errors()->add('donation_percent.'.(((count(request("donation_percent")))) - $reverse),'The Donation Percent must be a number.');
                        }
                        else{

                            if(!empty(request("donation_percent")[$i]))
                            {
                                $a[] = (((count(request("donation_percent")))+1) - $reverse);
                                $total = request('donation_percent')[$i] + $total;
                            }
                        }
                    }
                    if($total != 100) {
                        for($i=(count($a)-1);$i >= 0;$i--){
                            $validator->errors()->add('donation_percent.' . $a[$i], 'The Donation Percent Does not equal 100%.');
                        }
                    }
                }
            }

            $existing = [];
            if($request->organization_code == "GOV") {
                $existing = BankDepositForm::where("organization_code", "=", "GOV")
                    ->where("event_type", "=", "Cash One-time Donation")
                    ->where("form_submitter_id", "=", $request->form_submitter_id)
                    ->get();
                if (empty(!$existing) && !empty($request->pecsf_id)) {
                    if (strtolower($request->pecsf_id[0]) != "s" || !is_numeric(substr($request->pecsf_id, 1))) {
                       // $validator->errors()->add('pecsf_id', 'Previous Cash One-time donation for this form submitter detected; The PECSF ID must be a number prepended with an S.');
                    }
                }
            }

            if($request->pecsf_id){
                $existing_pecsf_id = BankDepositForm::where("campaign_year_id","=",$request->campaign_year)
                    ->whereIn("pecsf_id",[$request->pecsf_id,"S".$request->pecsf_id,"s".$request->pecsf_id])
                    ->where("approved","=",1)
                    ->get();
                if(count($existing_pecsf_id) > 0)
                {
                    $validator->errors()->add('pecsf_id','The PECSF ID has already been used for another Donation.');
                }
            }


        });



        $validator->validate();
        if(!isset($request->sub_type)){
            $sub_type = 'false';
        } else {
            $sub_type = $request->sub_type;
        }
        $regional_pool_id = ($request->charity_selection == "fsp") ? $request->regional_pool_id : null;
   
        // Get deptid and name from employee primary job if bc_gov_id entered
        $deptid = null;
        $dept_name = null;
        if ($request->bc_gov_id) {
            $job = EmployeeJob::where('emplid', $request->bc_gov_id)
                        ->where( function($query) {
                            $query->where('employee_jobs.empl_rcd', '=', function($q) {
                                $q->from('employee_jobs as J2') 
                                    ->whereColumn('J2.emplid', 'employee_jobs.emplid')
                                    ->selectRaw('min(J2.empl_rcd)');
                            });
                        })->first();
            if ($job) {
                $deptid = $job->deptid;
                $dept_name = $job->dept_name;
            }
        }

        $organization_code = $request->organization_code;
        $event_type = $request->event_type;
        $pecsf_id = $this->assign_pecsf_id($organization_code, $event_type );
        // if($organization_code == "RET"){ //R****  PECSF ID
        //     $existing = BankDepositForm::where("pecsf_id","LIKE","R".substr(date("Y"),2,2)."%")
        //         ->orderBy("pecsf_id","desc")
        //         ->get();

        //     if(count($existing) > 0){
        //         $pecsf_id = "R".substr(date("Y"),2,2).str_pad((intval(count($existing)) +1),3,'0',STR_PAD_LEFT);
        //     }
        //     else{
        //         $pecsf_id = "R".substr(date("Y"),2,2)."001";
        //     }
        // } else {
        //     if($event_type == "Gaming"){ //G****  PECSF ID
        //         $existing = BankDepositForm::where("event_type","=","Gaming")
        //         ->where("pecsf_id","LIKE","G".substr(date("Y"),2,2)."%")
        //         ->orderBy("pecsf_id","desc")
        //         ->get();

        //         if(count($existing) > 0){
        //             $pecsf_id = "G".substr(date("Y"),2,2).str_pad((intval(count($existing)) +1),3,'0',STR_PAD_LEFT);
        //         }
        //         else{
        //             $pecsf_id = "G".substr(date("Y"),2,2)."001";
        //         }

        //     } elseif ($event_type == "Fundraiser") { //F****  PECSF ID
        //         $existing = BankDepositForm::where("event_type","=","Fundraiser")
        //         ->where("pecsf_id","LIKE","F".substr(date("Y"),2,2)."%")
        //         ->orderBy("pecsf_id","desc")
        //         ->get();
               
        //         if(count($existing) > 0){
        //             $pecsf_id = "F".substr(date("Y"),2,2).str_pad((intval(count($existing)) +1),3,'0',STR_PAD_LEFT);
        //         }
        //         else{
        //             $pecsf_id = "F".substr(date("Y"),2,2)."001";
        //         }
        //     } else {
        //         if ($organization_code == "GOV") {  //S****  PECSF ID
        //             $existing = BankDepositForm::whereIn("event_type", ["Cash One-Time Donation", "Cheque One-Time Donation"])
        //                         ->where("pecsf_id","LIKE","S".substr(date("Y"),2,2)."%")
        //                         ->orderBy("pecsf_id", "desc")
        //                         ->get();

        //             if(count($existing) > 0){
        //                 $pecsf_id = "S".substr(date("Y"),2,2).str_pad((intval(count($existing)) +1),3,'0',STR_PAD_LEFT);
        //             }
        //             else{
        //                 $pecsf_id = "S".substr(date("Y"),2,2)."001";
        //             }
        //         } else {
        //             //do nothing, we don't support one-time cheque/cash donation for non-GOV organziations.
        //         }
        //     } 

        // }


        $form = BankDepositForm::Create(
            [
                'business_unit' => $request->business_unit,
                'organization_code' => $request->organization_code,
                'form_submitter_id' =>  $request->form_submitter,
                'campaign_year_id' =>  $request->campaign_year,
                'event_type' =>  $request->event_type,
                'sub_type' => $sub_type,
                'deposit_date' => $request->deposit_date,
                'deposit_amount' => $request->deposit_amount,
                'description' => $request->description,
                'employment_city' => $request->employment_city,
                'region_id' => $request->region,
                'regional_pool_id' => $regional_pool_id,
                'address_line_1' => $request->address_1,
                'address_line_2' => $request->address_2,
                'address_city' => $request->city,
                'address_province' => $request->province,
                'address_postal_code' => $request->postal_code,
                'bc_gov_id' => $request->bc_gov_id,
                'pecsf_id' => $pecsf_id,

                'deptid' => $deptid,
                'dept_name'  => $dept_name,
                
                'employee_name' => $request->employee_name,
                'created_by_id' => Auth::id(),
                'updated_by_id' => Auth::id(),
            ]
        );
        

        //var_dump($request->all());

        if($request->charity_selection == "dc"){
            // $orgName = count($request->organization_name) -1;
            // $orgCount = $orgName;
            // $count = 0;
            foreach($request->organization_name as $key => $org){

                BankDepositFormOrganizations::create([
                    'bank_deposit_form_id' => $form->id,
                    'organization_name' => $org,
                    'vendor_id' =>  $request->vendor_id[$key],
                    'donation_percent' => $request->donation_percent[$key],
                    'specific_community_or_initiative' => $request->additional[$key] ?? '', 
                ]);

                // if($key <= ($orgCount - $request->org_count)){
                //     continue;
                // }

                // $toSave = [
                //     'organization_name' => $request->organization_name[$key],
                //     'vendor_id' => $request->vendor_id[(count($request->vendor_id) - $request->org_count) + $count],
                //     'donation_percent' => $request->donation_percent[(count($request->donation_percent) - $request->org_count) + $count],
                //     'bank_deposit_form_id' => $form->id
                // ];

                // if(isset($request->additional) && !empty($request->additional)){
                //     $toSave['specific_community_or_initiative'] =  $request->additional[(count($request->additional) - $request->org_count) + $count];
                // }

                // BankDepositFormOrganizations::create();
                // $count++;
                // $orgName--;

            }
        }

        
        foreach ($request->input('attachments', []) as $filename) {
            
            $doc = file_get_contents( storage_path( 'app/tmp/'. $filename ) );
            $base64 = base64_encode($doc);
            $mime = pathinfo( storage_path( 'app/tmp/'. $filename ), PATHINFO_EXTENSION);

            // File::move( storage_path( 'app/tmp/'. $filename ), storage_path( $this->doc_folder ."/". $filename));
            
            BankDepositFormAttachments::create([
                'bank_deposit_form_id' => $form->id,
                'filename' => $filename,
                'original_filename' => substr($filename, strpos($filename,'_')+1),
                'mime' => $mime,
                'local_path' => storage_path( $this->doc_folder )."/".$filename,
                'file' => $base64,
            ]);

        }
        


        // $upload_images = $request->file('attachments') ? $request->file('attachments') : [];

        // foreach($upload_images as $key => $file){

        //     if(is_array($request->ignoreFiles)){
        //         if(in_array($file->getClientOriginalName(),$request->ignoreFiles))
        //         {
        //             continue;
        //         }
        //     }


        //         $filename=date('YmdHis').'_'. str_replace(' ', '_', $file->getClientOriginalName() );

        //         $filePath = $file->storeAs(  "/uploads/bank_deposit_form_attachments" , $filename);
        //         BankDepositFormAttachments::create([
        //         'local_path' => storage_path( $this->doc_folder )."/".$filename,
        //         'bank_deposit_form_id' => $form->id
        //     ]);
        // }

        if(strpos($_SERVER['HTTP_REFERER'],'admin-pledge') !== FALSE)
        {
            echo  json_encode(array(route('admin-pledge.maintain-event.index')));

        }
        else{
            echo  json_encode(array(route('bank_deposit_form')));
        }

    }

    
    public function update(Request $request) {

        $bu_election_bc = BusinessUnit::where('code', 'BC015')->first();

        $no_of_attachments = BankDepositFormAttachments::where('bank_deposit_form_id', $request->form_id)->count();
        $max_attachments = 3 - $no_of_attachments;

        $validator = Validator::make(request()->all(), [
            'organization_code'         => 'required',
            'form_submitter'         => 'required',
            'campaign_year'         => 'required',
            'event_type'         => 'required',
            //'sub_type'         => 'required',
            //'sub_type' => ['sometimes', 'boolean', 'default:false'], // Make it not required and set default to false
            'bc_gov_id'  => [ Rule::when( $request->organization_code == 'GOV', ['required_unless:event_type,Fundraiser,Gaming']),
                              Rule::when( $request->organization_code == 'GOV' 
                                            && (!($request->business_unit == ($bu_election_bc ? $bu_election_bc->id : null) && $request->bc_gov_id == '000000')),
                                    ['exists:users,emplid']),
                              Rule::when( $request->organization_code == 'GOV' && substr($request->event_type,0,1) == 'C', "numeric|digits:6"),
                            ], 
            'employee_name'  => [ Rule::when( $request->organization_code == 'GOV', ['required_unless:event_type,Fundraiser,Gaming']) ], 
            'deposit_date'         => 'required|before:tomorrow',
            'deposit_amount'         => 'required|numeric|between:0.01,999999.99|regex:/^\d+(\.\d{1,2})?$/',
            'employment_city'         => 'required',
            'region'                 => 'required',
            'business_unit'         => 'required',

            // 'postal_code'         => ($request->event_type == "Fundraiser" || $request->event_type == "Gaming") ? " ":'postal_code:CA',

            'address_1'    =>  'required_unless:event_type,Fundraiser,Gaming',
            'city'         =>  'required_unless:event_type,Fundraiser,Gaming',
            'province'     =>  'required_unless:event_type,Fundraiser,Gaming',
            'postal_code'  =>  'required_unless:event_type,Fundraiser,Gaming',

            'charity_selection' => ['required', Rule::in(['fsp', 'dc']) ],
            'regional_pool_id'       => ['required_if:charity_selection,fsp', Rule::when( $request->charity_selection == 'fsp', ['exists:f_s_pools,id']) ],

            'description' => 'required',
            'attachments' => "max:{$max_attachments}",
        ],[
            'organization_code' => 'The Organization Code is required.',

            'deposit_amount.regex' => 'Only two decimal places allowed.',
            'attachments.max' => 'The total number of attachments must not be greater than 5.',

        ]);
        $validator->after(function ($validator) use($request) {
            // if($request->event_type != "Gaming" && $request->event_type != "Fundraiser"){
            //     if($request->organization_code != "GOV")
            //     {
            //         if(empty($request->pecsf_id))
            //         {
            //             $validator->errors()->add('pecsf_id','A PECSF ID is required.');
            //         }
            //     }

            // }
            // if($request->event_type == "Cash One-Time Donation" || $request->event_type == "Cheque One-Time Donation")
            // {
            //     if(empty($request->address_1))
            //     {
            //         $validator->errors()->add('address_1','An Address is required.');
            //     }

            //     if(empty($request->city))
            //     {
            //         $validator->errors()->add('city','An City is required.');
            //     }

            //     if(empty($request->province))
            //     {
            //         $validator->errors()->add('province','An Province is required.');
            //     }

            //     if(empty($request->postal_code))
            //     {
            //         $validator->errors()->add('postal_code','An Postal Code is required.');
            //     }
            // }

            if($request->charity_selection == "dc")
            {
            //     if(empty($request->regional_pool_id)){
            //         $validator->errors()->add('regional_pool_id','Select a Regional Pool.');
            //     }
            // }
            // else{
                $total = 0;

                if($request->org_count < 1){
                    $validator->errors()->add('charity','You need to Select a Charity.');
                }
                else{
                    $a = [];
                    for($i=(count(request("donation_percent")) -1);$i >= (count(request("donation_percent")) - $request->org_count);$i--){
                        if(empty(request("organization_name")[$i]))
                        {
                            $validator->errors()->add('organization_name.'.$i+1,'The Organization name is required.');
                        }
                        if(empty(request('vendor_id')[$i])){

                            $validator->errors()->add('vendor_id.'.$i,'The Vendor Id is required.');
                        };
                        if(empty(request('donation_percent')[$i])){

                            $validator->errors()->add('donation_percent.'.$i+1,'The Donation Percent is required.');
                        }
                        else if(!is_numeric(request('donation_percent')[$i])){

                            $validator->errors()->add('donation_percent.'.$i+1,'The Donation Percent must be a number.');
                        }
                        else{
                            $a[] = $i;
                            if(!empty(request("donation_percent")[$i]))
                            {

                                $total = request('donation_percent')[$i] + $total;
                            }
                        }
                    }


                    if($total != 100) {
                        for($i=(count($a) - 1);$i > -1;$i--){
                                $validator->errors()->add('donation_percent.' . $i, 'The Donation Percent Does not equal 100%.');
                        }
                    }
                }
            }

            // $existing = [];
            // if($request->organization_code == "GOV"){
            //     $existing = BankDepositForm::where("organization_code","=","GOV")
            //         ->whereIn("event_type",["Cash One-time Donation","Cheque One-time Donation"])
            //         ->where("form_submitter_id","=",$request->form_submitter_id)
            //         ->get();

            // if(!empty($existing) && ($request->event_type != "Gaming" && $request->event_type != "Fundraiser"))
            //     {
            //         if(!empty($request->pecsf_id)){

            //             $existingPecsfId = BankDepositForm::where("organization_code","=","GOV")
            //                 ->whereIn("event_type",["Cash One-time Donation","Cheque One-time Donation"])
            //                 ->where("pecsf_id","=",$request->pecsf_id)
            //                 ->orWhere("pecsf_id","=",substr($request->pecsf_id,1))
            //                 ->get();

            //             if((strtolower($request->pecsf_id[0]) != "s" || !is_numeric(substr($request->pecsf_id,1))) && !empty($exsitingPecsfId))
            //             {
            //                 $validator->errors()->add('pecsf_id','Previous Cash One-time donation for this form submitter detected; The PECSF ID must be a number prepended with an S.');
            //             }
            //         }
            //      else{
            //          if($request->organization_code != "GOV") {
            //             $validator->errors()->add('pecsf_id','The PECSF ID is required.');
            //          }
                     
            //      }
            //     }
            //     else if(($request->event_type != "Gaming" && $request->event_type != "Fundraiser")){
            //             if(empty($request->bc_gov_id))
            //             {
            //                 $validator->errors()->add('bc_gov_id','An Employee ID is required.');
            //             }
            //             else if(!is_numeric($request->bc_gov_id))
            //             {
            //                 $validator->errors()->add('bc_gov_id','The Employee ID must be a number.');
            //             }
            //     }

            // }
            /*
            if(($request->event_type != "Gaming" && $request->event_type != "Fundraiser")){
                $existing_pecsf_id = BankDepositForm::where("campaign_year_id","=",$request->campaign_year)
                    ->whereIn("pecsf_id",[$request->pecsf_id,"S".$request->pecsf_id,"s".$request->pecsf_id])
                    ->where("approved","=",1)
                    ->get();
                if(count($existing_pecsf_id) > 0)
                {
                    $validator->errors()->add('pecsf_id','The PECSF ID has already been used for another Donation.');
                }
            }
            */
            // if($request->pecsf_id){
            //     $existing_pecsf_id = BankDepositForm::where("campaign_year_id","=",$request->campaign_year)
            //         ->whereIn("pecsf_id",[$request->pecsf_id,"S".$request->pecsf_id,"s".$request->pecsf_id])
            //         ->where("approved","=",1)
            //         ->get();
            //     if(count($existing_pecsf_id) > 0)
            //     {
            //         $validator->errors()->add('pecsf_id','The PECSF ID has already been used for another Donation.');
            //     }
            // }


            // if(!empty(request("attachments"))){
            //     $fileFound = false;
            //     foreach(array_reverse(request('attachments')) as $key => $attachment){
            //         if(in_array($attachment->getClientOriginalName(),explode(",",$request->ignoreFiles)) || empty($attachment) || $attachment == "undefined"){
            //         }
            //         else{
            //             $fileFound = true;
            //             break;
            //         }
            //     }
            //     if(!$fileFound){
            //         $validator->errors()->add('attachment','At least one attachment is required.');
            //     }
            // }
            // else{
            //     //$validator->errors()->add('attachment','At least one attachment is required.');
            // }



        });
        $regional_pool_id = ($request->charity_selection == "fsp") ? $request->regional_pool_id : null;
        $validator->validate();
        if(!isset($request->sub_type)){
            $sub_type = 'false';
        } else {
            $sub_type = $request->sub_type;
        }

        $organization_code = $request->organization_code;
        $event_type = $request->event_type;
        $pecsf_id = $request->pecsf_id;
        $prefix = substr($pecsf_id,0,1);
        if ($organization_code == "RET" && $prefix != 'R') { //R****  PECSF ID
            $pecsf_id = $this->assign_pecsf_id($organization_code, $event_type );        
        } else {

            if (($event_type == "Fundraiser" &&  $prefix != 'F') ||
                ($event_type == "Gaming" &&  $prefix != 'G') ||
                ( substr($event_type,0,1) == 'C' &&  $prefix != 'S') ) {
                $pecsf_id = $this->assign_pecsf_id($organization_code, $event_type );        
            } else {
                //do nothing, we don't support one-time cheque/cash donation for non-GOV organziations.
            }
        }

        $form = BankDepositForm::find($request->form_id)->update(
            [
                'organization_code' => $request->organization_code,
                'event_type' =>  $request->event_type,
                'sub_type' => $sub_type,
                'deposit_date' => $request->deposit_date,
                'deposit_amount' => $request->deposit_amount,
                'description' => $request->description,
                'employment_city' => $request->employment_city,
                'region_id' => $request->region,
                'regional_pool_id' => $regional_pool_id,
                'address_line_1' => $request->address_1,
                'address_line_2' => $request->address_2,
                'address_city' => $request->city,
                'address_province' => $request->province,
                'address_postal_code' => $request->postal_code,
                'bc_gov_id' => $request->bc_gov_id,
                'pecsf_id' => $pecsf_id,
                'business_unit' => $request->business_unit,

                'employee_name' => $request->employee_name,
                'updated_by_id' => Auth::id(),
            ]
        );
        if($request->charity_selection == "fsp" && $regional_pool_id != ''){ 
            BankDepositFormOrganizations::where("bank_deposit_form_id",$request->form_id)->delete();
        }

        if($request->charity_selection == "dc"){            
            $orgName = count($request->organization_name) -1;
            $orgCount = $orgName;
            $arr_lenth = $request->arr_lenth;
            $form_organization_name = $request->organization_name;
            $form_vendor_id = $request->vendor_id;
            $form_donation_percent = $request->donation_percent;
            $form_additional = $request->additional;
            if ($arr_lenth > 0) {
                $form_organization_name = array_slice($form_organization_name, -$arr_lenth);
                $form_vendor_id = array_slice($form_vendor_id, -$arr_lenth);
                $form_donation_percent = array_slice($form_donation_percent, -$arr_lenth);
                $form_additional = array_slice($form_additional, -$arr_lenth);
            }

            BankDepositFormOrganizations::where("bank_deposit_form_id",$request->form_id)->delete();
            //foreach($request->organization_name as $org){
            if($arr_lenth == 0) {
                for($i=(count(request("donation_percent")) -1);$i >= (count(request("donation_percent")) - $request->org_count);$i--){
                    BankDepositFormOrganizations::create([
                        'organization_name' => $request->organization_name[$i],
                        'vendor_id' => $request->vendor_id[$i],
                        'donation_percent' => $request->donation_percent[$i],
                        'specific_community_or_initiative' =>  (isset($request->additional[$i])?$request->additional[$i]:" "),
                        'bank_deposit_form_id' => $request->form_id
                    ]);
                }
            } else {
                //for handling the validation error re-submit form
                for($i=0; $i < $arr_lenth; $i++){ 
                    $specificCommunityOrInitiative = $form_additional[$i] != '' ? $form_additional[$i] : ' ';
                    BankDepositFormOrganizations::create([
                        'organization_name' => $form_organization_name[$i],
                        'vendor_id' => $form_vendor_id[$i],
                        'donation_percent' => $form_donation_percent[$i],
                        'specific_community_or_initiative' =>  $specificCommunityOrInitiative,
                        'bank_deposit_form_id' => $request->form_id
                    ]);
                }

            }
        }

        // attachments
        foreach ($request->input('attachments', []) as $filename) {
            
            $doc = file_get_contents( storage_path( 'app/tmp/'. $filename ) );
            $base64 = base64_encode($doc);
            $mime = pathinfo( storage_path( 'app/tmp/'. $filename ), PATHINFO_EXTENSION);

            // File::move( storage_path( 'app/tmp/'. $filename ), storage_path( $this->doc_folder ."/". $filename));
            
            BankDepositFormAttachments::create([
                'bank_deposit_form_id' => $request->form_id,
                'filename' => $filename,
                'original_filename' => substr($filename, strpos($filename,'_')+1),
                'mime' => $mime,
                'local_path' => storage_path( $this->doc_folder )."/".$filename,
                'file' => $base64,
            ]);

        }


        echo json_encode(["/admin-pledge/submission-queue"]);
    }


    public function organization_code(Request $request)
    {
        if(empty($request->term))
        {
            $organizations = Organization::where("status","=","A")->orderBy('name')->get();
        }
        else{
            $organizations = Organization::where( function($q) use($request) {
                                    $q->where("code", "like", "%" . $request->term . "%")
                                      ->orWhere("name", "like", "%" . $request->term . "%");
                                })
                                ->where("status","=","A")
                                ->orderBy('name')
                                ->get();
        }

        $response = ['results' => []];
        $response['results'][] = ["id" => "false", "text" => "Choose an organization"];
        foreach ($organizations as $organization) {
            if(!empty($organization->code)){
                $response['results'][] = ["id" =>  $organization->code,"text" => $organization->name,  'bu_code' => $organization->bu_code];
            }
        }
        echo json_encode($response);
    }

    public function organization_name(Request $request)
    {
        if(empty($request->term))
        {
            $organizations = Charity::where("charity_status","=","Registered")->limit(30)->get();
        }
        else{
            $organizations = Charity::where("charity_name", "LIKE", $request->term . "%")->where("charity_status","=","Registered")->get();
        }
        $response = ['results' => []];
        $response['results'][] = ["id" => "false", "text" => "Choose an Organization Name"];
        foreach ($organizations as $organization) {
            if(!empty($organization->charity_name)){
                $response['results'][] = ["id" => $organization->id,"text" => $organization->charity_name." (".$organization->registration_number.")"];
            }
        }
        echo json_encode($response);
    }

    public function donors(Request $request)
    {
        if($request->isAjax())
        {

        }

    }

    public function organizations(Request $request)
    {
        $organizations = Charity::selectRaw("charities.id as id, charity_name, effective_date_of_status, category_code, registration_number, charity_status, address, city, province, country, postal_code, sanction")->where("charity_status","=","Registered");

        if($request->province != "")
        {
            $organizations->where("province","=",$request->province);
        }

        if($request->category != "")
        {
            $organizations->where("category_code","=",$request->category);
        }

        if($request->keyword != "")
        {
            $organizations->where( function ($query) use($request) {
                    $query->where("charity_name","LIKE","%".$request->keyword."%")
                          ->orWhere('registration_number',"LIKE","%".$request->keyword."%");
            });
        }
        if (is_numeric($request->pool_filter)){
            $pool = FSPool::current()->where('region_id', $request->get('pool_filter') )->first();
            $organizations->join('f_s_pool_charities',"charities.id","f_s_pool_charities.charity_id");
            $organizations->where("f_s_pool_charities.status","=","A");
            $organizations->where("f_s_pool_charities.f_s_pool_id","=",$pool->id);
            $organizations->selectRaw("image, f_s_pool_charities.description as pool_description, f_s_pool_charities.name as program_name");
            $organizations->whereIn('charities.id', $pool->charities->pluck('charity_id') );
            $organizations->groupBy("f_s_pool_charities.charity_id");
        }

        $organizations = $organizations->where("charity_status","=","Registered")->orderBy("charity_name","asc")->paginate(7)->onEachSide(1);
        $total = $organizations->total();
        $selected_vendors = explode(",",$request->selected_vendors);

        return view('volunteering.partials.organizations', compact('selected_vendors','organizations','total'))->render();
    }

    function bc_gov_id(Request $request){
        $record = EmployeeJob::where("emplid","=",$request->id)->join("business_units","business_units.code","employee_jobs.business_unit")->join("cities","cities.city","employee_jobs.office_city")->selectRaw("business_units.id as business_unit_id, employee_jobs.office_city, employee_jobs.region_id, cities.TGB_REG_DISTRICT as tgb_reg_district, employee_jobs.first_name, employee_jobs.last_name")->first();
        if(!empty($record)){
            return response()->json($record, 200);
        }
        else{
            return response()->json([
                'message' => 'Employee Id not found'], 404);
        }
    }

    function business_unit(Request $request){
        $record = Organization::where("organizations.code","=",$request->id)->join("business_units","business_units.code","organizations.bu_code")->selectRaw("business_units.id as business_unit_id, organizations.bu_code")->first();
        if(!empty($record->bu_code)){
            return response()->json($record, 200);
        }
        else{
            return response()->json([
                'message' => 'Organization Code not found'], 404);
        }
    }

    public function download($id) {
        // $headers = [
        //     'Content-Description' => 'File Transfer',
        //     'Content-Type' => 'application/csv',
        //     "Content-Transfer-Encoding: UTF-8",
        // ];
        // // return Storage::download($path);
        // return Storage::disk('uploads')->download("/bank_deposit_form_attachments/".$fileName, $fileName, $headers);
        $document = BankDepositFormAttachments::find($id);
        $file_contents = base64_decode($document->file);
        
        return response($file_contents)
                ->header('Cache-Control', 'no-cache private')
                ->header('Content-Description', 'File Transfer')
                ->header('Content-Type', $document->mime)
                ->header('Content-length', strlen($file_contents))
                ->header('Content-Disposition', 'attachment; filename=' . $document->original_filename)
                ->header('Content-Transfer-Encoding', 'binary');

    }


        public function delete(Request $request, $form_id, $fileName) {
            // The $form_id and $fileName are route parameters
        
            $path = "/bank_deposit_form_attachments/" . $fileName;
            
            // Check if the file exists before attempting to delete it
            if (Storage::disk('uploads')->exists($path)) {
                // Delete the file
                error_log(storage_path($this->doc_folder) . "/" . $fileName);
                Storage::disk('uploads')->delete($path);
                
                // Use $form_id to delete records from the database
                BankDepositFormAttachments::where([
                    'local_path' => storage_path($this->doc_folder) . "/" . $fileName,
                    'bank_deposit_form_id' => $form_id
                ])->delete();
        
                $msg = "Attachment '$fileName' has been deleted.";
            } else {
                $msg = "Attachment '$fileName' does not exist or has already been deleted.";
            }
            
            return response()->json(['msg' => $msg]);
        }

    public function assign_pecsf_id( $organization_code, $event_type) 
    {

        $pecsf_id = null;
        $prefix = '';
        $yy = substr(date("Y"),2,2);
        $seq = '001';

        switch ($organization_code) {
            case "RET":
                $prefix = "R";
                break;
            default:
                if (substr($event_type,0,1) == 'C') {     // ["Cash One-Time Donation", "Cheque One-Time Donation"]
                    $prefix = "S";
                } else {
                    $prefix = substr($event_type,0,1); 
                }
        }

        $existing = BankDepositForm::where("pecsf_id","LIKE", $prefix . $yy ."%")
                            ->orderBy("pecsf_id","desc")
                            ->selectRaw('SUBSTRING( pecsf_id, -3) as sequence')
                            ->first();

        if ($existing){
            $seq = str_pad((intval($existing->sequence) +1),3,'0',STR_PAD_LEFT);
            // $pecsf_id = "R". $yy . str_pad((intval(count($existing)) +1),3,'0',STR_PAD_LEFT);
        }
        $pecsf_id = $prefix . $yy . $seq;

        return $pecsf_id;

    }

    public function storeMedia(Request $request)
    {
       
        $path = storage_path( 'app/tmp' );

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $request->file('file');
        $name = uniqid() . '_' . str_replace(' ', '_', trim($file->getClientOriginalName()) );

        $file->move($path, $name);

        return response()->json([
            'name'          => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

}
