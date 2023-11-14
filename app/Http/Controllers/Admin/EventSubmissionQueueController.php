<?php

namespace App\Http\Controllers\Admin;

use App\Models\BankDepositForm;
use App\Models\CampaignYear;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Models\FSPool;
use App\Models\City;
use App\Models\User;
use App\Models\BusinessUnit;
use App\Models\Region;
use App\Models\Department;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\BankDepositFormOrganizations;
use App\Models\BankDepositFormAttachments;


class EventSubmissionQueueController extends Controller
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

    public function status(Request $request){

        $form = BankDepositForm::where("id",$request->submission_id)->first();
        if($request->status == 1){

            if($form->event_type == "Gaming")
            {
                BankDepositForm::find($request->submission_id)->update([
                    'approved' => $request->status,
                    'approved_by_id' =>  Auth::id(),
                    'approved_at' => now(),
                ]);
            }
            else if($form->event_type == "Fundraiser")
            {
                BankDepositForm::find($request->submission_id)->update([
                    'approved' => $request->status,
                    'approved_by_id' =>  Auth::id(),
                    'approved_at' => now(),
                ]);
            }
            else if($form->organization_code == "RET"){
                $count = BankDepositForm::where("organization_code","RET")->count() + 1;
                $zeroes = 3 - strlen($count);
                $id = "R".date("y");
                for($i = 0; $i<$zeroes;$i++){
                    $id.= "0";
                }
                $id .= $count;
                BankDepositForm::find($request->submission_id)->update([
                    'approved' => $request->status,
                    'pecsf_id' => $id,
                    'approved_by_id' =>  Auth::id(),
                    'approved_at' => now(),
                ]);
            }


                BankDepositForm::find($request->submission_id)->update([
                        'approved' => $request->status,
                        'approved_by_id' =>  Auth::id(),
                        'approved_at' => now(),
                    ]);
                $year =  intval(date("Y")) + 1;
                do{
                    $campaign_year = CampaignYear::where('calendar_year', $year)->first();
                    if(empty($campaign_year))
                    {
                        break;
                    }
                    $year--;

                    if($year == 2005){
                        break;
                    }
                }while(!$campaign_year->isOpen());
                if(empty($campaign_year) || !$campaign_year->isOpen()){
                    $campaign_year = CampaignYear::where('calendar_year', intval(date("Y")))->first();
                }
        }
        if($request->status == 2){
            BankDepositForm::where("id","=",$request->submission_id)->update(['approved' => $request->status]);

        }
        if($request->status == 0){
            BankDepositForm::where("id","=",$request->submission_id)->update(['approved' => $request->status]);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
     $submissions = BankDepositForm::selectRaw("*,bank_deposit_forms.id as bank_deposit_form_id")
         ->join("users","bank_deposit_forms.form_submitter_id","=","users.id")
         ->where("approved","!=",1)
         ->get();
        $pools = FSPool::where('start_date', '=', function ($query) {
            $query->selectRaw('max(start_date)')
                ->from('f_s_pools as A')
                ->whereColumn('A.region_id', 'f_s_pools.region_id')
                ->whereNull('A.deleted_at')
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
        $cities = City::all();
        $organizations = [];
        $selected_charities = [];
        $fund_support_pool_list = FSPool::current()->where('status', 'A')->with('region')->get()->sortBy(function($pool, $key) {
            return $pool->region->name;
        });

        // load the view and pass
        return view('admin-pledge.submission-queue.index',compact('fund_support_pool_list','selected_charities','organizations','cities','pools','regional_pool_id','business_units','regions','departments','campaign_year','submissions','current_user'));
    }
    /**
     * Display a listing of pledge details.
     *
     * @return \Illuminate\Http\Response
     */
    public function details(Request $request){
        $submissions = BankDepositForm::selectRaw("*,bank_deposit_forms.id as bank_deposit_form_id, campaign_years.calendar_year as calendar_year")
            ->where("bank_deposit_forms.id","=",$request->form_id)
            ->join("users","bank_deposit_forms.form_submitter_id","=","users.id")
            ->join("campaign_years","bank_deposit_forms.campaign_year_id","=","campaign_years.id")
            ->with('form_submitted_by')
            ->get();
            error_log('1');
        foreach($submissions as $index => $submission){
            $submissions[$index]["charities"] = BankDepositFormOrganizations::where("bank_deposit_form_id", $request->form_id)
                                                ->orderByRaw('CAST(donation_percent AS DECIMAL(10, 2)) DESC')
                                                ->get();
            $submissions[$index]["attachments"] = BankDepositFormAttachments::where("bank_deposit_form_id","=",$request->form_id)->get();
        }

        $existing = [];


        if($submissions[0]->organization_code == "RET"){
            $existing = BankDepositForm::where("pecsf_id","LIKE","R".substr(date("Y"),2,2)."%")
                ->orderBy("pecsf_id","desc")
                ->get();

            if(count($existing) > 0)
            {
                $submissions[0]->pecsf_id = "R".substr(date("Y"),2,2).str_pad((intval(count($existing)) +1),3,'0',STR_PAD_LEFT);
            }
            else{
                $submissions[0]->pecsf_id = "R".substr(date("Y"),2,2)."001";
            }
        }
        $existing = [];

        if($submissions[0]->event_type == "Gaming")
        {
            $existing = BankDepositForm::where("event_type","=","Gaming")
                ->where("pecsf_id","LIKE","G".substr(date("Y"),2,2)."%")
                ->orderBy("pecsf_id","desc")
                ->get();

            if(count($existing) > 0)
            {
                $submissions[0]->pecsf_id = "G".substr(date("Y"),2,2).str_pad((intval(count($existing)) +1),3,'0',STR_PAD_LEFT);
            }
            else{
                $submissions[0]->pecsf_id = "G".substr(date("Y"),2,2)."001";
            }
        }
        $existing = [];

        if($submissions[0]->event_type == "Fundraiser")
        {
            $existing = BankDepositForm::where("event_type","=","Fundraiser")
                ->where("pecsf_id","LIKE","F".substr(date("Y"),2,2)."%")
                ->orderBy("pecsf_id","desc")
                ->get();

            if(count($existing) > 0)
            {
                $submissions[0]->pecsf_id = "F".substr(date("Y"),2,2).str_pad((intval(count($existing)) +1),3,'0',STR_PAD_LEFT);
            }
            else{
                $submissions[0]->pecsf_id = "F".substr(date("Y"),2,2)."001";
            }
        }

        $gov_organization = Organization::where('code', 'GOV')->first();
        foreach($submissions as $index => $submission){
            $is_GOV = ($submission->organization_code == $gov_organization->code);
            $existing = false;
            if($is_GOV){
                $existing = BankDepositForm::where("organization_code","=","GOV")
                    ->where("event_type","=","Cash One-time Donation")
                    ->where("form_submitter_id","=",$submission->form_submitter_id)
                    ->get();
            }
            $submissions[$index]->existing = $existing ? true : false;
        }

        echo json_encode($submissions);
    }
}
