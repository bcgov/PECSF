<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Pledge;
use App\Models\CampaignYear;
use Illuminate\Http\Request;
use App\Models\PledgeHistory;
use App\Models\BankDepositForm;
use App\Models\DonateNowPledge;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\SpecialCampaignPledge;


class DonationController extends Controller {

    public function index(Request $request) {
        $currentYear = Carbon::now()->format('Y');
        // $pledges = Pledge::with('charities')
        //     ->with('charities.charity')
        //     ->where('user_id', Auth::id())
        //     ->whereYear('created_at', $currentYear)
        //     ->get();
        // $totalPledgedDataTillNow = "$".Pledge::where('user_id', Auth::id())->sum('goal_amount');

        // Campaign Year
        $campaignYear = CampaignYear::where('calendar_year', '<=', today()->year + 1 )
                            ->orderBy('calendar_year', 'desc')
                            ->first();

        $user = User::where('id', Auth::id() )->first();
        $current_pledge = Pledge::where('emplid', $user->emplid)
                         ->whereHas('campaign_year', function($q){
                             $q->where('calendar_year','=', today()->year + 1 );
                         })->first();

        // NOTE: Must use the raw select statement in Laravel for querying this custom SQL view due to the performance issue
        // $all_pledges = DB::select( DB::raw("SELECT * FROM pledge_history_view WHERE (GUID = '" . $user->guid .
        //                                         "' and GUID <> '') OR (source = 'GF' and user_id = " . Auth::id() . ") order by yearcd desc, donation_type desc;" ) );
        // $all_pledges = DB::select( DB::raw("SELECT * FROM pledge_history_view WHERE (emplid = '" . $user->emplid .
        //                                         "' and emplid <> '') OR (source = 'GF' and user_id = " . Auth::id() . ") order by yearcd desc, donation_type desc;" ) );

        $annual_pay_period_pledges = DB::table('pledges')
                ->join('campaign_years', 'campaign_years.id', 'pledges.campaign_year_id')
                ->where('pledges.pay_period_amount', '<>', 0)
                ->whereNull('pledges.deleted_at')
                ->where('pledges.organization_id', $user->organization_id)
                ->where('pledges.emplid', $user->emplid)
                ->whereNotNull('pledges.emplid')
                ->selectRaw("'GF' as source, pledges.user_id,  pledges.id, 'pledge' as model, pledges.emplid, campaign_years.calendar_year, type,
                            'Annual' , 'Bi-Weekly', pledges.pay_period_amount, pledges.goal_amount - pledges.one_time_amount,
                            (select regions.name from f_s_pools, regions where f_s_pools.region_id = regions.id and f_s_pools.id = pledges.f_s_pool_id),
                                case when type = 'P' then 0 else (select GROUP_CONCAT(charity_name) from pledge_charities, charities
                                            where pledge_charities.charity_id = charities.id
                                              and pledge_charities.pledge_id = pledges.id
                                              and pledge_charities.frequency = 'bi-weekly'
                                              and pledge_charities.deleted_at is null) end");

        $annual_one_time_pledges = DB::table('pledges')
                ->join('campaign_years', 'campaign_years.id', 'pledges.campaign_year_id')
                ->where('pledges.one_time_amount', '<>', 0)
                ->whereNull('pledges.deleted_at')
                ->where('pledges.organization_id', $user->organization_id)
                ->where('pledges.emplid', $user->emplid)
                ->whereNotNull('pledges.emplid')
                ->selectRaw("'GF' as source, pledges.user_id, pledges.id, 'pledge' as model, pledges.emplid, campaign_years.calendar_year, type,
                          'Annual' , 'One-Time', pledges.one_time_amount, pledges.one_time_amount,
                             (select regions.name from f_s_pools, regions where f_s_pools.region_id = regions.id and f_s_pools.id = pledges.f_s_pool_id),
                            case when type = 'P' then 0 else (select GROUP_CONCAT(charity_name) from pledge_charities, charities
                                        where pledge_charities.charity_id = charities.id
                                          and pledge_charities.pledge_id = pledges.id
                                          and pledge_charities.frequency = 'one-time'
                                          and pledge_charities.deleted_at is null) end");

        $donate_now_pledges = DB::table('donate_now_pledges')
                ->whereNull('donate_now_pledges.deleted_at')
                ->where('donate_now_pledges.organization_id', $user->organization_id)
                ->where('donate_now_pledges.emplid', $user->emplid)
                ->whereNotNull('donate_now_pledges.emplid')
                ->selectRaw("'GF' as source, donate_now_pledges.user_id,donate_now_pledges.id, 'donate_now_pledges' as model,  donate_now_pledges.emplid, yearcd, type,
                            'Donate Now', 'One-Time', donate_now_pledges.one_time_amount, donate_now_pledges.one_time_amount,
                            case when type = 'P' then
                                (select regions.name from f_s_pools, regions where f_s_pools.region_id = regions.id and f_s_pools.id = donate_now_pledges.f_s_pool_id)
                            else
                                (select charities.charity_name from charities where donate_now_pledges.charity_id = charities.id )
                            end, 1");

        $special_campaign_pledges = DB::table('special_campaign_pledges')
                ->whereNull('special_campaign_pledges.deleted_at')
                ->where('special_campaign_pledges.organization_id', $user->organization_id)
                ->where('special_campaign_pledges.emplid', $user->emplid)
                ->whereNotNull('special_campaign_pledges.emplid')
                ->selectRaw("'GF' as source, special_campaign_pledges.user_id, special_campaign_pledges.id,'special_campaign_pledges' as model, special_campaign_pledges.emplid, yearcd, 'C',
                            'Special Campaign', 'One-Time', special_campaign_pledges.one_time_amount, special_campaign_pledges.one_time_amount,
                            (select special_campaigns.name from special_campaigns where special_campaign_pledges.special_campaign_id = special_campaigns.id)
                            , 1");

        $event_pledges = DB::table('bank_deposit_forms')
                ->join('campaign_years', 'campaign_years.id', 'bank_deposit_forms.campaign_year_id')
                ->whereIn('bank_deposit_forms.event_type', ['Cash One-Time Donation','Cheque One-Time Donation'])
                ->where('bank_deposit_forms.approved', 1)
                ->whereNull('bank_deposit_forms.deleted_at')
                ->where('bank_deposit_forms.organization_code', $user->organization ? $user->organization->code : null)
                ->where('bank_deposit_forms.bc_gov_id', $user->emplid)
                ->whereNotNull('bank_deposit_forms.bc_gov_id')
                ->selectRaw("'GF' as source, null, bank_deposit_forms.id,'bank_deposit_forms' as model, bc_gov_id, campaign_years.calendar_year,
                            case when regional_pool_id is null then 'C' else 'P' end,
                            'Event', 'One-Time', bank_deposit_forms.deposit_amount, bank_deposit_forms.deposit_amount,
                            (select regions.name from f_s_pools, regions where f_s_pools.region_id = regions.id and f_s_pools.id = bank_deposit_forms.regional_pool_id),
                            case when regional_pool_id is null then (select count(*) from bank_deposit_form_organizations
                                where bank_deposit_form_organizations.bank_deposit_form_id = bank_deposit_forms.id
                                        and bank_deposit_form_organizations.deleted_at is null) else 0 end");

        $all_pledges = DB::table('pledge_history_summaries')
                            ->where('emplid', $user->emplid)
                            ->whereNotNull('emplid')
                            ->selectRaw("'BI' as source, NULL as user_id,  pledge_history_id as id,'pledge_history_summaries' as model, emplid, yearcd, source as type,
                                         campaign_type as donation_type, frequency, per_pay_amt as amount, pledge,
                                         (select regions.name from regions where regions.code = pledge_history_summaries.region) as region,
                                         case when source = 'P' then 0 else
                                            case when campaign_type = 'Donate Today'
                                                then (select charity_name from charities a, pledge_histories b where a.registration_number = b.charity_bn and b.id = pledge_history_summaries.pledge_history_id)
                                                else (select GROUP_CONCAT(vendor_name1) from pledge_histories where emplid = pledge_history_summaries.emplid
                                                    and yearcd = pledge_history_summaries.yearcd
                                                    and source = 'Non-Pool'
                                                    and campaign_type = pledge_history_summaries.campaign_type
                                                    and frequency = pledge_history_summaries.frequency
                                                    )
                                            end
                                        end as number_of_charities")
                            ->unionAll($annual_pay_period_pledges)
                            ->unionAll($annual_one_time_pledges)
                            ->unionAll($donate_now_pledges)
                            ->unionAll($special_campaign_pledges)
                            ->unionAll($event_pledges)
                            ->get();

        $pledges_by_yearcd = collect( $all_pledges )->sortByDesc('yearcd')
                                    ->sortByDesc('source')->sortBy('donation_type')
                                    ->groupBy('yearcd')->sortKeysDesc();

        $totalPledgedDataTillNow = 0;
        foreach ($all_pledges as $pledge) {
            $totalPledgedDataTillNow += $pledge->pledge;
        }

        foreach($pledges_by_yearcd as $yearcd => $pledges){
            foreach($pledges as $index => $pledge){
                if($pledge->model == "pledge")
                {
                    if(!empty(Pledge::where("id","=",$pledge->id)->first()))
                    {
                        $pledges_by_yearcd[$yearcd][$index]->charities = Pledge::where("id","=",$pledge->id)->first()->distinct_charities;
                    }
                    else
                    {
                        $pledges_by_yearcd[$yearcd][$index]->charities = [];
                    }
                }
                else if($pledge->model == "donate_now_pledges"){
                    if(!empty(DonateNowPledge::where("id","=",$pledge->id)->first()))
                    {
                        $dnp = DonateNowPledge::where("id","=",$pledge->id)->first();

                        if($dnp->f_s_pool_id > 0){
                            $pledges_by_yearcd[$yearcd][$index]->charities = DonateNowPledge::where("id","=",$pledge->id)->first()->charities;
                        }
                        else{
                            $pledges_by_yearcd[$yearcd][$index]->charities = [DonateNowPledge::where("id","=",$pledge->id)->first()->charity];

                        }

                    }
                    else
                    {
                        $pledges_by_yearcd[$yearcd][$index]->charities = [];
                    }
                }
                else if($pledge->model == "special_campaign_pledges"){
                    if(!empty(SpecialCampaignPledge::where("id","=",$pledge->id)->first()))
                    {
                        $pledges_by_yearcd[$yearcd][$index]->charities = [SpecialCampaignPledge::where("id","=",$pledge->id)->first()->organization];
                    }
                    else
                    {
                        $pledges_by_yearcd[$yearcd][$index]->charities = [];
                    }
                }
                else if($pledge->model == "bank_deposit_forms")
                {
                    if(!empty(BankDepositForm::where("id","=",$pledge->id)->first()))
                    {
                        $pledges_by_yearcd[$yearcd][$index]->charities = BankDepositForm::where("id","=",$pledge->id)->first()->charities;
                    }
                    else
                    {
                        $pledges_by_yearcd[$yearcd][$index]->charities = [];
                    }
                }
                else if($pledge->model == "pledge_history_summaries")
                {
                    if(!empty(PledgeHistory::where("id","=",$pledge->id)->first()))
                    {
                        $pledges_by_yearcd[$yearcd][$index]->charities = PledgeHistory::where('emplid', $pledge->emplid)
                            ->where('campaign_type', $pledge->donation_type)
                            ->where('yearcd', $pledge->yearcd)
                            ->where('frequency', $pledge->frequency)
                            ->when( $pledge->donation_type == 'Donate Today', function($query) use($pledge) {
                                return $query->where('id', $pledge->id);
                            })
                            ->orderBy('name1')
                            ->get();;
                    }
                    else
                    {
                        $pledges_by_yearcd[$yearcd][$index]->charities = [];
                    }
                }
                else{
                    $pledges_by_yearcd[$yearcd][$index]->charities = [];

                }
            }
        }

        $fsp_name = false;

        // download PDF file with download method
        if(isset($request->download_pdf)){
            // view()->share('donations.index',compact('pledges', 'currentYear', 'totalPledgedDataTillNow', 'campaignYear',
            //     'pledge', 'pledges_by_yearcd'));

            $pdf = PDF::loadView('donations.partials.pdf', compact(//'pledges',
                'currentYear', 'totalPledgedDataTillNow', 'campaignYear', 'current_pledge',
                'pledges_by_yearcd','fsp_name'));
            return $pdf->download('Donation History Summary.pdf');
        }
        else{
            return view('donations.index', compact(//'pledges',
                'currentYear',  'totalPledgedDataTillNow','campaignYear', 'current_pledge',
                'pledges_by_yearcd','fsp_name'));
        }
    }

    public function pledgeDetail(Request $request)
    {

        if ($request->ajax()) {
            $campaign_year = CampaignYear::where('calendar_year', $request->yearcd)->first();

            if ($request->source == 'BI') {

                $user = User::where('id', Auth::id() )->first();

                $donate_today_pledge = PledgeHistory::where('id', $request->id)->first();
                $old_pledges = PledgeHistory::where('emplid', $user->emplid)
                                ->where('campaign_type', $request->donation_type)
                                ->where('yearcd', $request->yearcd)
                                ->where('frequency', $request->frequency)
                                ->when( $request->donation_type == 'Donate Today', function($query) use($request) {
                                    return $query->where('id', $request->id);
                                })
                                ->orderBy('name1')
                                ->get();

                $campaign_year = CampaignYear::where('calendar_year', $request->yearcd)->first();

                $type = $request->donation_type;
                $year = $request->yearcd;
                $frequency = $request->frequency;
                if ($request->frequency == 'One-Time') {
                    $pledge_amt = $old_pledges->first()->pledge;    // Note: Donate Today -- per_pay_amt is always zero
                } else {
                    // $pledge_amt = $old_pledges->first()->per_pay_amt;
                    $pledge_amt = round($old_pledges->sum('per_pay_amt'),0);
                }
                //  $request->frequency == 'One-Time' ? $pledge->one_time_amount : $pledge->pay_period_amount ;
                $number_of_periods = $request->frequency == 'One-Time' ? 1 : $campaign_year->number_of_periods;
                // $total_amount = $request->frequency == 'One-Time' ? $pledge_amt : $pledge_amt * $number_of_periods;
                $total_amount = $old_pledges->first()->pledge;
                $pool_name = $old_pledges->first()->source == "Pool" ? $old_pledges->first()->region->name : '';

                return view('donations.partials.bi-pledge-detail-modal',
                        compact('type', 'year', 'frequency', 'pool_name', 'pledge_amt', 'number_of_periods',
                                    'total_amount', 'old_pledges', 'donate_today_pledge') )->render();

            } else {


                if ($request->donation_type == 'Annual') {

                    $pledge = Pledge::where('id', $request->id)->first();

                    $year = $pledge->campaign_year->calendar_year;
                    $frequency = $request->frequency;
                    $pledge_amt = $request->frequency == 'One-Time' ? $pledge->one_time_amount : $pledge->pay_period_amount;
                    $number_of_periods = $pledge->campaign_year->number_of_periods;
                    $total_amount = $request->frequency == 'One-Time' ? $pledge->one_time_amount : ($pledge->pay_period_amount * $campaign_year->number_of_periods) ;

                    return view('donations.partials.pledge-detail-modal',
                            compact('year', 'frequency', 'pledge_amt', 'number_of_periods', 'total_amount', 'pledge') )->render();

                } elseif ($request->donation_type == 'Donate Now') {

                    $pledge = DonateNowPledge::where('id', $request->id)->first();

                    $year = $request->yearcd;
                    $frequency = $request->frequency;
                    $pledge_amt = $pledge->one_time_amount;
                    $total_amount = $pledge->one_time_amount;

                    return view('donations.partials.donate-now-pledge-detail-modal',
                            compact('year', 'frequency', 'pledge_amt', 'total_amount', 'pledge') )->render();

                } elseif ($request->donation_type == 'Special Campaign') {
                    // Special Campaign - Detail

                    $pledge = SpecialCampaignPledge::where('id', $request->id)->first();

                    $year = $request->yearcd;
                    $frequency = $request->frequency;
                    $pledge_amt = $pledge->one_time_amount;
                    $total_amount = $pledge->one_time_amount;
                    $check_dt = $pledge->deduct_pay_from;

                    // $special_campaign = SpecialCampaign::where('id', $pledge->special_campaign_id)->first();
                    $in_support_of = $pledge ? $pledge->special_campaign->charity->charity_name : '';
                    $special_campaign_name = $pledge ? $pledge->special_campaign->name : '';

                    return view('donations.partials.special-campaign-pledge-detail-modal',
                            compact('year', 'frequency', 'pledge_amt',
                                    'in_support_of', 'special_campaign_name', 'check_dt',
                                        'total_amount', 'pledge') )->render();

                } elseif ($request->donation_type == 'Event') {
                    // Event - Detail

                    $pledge = BankDepositForm::where('id', $request->id)->first();

                    $year = $pledge->created_at->format('Y');
                    $frequency = $request->frequency;
                    $pledge_amt = $pledge->deposit_amount;
                    $total_amount = $pledge->deposit_amount;

                    return view('donations.partials.event-detail-modal',
                            compact('year', 'frequency', 'pledge_amt', 'total_amount', 'pledge') )->render();

                    return 'to be developed'                    ;
                }

            }
        } else {
            return redirect('/');
        }

    }

}

?>
