<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Imports\CharitiesImport;
use Maatwebsite\Excel\Facades\Excel;

class ImportCharities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ImportCharities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command is used to import the charity detail files downloaded from CRA website';

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
        try {
            Excel::import(new CharitiesImport, 'database/seeds/Charities_results_2022-12-18-17-21-02.txt');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            
            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
        }    
        echo now() . PHP_EOL;

        return 0;
    }
}
