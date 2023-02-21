<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\EmployeeJob;
use App\Models\BusinessUnit;
use App\Models\Organization;
use Illuminate\Console\Command;
use App\Models\ScheduleJobAudit;
use App\Models\ElligibleEmployee;
use Illuminate\Support\Facades\DB;

class SetEligibleEmployeeBUCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:SetEligibleEmployeeBUCount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To capture a snapshot of the Eligible Employee BU count';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->message = '';
        $this->status = 'Completed';
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->task = ScheduleJobAudit::Create([
            'job_name' => $this->signature,
            'start_time' => Carbon::now(),
            'status' => 'Processing',
        ]);

        $this->LogMessage( now() );
        $this->LogMessage("Task -- Capture a snapshot of Eligible Employee BU count");
        $this->SetEligibleEmployeeBUCount();
        $this->LogMessage( now() );

        // Update the Task Audit log
        $this->task->end_time = Carbon::now();
        $this->task->status = $this->status;
        $this->task->message = $this->message;
        $this->task->save();
    
        return 0;
    }

    protected function SetEligibleEmployeeBUCount()
    {

        
        $total_count = 0;
        $created_count = 0;
        $updated_count = 0;

        $as_of_date = today()->format('Y-m-d');
        $year = today()->year;

        $bu_groups = EmployeeJob::where( function($query) {
                                $query->where('employee_jobs.empl_rcd', '=', function($q) {
                                        $q->from('employee_jobs as J2') 
                                            ->whereColumn('J2.emplid', 'employee_jobs.emplid')
                                            ->whereNull('date_deleted')
                                            // ->where('J2.empl_status', 'A')
                                            ->selectRaw('min(J2.empl_rcd)');
                                    })
                                    ->orWhereNull('employee_jobs.empl_rcd');
                            })
                            ->where('employee_jobs.empl_status', 'A')
                            ->whereNull('date_deleted')
                    ->select('business_unit', 'organization_id', DB::raw('count(*) as ee_cnt'))
                    ->groupBy('business_unit', 'organization_id')
                    ->get();

        if ($bu_groups->count() > 0) {
            // clean up the old data before insert if exists
            ElligibleEmployee::where('as_of_date', $as_of_date)->delete();   
        }

        foreach($bu_groups as $row) {
            $total_count++;

            $org = Organization::where('id', $row->organization_id)->where('effdt','<=', $as_of_date)->orderBy('effdt', 'desc')->first();
            $bu = BusinessUnit::where('code', $row->business_unit)->where('effdt','<=', $as_of_date)->orderBy('effdt', 'desc')->first();

            $ee = ElligibleEmployee::create([
                'as_of_date' => $as_of_date,
                'ee_count' => $row->ee_cnt,
                'business_unit' => $row->business_unit,
                'business_unit_name' => $bu ? $bu->name : null,
                'cde' => $org->code,
                'year' => $year
            ]);

            $created_count++;
            $this->LogMessage('   (CREATED) => ' . $ee->toJson() );

            if (!$bu) {
                $this->LogMessage('** Warning ** -- Business Unit ' . $row->business_unit . ' not found.');
            }

        }

        $this->LogMessage('');
        $this->LogMessage('Total created count : ' . $created_count  );
     
    }

    protected function LogMessage($text)
    {

        $this->info( $text );

        // write to log message
        $this->message .= $text . PHP_EOL;

        $this->task->message = $this->message;
        $this->task->save();

    }

}
