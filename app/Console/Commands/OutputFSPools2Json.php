<?php

namespace App\Console\Commands;

use App\Models\FSPool;
use Illuminate\Console\Command;

class OutputFSPools2Json extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:OutputFSPools2Json';

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
        $pools = FSPool::with('region', 'charities', 'charities.charity')
                ->orderBy('start_date')
                ->get()->sortBy(function($pool) { 
                    return $pool->region->name;
               });

        $path = storage_path('app/uploads/f_s_pools.json');
        $sql = file_put_contents($path, json_encode($pools, JSON_PRETTY_PRINT) );

        return 0;

    }
}
