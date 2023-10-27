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

        // **************************
        // *** Outbound Processes ***
        // **************************

        if (env('TASK_SCHEDULING_OUTBOUND_PSFT_ENABLED')) 
        { 
                // Note: The export processes are only execute in TEST and Production  
                $schedule->command('command:ExportPledgesToPSFT')
                        ->dailyAt('1:00')
                        ->environments(['TEST', 'prod'])
                        ->appendOutputTo(storage_path('logs/ExportPledgesToPSFT.log'));
        }

        if (env('TASK_SCHEDULING_OUTBOUND_BI_ENABLED')) 
        {
                $schedule->command('command:ExportDatabaseToBI')
                        ->weekdays()
                        ->at('1:15')
                        ->environments(['TEST', 'prod'])
                        ->appendOutputTo(storage_path('logs/ExportDatabaseToBI.log'));

        }

        // **************************
        // *** Inbound  Processes ***
        // **************************
        if (env('TASK_SCHEDULING_INBOUND_ENABLED')) 
        { 

                // Foundation table
                $schedule->command('command:ImportPayCalendar')
                        ->weekdays()
                        ->at('2:00')
                        //  ->everyFifteenMinutes()
                        ->sendOutputTo(storage_path('logs/ImportPayCalendar.log'));
                        

                // Pledge History Data (refresh the current year +2 when Jan-Mar OR +1 when Apr - Dec
                // $schedule->command('command:ImportNonGovPledgeHistory')
                //         ->dailyAt('2:05')
                //         ->sendOutputTo(storage_path('logs/ImportNonPledgeHistory.log'));

                // $schedule->command('command:ImportPledgeHistory')
                //         ->dailyAt('2:15')
                //         ->sendOutputTo(storage_path('logs/ImportPledgeHistory.log'));    

                $schedule->command('command:ImportCities')
                        //  ->yearlyOn(9, 1, '02:30')
                        ->dailyAt('2:30')
                        ->sendOutputTo(storage_path('logs/ImportCities.log')); 

                $schedule->command('command:ImportDepartments')
                        ->dailyAt('2:35')
                        ->sendOutputTo(storage_path('logs/ImportDepartments.log'));

                // For testing purpose: to generate 2022 pledges based on the BI pledge history
                // $schedule->command('command:GeneratePledgeFromHistory')
                //         ->dailyAt('3:00');                    
                
                // Demography data and user profiles
                $schedule->command('command:ImportEmployeeJob')
                        ->weekdays()
                        ->at('4:00')
                        ->sendOutputTo(storage_path('logs/ImportEmployeeJob.log'));

                $schedule->command('command:SyncUserProfile')
                        ->weekdays()
                        ->at('4:15')
                        ->sendOutputTo(storage_path('logs/SyncUserProfile.log'));

        }

        // Snapshot of eligible employees 
        $schedule->command('command:UpdateEligibleEmployeeSnapshot')
                ->dailyAt('4:30')
                ->appendOutputTo(storage_path('logs/UpdateEligibleEmployeeSnapshot.log'));

        $schedule->command('command:UpdateDailyCampaign')
                ->dailyAt('4:45')
                ->appendOutputTo(storage_path('logs/UpdateDailyCampaign.log'));
        
        $schedule->command('command:SystemCleanUp')
                ->dailyAt('5:30')
                ->appendOutputTo(storage_path('logs/SystemCleanUp.log'));
        
        // Daily Testing 
        $schedule->command('notify:daily')
                ->dailyAt('08:30')
                ->appendOutputTo(storage_path('logs/daily.log'));

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
