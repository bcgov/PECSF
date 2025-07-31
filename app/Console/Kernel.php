<?php

namespace App\Console;

use App\Models\Pledge;
use App\Models\CampaignYear;
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

                $schedule->command('command:ExportPledgesToPSFT')
                        ->name('ExportPledgesToPSFT:1')
                        ->dailyAt('00:50')->environments(['prod'])->sendOutputTo(storage_path('logs/ExportPledgesToPSFT.log'))
                        ->onOneServer();

                // Note: The export processes are only execute in TEST and Production (1000 per hour limiation on ODS)                 
                $schedule->command('command:ExportPledgesToPSFT')
                         ->when(function () {
                             return Pledge::hasDataToSend();
                         })
                         ->name('ExportPledgesToPSFT:2')
                        ->dailyAt('01:50')->environments(['prod'])->appendOutputTo(storage_path('logs/ExportPledgesToPSFT.log'))
                        ->onOneServer();
                $schedule->command('command:ExportPledgesToPSFT')
                        ->when(function () {
                                return Pledge::hasDataToSend();
                        })
                        ->name('ExportPledgesToPSFT:3')
                        ->dailyAt('02:50')->environments(['prod'])->appendOutputTo(storage_path('logs/ExportPledgesToPSFT.log'))
                        ->onOneServer();
                $schedule->command('command:ExportPledgesToPSFT')
                        ->when(function () {
                                return Pledge::hasDataToSend();
                        })
                        ->name('ExportPledgesToPSFT:4')
                        ->dailyAt('03:50')->environments(['prod'])->appendOutputTo(storage_path('logs/ExportPledgesToPSFT.log'))
                        ->onOneServer();
                $schedule->command('command:ExportPledgesToPSFT')
                        ->when(function () {
                                return Pledge::hasDataToSend();
                        })
                        ->name('ExportPledgesToPSFT:5')
                        ->dailyAt('04:50')->environments(['prod'])->appendOutputTo(storage_path('logs/ExportPledgesToPSFT.log'))
                        ->onOneServer();
                $schedule->command('command:ExportPledgesToPSFT')
                        ->when(function () {
                                return Pledge::hasDataToSend();
                        })
                        ->name('ExportPledgesToPSFT:6')
                        ->dailyAt('05:50')->environments(['prod'])->appendOutputTo(storage_path('logs/ExportPledgesToPSFT.log'))
                        ->onOneServer();
                $schedule->command('command:ExportPledgesToPSFT')
                        ->when(function () {
                                return Pledge::hasDataToSend();
                        })
                        ->name('ExportPledgesToPSFT:7')
                        ->dailyAt('06:50')->environments(['prod'])->appendOutputTo(storage_path('logs/ExportPledgesToPSFT.log'))
                        ->onOneServer();                        
                $schedule->command('command:ExportPledgesToPSFT')
                        ->when(function () {
                              return Pledge::hasDataToSend();
                        })
                        ->name('ExportPledgesToPSFT:8')
                        ->dailyAt('7:50')->environments(['prod'])->appendOutputTo(storage_path('logs/ExportPledgesToPSFT.log'))
                        ->onOneServer(); 

                // Non-Production (TEST region)
                $schedule->command('command:ExportPledgesToPSFT')
                        ->dailyAt('08:00')->environments(['TEST'])->sendOutputTo(storage_path('logs/ExportPledgesToPSFT.log'))
                        ->onOneServer(); 


        }

        if (env('TASK_SCHEDULING_OUTBOUND_BI_ENABLED')) 
        {
                // Production region
                $schedule->command('command:ExportDatabaseToBI')
                        ->weekdays()
                        ->at('1:15')
                        ->environments(['prod'])
                        ->sendOutputTo(storage_path('logs/ExportDatabaseToBI.log'))
                        ->onOneServer();

                // Non-Production - TEST region
                $schedule->command('command:ExportDatabaseToBI')
                        ->weekdays()
                        ->at('8:00')
                        ->environments(['TEST'])
                        ->sendOutputTo(storage_path('logs/ExportDatabaseToBI.log'))
                        ->onOneServer();                        

        }

        // **************************
        // *** Inbound  Processes ***
        // **************************
        if (env('TASK_SCHEDULING_INBOUND_ENABLED')) 
        { 

                if (App::environment('prod')) {
                        // Production -- Foundation table
                        $schedule->command('command:ImportPayCalendar')
                                ->weekly()
                                ->mondays()
                                ->at('2:15')
                                //  ->everyFifteenMinutes()
                                ->sendOutputTo(storage_path('logs/ImportPayCalendar.log'))
                                ->onOneServer(); 
                } else {        
                        // Non-Production -- Foundation table                        
                        $schedule->command('command:ImportPayCalendar')
                                ->weekly()
                                ->mondays()
                                ->at('8:15')
                                //  ->everyFifteenMinutes()
                                ->sendOutputTo(storage_path('logs/ImportPayCalendar.log'))
                                ->onOneServer(); 
                }

                // Pledge History Data (refresh the current year +2 when Jan-Mar OR +1 when Apr - Dec
                // $schedule->command('command:ImportNonGovPledgeHistory')
                //         ->dailyAt('2:05')
                //         ->sendOutputTo(storage_path('logs/ImportNonPledgeHistory.log'));

                // $schedule->command('command:ImportPledgeHistory')
                //         ->dailyAt('2:15')
                //         ->sendOutputTo(storage_path('logs/ImportPledgeHistory.log'));    

                if (CampaignYear::isAnnualCampaignOpenNow() || (today()->dayOfWeek == 1)) {

                        if (App::environment('prod')) {
                                // Production - Cities
                                $schedule->command('command:ImportCities')
                                        // ->skip(function () {
                                        //         return (!(CampaignYear::isAnnualCampaignOpenNow())) and (today()->dayOfWeek != 1);
                                        // })
                                        ->weekdays()
                                        ->at('2:30')
                                        ->sendOutputTo(storage_path('logs/ImportCities.log'))
                                        ->onOneServer();  

                                // Production - Department
                                $schedule->command('command:ImportDepartments')
                                        // ->skip(function () {
                                        //         return (!(CampaignYear::isAnnualCampaignOpenNow())) and (today()->dayOfWeek != 1);
                                        // })
                                        ->weekdays()
                                        ->at('2:45')
                                        ->sendOutputTo(storage_path('logs/ImportDepartments.log'))
                                        ->onOneServer(); 
                        } else {

                                // Non-Production - Cities 
                                $schedule->command('command:ImportCities')
                                        // ->skip(function () {
                                        //         return (!(CampaignYear::isAnnualCampaignOpenNow())) and (today()->dayOfWeek != 1);
                                        // })
                                        ->weekdays()
                                        ->at('8:30')
                                        ->sendOutputTo(storage_path('logs/ImportCities.log'))
                                        ->onOneServer(); 

                                // Non-Production - Departments
                                $schedule->command('command:ImportDepartments')
                                        // ->skip(function () {
                                        //         return (!(CampaignYear::isAnnualCampaignOpenNow())) and (today()->dayOfWeek != 1);
                                        // })
                                        ->weekdays()
                                        ->at('8:45')
                                        ->sendOutputTo(storage_path('logs/ImportDepartments.log'))
                                        ->onOneServer();
                        }
                }

                // For testing purpose: to generate 2022 pledges based on the BI pledge history
                // $schedule->command('command:GeneratePledgeFromHistory')
                //         ->dailyAt('3:00');                    
                
                // Demography data and user profiles
                if (App::environment('prod')) {

                        // Non-Production -- Demography data and user profiles                        
                        $schedule->command('command:ImportEmployeeJob')
                                ->weekdays()
                                ->at('4:00')
                                ->sendOutputTo(storage_path('logs/ImportEmployeeJob.log'))
                                ->onOneServer(); 

                        $schedule->command('command:SyncUserProfile')
                                ->weekdays()
                                ->at('4:30')
                                ->sendOutputTo(storage_path('logs/SyncUserProfile.log'))
                                ->onOneServer(); 
                } else {
                        // Non-Production -- Demography data and user profiles                        
                        $schedule->command('command:ImportEmployeeJob')
                                ->weekdays()
                                ->at('9:00')
                                ->sendOutputTo(storage_path('logs/ImportEmployeeJob.log'))
                                ->onOneServer(); 

                        $schedule->command('command:SyncUserProfile')
                                ->weekdays()
                                ->at('9:15')
                                ->sendOutputTo(storage_path('logs/SyncUserProfile.log'))
                                ->onOneServer();   
                }

        }

        // Snapshot of eligible employees 
        $schedule->command('command:UpdateEligibleEmployeeSnapshot')
                ->dailyAt('4:45')
                ->appendOutputTo(storage_path('logs/UpdateEligibleEmployeeSnapshot.log'))
                ->onOneServer(); 

        $schedule->command('command:UpdateDailyCampaign')
                ->dailyAt('5:00')
                ->appendOutputTo(storage_path('logs/UpdateDailyCampaign.log'))
                ->onOneServer(); 
        
        $schedule->command('command:SystemCleanUp')
                ->dailyAt('5:30')
                ->appendOutputTo(storage_path('logs/SystemCleanUp.log'))
                ->onOneServer();

        // Monitoring Process Queue every 5 mins 
        // $schedule->command('command:queueStatus')
        //         ->everyFiveMinutes()
        //         ->between('7:00', '19:00')
        //         ->appendOutputTo(storage_path('logs/queueStatus.log'));

        // Daily Testing 
        $schedule->command('notify:daily')
                ->dailyAt('08:30')
                ->appendOutputTo(storage_path('logs/daily.log'))
                ->onOneServer(); 

                
        // This is for testing purpose on lower resgions
        $schedule->exec("echo Schedule Task to verify un on one server every 15 mins - " . now() . PHP_EOL )
          ->name('Test onOneServer task')
          ->environments(['local', 'dev','TEST'])
          ->everyFifteenMinutes()
          ->onOneServer();        

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
