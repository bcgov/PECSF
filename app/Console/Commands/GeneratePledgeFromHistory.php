<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\FSPool;
use App\Models\Pledge;
use App\Models\Region;
use App\Models\Charity;
use App\Models\EmployeeJob;
use App\Models\BusinessUnit;
use App\Models\CampaignYear;
use App\Models\Organization;
use App\Models\PledgeCharity;
use App\Models\PledgeHistory;
use App\Models\BankDepositForm;
use App\Models\DonateNowPledge;
use Illuminate\Console\Command;
use App\Models\ScheduleJobAudit;
use Illuminate\Support\Facades\DB;
use App\Models\NonGovPledgeHistory;
use App\Models\PledgeHistorySummary;
use App\Models\NonGovPledgeHistorySummary;
use App\Models\BankDepositFormOrganizations;

class GeneratePledgeFromHistory extends Command
{
     
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:generatePledgeFromHistory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To generate pledge data from bolt-on history data';

    protected $message;
    protected $status;
    protected $yearcd;
    protected $created_date;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->validation_pass  = true;

        $this->yearcd       = '2022';
        $this->created_date = '2022-12-31';
        
        $this->message = '';
        $this->status = 'Completed';
        
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->task = ScheduleJobAudit::Create([
            'job_name' => $this->signature,
            'start_time' => Carbon::now(),
            'status' => 'Processing',
        ]);
        
        $this->LogMessage("The criteria for pledges to be generated: ");
        $this->LogMessage("  Campaign Year       : " . $this->yearcd);
        $this->LogMessage("  Up to creation date : " . $this->created_date);
        $this->LogMessage("");

        $this->LogMessage( now() );
        $this->LogMessage("Step - 1A : Verify Government History Data");
        $this->verifyHistoryData();

        $this->LogMessage( now() );
        $this->LogMessage("Step - 1B : Verify Non-Government History Data");
        $this->verifyNonGovHistoryData();

        if ($this->validation_pass) {
            // Steps -- Gov pledges History

            $this->LogMessage( now() );    
            $this->LogMessage("Step - 2A : Generate Gov Annual Campaign Pledge");
            $this->generateAnnualCampaign();

            $this->LogMessage( now() );    
            $this->LogMessage("Step - 2B : Generate Government Donate Today Pledge");
            $this->generateDonateNowPledge();

            $this->LogMessage( now() );    
            $this->LogMessage("Step - 2C : Generate Government Event Pledge");
            $this->generateEventPledge();

            // Steps -- Nov Gov pledges History
            $this->LogMessage( now() );    
            $this->LogMessage("Step - 3A : Generate Non-Gov Annual Campaign Pledge");
            $this->generateNonGovAnnualCampaign();

            $this->LogMessage( now() );    
            $this->LogMessage("Step - 3B : Generate Non-Gov Event Pledge");
            $this->generateNonGovEventPledge();

        } else {

            $this->LogMessage( 'Verificaton completed but exceptional found. No pledges were created!' ); 
        }

        // Update the Task Audit log
        $this->task->end_time = Carbon::now();
        $this->task->status = $this->status;
        $this->task->message = $this->message;
        $this->task->save();

        return 0;
    }


    protected function verifyHistoryData()  {

        $n = 0;

        $sql = PledgeHistorySummary::where('yearcd', $this->yearcd)
                        // ->DonateTodayType()
                        ->orderBy('emplid')
                        ->orderBy('pledge_history_id');        

        // Chucking
        $row_count = 0;
        $error_count = 0;

        $sql->chunk(100, function($chuck) use( &$row_count, &$error_count, &$n) {
            $this->LogMessage( "Validating batch (100) - " . ++$n );

            foreach($chuck as $bi_pledge) {

                $row_count += 1;
                $valid = true;

                $campaign_year = CampaignYear::where('calendar_year', $this->yearcd)->first();
                if (!$campaign_year) {
                    $valid = false;
                    $this->LogMessage('   Exception -- Campaign Year not defined in the system - ' . $bi_pledge->yearcd . ' (id - ' . $bi_pledge->id . ')');
                }


                $user = User::where('source_type', 'HCM')->where('emplid', $bi_pledge->emplid )->orderby('id')->first();
                if (!$user) {
                    if (!$bi_pledge->campaign_type == 'Event') {
                        $valid = false;
                        $this->LogMessage('   Exception -- User not found    - ' . $bi_pledge->emplid . ' (id - ' . $bi_pledge->id . ')');
                    }
                }

                // $charity = Charity::where('registration_number', $bi_pledge->first_detail->charity_bn)->first();

                // if (!$charity) {

                //     $vendor_charity = Charity::where('registration_number', $bi_pledge->first_detail->vendor_bn)->first();

                //     if (!$vendor_charity) {
                //         $valid = false;
                //         // $this->LogMessage('Record: ' . json_encode( $bi_pledge->only(['id', 'pledge_history_id', 'GUID', 'yearcd', 'source', 'campaign_type', 'frequency', 'region'])) );
                //         $this->LogMessage('   Exception -- Charity not found - ' . $bi_pledge->first_detail->charity_bn . ' (id - ' . $bi_pledge->id . ')' );
                //     }
                // }

                if ( $bi_pledge->source == 'P') {

                    $pool = FSPool::join('regions', 'regions.id', 'f_s_pools.region_id')
                                       ->where('regions.code', '=', $bi_pledge->region )
                                       ->select('f_s_pools.*')
                                       ->first();

                    if (!$pool) {
                        $valid = false;
                        $this->LogMessage('   Exception -- FS Pool not found - ' . $bi_pledge->region . ' (id - ' . $bi_pledge->id . ')' );
                    }

                } else {

                    $bi_pledge_charities = PledgeHistory::where('emplid', $bi_pledge->emplid)
                                                ->where('yearcd', $bi_pledge->yearcd)
                                                ->where('source', 'Non-Pool')
                                                ->where('campaign_type', $bi_pledge->campaign_type)
                                                ->where('event_type', $bi_pledge->event_type)
                                                ->where('event_sub_type', $bi_pledge->event_sub_type)
                                                ->where('event_deposit_date', $bi_pledge->event_deposit_date)
                                                ->orderBy('frequency')
                                                ->get();

                    foreach($bi_pledge_charities as  $bi_pledge_charity)  {

                        $charity = $this->getCharity($bi_pledge_charity->charity_bn, $bi_pledge_charity->vendor_bn );
                        // $charity = Charity::where('registration_number', $bi_pledge_charity->charity_bn)->first();

                        if (!$charity) {
        
                            $valid = false;
                            // $this->LogMessage('Record: ' . json_encode( $bi_pledge->only(['id', 'pledge_history_id', 'GUID', 'yearcd', 'source', 'campaign_type', 'frequency', 'region'])) );
                            $this->LogMessage('   Exception -- Charity not found - ' . $bi_pledge_charity->charity_bn . ' (id - ' . $bi_pledge_charity->id . ')' );

                        }

                    }

                }


                if (!($valid)) {
                    $error_count++ ;
                    $this->validation_pass = false;
                }

            }

        });

        $this->LogMessage('Verified rows '. $row_count );
        $this->LogMessage('Exception rows '. $error_count );


    }


    protected function verifyNonGovHistoryData()  {

        $n = 0;

        $sql = nonGovPledgeHistorySummary::where('yearcd', $this->yearcd)
                        ->orderByRaw('org_code, emplid, pecsf_id')
                        ->orderBy('pledge_history_id');        
        
        // Chucking
        $row_count = 0;
        $error_count = 0;

        $sql->chunk(100, function($chuck) use( &$row_count, &$error_count, &$n) {
            $this->LogMessage( "Validating batch (100) - " . ++$n );

            foreach($chuck as $bi_pledge) {

                $row_count += 1;
                $valid = true;


                $org = Organization::where('code', $bi_pledge->org_code)->first();

                if (!$org) {
                    $valid = false;
                    $this->LogMessage('   Exception -- Organization not found - ' . $bi_pledge->org_code . ' (id - ' . $bi_pledge->id . ')' );
                }

                $region = Region::where('code', $bi_pledge->region)->first();
                if ($bi_pledge->region && (!$region)) {
                    $valid = false;
                    $this->LogMessage('   Exception -- Region not found - ' . $bi_pledge->tgb_reg_district . ' (id - ' . $bi_pledge->id . ')' );
                }

                $bu = BusinessUnit::where('code', $bi_pledge->business_unit)->first();
                if ($bu && (!($bu))) {
                    $valid = false;
                    $this->LogMessage('   Exception -- Business Unit not found - ' . $bi_pledge->business_unit . ' (id - ' . $bi_pledge->id . ')' );
                }
                

                // $charity = Charity::where('registration_number', $bi_pledge->first_detail->charity_bn)->first();
                // if (!$charity) {

                //     // echo $bi_pledge->first_detail->vendor_bn . PHP_EOL;
                //     $vendor_charity = Charity::where('registration_number', $bi_pledge->first_detail->vendor_bn)->first();

                //     if (!$vendor_charity) {
                //         $valid = false;
                //         // $this->LogMessage('Record: ' . json_encode( $bi_pledge->only(['id', 'pledge_history_id', 'org_code','emplid','pecsf_id', 'yearcd', 'source', 'pledge_type', 'frequency', 'region'])) );
                //         $this->LogMessage('   Exception -- Charity not found - ' . $bi_pledge->first_detail->charity_bn . ' (id - ' . $bi_pledge->id . ')' );
                //     }
                // }

                if ( $bi_pledge->source == 'P') {

                    $pool = FSPool::join('regions', 'regions.id', 'f_s_pools.region_id')
                                       ->where('regions.code', '=', $bi_pledge->region )
                                       ->select('f_s_pools.*')
                                       ->first();
                    if (!$pool) {
                        $valid = false;
                        $this->LogMessage('   Exception -- FS Pool not found - ' . $bi_pledge->region . ' (id - ' . $bi_pledge->id . ')' );
                    }

                } else {

                    $bi_pledge_charities = NonGovPledgeHistory::where('emplid', $bi_pledge->emplid)
                                                ->where('pecsf_id', $bi_pledge->pecsf_id)
                                                ->where('yearcd', $bi_pledge->yearcd)
                                                ->where('source', 'Non-Pool')
                                                ->where('pledge_type', $bi_pledge->pledge_type)
                                                ->where('event_type', $bi_pledge->event_type)
                                                ->where('event_sub_type', $bi_pledge->event_sub_type)
                                                ->where('event_deposit_date', $bi_pledge->event_deposit_date)
                                                ->orderBy('frequency')
                                                ->get();

                    foreach($bi_pledge_charities as  $bi_pledge_charity)  {

                        $charity = $this->getCharity($bi_pledge_charity->charity_bn, $bi_pledge_charity->vendor_bn );
                        // $charity = Charity::where('registration_number', $bi_pledge_charity->charity_bn)->first();

                        if (!$charity) {
        
                            $valid = false;
                            // $this->LogMessage('Record: ' . json_encode( $bi_pledge->only(['id', 'pledge_history_id', 'GUID', 'yearcd', 'source', 'campaign_type', 'frequency', 'region'])) );
                            $this->LogMessage('   Exception -- Charity not found - ' . $bi_pledge_charity->charity_bn . ' (id - ' . $bi_pledge_charity->id . ')' );

                        }

                    }

                }


                if (!($valid)) {
                    $error_count++ ;
                    $this->validation_pass = false;
                }

            }

        });

        $this->LogMessage('Verified rows '. $row_count );
        $this->LogMessage('Exception rows '. $error_count );

    }


    protected function generateAnnualCampaign()  {

        /*
        select GUID, yearcd, source, campaign_type, 
            sum(case when frequency = 'Bi-Weekly' then pledge else 0 end) as pay_period_amount,
            sum(case when frequency = 'One-time' then pledge else 0 end) as one_time_amount,
            sum(pledge) as goal_amount
            from pledge_history_summaries 
        where GUID = '0B4B78061F394658831DC2150C01AA70'
            and campaign_type = 'Annual'
            and yearcd = 2022
        group by GUID, yearcd, source, campaign_type;
        */
        $created_date = $this->created_date;

        $sql = PledgeHistorySummary::select('emplid', 'yearcd', 'source', 'campaign_type', 
                            DB::raw("case when source = 'P' then region else null end as region"),
                            DB::raw("sum(case when frequency = 'Bi-Weekly' then per_pay_amt else 0 end) as pay_period_amount"),
                            DB::raw("sum(case when frequency = 'One-time' then pledge else 0 end) as one_time_amount"),
                            DB::raw("sum(pledge) as goal_amount"),
                            DB::raw("min(pledge_history_id) as pledge_history_id")
                        )
                        ->where('yearcd', $this->yearcd)
// ->whereIn('GUID', ['0AB321049CB54AEEB12681E8F3FF6404', '0B4B78061F394658831DC2150C01AA70'])
                        ->whereExists(function ($query) use($created_date) {
                                    $query->select(DB::raw(1))
                                            ->from('pledge_histories')
                                            ->whereColumn('pledge_histories.id', 'pledge_history_summaries.pledge_history_id')
                                            ->where('pledge_histories.created_date', '<=', $created_date);
                        })
                        ->where('campaign_type','Annual')
                        ->groupBy('emplid', 'yearcd', 'source', 'campaign_type')
                        ->orderBy('emplid');
        
        $campaign_year = CampaignYear::where('calendar_year', $this->yearcd)->first();
        

        // Chucking
        $n = 0;
        $created_count = 0;   
        $updated_count = 0;
        $row_count = 0;
        $error_count = 0;
        $no_change_count = 0;

        $sql->chunk(100, function($chuck) use( $campaign_year, &$created_count, &$updated_count, &$no_change_count, &$row_count, &$error_count, &$n) {
            $this->LogMessage( "Processing batch (100) - " . ++$n );

            foreach($chuck as $bi_pledge) {

                $row_count += 1;

                $user = User::where('source_type', 'HCM')
                        ->where('emplid', $bi_pledge->emplid )->orderby('id')->first();
          
                // $charity = Charity::where('registration_number', $bi_pledge->first_detail->charity_bn)->first();

                // if (!$charity) {
                //     echo $bi_pledge->first_detail->vendor_bn . PHP_EOL;
                //     $charity = Charity::where('registration_number', $bi_pledge->first_detail->vendor_bn)->first();
                // }
                $bi_pledge_detail = $bi_pledge->first_detail;

                $pool = null;
                if ( $bi_pledge->source == 'P') {

                    $pool = FSPool::join('regions', 'regions.id', 'f_s_pools.region_id')
                                       ->where('regions.code', '=', $bi_pledge->region )
                                       ->select('f_s_pools.*')                                       
                                       ->first();
                }
//  echo ( json_encode([ $bi_pledge->GUID, $user->id])) . PHP_EOL;

                $old_pledge = Pledge::where('organization_id',  $user->organization_id)
                                                ->where('emplid', $user->emplid)
                                                ->where('campaign_year_id', $campaign_year->id)
                                                ->first();       

                // determine the change based on diffrence created_at and created_at
// dd([$old_pledge->created_at, $bi_pledge_detail->created_date
//                 , $old_pledge->created_at->toDateString()
//                 , $old_pledge->created_at->toDateString() == $bi_pledge_detail->created_date]);

                if ((!$old_pledge) 
                            || (!($old_pledge->created_at->toDateString() == $bi_pledge_detail->created_date))
                            || (!($old_pledge->pay_period_amount == $bi_pledge->pay_period_amount))) {

                    $pledge = Pledge::updateOrCreate([
                        'organization_id' => $user->organization_id,
                        'emplid' => $user->emplid,
                        // 'user_id' => $user->id,
                        // 'pecsf_id' => 
                        'campaign_year_id' => $campaign_year->id,
                    ],[
                        // 'first_name',
                        // 'last_name',
                        // 'city',
                        'user_id' => $user->id,
                        'type' => $bi_pledge->source,
                        'region_id' => $bi_pledge->source == 'P' ? $pool->region_id : null,
                        'f_s_pool_id' => $bi_pledge->source == 'P' ? $pool->id : 0,

                        'one_time_amount' => $bi_pledge->one_time_amount,
                        'pay_period_amount' => $bi_pledge->pay_period_amount,
                        'goal_amount' => $bi_pledge->goal_amount,

                        'ods_export_status' => 'C',
                        'ods_export_at' => $this->yearcd . '-12-31 00:00:00',

                        'created_at' => $bi_pledge_detail->created_date,
                    ]); 

                    if ($pledge->wasRecentlyCreated) {

                        $created_count += 1;

                        $this->LogMessage('(CREATED) => ID | ' . $pledge->id . ' | ' . $pledge->organization_id . ' | ' . $pledge->user_id . ' | ' . $pledge->user->emplid . ' | ' . $pledge->campaign_year_id );

                    } elseif ($pledge->wasChanged() ) {

                        $updated_count += 1;

                        $this->LogMessage('(UPDATED) => ID | ' . $pledge->id . ' | ' . $pledge->organization_id . ' | ' . $pledge->user_id . ' | ' . $pledge->user->emplid . ' | ' . $pledge->campaign_year_id );
                        $changes = $pledge->getChanges();
                        unset($changes["updated_at"]);

                        $original = array_intersect_key($old_pledge->toArray(),$changes);
                        $this->LogMessage('  summary => ' );
                        $this->LogMessage('      original : '. json_encode( $original ) );
                        $this->LogMessage('      change   : '. json_encode( $changes ) );

                    } else {
                        // No Action
                        $no_change_count += 1;
                    }

                    // Update the created_at to match with created_date when the record already created
                    if (!($pledge->created_at == $bi_pledge_detail->created_date)) {
                        $pledge->created_at = $bi_pledge_detail->created_date;
                        $pledge->save(['timestamps' => false]);
                    }  

                    // echo json_encode($pledge) . PHP_EOL;
                    
                    if ($bi_pledge->source == 'P') {
                        // No action required
                    } else {

                        $bi_pledge_charites = PledgeHistory::where('emplid', $bi_pledge->emplid)
                                                    ->where('yearcd', $bi_pledge->yearcd)
                                                    ->where('source', 'Non-Pool')
                                                    ->where('campaign_type', $bi_pledge->campaign_type)
                                                    ->orderBy('frequency')
                                                    ->get();

                        $pledge->charities()->delete();         

                        foreach ($bi_pledge_charites as $bi_pledge_charity) {
                            
                            $charity = $this->getCharity($bi_pledge_charity->charity_bn, $bi_pledge_charity->vendor_bn );

                            PledgeCharity::create([
                                'charity_id' => $charity->id,
                                'pledge_id' => $pledge->id,
                                'frequency' => strtolower($bi_pledge_charity->frequency), // === 'BiWeekly' ? 'bi-weekly' : 'one-time',
                                'additional' => $bi_pledge_charity->name1,
                                'percentage' => $bi_pledge_charity->percent,
                                'amount' => $bi_pledge_charity->frequency == 'One-Time' ? $bi_pledge_charity->amount : $bi_pledge_charity->per_pay_amt,            // pay per period
                                /* 'cheque_pending' => $multiplier, */
                                'goal_amount' => $bi_pledge_charity->amount,        // amount * 26
                            ]);
                        }

                    }
                
                } else {
                    $no_change_count += 1;
                }
            }

        });

        $this->LogMessage( PHP_EOL );
        $this->LogMessage('Processed rows : '. $row_count );
        $this->LogMessage('Created rows   : '. $created_count );   
        $this->LogMessage('Updated rows   : '. $updated_count );  
        $this->LogMessage('No change rows : '. $no_change_count );  
        $this->LogMessage('Exception rows : '. $error_count );

    }

    protected function generateDonateNowPledge()  {


        $sql = PledgeHistory::where('yearcd', $this->yearcd)
// ->whereIn('GUID', ['846BA510E5724268A34FE8A33CDCA67C', '8DA0BC025EF2499FB07E5A92D8BBDC59'])
                        ->where('campaign_type','Donate Today')
                        ->where('created_date', '<=', $this->created_date)
                        ->orderByRaw('emplid, yearcd, campaign_type, source, vendor_id, additional_info');
        
        $campaign_year = CampaignYear::where('calendar_year', $this->yearcd)->first();
        

        // Chucking
        $n = 0;
        $created_count = 0;   
        $updated_count = 0;
        $row_count = 0;
        $error_count = 0;
        $no_change_count = 0; 
        $last_emplid = '';
        $seqno = 1;

        $sql->chunk(100, function($chuck) use( $campaign_year, &$created_count, &$updated_count, &$no_change_count, &$row_count, &$error_count, &$n, &$last_emplid, &$seqno) {
            $this->LogMessage( "Processing batch (100) - " . ++$n );

            foreach($chuck as $bi_pledge) {

                $row_count += 1;

                $user = User::where('source_type', 'HCM')
                            ->where('emplid', $bi_pledge->emplid )->orderby('id')->first();
          
                // $charity = Charity::where('registration_number', $bi_pledge->first_detail->charity_bn)->first();

                // if (!$charity) {
                //     echo $bi_pledge->first_detail->vendor_bn . PHP_EOL;
                //     $charity = Charity::where('registration_number', $bi_pledge->first_detail->vendor_bn)->first();
                // }

                $pool = null;
                if ( $bi_pledge->source == 'P') {
                    $pool = FSPool::join('regions', 'regions.id', 'f_s_pools.region_id')
                                       ->where('regions.code', '=', $bi_pledge->region )
                                       ->select('f_s_pools.*')
                                       ->first();
                }

                $charity = $this->getCharity($bi_pledge->charity_bn, $bi_pledge->vendor_bn );

//  echo ( json_encode([ $bi_pledge->GUID, $user->id])) . PHP_EOL;

                // Increase the seqno for the same employee (emplid)
                if ($last_emplid == $bi_pledge->emplid) {
                    $seqno += 1;
                } else {
                    $seqno = 1;
                    $last_emplid = $bi_pledge->emplid;
                }

// echo json_encode([ $bi_pledge->GUID, $last_guid, $seqno ]);

                $old_pledge = DonateNowPledge::where('organization_id',  $user->organization_id)
                                    ->where('emplid', $user->emplid)
                                    ->where('yearcd', $bi_pledge->yearcd)
                                    ->where('seqno', $seqno)
                                    ->first();         

                // determine the change based on diffrence created_at and created_at
// dd([$old_pledge->created_at, $bi_pledge->created_date
//                 , $old_pledge->created_at->toDateString()
//                 , $old_pledge->created_at->toDateString() == $bi_pledge->created_date]);

                if ((!$old_pledge) || (!($old_pledge->created_at->toDateString() == $bi_pledge->created_date))) {                    

                    $pledge = DonateNowPledge::updateOrCreate([
                        'organization_id' => $user->organization_id,
                        'emplid'  => $user->emplid,
                        'yearcd'  => $bi_pledge->yearcd,
                        'seqno'   => $seqno,
                    ],[
                        'user_id' => $user->id,
                        'type'    => $bi_pledge->source == 'Pool' ? 'P' : 'C', 
                        'region_id' => $bi_pledge->source == 'P' ? $pool->region_id : 0,
                        'f_s_pool_id' => $bi_pledge->source == 'Pool' ? $pool->id : null,
                        'charity_id' =>  $bi_pledge->source == 'Non-Pool' ? $charity->id : null,
                        'one_time_amount' => $bi_pledge->pledge,
                        'deduct_pay_from' => strtr(substr($bi_pledge->additional_info,18),'/','-'),
                        'special_program' => $bi_pledge->name2,

                        'ods_export_status' => 'C',
                        'ods_export_at' => $this->yearcd . '-12-31 00:00:00',

                        'created_at' => $bi_pledge->created_date,
                    ]);

                    if ($pledge->wasRecentlyCreated) {

                        $created_count += 1;

                        $this->LogMessage('(CREATED) => ID | ' . $pledge->id . ' | ' . $pledge->organization_id . ' | ' . $pledge->user_id . ' | ' . $pledge->user->emplid . ' | ' . $pledge->yearcd . ' | ' .  $pledge->seqno );
                        

                    } elseif ($pledge->wasChanged() ) {

                        $updated_count += 1;

                        $this->LogMessage('(UPDATED) => ID | ' . $pledge->id . ' | ' . $pledge->organization_id . ' | ' . $pledge->user_id . ' | ' . $pledge->user->emplid . ' | ' . $pledge->yearcd . ' | ' .  $pledge->seqno );
                        $changes = $pledge->getChanges();
                        unset($changes["updated_at"]);

                        $original = array_intersect_key($old_pledge->toArray(),$changes);
                        $this->LogMessage('  summary => ' );
                        $this->LogMessage('      original : '. json_encode( $original ) );
                        $this->LogMessage('      change   : '. json_encode( $changes ) );

                    } else {
                        // No Action
                        $no_change_count += 1;
                    }

                    // Update the created_at to match with created_date when the record already created
                    if (!($pledge->created_at == $bi_pledge->created_date)) {
                        $pledge->created_at = $bi_pledge->created_date;
                        $pledge->save(['timestamps' => false]);
                    }  
    // echo json_encode($pledge) . PHP_EOL;

                } else {
                    $no_change_count += 1;
                }

            }

        });

        $this->LogMessage( PHP_EOL );
        $this->LogMessage('Processed rows : '. $row_count );
        $this->LogMessage('Created rows   : '. $created_count );   
        $this->LogMessage('Updated rows   : '. $updated_count );  
        $this->LogMessage('No change rows : '. $no_change_count );  
        $this->LogMessage('Exception rows : '. $error_count );

    }


    protected function generateEventPledge()  {

        $created_date = $this->created_date;

        $sql = PledgeHistorySummary::select('emplid', 'yearcd', 'source', 'campaign_type', 
                        'event_type', 'event_sub_type', 'event_deposit_date',
                            DB::raw("sum(pledge) as goal_amount"),
                            DB::raw("min(pledge_history_id) as pledge_history_id")
                        )
                        ->where('yearcd', $this->yearcd)
// ->whereIn('GUID', ['0AB321049CB54AEEB12681E8F3FF6404', '0B4B78061F394658831DC2150C01AA70'])
                        ->whereExists(function ($query) use($created_date) {
                                    $query->select(DB::raw(1))
                                            ->from('pledge_histories')
                                            ->whereColumn('pledge_histories.id', 'pledge_history_summaries.pledge_history_id')
                                            ->where('pledge_histories.created_date', '<=', $created_date);
                        })
                        ->where('campaign_type','Event')
                        ->where('emplid', '<>', 0)
                        ->groupBy( 'emplid', 'yearcd', 'source', 'campaign_type',
                                    'event_type', 'event_sub_type', 'event_deposit_date')
                        ->orderBy('emplid');
        
        $campaign_year = CampaignYear::where('calendar_year', $this->yearcd)->first();
        

        // Chucking
        $n = 0;
        $created_count = 0;   
        $updated_count = 0;
        $row_count = 0;
        $error_count = 0;
        $no_change_count =0; 

        $sql->chunk(100, function($chuck) use( $campaign_year, &$created_count, &$updated_count, &$no_change_count, &$row_count, &$error_count, &$n) {
            $this->LogMessage( "Processing batch (100) - " . ++$n );

            foreach($chuck as $bi_pledge) {

                $row_count += 1;
                $message = '';

                // $user = User::where('source_type', 'HCM')->where('emplid', $bi_pledge->emplid )->orderby('id')->first();
          
                // $charity = Charity::where('registration_number', $bi_pledge->first_detail->charity_bn)->first();

                // if (!$charity) {
                //     echo $bi_pledge->first_detail->vendor_bn . PHP_EOL;
                //     $charity = Charity::where('registration_number', $bi_pledge->first_detail->vendor_bn)->first();
                // }

                $pool = null;
                if ( $bi_pledge->source == 'P') {

                    $pool = FSPool::join('regions', 'regions.id', 'f_s_pools.region_id')
                                       ->where('regions.code', '=', $bi_pledge->region )
                                       ->select('f_s_pools.*')
                                       ->first();

                }

                $bi_pledge_detail = $bi_pledge->first_detail;
// echo ( json_encode([ $bi_pledge->GUID, $user->id])) . PHP_EOL;

                [$event_type,  $sub_type] = $this->convertEventAndSubType($bi_pledge->event_type, $bi_pledge->event_sub_type);

                $old_pledge = BankDepositForm::where('organization_code',  'GOV')
                                    ->where('form_submitter_id', 999)
                                    ->where('bc_gov_id', $bi_pledge->emplid)
                                    ->where('pecsf_id', null)
                                    ->where('event_type', $event_type)
                                    ->where('sub_type', $sub_type)
                                    ->where('deposit_date', $bi_pledge->event_deposit_date)
                                    ->first();         

                // determine the change based on diffrence created_at and created_at
// dd([$old_pledge->created_at, $bi_pledge_detail->created_date
//                 , $old_pledge->created_at->toDateString()
//                 , $old_pledge->created_at->toDateString() == $bi_pledge_detail->created_date]);

                if ((!$old_pledge) || (!($old_pledge->created_at->toDateString() == $bi_pledge_detail->created_date))) {                    

                    $pledge = BankDepositForm::updateOrCreate([
                            'organization_code' => 'GOV',
                            'form_submitter_id' => 999,
                            'bc_gov_id' => $bi_pledge->emplid,
                            'pecsf_id' => null,
                            'event_type' => $event_type, 
                            'sub_type' => $sub_type,
                            'deposit_date' => $bi_pledge->event_deposit_date,
                        ],[
                            'business_unit' => $bi_pledge_detail->bu->id,
                            'deposit_amount' => $bi_pledge->goal_amount,
                            'description' => $bi_pledge_detail->event_descr,

                            'employment_city' => $bi_pledge_detail->city, // $user->primary_job->office_city,
                            'region_id' => $bi_pledge_detail->region->id,
                            'regional_pool_id' =>  $bi_pledge->source == 'P' ? $bi_pledge_detail->fund_supported_pool->id : null,
                            'address_line_1' => 'Address 1',
                            'address_line_2' => 'Address 2',
                            'address_city' => 'City',
                            'address_province' => 'BC',
                            'address_postal_code' => 'Postal',

                            'approved' => 1,            // Always approved

                            'created_at' => $bi_pledge_detail->created_date,
                        ]);


                    if ($pledge->wasRecentlyCreated) {

                        $created_count += 1;

                        $this->LogMessage('(CREATED) => ID | ' . $pledge->id . ' | ' . $pledge->organization_id . ' | ' . $pledge->bc_gov_id . ' | ' . $pledge->event_type . ' | ' .  $pledge->event_sub_type . ' | ' . $pledge->event_deposit_date );
                        // $this->LogMessage('    New record : '. json_encode( $pledge ) );

                    } elseif ($pledge->wasChanged() ) {

                        $updated_count += 1;

                        $this->LogMessage('(UPDATED) => ID | ' . $pledge->id . ' | ' . $pledge->organization_id . ' | ' . $pledge->bc_gov_id . ' | ' . $pledge->event_type . ' | ' .  $pledge->event_sub_type . ' | ' . $pledge->event_deposit_date );
                        $changes = $pledge->getChanges();
                        unset($changes["updated_at"]);

                        $original = array_intersect_key($old_pledge->toArray(),$changes);
                        $this->LogMessage('  summary => ' );
                        $this->LogMessage('      original : '. json_encode( $original ) );
                        $this->LogMessage('      change   : '. json_encode( $changes ) );

                    } else {
                        // No Action
                        $no_change_count += 1;
                    }

                    // echo json_encode($pledge) . PHP_EOL;
                    // Update the created_at to match with created_date when the record already created
                    if (!($pledge->created_at == $bi_pledge_detail->created_date)) {
                        $pledge->created_at = $bi_pledge_detail->created_date;
                        $pledge->save(['timestamps' => false]);
                    }  
                    
                    if ($bi_pledge->source == 'P') {
                        // No action required
                    } else {

                        // $bi_pledge_charites = $bi_pledge->details->where('source', 'Non-pool');
                        $bi_pledge_charites = PledgeHistory::where('emplid', $bi_pledge->emplid)
                                                    ->where('yearcd', $bi_pledge->yearcd)
                                                    ->where('source', 'Non-Pool')
                                                    ->where('campaign_type', $bi_pledge->campaign_type)
                                                    ->where('event_type', $bi_pledge->event_type)
                                                    ->where('event_sub_type', $bi_pledge->event_sub_type)
                                                    ->where('event_deposit_date', $bi_pledge->event_deposit_date)
                                                    ->orderBy('frequency')
                                                    ->get();

                        BankDepositFormOrganizations::where("bank_deposit_form_id",$pledge->id)->delete();

                        foreach ($bi_pledge_charites as $bi_pledge_charity) {
                            
                            $charity = $this->getCharity($bi_pledge_charity->charity_bn, $bi_pledge_charity->vendor_bn );

                            BankDepositFormOrganizations::create([
                                'bank_deposit_form_id' => $pledge->id,

                                'vendor_id' => $charity->id,
                                'organization_name' => $charity->charity_name,
                                'donation_percent' => $bi_pledge_charity->percent,
                                // 'amount' => $bi_pledge_charity->frequency == 'One-Time' ? $bi_pledge_charity->amount : $bi_pledge_charity->per_pay_amt,            // pay per period
                                /* 'cheque_pending' => $multiplier, */
                                // 'goal_amount' => $bi_pledge_charity->amount,        // amount * 26
                                'specific_community_or_initiative' => $bi_pledge_charity->vendor_name2,
                            ]);
                        }

                    }

                } else {
                    $no_change_count += 1;
                }

            }

        });

        $this->LogMessage( PHP_EOL );
        $this->LogMessage('Processed rows : '. $row_count );
        $this->LogMessage('Created rows   : '. $created_count );   
        $this->LogMessage('Updated rows   : '. $updated_count );  
        $this->LogMessage('No change rows : '. $no_change_count );  
        $this->LogMessage('Exception rows : '. $error_count );

    }



    protected function generateNonGovAnnualCampaign()  {

        $created_date = $this->created_date;

        $sql = NonGovPledgeHistorySummary::select('org_code', 'emplid', 'pecsf_id', 'yearcd', 'source', 'pledge_type', 
                    DB::raw("case when source = 'P' then region else null end as region"),
                    DB::raw("sum(case when frequency = 'Bi-Weekly' then per_pay_amt else 0 end) as pay_period_amount"),
                    DB::raw("sum(case when frequency = 'One-time' then pledge else 0 end) as one_time_amount"),
                    DB::raw("sum(pledge) as goal_amount"),
                    DB::raw("min(pledge_history_id) as pledge_history_id")
                )
                ->where('yearcd', $this->yearcd)
// ->whereIn('GUID', ['0AB321049CB54AEEB12681E8F3FF6404', '0B4B78061F394658831DC2150C01AA70'])
                ->where('pledge_type', 'Annual')
                ->whereExists(function ($query) use($created_date) {
                    $query->select(DB::raw(1))
                            ->from('non_gov_pledge_histories')
                            ->whereColumn('non_gov_pledge_histories.id', 'non_gov_pledge_history_summaries.pledge_history_id')
                            ->where('non_gov_pledge_histories.created_date', '<=', $created_date);
                })
                ->groupBy('org_code', 'emplid', 'pecsf_id', 'yearcd', 'source', 'pledge_type')
                ->orderByRaw('org_code, emplid, pecsf_id');

        $campaign_year = CampaignYear::where('calendar_year', $this->yearcd )->first();
        

        // Chucking
        $n = 0;
        $created_count = 0;   
        $updated_count = 0;
        $no_change_count = 0;
        $row_count = 0;
        $error_count = 0;


        $sql->chunk(100, function($chuck) use( $campaign_year, &$created_count, &$updated_count, &$no_change_count, &$row_count, &$error_count, &$n) {
            $this->LogMessage( "Processing batch (100) - " . ++$n );

            foreach($chuck as $bi_pledge) {

                $row_count += 1;

                $bi_pledge_detail = $bi_pledge->first_detail;
               
                $pool = null;
                if ( $bi_pledge->source == 'P') {

                    $pool = FSPool::join('regions', 'regions.id', 'f_s_pools.region_id')
                                       ->where('regions.code', '=', $bi_pledge->region )
                                       ->select('f_s_pools.*')
                                       ->first();
                }

                $organization = Organization::where('code',$bi_pledge->org_code )->first();

                // For generate the changes
                $old_pledge = Pledge::where('organization_id',  $organization->id)
                                    ->where('user_id', 0)
                                    ->where('pecsf_id', $bi_pledge->pecsf_id)
                                    ->where('campaign_year_id', $campaign_year->id)
                                    ->first();                     
// echo ( json_encode([ $bi_pledge->GUID, $user->id])) . PHP_EOL;
  
                // determine the change based on diffrence created_at and created_at
// dd([$old_pledge->created_at, $bi_pledge_detail->created_date
//                 , $old_pledge->created_at->toDateString()
//                 , $old_pledge->created_at->toDateString() == $bi_pledge_detail->created_date]);

                if ((!$old_pledge) || (!($old_pledge->created_at->toDateString() == $bi_pledge_detail->created_date))
                                || (!($old_pledge->pay_period_amount == $bi_pledge->pay_period_amount)))  {                    

                    $pledge = Pledge::updateOrCreate([
                        'organization_id' => $organization->id,
                        'user_id' => 0,
                        'pecsf_id' => $bi_pledge->pecsf_id,
                        'campaign_year_id' => $campaign_year->id,
                    ],[
                        'first_name' => $bi_pledge_detail->first_name,
                        'last_name' => $bi_pledge_detail->last_name,
                        'city' => $bi_pledge_detail->city,
                        'type' => $bi_pledge->source,
                        'region_id' => $bi_pledge->source == 'P' ? $pool->region_id : 0,
                        'f_s_pool_id' => $bi_pledge->source == 'P' ? $pool->id : 0,

                        'one_time_amount' => $bi_pledge->one_time_amount,
                        'pay_period_amount' => $bi_pledge->pay_period_amount,
                        'goal_amount' => $bi_pledge->goal_amount,

                        'ods_export_status' => 'C',
                        'ods_export_at' => $this->yearcd . '-12-31 00:00:00',

                        'created_at' => $bi_pledge_detail->created_date,
                    ]); 

                    if ($pledge->wasRecentlyCreated) {

                        $created_count += 1;

                        $this->LogMessage('(CREATED) => ID | ' . $pledge->id . ' | ' . $pledge->organization_id . ' | ' . $pledge->user_id . ' | ' . $pledge->campaign_year_id );

                    } elseif ($pledge->wasChanged() ) {

                        $updated_count += 1;

                        $this->LogMessage('(UPDATED) => ID | ' . $pledge->id . ' | ' . $pledge->organization_id . ' | ' . $pledge->user_id . ' | ' . $pledge->campaign_year_id );
                        $changes = $pledge->getChanges();
                        unset($changes["updated_at"]);

                        $original = array_intersect_key($old_pledge->toArray(),$changes);
                        $this->LogMessage('  summary => ' );
                        $this->LogMessage('      original : '. json_encode( $original ) );
                        $this->LogMessage('      change   : '. json_encode( $changes ) );

                    } else {
                        // No Action
                        $no_change_count += 1;
                        //$this->LogMessage(' NO CHANGE : '. json_encode( $pledge ) );
                    }

                    // echo json_encode($pledge) . PHP_EOL;
                    // Update the created_at to match with created_date when the record already created
                    if (!($pledge->created_at == $bi_pledge_detail->created_date)) {
                        $pledge->created_at = $bi_pledge_detail->created_date;
                        $pledge->save(['timestamps' => false]);
                    }  
                    
                    if ($bi_pledge->source == 'P') {
                        // No action required
                    } else {

                        $bi_pledge_charites = NonGovPledgeHistory::where('org_code',  $bi_pledge->org_code)
                                                    ->where('emplid', $bi_pledge->emplid)
                                                    ->where('pecsf_id', $bi_pledge->pecsf_id)
                                                    ->where('yearcd', $bi_pledge->yearcd)
                                                    ->where('source', 'Non-Pool')
                                                    ->where('pledge_type', $bi_pledge->pledge_type)
                                                    ->orderBy('frequency')
                                                    ->get();

                        $pledge->charities()->delete();         

                        foreach ($bi_pledge_charites as $bi_pledge_charity) {
                            
                            $charity = $this->getCharity($bi_pledge_charity->charity_bn, $bi_pledge_charity->vendor_bn );

                            PledgeCharity::create([
                                'charity_id' => $charity->id,
                                'pledge_id' => $pledge->id,
                                'frequency' => strtolower($bi_pledge_charity->frequency), // === 'BiWeekly' ? 'bi-weekly' : 'one-time',
                                'additional' => $bi_pledge_charity->vendor_name2,
                                'percentage' => $bi_pledge_charity->percent,
                                'amount' => $bi_pledge_charity->frequency == 'One-Time' ? $bi_pledge_charity->amount : $bi_pledge_charity->per_pay_amt,            // pay per period
                                /* 'cheque_pending' => $multiplier, */
                                'goal_amount' => $bi_pledge_charity->amount,        // amount * 26
                            ]);
                        }

                    }

                } else {
                    $no_change_count += 1;
                }
            }

        });

        $this->LogMessage( PHP_EOL );
        $this->LogMessage('Processed rows : '. $row_count );
        $this->LogMessage('Created rows   : '. $created_count );   
        $this->LogMessage('Updated rows   : '. $updated_count );  
        $this->LogMessage('No change rows : '. $no_change_count );  
        $this->LogMessage('Exception rows : '. $error_count );

    }


    protected function generateNonGovEventPledge()  {

        $created_date = $this->created_date;

        $sql = NonGovPledgeHistorySummary::select('pledge_history_id', 'org_code', 'emplid', 'pecsf_id', 'yearcd', 'source', 'pledge_type', 
                            'region',
                           'event_type', 'event_sub_type', 'event_deposit_date',
                            DB::raw("sum(pledge) as goal_amount")
                        )
                        ->where('yearcd', $this->yearcd)
// ->whereIn('GUID', ['0AB321049CB54AEEB12681E8F3FF6404', '0B4B78061F394658831DC2150C01AA70'])
                        ->where('pledge_type', 'Event')
                        ->whereExists(function ($query) use($created_date) {
                            $query->select(DB::raw(1))
                                    ->from('non_gov_pledge_histories')
                                    ->whereColumn('non_gov_pledge_histories.id', 'non_gov_pledge_history_summaries.pledge_history_id')
                                    ->where('non_gov_pledge_histories.created_date', '<=', $created_date);
                        })
                        ->groupBy('pledge_history_id', 'org_code', 'emplid', 'pecsf_id', 'yearcd', 'source', 'pledge_type',
                                    'event_type', 'event_sub_type', 'event_deposit_date')
                        ->orderByRaw('org_code, emplid, pecsf_id');

        $campaign_year = CampaignYear::where('calendar_year', $this->yearcd)->first();
        

        // Chucking
        $n = 0;
        $created_count = 0;   
        $updated_count = 0;
        $row_count = 0;
        $error_count = 0;
        $no_change_count =0; 

        $sql->chunk(100, function($chuck) use( $campaign_year, &$created_count, &$updated_count, &$no_change_count, &$row_count, &$error_count, &$n) {
            $this->LogMessage( "Processing batch (100) - " . ++$n );

            foreach($chuck as $bi_pledge) {

                $row_count += 1;

                $pool = null;
                if ( $bi_pledge->source == 'P') {
                    $pool = FSPool::join('regions', 'regions.id', 'f_s_pools.region_id')
                                       ->where('regions.code', '=', $bi_pledge->region )
                                       ->select('f_s_pools.*')
                                       ->first();
                }

                $bi_pledge_detail = $bi_pledge->first_detail;
// echo ( json_encode([ $bi_pledge->GUID, $user->id])) . PHP_EOL;

                [$event_type,  $sub_type] = $this->convertEventAndSubType($bi_pledge->event_type, $bi_pledge->event_sub_type);

                $old_pledge = BankDepositForm::where('organization_code', $bi_pledge->org_code)
                                    ->where('form_submitter_id', 999)
                                    ->where('bc_gov_id', $bi_pledge->emplid)
                                    ->where('pecsf_id', $bi_pledge->pecsf_id)
                                    ->where('event_type', $event_type)
                                    ->where('sub_type', $sub_type)
                                    ->where('deposit_date', $bi_pledge->event_deposit_date)
                                    ->first();         

// dd([$bi_pledge_detail->business_unit, $bi_pledge_detail->bu->id]);

                // determine the change based on diffrence created_at and created_at          
                if ((!$old_pledge) || (!($old_pledge->created_at->toDateString() == $bi_pledge_detail->created_date))) {

                    $pledge = BankDepositForm::updateOrCreate([
                            'organization_code' => $bi_pledge->org_code,
                            'form_submitter_id' => 999,
                            'bc_gov_id' => $bi_pledge->emplid,
                            'pecsf_id' => $bi_pledge->pecsf_id,
                            'event_type' => $event_type,
                            'sub_type' => $sub_type,
                            'deposit_date' => $bi_pledge->event_deposit_date,
                        ],[
                            'business_unit' => $bi_pledge_detail->bu ? $bi_pledge_detail->bu->id : 0,
                            'deposit_amount' => $bi_pledge->goal_amount,
                            'description' => $bi_pledge_detail->event_descr,

                            'employment_city' => $bi_pledge_detail->city,
                            'region_id' => $bi_pledge_detail->region->id,
                            'regional_pool_id' =>  $bi_pledge->source == 'P' && $pool ? $pool->id : null,
                            'address_line_1' => 'Address 1',
                            'address_line_2' => 'Address 2',
                            'address_city' => 'City',
                            'address_province' => 'BC',
                            'address_postal_code' => 'Postal',

                            'approved' => 1,            // Always approved

                            'created_at' => $bi_pledge_detail->created_date,
                        ]);


                    if ($pledge->wasRecentlyCreated) {

                        $created_count += 1;

                        $this->LogMessage('(CREATED) => ID | ' . $pledge->id . ' | ' . $pledge->organization_code . ' | ' . $pledge->bc_gov_id . ' | ' .  $pledge->pecsf_id . ' | ' . $pledge->event_type . ' | ' .  $pledge->sub_type . ' | ' . $pledge->deposit_date );
                        // $this->LogMessage('    New record : '. json_encode( $pledge ) );

                    } elseif ($pledge->wasChanged() ) {

                        $updated_count += 1;

                        $this->LogMessage('(UPDATED) => ID | ' . $pledge->id . ' | ' . $pledge->organization_code . ' | ' . $pledge->bc_gov_id . ' | ' .  $pledge->pecsf_id . ' | ' . $pledge->event_type . ' | ' .  $pledge->sub_type . ' | ' . $pledge->deposit_date );
                        $changes = $pledge->getChanges();
                        unset($changes["updated_at"]);

                        $original = array_intersect_key($old_pledge->toArray(),$changes);
                        $this->LogMessage('  summary => ' );
                        $this->LogMessage('      original : '. json_encode( $original ) );
                        $this->LogMessage('      change   : '. json_encode( $changes ) );

                    } else {
                        // No Action
                        $no_change_count += 1;
                    }

                    // echo json_encode($pledge) . PHP_EOL;
                    // Update the created_at to match with created_date when the record already created
                    if (!($pledge->created_at == $bi_pledge_detail->created_date)) {
                        $pledge->created_at = $bi_pledge_detail->created_date;
                        $pledge->save(['timestamps' => false]);
                    }  
                    
                    if ($bi_pledge->source == 'P') {
                        // No action required
                    } else {

                        // $bi_pledge_charites = $bi_pledge->details->where('source', 'Non-pool');
                        // PledgeHistory::where('GUID', $bi_pledge->GUID)
                        //                             ->where('yearcd', $bi_pledge->yearcd)
                        //                             ->where('source', 'Non-Pool')
                        //                             ->where('campaign_type', $bi_pledge->campaign_type)
                        //                             ->orderBy('frequency')
                        //                             ->get();
                        $bi_pledge_charities = NonGovPledgeHistory::where('emplid', $bi_pledge->emplid)
                                                    ->where('pecsf_id', $bi_pledge->pecsf_id)
                                                    ->where('yearcd', $bi_pledge->yearcd)
                                                    ->where('source', 'Non-Pool')
                                                    ->where('pledge_type', $bi_pledge->pledge_type)
                                                    ->where('event_type', $bi_pledge->event_type)
                                                    ->where('event_sub_type', $bi_pledge->event_sub_type)
                                                    ->where('event_deposit_date', $bi_pledge->event_deposit_date)
                                                    ->orderBy('frequency')
                                                    ->get();

                        BankDepositFormOrganizations::where("bank_deposit_form_id",$pledge->id)->delete();

                        foreach ($bi_pledge_charities as $bi_pledge_charity) {
                            
                            $charity = $this->getCharity($bi_pledge_charity->charity_bn, $bi_pledge_charity->vendor_bn );

                            BankDepositFormOrganizations::create([
                                'bank_deposit_form_id' => $pledge->id,

                                'vendor_id' => $charity->id,
                                'organization_name' => $charity->charity_name,
                                'donation_percent' => $bi_pledge_charity->percent,
                                // 'amount' => $bi_pledge_charity->frequency == 'One-Time' ? $bi_pledge_charity->amount : $bi_pledge_charity->per_pay_amt,            // pay per period
                                /* 'cheque_pending' => $multiplier, */
                                // 'goal_amount' => $bi_pledge_charity->amount,        // amount * 26
                                'specific_community_or_initiative' => $bi_pledge_charity->vendor_name2,
                            ]);
                        }

                    }

                } else {
                    $no_change_count += 1;
                }

            }

        });

        $this->LogMessage( PHP_EOL );
        $this->LogMessage('Processed rows : '. $row_count );
        $this->LogMessage('Created rows   : '. $created_count );   
        $this->LogMessage('Updated rows   : '. $updated_count );  
        $this->LogMessage('No change rows : '. $no_change_count );  
        $this->LogMessage('Exception rows : '. $error_count );

    }    


    protected function getCharity($charity_bn, $vendor_bn) 
    {
            $charity = Charity::where('registration_number', $charity_bn)->first();

            if (!$charity) {
                $charity = Charity::where('registration_number', $vendor_bn)->first();
            }

            return $charity;
    }

    protected function convertEventAndSubType($old_type, $old_sub_type) 
    {
        $new_type = $old_type;
        $new_sub_type = $old_sub_type;

        switch ($old_type) {
            case 'Cash':
                $new_type = 'Cash One-Time Donation';
                $new_sub_type = null;
                break;
            case 'Personal Cheque':
                $new_type = 'Cheque One-Time Donation';
                $new_sub_type = null;
                break;
            case 'Fund Raiser Event':
                $new_type = 'Fundraiser';
                switch ($old_sub_type) {
                    case 'Auctions':
                        $new_sub_type = 'Auction';
                        break;
                    case 'Entertainment':
                        $new_sub_type = 'Entertainment';
                        break;
                    case 'Food':
                        $new_sub_type = 'Food';
                        break;
                    case 'Sports':
                        $new_sub_type = 'Sports';
                        break;
                    case '50/50 Draw':
                        $new_sub_type = '50/50 Draw';
                        break;
                    default:
                        $new_sub_type = 'Other';
                }
                break;
            case 'Gaming':
                $new_type ='Gaming';
                switch ($old_sub_type) {
                    case '50/50 Draw':
                        $new_sub_type = '50/50 Draw';
                        break;
                    default:
                        $new_sub_type = 'Other';
                }
                break;

        }

        return [ $new_type, $new_sub_type ];
    }


    protected function LogMessage($text) 
    {

        $this->info( $text );

        // write to log message 
        $this->message .= $text . PHP_EOL;

        // $this->task->message = $this->message;
        // $this->task->save();
        
    }




}
