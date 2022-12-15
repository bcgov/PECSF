<?php

namespace App\Console\Commands;

use App\Models\Organization;
use Illuminate\Console\Command;

class OutputOrganizations2Json extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:OutputOrganizations2Json';

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
        // Fund Support Pools 
        $orgs = Organization::orderBy('code')->get();
               
        $path = database_path('seeds/organizations.json');
        $sql = file_put_contents($path, json_encode($orgs, JSON_PRETTY_PRINT) );

        return 0;

    }
}
