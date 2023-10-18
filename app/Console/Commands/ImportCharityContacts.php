<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Imports\CharitiesImport;
use App\Models\ScheduleJobAudit;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CharityContactsImport;

class ImportCharityContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ImportCharityContacts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command is used to import the charity contact information';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        echo now() . PHP_EOL;   

        $this->task = ScheduleJobAudit::Create([
            'job_name' => $this->signature,
            'start_time' => now(),
            'status' => 'Processing',
        ]);

        try {
            Excel::import(new CharityContactsImport($this->task->id), 'database/seeds/1_PECSF_VENDOR_TABLE.xlsx');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            
            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
        }    

        return 0;
    }
}
