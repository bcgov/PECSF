<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BankDepositForm;
use App\Models\BankDepositFormOrganizations;
use App\Models\BankDepositFormAttachments;
use App\Models\Charity;
use App\Models\EmployeeJob;
use App\Models\Organization;
use App\Models\Pledge;
use App\Models\ProcessHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use App\Models\FSPool;
use App\Models\Region;
use App\Models\BusinessUnit;
use App\Models\Department;
use App\Models\CampaignYear;
use App\Models\User;
use App\Models\City;
use Illuminate\Support\Facades\Auth;

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
        $pools = FSPool::where('start_date', '=', function ($query) {
            $query->selectRaw('max(start_date)')
                ->from('f_s_pools as A')
                ->whereColumn('A.region_id', 'f_s_pools.region_id')
                ->where('A.start_date', '<=', today());
        })
            ->where('status', 'A')
            ->get();
        $regional_pool_id = $pools->count() > 0 ? $pools->first()->id : null;
        $business_units = BusinessUnit::where("status","=","A")->whereColumn("code","linked_bu_code")->groupBy("linked_bu_code")->orderBy("name")->get();
        $regions = Region::where("status","=","A")->orderby("name", "asc")->get();
        $departments = Department::all();
        $campaign_year = CampaignYear::where('calendar_year', '<=', today()->year + 1 )->orderBy('calendar_year', 'desc')
            ->first();
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
                $q->whereRaw("LOWER(charity_name) LIKE '%" . strtolower($term) . "%'");
            }
            return $q->orderby('charity_name','asc');

        })->where('charity_status','Registered')->paginate(10);
        $cities = City::all();
        $designation_list = Charity::DESIGNATION_LIST;
        $category_list = Charity::CATEGORY_LIST;
        $province_list = Charity::PROVINCE_LIST;
        $terms = explode(" ", $request->get("title") );
        $multiple = 'false';
        $selected_charities = [];


        $fund_support_pool_list = FSPool::current()->where('status', 'A')->with('region')->get()->sortBy(function($pool, $key) {
            return $pool->region->name;
        });

        return view('volunteering.forms',compact('fund_support_pool_list','organizations','selected_charities','multiple','charities','terms','province_list','category_list','designation_list','cities','campaign_year','current_user','pools','regional_pool_id','business_units','regions','departments'));
    }

    public function ignoreRemovedFiles($request){
        if(!empty(request()->ignoreFiles))
        {
            $fields = $request['attachments'];
            $request['attachments'] = [];
            foreach( $fields as $index => $file )
            {
                if(!in_array($file->getClientOriginalName(),explode(",",request()->ignoreFiles)))
                {
                    $request['attachments'][] = $file;
                }
            }
        }
        return $request;
    }

    public function store(Request $request) {

        $validator = Validator::make($this->ignoreRemovedFiles($request->all()), [
            'organization_code'         => 'required',
            'form_submitter'         => 'required',
            'campaign_year'         => 'required',
            'event_type'         => 'required',
            'sub_type'         => 'required',
            'deposit_date'         => 'required|before:tomorrow',
            'deposit_amount'         => 'required|numeric|gt:0',
            'employment_city'         => 'required',
            'postal_code'         => ($request->event_type == "Fundraiser" || $request->event_type == "Gaming") ? " ":'postal_code:CA',
            'region'         => 'required',
            'business_unit'         => 'required',
            'charity_selection' => 'required',
            'description' => 'required',
            'attachments.*' => 'required|mimes:pdf,xls,xlsx,csv,png,jpg,jpeg',
        ],[
            'organization_code' => 'The Organization Code is required.',
            'deposit_date.before' => 'The deposit date must be the current date or a date before the current date.'
         ]);
        $validator->after(function ($validator) use($request) {

            if(strpos($request->deposit_amount,".") !== FALSE){
                $decimals = substr($request->deposit_amount,strpos($request->deposit_amount,".")+1,3);
                if(strlen($decimals) == 3)
                {
                    $validator->errors()->add('deposit_amount','Only two decimal places allowed.');
                }
            }


            if($request->event_type != "Gaming" && $request->event_type != "Fundraiser"){
            if($request->organization_code != "GOV" && $request->organization_code != "RET")
            {
                    if(empty($request->pecsf_id))
                    {
                        $validator->errors()->add('pecsf_id','A PECSF ID is required.');
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
            }
            }
            if($request->event_type == "Cash One-Time Donation" || $request->event_type == "Cheque One-Time Donation")
            {
                if(empty($request->address_1))
                {
                    $validator->errors()->add('address_1','An Address is required.');
                }

                if(empty($request->city))
                {
                    $validator->errors()->add('city','An City is required.');
                }

                if(empty($request->province))
                {
                    $validator->errors()->add('province','An Province is required.');
                }

                if(empty($request->postal_code))
                {
                    $validator->errors()->add('postal_code','An Postal Code is required.');
                }
            }

            if($request->charity_selection == "fsp")
            {
                if(empty($request->regional_pool_id)){
                    $validator->errors()->add('regional_pool_id','Select a Regional Pool.');
                }
            }
            else{
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
                if (empty(!$existing)) {
                    if (strtolower($request->pecsf_id[0]) != "s" || !is_numeric(substr($request->pecsf_id, 1))) {
                        $validator->errors()->add('pecsf_id', 'Previous Cash One-time donation for this form submitter detected; The PECSF ID must be a number prepended with an S.');
                    }
                }
            }

            $existing_pecsf_id = BankDepositForm::where("organization_code","=","GOV")
                ->where("campaign_year_id","=",$request->campaign_year)
                ->whereIn("pecsf_id",[$request->pecsf_id,"S".$request->pecsf_id,"s".$request->pecsf_id])
                ->get();
            if(count($existing_pecsf_id) > 0)
            {
                $validator->errors()->add('pecsf_id','The PECSF ID has already been used for another Donation.');
            }

            if(!empty(request("attachments"))){
                $fileFound = false;
                foreach(array_reverse(request('attachments')) as $key => $attachment){
                    if(in_array($attachment->getClientOriginalName(),explode(",",$request->ignoreFiles)) || empty($attachment) || $attachment == "undefined"){
                    }
                    else{
                        $fileFound = true;
                        break;
                    }
                }
                if(!$fileFound){
                    $validator->errors()->add('attachment','Atleast one attachment is required.');
                }
            }
            else{
                $validator->errors()->add('attachment','Atleast one attachment is required.');
            }
        });



        $validator->validate();
        $regional_pool_id = ($request->charity_selection == "fsp") ? $request->regional_pool_id : null;

        $form = BankDepositForm::Create(
            [
                'business_unit' => $request->business_unit,
                'organization_code' => $request->organization_code,
                'form_submitter_id' =>  $request->form_submitter,
                'campaign_year_id' =>  $request->campaign_year,
                'event_type' =>  $request->event_type,
                'sub_type' => $request->sub_type,
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
                'pecsf_id' => $request->pecsf_id,

            ]
        );

        if($request->charity_selection == "dc"){
            $orgName = count($request->organization_name) -1;
            $orgCount = $orgName;
            foreach($request->organization_name as $org){

                if($orgName <= ($orgCount - $request->org_count)){
                    break;
                }
                BankDepositFormOrganizations::create([
                    'organization_name' => $request->organization_name[$orgName],
                    'vendor_id' => $request->vendor_id[$orgName],
                    'donation_percent' => $request->donation_percent[$orgName],
                    'specific_community_or_initiative' =>  (isset($request->additional[$orgName])?$request->additional[$orgName]:""),
                    'bank_deposit_form_id' => $form->id
                ]);
                $orgName--;
            }
        }

        $upload_images = $request->file('attachments') ? $request->file('attachments') : [];

        foreach($upload_images as $key => $file){

            if(is_array($request->ignoreFiles)){
                if(in_array($file->getClientOriginalName(),$request->ignoreFiles))
                {
                    continue;
                }
            }


                $filename=date('YmdHis').'_'. str_replace(' ', '_', $file->getClientOriginalName() );

                $filePath = $file->storeAs(  "/uploads/bank_deposit_form_attachments" , $filename);
                BankDepositFormAttachments::create([
                'local_path' => storage_path( $this->doc_folder )."/".$filename,
                'bank_deposit_form_id' => $form->id
            ]);
        }

        if(strpos($_SERVER['HTTP_REFERER'],'admin-pledge') !== FALSE)
        {
            echo  json_encode(array(route('admin-pledge.maintain-event.index')));

        }
        else{
            echo  json_encode(array(route('bank_deposit_form')));
        }

    }
    public function update(Request $request) {
        $validator = Validator::make(request()->all(), [
            'organization_code'         => 'required',
            'form_submitter'         => 'required',
            'campaign_year'         => 'required',
            'event_type'         => 'required',
            'sub_type'         => 'required',
            'deposit_date'         => 'required|before:tomorrow',
            'deposit_amount'         => 'required|numeric|gt:0',
            'employment_city'         => 'required',
            'postal_code'         => ($request->event_type == "Fundraiser" || $request->event_type == "Gaming") ? " ":'postal_code:CA',
            'region'         => 'required',
            'business_unit'         => 'required',
            'charity_selection' => 'required',
            'description' => 'required',
        ],[
            'organization_code' => 'The Organization Code is required.',
        ]);
        $validator->after(function ($validator) use($request) {
            if($request->event_type != "Gaming" && $request->event_type != "Fundraiser"){
                if($request->organization_code != "GOV")
                {
                    if(empty($request->pecsf_id))
                    {
                        $validator->errors()->add('pecsf_id','A PECSF ID is required.');
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
                }
            }
            if($request->event_type == "Cash One-Time Donation" || $request->event_type == "Cheque One-Time Donation")
            {
                if(empty($request->address_1))
                {
                    $validator->errors()->add('address_1','An Address is required.');
                }

                if(empty($request->city))
                {
                    $validator->errors()->add('city','An City is required.');
                }

                if(empty($request->province))
                {
                    $validator->errors()->add('province','An Province is required.');
                }

                if(empty($request->postal_code))
                {
                    $validator->errors()->add('postal_code','An Postal Code is required.');
                }
            }

            if($request->charity_selection == "fsp")
            {
                if(empty($request->regional_pool_id)){
                    $validator->errors()->add('regional_pool_id','Select a Regional Pool.');
                }
            }
            else{
                $total = 0;

                if($request->org_count < 1){
                    $validator->errors()->add('charity','You need to Select a Charity.');
                }
                else{
                    $a = [];
                    for($i=(count(request("donation_percent")) -1);$i >= (count(request("donation_percent")) - $request->org_count);$i--){
                        if(empty(request("organization_name")[$i]))
                        {
                            $validator->errors()->add('organization_name.'.$i,'The Organization name is required.');
                        }
                        if(empty(request('vendor_id')[$i])){

                            $validator->errors()->add('vendor_id.'.$i,'The Vendor Id is required.');
                        };
                        if(empty(request('donation_percent')[$i])){

                            $validator->errors()->add('donation_percent.'.$i,'The Donation Percent is required.');
                        }
                        else if(!is_numeric(request('donation_percent')[$i])){

                            $validator->errors()->add('donation_percent.'.$i,'The Donation Percent must be a number.');
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

            $existing = [];
            if($request->organization_code == "GOV"){
                $existing = BankDepositForm::where("organization_code","=","GOV")
                    ->where("event_type","=","Cash One-time Donation")
                    ->where("form_submitter_id","=",$request->form_submitter_id)
                    ->get();
                if(empty(!$existing))
                {
                   if(strtolower($request->pecsf_id[0]) != "s" || !is_numeric(substr($request->pecsf_id,1)))
                    {
                        $validator->errors()->add('pecsf_id','Previous Cash One-time donation for this form submitter detected; The PECSF ID must be a number prepended with an S.');
                    }
                }

            }
            $existing_pecsf_id = BankDepositForm::where("organization_code","=","GOV")
                ->where("campaign_year_id","=",$request->campaign_year)
                ->whereIn("pecsf_id",[$request->pecsf_id,"S".$request->pecsf_id,"s".$request->pecsf_id])
                ->get();
            if(count($existing_pecsf_id) > 0)
            {
                $validator->errors()->add('pecsf_id','The PECSF ID has already been used for another Donation.');
            }
        });
        $validator->validate();
        $regional_pool_id = ($request->charity_selection == "fsp") ? $request->regional_pool_id : null;

        $form = BankDepositForm::where("id","=",$request->form_id)->update(
            [
                'organization_code' => $request->organization_code,
                'event_type' =>  $request->event_type,
                'sub_type' => $request->sub_type,
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
                'pecsf_id' => $request->pecsf_id,
                'business_unit' => $request->business_unit
            ]
        );

        if($request->charity_selection == "dc"){
            $orgName = count($request->organization_name) -1;
            $orgCount = $orgName;
            BankDepositFormOrganizations::where("bank_deposit_form_id",$request->id)->delete();

            foreach($request->organization_name as $org){

                if($orgName <= ($orgCount - $request->org_count)){
                    break;
                }
                BankDepositFormOrganizations::create([
                    'organization_name' => $request->organization_name[$orgName],
                    'vendor_id' => $request->vendor_id[$orgName],
                    'donation_percent' => $request->donation_percent[$orgName],
                    'specific_community_or_initiative' =>  (isset($request->additional[$orgName])?$request->additional[$orgName]:""),
                    'bank_deposit_form_id' => $request->form_id
                ]);
                $orgName--;
            }
        }
        echo json_encode(["/admin-pledge/submission-queue"]);
    }


    public function organization_code(Request $request)
    {
        if(empty($request->term))
        {
            $organizations = Organization::where("status","=","A")->limit(30)->get();
        }
        else{
            $organizations = Organization::where("code", "LIKE", $request->term . "%")->where("status","=","A")->get();
        }
        $response = ['results' => []];
        $response['results'][] = ["id" => "false", "text" => "Choose an Org Code"];
        foreach ($organizations as $organization) {
            if(!empty($organization->code)){
                $response['results'][] = ["id" =>  $organization->code,"text" => $organization->code];
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
            $organizations->where("charity_name","LIKE","%".$request->keyword."%");
        }
        if (is_numeric($request->pool_filter)){
            $pool = FSPool::current()->where('id', $request->get('pool_filter') )->first();
            $organizations->whereIn('charities.id', $pool->charities->pluck('charity_id') );
            $organizations->join('f_s_pool_charities',"charities.id","f_s_pool_charities.charity_id");
            $organizations->where("f_s_pool_charities.status","=","A");
            $organizations->selectRaw("image, f_s_pool_charities.description as pool_description");
            $organizations->groupBy("f_s_pool_charities.charity_id");
        }

        $organizations = $organizations->where("charity_status","=","Registered")->paginate(7)->onEachSide(1);
        $total = $organizations->total();
        $selected_vendors = explode(",",$request->selected_vendors);

        return view('volunteering.partials.organizations', compact('selected_vendors','organizations','total'))->render();
    }

    function bc_gov_id(Request $request){
        $record = EmployeeJob::where("emplid","=",$request->id)->join("business_units","business_units.code","employee_jobs.business_unit")->selectRaw("business_units.id as business_unit_id, employee_jobs.office_city, employee_jobs.region_id")->first();
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
        public function download(Request $request, $fileName) {
            $headers = [
                'Content-Description' => 'File Transfer',
                'Content-Type' => 'application/csv',
                "Content-Transfer-Encoding: UTF-8",
            ];
            // return Storage::download($path);
            return Storage::disk('uploads')->download("/bank_deposit_form_attachments/".$fileName, $fileName, $headers);
        }
}
