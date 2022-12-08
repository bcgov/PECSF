<?php

namespace App\Console;

use Illuminate\Support\Facades\App;
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
        Commands\DonorHistoryDataFromBI::class,
        Commands\ImportEligibleEmployee::class,
        Commands\ImportPayCalendar::class,
        Commands\ImportCities::class,
        
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

        if (App::environment('production') || App::environment('testing')) {

            $schedule->command('command:ExportDatabaseToBI')
                    ->weekdays()
                    ->at('7:55');

            $schedule->command('command:ExportPledgesToPSFT')
                    ->dailyAt('0:15');

        }

        // Foundation table
        $schedule->command('command:ImportPayCalendar')
                 ->weekdays()
                 ->at('2:00');

        $schedule->command('command:ImportCities')
                 ->yearlyOn(9, 1, '02:30');

        // Demography data and user profiles
        $schedule->command('command:ImportEmployeeJob')
                 ->weekdays()
                 ->at('4:00');

        $schedule->command('command:SyncUserProfile')
                 ->weekdays()
                 ->at('4:15');

        // Donor statitsics for challenge pages                  
        // $schedule->command('command:DonorHistoryDataFromBI')
        //          ->weekdays()
        //          ->at('5:00');      
                 
        // $schedule->command('command:ImportEligibleEmployees')
        //          ->weekdays()
        //          ->at('5:10');      
                 
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
