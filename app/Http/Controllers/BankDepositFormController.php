<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BankDepositForm;
use App\Models\BankDepositFormOrganizations;
use App\Models\BankDepositFormAttachments;
use App\Models\Charity;
use App\Models\Organization;
use App\Models\Pledge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
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
        $this->doc_folder = "bank_deposit_form_attachments";
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
        $business_units = BusinessUnit::all();
        $regions = Region::where("status","=","A")->get();
        $departments = Department::all();
        $campaign_year = CampaignYear::where('calendar_year', '<=', today()->year + 1 )->orderBy('calendar_year', 'desc')
            ->first();
        $current_user = User::where('id', Auth::id() )->first();
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
        if (Session::has('charities')) {
            $selectedCharities = Session::get('charities');

            $_charities = Charity::whereIn('id', $selectedCharities['id'])
                ->get(['id', 'charity_name as text']);

            foreach ($_charities as $charity) {
                $charity['additional'] = $selectedCharities['additional'][array_search($charity['id'], $selectedCharities['id'])];
                if (!$charity['additional']) {
                    $charity['additional'] = '';
                }

                array_push($selected_charities, $charity);
            }
        } else {

            // reload the existig pledge
            $errors = session('errors');

            if (!$errors) {

                $campaignYear = CampaignYear::where('calendar_year', '<=', today()->year + 1 )->orderBy('calendar_year', 'desc')
                    ->first();
                $pledge = Pledge::where('user_id', Auth::id())
                    ->whereHas('campaign_year', function($q){
                        $q->where('calendar_year','=', today()->year + 1 );
                    })->first();

                if ( $campaignYear->isOpen() && $pledge && count($pledge->charities) > 0 )  {

                    $_ids = $pledge->charities->pluck(['charity_id'])->toArray();

                    $_charities = Charity::whereIn('id', $_ids )
                        ->get(['id', 'charity_name as text']);

                    foreach ($_charities as $charity) {
                        $pledge_charity = $pledge->charities->where('charity_id', $charity->id)->first();

                        $charity['additional'] = '';
                        if ($pledge_charity) {
                            $charity['additional'] = $pledge_charity->additional ?? '';
                        }

                        array_push($selected_charities, $charity);
                    }
                }
            }
        }
        return view('volunteering.forms',compact('selected_charities','multiple','charities','terms','province_list','category_list','designation_list','cities','campaign_year','current_user','pools','regional_pool_id','business_units','regions','departments'));
    }

    public function store(Request $request) {
        $validator = Validator::make(request()->all(), [
            'organization_code'         => 'required',
            'form_submitter'         => 'required',
            'campaign_year'         => 'required',
            'event_type'         => 'required',
            'sub_type'         => 'required',
            'deposit_date'         => 'required|before:today',
            'deposit_amount'         => 'required|integer',
            'employment_city'         => 'required',
            'postal_code'         => 'postal_code:CA',
            'region'         => 'required',
            'business_unit'         => 'required',
            'charity_selection' => 'required',
            'description' => 'required',
            'attachments.*' => 'required',
        ],[
            'organization_code' => 'The Organization Code is required.',
         ]);
        $validator->after(function ($validator) use($request) {

            if(empty($request->pecsf_id) && $request->organization_code != "GOV")
            {
                $validator->errors()->add('pecsf_id','A PECSF ID is required.');
            }
            if(empty($request->bc_gov_id) && $request->organization_code == "GOV"){
                $validator->errors()->add('bc_gov_id','A BC GOV ID is required.');
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
                for($i=0;$i<=$request->org_count;$i++){



                    if(empty(request("id")[$i]))
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
                        if(!empty(request("donation_percent")[$i]))
                        {
                            $total = request('donation_percent')[$i] + $total;
                        }
                    }

                }
                if($total != 100) {
                    for ($j = 0; $j < $request->org_count; $j++) {
                        $validator->errors()->add('donation_percent.' . $j, 'The Donation Percent is Does not equal 100%.');
                    }
                }
            }

            if(!empty(request("attachments"))){
                foreach(request('attachments') as $key => $attachment){
                    if(!in_array($attachment,$request->ignoreFiles)){
                        if(empty($attachment) || $attachment == "undefined"){
                            $validator->errors()->add('attachment.0','Atleast one attachment is required.');
                        };
                    }
                }
            }
            else{
                $validator->errors()->add('attachment.0','Atleast one attachment is required.');

            }



        });
        $validator->validate();
        $regional_pool_id = ($request->charity_selection == "fsp") ? $request->regional_pool_id : null;

        $form = BankDepositForm::Create(
            [
                'organization_code' => $request->organization_code,
                'form_submitter' =>  $request->form_submitter,
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
                'pecsf_id' => $request->pecsf_id
            ]
        );

        if($request->charity_selection == "dc"){
            foreach($request->id as $key => $name){

               ;

                BankDepositFormOrganizations::create([
                    'organization_name' => $request->id[$key],
                    'vendor_id' => $request->vendor_id[$key],
                    'donation_percent' => $request->donation_percent[$key],
                    'specific_community_or_initiative' =>  (isset($request->specific_community_or_initiative[$key])?$request->specific_community_or_initiative[$key]:""),
                    'bank_deposit_form_id' => $form->id
                ]);
            }
        }

        $upload_images = $request->file('attachments') ? $request->file('attachments') : [];

        foreach($upload_images as $key => $file){

            if(in_array($file->getClientOriginalName(),$request->ignoreFiles))
            {
                continue;
            }

                $filename=date('YmdHis').'_'. str_replace(' ', '_', $file->getClientOriginalName() );
                $file->move(public_path( $this->doc_folder ), $filename);
                BankDepositFormAttachments::create([
                'local_path' => public_path( $this->doc_folder )."/".$filename,
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
}
