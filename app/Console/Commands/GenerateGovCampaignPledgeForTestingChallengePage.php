<?php

namespace App\Console\Commands;

use DateTime;
use Carbon\Carbon;
use App\Models\User;
use App\Models\FSPool;
use App\Models\Pledge;
use App\Models\Charity;
use App\Models\CampaignYear;
use App\Models\PledgeCharity;
use App\Models\PledgeHistory;
use App\Models\BankDepositForm;
use Illuminate\Console\Command;
use App\Models\ScheduleJobAudit;
use Illuminate\Support\Facades\DB;
use App\Models\PledgeHistorySummary;
use App\Models\BankDepositFormOrganizations;

class GenerateGovCampaignPledgeForTestingChallengePage extends Command
{
     
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:GenerateGovCampaignPledgeForTestingChallengePage'; 
    // protected $signature = 'command:GenerateGovCampaignPledgeForTestingChallengePage 
    //                         {created_date : The date of the pledge create e.g. 2021-09-28 } 
    //                         {to_campaign_year : The target campaign year e.g 2022 }';

    protected $message;
    protected $status;
    // protected $created_date;
    protected $to_campaign_year;
    protected $start_date, $end_date;
  
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To generate annual campaign pledge for testing challenge page ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->message = '';
        $this->status = 'Completed';

        $this->created_date = null;
        $this->to_campaign_year = null;

        $this->start_date = '2021-09-01';
        $this->end_date =   '2021-11-05';
        $this->to_campaign_year = 2023;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        // // Validate argument 
        // $d = DateTime::createFromFormat('Y-m-d', $this->argument('created_date'));
        // if (!($d && $d->format('Y-m-d') === $this->argument('created_date'))) {
        //     echo "Invalid to date (YYYY-MM-DD)";
        //     exit;
        // };

        // if (!(is_numeric($this->argument('to_campaign_year')))) {
        //     echo "Invalid to campaign year ";
        //     exit;
        // }

        // // Passed validation
        // $this->created_date = Carbon::createFromFormat('Y-m-d', $this->argument('created_date'));
        // $this->to_campaign_year = $this->argument('to_campaign_year');

        $this->task = ScheduleJobAudit::Create([
            'job_name' => $this->signature,
            'start_time' => Carbon::now(),
            'status' => 'Processing',
        ]);

        // Main Process
        $this->LogMessage("From start date  of creation : " . $this->start_date);
        $this->LogMessage("     end  date of creation   : " . $this->end_date);
        $this->LogMessage("To Campaign Year             : " . $this->to_campaign_year);

        $this->LogMessage( now() );    
        $this->LogMessage("Step - 1 : Generate Gov Annual Campaigm Pledges from History Data");
        $this->generateGovAnnualCampaign();

        $this->LogMessage( '' );  
        $this->LogMessage( now() );    
        $this->LogMessage("Step - 2 : Generate Government Event Pledge");
        $this->generateEventPledge();
        


        // Update the Task Audit log
        $this->task->end_time = Carbon::now();
        $this->task->status = $this->status;
        $this->task->message = $this->message;
        $this->task->save();

        return 0;
    }

    protected function generateGovAnnualCampaign()  {

        $n = 0;
        $created_count = 0;   
        $updated_count = 0;
        $no_change_count = 0;

        // Parameters
        // $created_date = $this->created_date->format('Y-m-d'); 
        $start_date = $this->start_date; 
        $end_date = $this->end_date; 
        $to_year = $this->to_campaign_year + 1;
        // $from_year = $this->created_date->year + 1;   
        $from_year = substr($this->start_date,0,4) + 1;

        $sql = PledgeHistorySummary::select('emplid', 'yearcd', 'source', 'campaign_type', 
                            DB::raw("case when source = 'P' then region else null end as region"),
                            DB::raw("sum(case when frequency = 'Bi-Weekly' then per_pay_amt else 0 end) as pay_period_amount"),
                            DB::raw("sum(case when frequency = 'One-time' then pledge else 0 end) as one_time_amount"),
                            DB::raw("sum(pledge) as goal_amount"),
                            DB::raw("min(pledge_history_id) as pledge_history_id")
                        )
                        ->where('yearcd', $from_year )
// ->whereIn('GUID', ['0AB321049CB54AEEB12681E8F3FF6404', '0B4B78061F394658831DC2150C01AA70'])
                        ->where('campaign_type','Annual')
                        ->whereExists(function ($query) use($start_date, $end_date) {
                                    $query->select(DB::raw(1))
                                            ->from('pledge_histories')
                                            ->whereColumn('pledge_histories.id', 'pledge_history_summaries.pledge_history_id')
                                            ->whereBetween('pledge_histories.created_date', [$start_date, $end_date]);
                        })
                        ->groupBy('emplid', 'yearcd', 'source', 'campaign_type')
                        ->orderBy('emplid');

        $campaign_year = CampaignYear::where('calendar_year', $to_year )->first();

// dd([ $sql->toSql(), $sql->getBindings(), $campaign_year->calendar_year, count($sql->get()) ] )        ;        

        // Chucking
        $row_count = 0;
        $error_count = 0;

        $sql->chunk(100, function($chuck) use( $campaign_year, &$created_count, &$updated_count, &$no_change_count, &$row_count, &$error_count, &$n) {
            $this->LogMessage( "Processing batch (100) - " . ++$n );

            foreach($chuck as $bi_pledge) {

                // $user = User::where('source_type', 'HCM')->where('guid', $bi_pledge->GUID )->first();
                $user = User::where('source_type', 'HCM')->where('emplid', $bi_pledge->emplid )->orderby('id')->first();

                if(!empty($user)){
                    if (!($user->acctlock == 0)) {
                        continue;
                    }
                }


                $row_count += 1;
                $message = '';

                $valid = $this->validate($bi_pledge);
                if (!($valid)) {
                    $error_count += 1;
                    continue;
                }
          
                $pool = null;
                if ( $bi_pledge->source == 'P') {
                    $pool = FSPool::join('regions', 'regions.id', 'f_s_pools.region_id')
                                       ->where('regions.code', '=', $bi_pledge->region )
                                       ->select('f_s_pools.*')
                                       ->first();
                }

                // For generate the changes
                $old_pledge = Pledge::where('organization_id',  $user->organization_id)
                                    ->where('user_id', $user->id)
                                    ->where('campaign_year_id', $campaign_year->id)
                                    ->first();                

                $pledge = Pledge::updateOrCreate([
                    'organization_id' => $user->organization_id,
                    'emplid' => $user->emplid,
                    // 'user_id' => $user->id,
                    'campaign_year_id' => $campaign_year->id,
                ],[
                    // 'first_name',
                    // 'last_name',
                    // 'city',
                    'user_id' => $user->id,
                    'type' => $bi_pledge->source,

                    'region_id' => $bi_pledge->source == 'P' ? $pool->region_id : 0,
                    'f_s_pool_id' => $bi_pledge->source == 'P' ? $pool->id : 0,

                    'one_time_amount' => $bi_pledge->one_time_amount,
                    'pay_period_amount' => $bi_pledge->pay_period_amount,
                    'goal_amount' => $bi_pledge->goal_amount,

                    'ods_export_status' => null,
                    'ods_export_at' => null,

                ]); 

                if ($pledge->wasRecentlyCreated) {

                    $created_count += 1;

                    $this->LogMessage('(CREATED) => ID | ' . $pledge->id . ' | ' . $pledge->organization_id . ' | ' . $pledge->user_id . ' | ' . $pledge->campaign_year->calendar_year );
                    // $this->LogMessage('    New record : '. json_encode( $pledge ) );

                } elseif ($pledge->wasChanged() ) {

                    $updated_count += 1;

                    $this->LogMessage('(UPDATED) => ID | ' . $pledge->id . ' | ' . $pledge->organization_id . ' | ' . $pledge->user_id . ' | ' . $pledge->campaign_year->calendar_year );
                    // $changes = $pledge->getChanges();
                    // unset($changes["updated_at"]);

                    // $original = array_intersect_key($old_pledge->toArray(),$changes);
                    // $this->LogMessage('  summary => ' );
                    // $this->LogMessage('      original : '. json_encode( $original ) );
                    // $this->LogMessage('      change   : '. json_encode( $changes ) );

                } else {
                    // No Action
                    $no_change_count += 1;
                }

                // echo json_encode($pledge) . PHP_EOL;

                // set the created_at matched with original date but difference year
                $bi_pledge_detail = $bi_pledge->first_detail;
                $t = $bi_pledge_detail->created_date;
                $pledge->created_at = ($campaign_year->calendar_year - 1) . substr($t, 4);
                $pledge->save(['timestamps' => false]);

                
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

        // $created_date = $this->created_date;
        $start_date = $this->start_date; 
        $end_date = $this->end_date; 
        $to_year = $this->to_campaign_year + 1;
        $from_year = substr($this->start_date,0,4) + 1;

        $sql = PledgeHistorySummary::select('emplid', 'yearcd', 'source', 'campaign_type', 
                        'event_type', 'event_sub_type', 'event_deposit_date',
                            DB::raw("sum(pledge) as goal_amount"),
                            DB::raw("min(pledge_history_id) as pledge_history_id")
                        )
                        // ->where('yearcd', $this->yearcd)
                        ->where('yearcd', $from_year )
// ->whereIn('GUID', ['0AB321049CB54AEEB12681E8F3FF6404', '0B4B78061F394658831DC2150C01AA70'])
                        ->whereExists(function ($query) use($start_date, $end_date) {
                                    $query->select(DB::raw(1))
                                            ->from('pledge_histories')
                                            ->whereColumn('pledge_histories.id', 'pledge_history_summaries.pledge_history_id')
                                            // ->where('pledge_histories.created_date', '<=', $created_date);
                                            ->whereBetween('pledge_histories.created_date', [$start_date, $end_date]);

                        })
                        ->where('campaign_type','Event')
                        ->where('emplid', '<>', 0)
                        ->groupBy( 'emplid', 'yearcd', 'source', 'campaign_type',
                                    'event_type', 'event_sub_type', 'event_deposit_date')
                        ->orderBy('emplid');
        
        $campaign_year = CampaignYear::where('calendar_year', $to_year )->first();
        

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

                $t = $bi_pledge_detail->event_deposit_date;
                $new_event_deposit_date = ($campaign_year->calendar_year - 1) . substr($t, 4);

                $old_pledge = BankDepositForm::where('organization_code',  'GOV')
                                    ->where('form_submitter_id', 999)
                                    ->where('bc_gov_id', $bi_pledge->emplid)
                                    ->where('pecsf_id', null)
                                    ->where('event_type', $event_type)
                                    ->where('sub_type', $sub_type)
                                    // ->where('deposit_date', $bi_pledge->event_deposit_date)
                                    ->where('deposit_date', $new_event_deposit_date)
                                    ->first();         

                // determine the change based on diffrence created_at and created_at
// dd([$old_pledge->created_at, $bi_pledge_detail->created_date
//                 , $old_pledge->created_at->toDateString()
//                 , $old_pledge->created_at->toDateString() == $bi_pledge_detail->created_date]);
                $t = $bi_pledge_detail->created_date;
                $new_created_at = ($campaign_year->calendar_year - 1) . substr($t, 4);

                if ((!$old_pledge) || (!($old_pledge->created_at->toDateString() == $new_created_at))) {                    

                    $pledge = BankDepositForm::updateOrCreate([
                            'organization_code' => 'GOV',
                            'form_submitter_id' => 999,
                            'bc_gov_id' => $bi_pledge->emplid,
                            'pecsf_id' => null,
                            'event_type' => $event_type, 
                            'sub_type' => $sub_type,
                            // 'deposit_date' => $bi_pledge->event_deposit_date,
                            'deposit_date' => $new_event_deposit_date,
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
                    if (!($pledge->created_at == $new_created_at)) {
                        $pledge->created_at = $new_created_at;
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
                                                    //->where('event_deposit_date', $bi_pledge->event_deposit_date)
                                                    ->where('event_deposit_date', $new_event_deposit_date)
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



    protected function validate($bi_pledge) {

        $valid = true;

        $user = User::where('source_type', 'HCM')->where('emplid', $bi_pledge->emplid )->first();
        if (!$user) {
            $valid = false;
            $this->LogMessage('   Exception -- User not found - ' . $bi_pledge->emplid  . ')' );
        }

        if ( $bi_pledge->source == 'P') {

            $pool = FSPool::join('regions', 'regions.id', 'f_s_pools.region_id')
                            ->where('regions.code', '=', $bi_pledge->region )
                            ->first();
            if (!$pool) {
                $valid = false;
                $this->LogMessage('   Exception -- Fund Support Pool not found - ' . $bi_pledge->region . ' (id - ' . $bi_pledge->id . ')' );
                echo 'Record: ' . json_encode( $bi_pledge->only(['id', 'pledge_history_id', 'emplid', 'yearcd', 'source', 'campaign_type', 'frequency', 'region']) ) . PHP_EOL;
            }

        } else {

            $bi_pledge_charities = PledgeHistory::where('emplid', $bi_pledge->emplid)
                                            ->where('yearcd', $bi_pledge->yearcd)
                                            ->where('source', 'Non-Pool')
                                            ->where('campaign_type', $bi_pledge->campaign_type)
                                            ->where('event_type', $bi_pledge->event_type)
                                            ->where('event_sub_type', $bi_pledge->event_sub_type)
                                            // ->where('event_deposit_date', $bi_pledge->event_deposit_date)
                                            // ->where('event_deposit_date', $new_event_deposit_date)
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
        
        // if (!($valid)) {
        //     echo 'Record: ' . json_encode( $bi_pledge->only(['id', 'pledge_history_id', 'emplid', 'yearcd', 'source', 'campaign_type', 'frequency', 'region']) ) . PHP_EOL;
        // }

        return $valid;


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
