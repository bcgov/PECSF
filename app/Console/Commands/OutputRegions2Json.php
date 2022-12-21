<?php

namespace App\Console\Commands;

use App\Models\Region;
use Illuminate\Console\Command;

class OutputRegions2Json extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:OutputRegions2Json';

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

        // Regions
        $regions = Region::orderBy('code')->get();
               
        $path = storage_path('app/uploads/regions.json');
        $sql = file_put_contents($path, json_encode($regions, JSON_PRETTY_PRINT) );

        return 0;
    }
}
