<?php

namespace App\Console\Commands;

use stdClass;
use Exception;
use Carbon\Carbon;
use App\Models\ExportAuditLog;
use Illuminate\Console\Command;
use App\Models\ScheduleJobAudit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ExportDatabaseToBI extends Command
{

    protected $db_tables = [
        ['name' => 'business_units',     'delta' => 'updated_at', 'hidden' => null ],
        ['name' => 'campaign_years',     'delta' => 'updated_at', 'hidden' => null ],
        ['name' => 'charities',          'delta' => 'updated_at', 'hidden' => null ],
        ['name' => 'donations',           'delta' => 'updated_at', 'hidden' => null ],
        ['name' => 'f_s_pools',          'delta' => 'updated_at', 'hidden' => null ],
        ['name' => 'f_s_pool_charities', 'delta' => 'updated_at', 'hidden' => ['image'] ],
        ['name' => 'organizations',      'delta' => 'updated_at', 'hidden' => null ],
        ['name' => 'special_campaigns',  'delta' => 'updated_at', 'hidden' => ['image'] ],
        ['name' => 'pledge_charities',   'delta' => 'updated_at', 'hidden' => null ],
        ['name' => 'pledges',            'delta' => 'updated_at', 'hidden' => null ],
        ['name' => 'donate_now_pledges', 'delta' => 'updated_at', 'hidden' => null ],
        ['name' => 'special_campaign_pledges', 'delta' => 'updated_at', 'hidden' => null ],

        ['name' => 'regions',            'delta' => 'updated_at', 'hidden' => null ],
        ['name' => 'users',              'delta' => 'updated_at', 'hidden' => ['password', 'remember_token'] ],

        ['name' => 'bank_deposit_forms',                'delta' => 'updated_at', 'hidden' => null ],
        ['name' => 'bank_deposit_form_organizations',   'delta' => 'updated_at', 'hidden' => null ],
        ['name' => 'bank_deposit_form_attachments',     'delta' => 'updated_at', 'hidden' => null ],
        ['name' => 'volunteers',                        'delta' => 'updated_at', 'hidden' => null ],

    ];
 
    protected $success;
    protected $failure;
    protected $message;
    protected $status;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ExportDatabaseToBI';  

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending Greenfield database to Datawarehouse vis ODS';

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

        $this->success = 0;
        $this->failure = 0;

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        // Main Loop
        foreach ($this->db_tables as $table) {
           $table_name =  $table['name'];
           $delta_field = $table['delta'];
           $hidden_fields = $table['hidden'];

           $this->sendTableDataToDataWarehouse($table_name, $delta_field, $hidden_fields);

        }

        return 0;
    }

    /**
     * Main Function for sending pledges transactions to Datawarehouse.
     *
     * @return int
     */
    private function sendTableDataToDataWarehouse($table_name, $delta_field, $hidden_fields) {
     
        $this->success = 0;
        $this->failure = 0;
        $this->message = '';
        $this->status = 'Completed';
        $n = 0;
        
        // Create the Task Audit log
        $job_name = $this->signature . ':'. $table_name;
        $task = ScheduleJobAudit::Create([
            'job_name' => $job_name,
            'start_time' => Carbon::now(),
            'status' => 'Processing',
        ]);

        $this->task = $task;
         
        // Get the latest success job 
        $last_job = ScheduleJobAudit::where('job_name', $job_name)
                      ->where('status','Completed')
                      ->orderBy('end_time', 'desc')->first();


        $last_start_time = $last_job ? $last_job->start_time : '2000-01-01' ; 

        $this->LogMessage("Table '{$table_name}' Detail to BI (Datawarehouse) start on " . now() );
        $this->Logmessage("");
        $this->Logmessage("Table Name                   : '{$table_name}' ");
        $this->LogMessage("The Last send completed time : " . $last_start_time);
        $this->LogMessage("The schedule Job id          : " . $task->id);
        $this->LogMessage("The command name             : " . $job_name);
        
        // Main Process for each table 
        $sql = DB::table($table_name)
            ->when( $last_job && $delta_field, function($q) use($last_start_time, $delta_field, $hidden_fields ) {
                return $q->where($delta_field, '>=', $last_start_time);
            })
            ->orderBy('id');
            
        // Chucking
        $row_count = 0;
        $sql->chunk(5000, function($chuck) use($task, $table_name, $hidden_fields, $last_job, &$row_count, &$n) {
            $this->LogMessage( "Sending table '{$table_name}' batch (5000) - " . ++$n );

            //$chuck->makeHidden(['password', 'remember_token']);
            if ($hidden_fields) {
                foreach($chuck as $item) {
                    foreach($hidden_fields as $hidden_field) {
                        // unset($item->password);
                        unset($item->$hidden_field);
                    }

                }
            }

            $pushdata = new stdClass();
            $pushdata->table_name = $table_name;
            $pushdata->table_data = json_encode($chuck);
            $pushdata->delta_ind = $last_job ? "1" : "0";
         
            $result = $this->sendData( $pushdata );
            if ($result) {
                // Log to the table
                foreach($chuck as $row)  {
                    ExportAuditLog::create([
                        'schedule_job_name' => $task->job_name,
                        'schedule_job_id' => $task->id,
                        'to_application' => 'BI',
                        'table_name' => $table_name,
                        'row_id' => $row->id,
                        'row_values' => json_encode($row),
                    ]);

                    $row_count += 1;
                }
            }
            
            unset($pushdata);
        });

        $this->LogMessage("" );
        $this->LogMessage("Success (No of Batch ) - " . $this->success);
        $this->LogMessage("Failure (No of Batch)  - " . $this->failure);
        $this->LogMessage("Total No of row sent   - " . $row_count);
        $this->LogMessage("" );
        $this->LogMessage("Table '{$table_name}' data to BI sent completed on " . now() );        
        $this->LogMessage("=========================================================================" );


        // Update the Task Audit log
        $task->end_time = Carbon::now();
        $task->status = $this->status;
        $task->message = $this->message;
        $task->save();

        return 0;

    }
    
    protected function sendData($pushdata) {
        $this->success += 1;
return true;        

        try {

            $response = Http::withBasicAuth(
                env('ODS_USERNAME'),
                env('ODS_TOKEN')
            )->withBody( json_encode($pushdata), 'application/json')
            ->post( env('ODS_OUTBOUND_BULK_UPLOAD_BI_ENDPOINT') );

            if ($response->successful()) {
                $this->success += 1;
                return true;

            } else {

                // log message in system
                $this->status = 'Error';
                $this->LogMessage( $response->status() . ' - ' . $response->body() );

                $this->failure += 1;
                return false;
            }
        
        } catch (\Exception $ex) {

            // log message in system
            $this->status = 'Error';
            $this->LogMessage( $ex->getMessage() );

            throw new Exception($ex);
        }

    }

    protected function LogMessage($text) 
    {

        $this->info( $text );

        // write to log message 
        $this->message .= $text . PHP_EOL;

        // 
        $this->task->message = $this->message;
        $this->task->save();
        
    }

}
