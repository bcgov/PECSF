<?php

namespace App\Http\Controllers\Api;

use DateTimeZone;
use Carbon\Carbon;
use Cron\CronExpression;
use Illuminate\Http\Request;
use App\Models\ScheduleJobAudit;
use Illuminate\Events\Dispatcher;
use App\Http\Controllers\Controller;
use Illuminate\Console\Scheduling\Schedule;

class SystemStatusController extends Controller
{
    //
    public function queueStatus(Request $request) {

        // Run the command
        $result = shell_exec('ps -eF');

        $status = "Failure -- The background process queue is not functioning.";
        if (str_contains( strtolower($result), 'artisan queue:work')) {
            $status = "running";
        }

        return response()->json([
            'queue status' => $status,
        ], 200);

    }

    public function scheduleStatus(Request $request) {

        // Get the current tasks 
        new \App\Console\Kernel(app(), new Dispatcher());
        $schedule = app(Schedule::class);

        // set timezone
        $timezone = new DateTimeZone( config('app.timezone'));

        $last_job_name = '';   
        foreach ($schedule->events() as $event) {

            $job_name = explode(" ", $event->command)[2];

            // SKIP -- serveral jobs which the history doesn't logged in audit table
            if ($job_name == 'command:queueStatus' || $job_name == 'notify:daily') {
                continue;
            }

            // SPECIAL -- job "command:ExportPledgesToPSFT"
            if ($job_name == $last_job_name && $job_name == 'command:ExportPledgesToPSFT') {
                continue;
            }
            $last_job_name = $job_name;

            // SPECIAL -- only run on Monday and weekdays if annual campaign period is open
            if ($job_name == 'command:ImportCities' || $job_name == 'command;ImportDepartments') {
                if (CampaignYear::isAnnualCampaignOpenNow() || (today()->dayOfWeek == 1)) {
                    // normal 
                } else {
                    continue;
                }
            }

            // check environments
            if ($event->environments && !(in_array( env('APP_ENV'), $event->environments)) ) {
                continue;
            }

            $previousDueDate = Carbon::instance(
                (new CronExpression($event->expression))
                    ->getPreviousRunDate(Carbon::now()->setTimezone($event->timezone), allowCurrentDate: true)
                    ->setTimezone($timezone)
            );
            $previousDueDateUpto = $previousDueDate->copy()->addMinutes(5)->format('Y-m-d H:i:s');

            $audit = ScheduleJobAudit::where('job_name', 'like', $job_name . '%')
                        ->whereBetween('start_time', [$previousDueDate, $previousDueDateUpto])
                        ->where('status', 'Completed')
                        ->first();

            if ($audit) {
                $status = 'Success';
            } else {
                $status = 'Failure -- The previous schedule did not run or fail.';
            }


            $rows[] = [
                'name' => $job_name,
                'cron' => $event->expression,
                'environments' => $event->environments,

                // $event->description,
                'previous' => $previousDueDate->format('Y-m-d H:i:s'),
                'status' => $status,
                'next' => (new CronExpression($event->expression))
                            ->getNextRunDate(Carbon::now())
                            ->setTimezone( $timezone )->format('Y-m-d H:i:s'),
            ];
        }

        return response()->json( $rows, 200);

    }
}
