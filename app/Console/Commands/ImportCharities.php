<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Imports\CharitiesImport;
use App\Imports\CharityURLsImport;
use App\Imports\CharityOngoingProgramsImport;
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
    protected $description = 'Command used to import the charities detail files downloaded from CRA website';

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
        Excel::import(new CharitiesImport, 'database/seeds/Charities_results_2022-03-05-19-06-03.txt');
        echo now() . PHP_EOL;
        Excel::import(new CharityURLsImport, 'database/seeds/weburl_2019.csv');
        echo now() . PHP_EOL;
        Excel::import(new CharityOngoingProgramsImport, 'database/seeds/new_ongoing_programs_2019.csv');
        echo now() . PHP_EOL;

        return 0;
    }
}
