<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmployeeJob;

class OutputEmployeeJobs2Json extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:OutputEmployeeJobs2Json';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

         // Jobs
         $jobs = EmployeeJob::orderBy('id')->get();
               
        //  $path = database_path('seeds/employee_jobs.json');
         $path = storage_path('app/uploads/employee_jobs.json');
         $sql = file_put_contents($path, json_encode($jobs, JSON_PRETTY_PRINT) );


        return 0;
    }
}
