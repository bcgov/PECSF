<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BankDepositForm;
use App\Models\BankDepositFormOrganizations;
use App\Models\BankDepositFormAttachments;
use Illuminate\Http\Request;
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

    public function index()
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
        $regions = Region::all();
        $departments = Department::all();
        $campaign_year = CampaignYear::where('calendar_year', '<=', today()->year + 1 )->orderBy('calendar_year', 'desc')
            ->first();
        $current_user = User::where('id', Auth::id() )->first();
        $cities = City::all();
        return view('volunteering.forms',compact('cities','campaign_year','current_user','pools','regional_pool_id','business_units','regions','departments'));
    }

    public function store(Request $request) {
        $validator = Validator::make(request()->all(), [
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
            'city'         => 'required',
            'province'         => 'required',
            'postal_code'         => 'required',
            'charity_selection' => 'required',
            'description' => 'required',
            'attachments.*' => 'required',
        ],[
            'organization_code' => 'The Organization Code is required.',
         ]);
        $validator->after(function ($validator) use($request) {


            if($request->event_type == "Fundraiser" || $request->event_type == "Gaming")
            {
                if(empty($request->address_line_1))
                {
                    $validator->errors()->add('address_line_1','An Address is required.');
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
                for($i=0;$i<$request->org_count;$i++){

                    if(!empty(request("donation_percent")[$i]))
                        {
                            $total = request('donation_percent')[$i] + $total;
                        }

                    if(empty(request("id")[$i]))
                    {
                        $validator->errors()->add('organization_name.'.$i,'The Organization name is required.');
                    }
                    if(empty(request('vendor_id')[$i])){
                        $validator->errors()->add('vendor_id.'.$i,'The Vendor Id is required.');
                    };
                    if(empty(request('donation_percent')[$i])){
                        $validator->errors()->add('donation_percent.'.$i,'The Donation Percent is required.');
                    };
                    if(empty(request('specific_community_or_initiative')[$i])){
                        $validator->errors()->add('specific_community_or_initiative.'.$i,'This Field is required.');
                    };
                }
                if($total != 100) {
                    for ($j = 0; $j < $request->org_count; $j++) {
                        $validator->errors()->add('donation_percent.' . $j, 'The Donation Percent is Does not equal 100%.');
                    }
                }
            }

            foreach(request('attachments') as $key => $attachment){
                if(empty($attachment) || $attachment == "undefined" ){
                    $validator->errors()->add('attachment.0','Atleast one attachment is required.');
                };
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
                'address_postal_code' => $request->postal_code
            ]
        );

        if($request->charity_selection == "dc"){
            foreach($request->id as $key => $name){
                BankDepositFormOrganizations::create([
                    'organization_name' => $request->id[$key],
                    'vendor_id' => $request->vendor_id[$key],
                    'donation_percent' => $request->donation_percent[$key],
                    'specific_community_or_initiative' => $request->specific_community_or_initiative[$key],
                    'bank_deposit_form_id' => $form->id
                ]);
            }
        }

        $upload_images = $request->file('attachments') ? $request->file('attachments') : [];

        foreach($upload_images as $key => $file){
                $filename=date('YmdHis').'_'. str_replace(' ', '_', $file->getClientOriginalName() );
                $file->move(public_path( $this->doc_folder ), $filename);
                BankDepositFormAttachments::create([
                'local_path' => public_path( $this->doc_folder )."/".$filename,
                'bank_deposit_form_id' => $form->id
            ]);
        }

        echo  json_encode(array(route('bank_deposit_form')));
    }



}
