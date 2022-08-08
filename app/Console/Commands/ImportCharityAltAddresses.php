<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\ScheduleJobAudit;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CharityAltAddressesImport;


class ImportCharityAltAddresses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ImportCharityAltAddresses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command is used to import the charity alternative address from xlsx file';


    /* Shared variables */
    protected $message;
    protected $status;

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




        $import = new CharityAltAddressesImport();
        $import->import('database/seeds/1_PECSF_VENDOR_TABLE_as Jul 25.xlsx');

        $this->LogMessage('job_name : ' . $this->signature );
        $this->LogMessage("Updating Charity's Alternative Address and Contact");
        $this->LogMessage("");

        $this->LogMessage("Exceptional found :");
        $this->LogMessage("");

            
        foreach ($import->failures() as $failure) {
            $text = 'Row : ' . $failure->row(); // row that went wrong
            $text .= ' - ' . $failure->attribute(); // either heading key (if using heading row concern) or column index
            $text .= ' - ' . implode(', ', $failure->errors()) ; // Actual error messages from Laravel validator
            $values = $failure->values();
            unset( $values['comment'] );
            $text .= ' - ' . implode(', ', $values ); // The values of the row that has failed.

            $this->LogMessage( $text );        
        }


        // Update the Task Audit log
        $this->task->end_time = Carbon::now();
        $this->task->status = $this->status;
        $this->task->message = $this->message;
        $this->task->save();

        return 0;
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
