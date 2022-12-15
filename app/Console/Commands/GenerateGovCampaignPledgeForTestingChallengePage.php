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
use Illuminate\Console\Command;
use App\Models\ScheduleJobAudit;
use Illuminate\Support\Facades\DB;
use App\Models\PledgeHistorySummary;

class GenerateGovCampaignPledgeForTestingChallengePage extends Command
{
     
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:GenerateGovCampaignPledgeForTestingChallengePage 
                            {created_date : The date of the pledge create e.g. 2021-09-28 } 
                            {to_campaign_year : The target campaign year e.g 2022 }';

    protected $message;
    protected $status;
    protected $created_date;
    protected $to_campaign_year;
  
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
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        // Validate argument 
        $d = DateTime::createFromFormat('Y-m-d', $this->argument('created_date'));
        if (!($d && $d->format('Y-m-d') === $this->argument('created_date'))) {
            echo "Invalid to date (YYYY-MM-DD)";
            exit;
        };

        if (!(is_numeric($this->argument('to_campaign_year')))) {
            echo "Invalid to campaign year ";
            exit;
        }

        // Passed validation
        $this->created_date = Carbon::createFromFormat('Y-m-d', $this->argument('created_date'));
        $this->to_campaign_year = $this->argument('to_campaign_year');

        $this->task = ScheduleJobAudit::Create([
            'job_name' => $this->signature,
            'start_time' => Carbon::now(),
            'status' => 'Processing',
        ]);

        // Main Process
        $this->LogMessage( now() );    
        $this->LogMessage("Step - 1 : Generate Gov Annual Campaigm Pledges from History Data");
        $this->LogMessage("From date of creation : " . $this->created_date);
        $this->LogMessage("To Campaign Year      : " . $this->to_campaign_year);

        $this->generateGovAnnualCampaign();


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
        $created_date = $this->created_date->format('Y-m-d'); 
        $to_year = $this->to_campaign_year + 1;
        $from_year = $this->created_date->year + 1;   

        $sql = PledgeHistorySummary::select('GUID', 'yearcd', 'source', 'campaign_type', 
                            DB::raw("case when source = 'P' then region else null end as region"),
                            DB::raw("sum(case when frequency = 'Bi-Weekly' then pledge else 0 end) as pay_period_amount"),
                            DB::raw("sum(case when frequency = 'One-time' then pledge else 0 end) as one_time_amount"),
                            DB::raw("sum(pledge) as goal_amount"),
                            DB::raw("min(pledge_history_id) as pledge_history_id")
                        )
                        ->where('yearcd', $from_year )
// ->whereIn('GUID', ['0AB321049CB54AEEB12681E8F3FF6404', '0B4B78061F394658831DC2150C01AA70'])
                        ->where('campaign_type','Annual')
                        ->whereExists(function ($query) use($created_date) {
                                    $query->select(DB::raw(1))
                                            ->from('pledge_histories')
                                            ->whereColumn('pledge_histories.id', 'pledge_history_summaries.pledge_history_id')
                                            ->where('pledge_histories.created_date', $created_date);
                        })
                        ->groupBy('GUID', 'yearcd', 'source', 'campaign_type')
                        ->orderBy('GUID');

        $campaign_year = CampaignYear::where('calendar_year', $to_year )->first();

// dd([ $sql->toSql(), $sql->getBindings(), $campaign_year->calendar_year, count($sql->get()) ] )        ;        

        // Chucking
        $row_count = 0;
        $error_count = 0;

        $sql->chunk(100, function($chuck) use( $campaign_year, &$created_count, &$updated_count, &$no_change_count, &$row_count, &$error_count, &$n) {
            $this->LogMessage( "Processing batch (100) - " . ++$n );

            foreach($chuck as $bi_pledge) {

                $user = User::where('source_type', 'HCM')->where('guid', $bi_pledge->GUID )->first();

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
                                       ->where('regions.name', '=', $bi_pledge->region )
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
                    'user_id' => $user->id,
                    'campaign_year_id' => $campaign_year->id,
                ],[
                    // 'first_name',
                    // 'last_name',
                    // 'city',
                    'type' => $bi_pledge->source,

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
                    $this->LogMessage('    New record : '. json_encode( $pledge ) );

                } elseif ($pledge->wasChanged() ) {

                    $updated_count += 1;

                    $this->LogMessage('(UPDATED) => ID | ' . $pledge->id . ' | ' . $pledge->organization_id . ' | ' . $pledge->user_id . ' | ' . $pledge->campaign_year->calendar_year );
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
                
                if ($bi_pledge->source == 'P') {
                    // No action required
                } else {

                    $bi_pledge_charites = PledgeHistory::where('GUID', $bi_pledge->GUID)
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

    protected function validate($bi_pledge) {

        $valid = true;

        $user = User::where('source_type', 'HCM')->where('guid', $bi_pledge->GUID )->first();
        if (!$user) {
            $valid = false;
        }

        if ( $bi_pledge->source == 'P') {

            $pool = FSPool::join('regions', 'regions.id', 'f_s_pools.region_id')
                            ->where('regions.name', '=', $bi_pledge->region )
                            ->first();
            if (!$pool) {
                $valid = false;
            }

        } else {

            $charity = Charity::where('registration_number', $bi_pledge->details->first()->charity_bn)->first();
            if (!$charity) {
                echo $bi_pledge->details->first()->vendor_bn . PHP_EOL;
                $vendor_charity = Charity::where('registration_number', $bi_pledge->details->first()->vendor_bn)->first();
    
                if (!$vendor_charity) {
                    $valid = false;
                }
            }

        }
        
        if (!($valid)) {
            echo 'Record: ' . json_encode( $bi_pledge->only(['id', 'pledge_history_id', 'GUID', 'yearcd', 'source', 'campaign_type', 'frequency', 'region']) ) . PHP_EOL;
        }

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

    protected function LogMessage($text) 
    {

        $this->info( $text );

        // write to log message 
        $this->message .= $text . PHP_EOL;

        // $this->task->message = $this->message;
        // $this->task->save();
        
    }

}
