<?php

namespace App\Http\Controllers\Api;

use Exception;
use DateTimeZone;
use Carbon\Carbon;
use App\Models\Setting;
use Cron\CronExpression;
use App\Models\CampaignYear;
use Illuminate\Http\Request;
use App\Models\ScheduleJobAudit;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Console\Scheduling\Schedule;

class SystemStatusController extends Controller
{
    //
    public function queueStatus(Request $request) {

        // Run the command
        $cmd_result = shell_exec('ps -eF');

        $status = "@@@ Failure @@@ -- The background process queue is not functioning.";
        if (str_contains( strtolower($cmd_result), 'artisan queue:work')) {
            $status = "running";
        }

        // checking queue
        $queues = DB::table(config('queue.connections.database.table'))->get();

        $jobs = [];
        if($queues) {

            foreach($queues as $queue) {

                $t = Carbon::parse($queue->available_at);
                $t->setTimezone('America/Vancouver');

                // if queue up more than 5 minutes
                if ( (!($queue->reserved_at)) && Carbon::now()->diffInSeconds($t) > 300) {

                    $payload = json_decode($queue->payload, true);
                    array_push($jobs, "The background queue process ". $payload['displayName'] . " submitted on " . $t . " has been in the queue for more than 5 minutes.");
                    
                }
            }

        }

        $result = [
            'queue status' => $status,
            'now' => now()->format('Y-m-d H:i:s'),
            'jobs' => $jobs,
        ];

        if (str_contains($status, 'Failure')) {
            $result['server'] = shell_exec('cat /proc/sys/kernel/hostname');
        }

        return response()->json( $result, 200, [], JSON_PRETTY_PRINT);

    }

    public function scheduleStatus(Request $request) {

        // Get the current tasks 
        new \App\Console\Kernel(app(), new Dispatcher());
        $schedule = app(Schedule::class);

        // set timezone
        $timezone = new DateTimeZone( config('app.timezone'));

        $count_fail = 0;
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
            if ($job_name == 'command:ImportCities' || $job_name == 'command:ImportDepartments') {
                $cy = CampaignYear::where('calendar_year', today()->year + 1)->first();
                $last_change_date = $cy ? $cy->updated_at->startOfDay()->copy()->addDay(1)->addHours(3) : null;

                if ( (now() > $last_change_date ) && (CampaignYear::isAnnualCampaignOpenNow() || (today()->dayOfWeek == 1))) {
                    // it should be processed 
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

            // 2 mins grace period for the schedule job start 
            if (now() >= $previousDueDate && now() <= $previousDueDate->copy()->addMinutes(2)) {
                $status = 'Pending -- The schedule job should start soon';
            } else {
                $audit = ScheduleJobAudit::where('job_name', 'like', $job_name . '%')
                            ->whereBetween('start_time', [$previousDueDate, $previousDueDateUpto])
                            ->first();

                if ($audit) {
                    $status = "Success -- The last run id: " . $audit->id . " | " . $audit->job_name . " | " . $audit->status .
                            " | start at " . $audit->start_time . " - end at " . $audit->end_time;
                } else {
                    $status = '@@@ Failure @@@ -- The previous schedule did not run';
                    $count_fail++;
                }
            }

            $tasks[] = [
                'name' => $job_name,
                'cron' => $event->expression,
                'environments' => $event->environments,

                // $event->description,
                'previous schedule time' => $previousDueDate->format('Y-m-d H:i:s'),
                'status' => $status,
                'next schedule time' => (new CronExpression($event->expression))
                            ->getNextRunDate(Carbon::now())
                            ->setTimezone( $timezone )->format('Y-m-d H:i:s'),
            ];
        }

        return response()->json([
            'failure task count' => $count_fail,
            'now' => now()->format('Y-m-d H:i:s'),
            'tasks' => $tasks,
        ], 200, [], JSON_PRETTY_PRINT);

    }

    public function databaseStatus(Request $request) 
    {

        $status = "running";
        $uptime = null;
        try {
            $setting = Setting::first();

            // Calculate UpTime
            $result = DB::select("SHOW GLOBAL STATUS LIKE 'Uptime'");
            $s = $result ? round($result[0]->Value) : null;
            $uptime = sprintf('%d day(s), %d hour(s), %d minute(s) and %d second(s)', $s/86400, round($s/3600) %24, round($s/60) %60, $s%60);

            if ($s < 300) {
                $status = "@@@ Failure @@@ -- The database was recently restarted, less than 5 minutes ago.";    
            }

        } catch (Exception $ex) {
            $status = "@@@ Failure @@@ -- " . $ex->getMessage();
        }       

        return response()->json([
            'database status' => $status,
            'up time' => $uptime,
            'now' => now()->format('Y-m-d H:i:s'),

        ], 200, [], JSON_PRETTY_PRINT);

    }

}