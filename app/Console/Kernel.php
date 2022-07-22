<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        
        Commands\ImportEmployeeJob::class,
        Commands\SyncUserProfile::class,
        Commands\ExportDatabaseToBI::class,
        
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('generate:report')->hourly();
        // $schedule->command('que')->everyMinute();

        $schedule->command('command:ExportDatabaseToBI')
                 ->weekdays()
                 ->at('4:00');

        $schedule->command('command:ImportEmployeeJob')
                 ->weekdays()
                 ->at('5:00');

        $schedule->command('command:SyncUserProfile')
                 ->weekdays()
                 ->at('5:30');

        // Donor statitsics for challenge pages                  
        $schedule->command('command:DonorHistoryDataFromBI')
                 ->weekdays()
                 ->at('6:00');      
                 
        $schedule->command('command:ImportEligibleEmployees')
                 ->weekdays()
                 ->at('6:10');      
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
