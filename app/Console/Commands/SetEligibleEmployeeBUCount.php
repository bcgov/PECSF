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
use App\Models\EligibleEmployeeDetail;

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
        $this->storeEligibleEmployee();
        $this->SetEligibleEmployeeBUCount();
        $this->LogMessage( now() );

        // Update the Task Audit log
        $this->task->end_time = Carbon::now();
        $this->task->status = $this->status;
        $this->task->message = $this->message;
        $this->task->save();
    
        return 0;
    }


    protected function StoreEligibleEmployee() 
    {

        $as_of_date = today()->format('Y-m-d');
        $year = today()->year;

        $sql = EmployeeJob::where( function($query) {
                            $query->where('employee_jobs.empl_rcd', '=', function($q) {
                                    $q->from('employee_jobs as J2') 
                                        ->whereColumn('J2.emplid', 'employee_jobs.emplid')
                                        ->whereNull('date_deleted')
                                        ->where('J2.empl_status', 'A')
                                        ->selectRaw('min(J2.empl_rcd)');
                                })
                                ->orWhereNull('employee_jobs.empl_rcd');
                        })
                        ->where('employee_jobs.empl_status', 'A')
                        ->whereNull('date_deleted');
              
        $n = 0;
        $row_count = 0;

        $all_bus = BusinessUnit::current()->orderBy('name')->pluck('name','code');

        $sql->chunk(1000, function($chuck) use($as_of_date, $year, $all_bus, &$row_count, &$n) {
            $this->LogMessage( "Capture snapshot of eligible employees batch (1000) - " . ++$n );

            if ($n == 1) {
                EligibleEmployeeDetail::where('year', $year)->delete();   
            }

            foreach($chuck as $row)  {

                // $bu = BusinessUnit::where('code', $row->business_unit)->where('effdt','<=', $as_of_date)->orderBy('effdt', 'desc')->first();
                $row_count++;

                EligibleEmployeeDetail::create([
                    'year' => today()->year,
                    'as_of_date' => today(),

                    'organization_code' => 'GOV',
                    'emplid' => $row->emplid,
                    'empl_status' => $row->empl_status,
                    'name' => $row->name, 

                    'business_unit' => $row->business_unit,
                    'business_unit_name' => $all_bus->has($row->business_unit) ? $all_bus[$row->business_unit] : null,

                    'deptid' => $row->deptid,
                    'dept_name' => $row->dept_name,

                    'tgb_reg_district' => $row->tgb_reg_district,
                    
                    'office_address1' => $row->office_address1,
                    'office_address2' => $row->office_address2,
                    'office_city' => $row->office_city,
                    'office_stateprovince' => $row->office_stateprovince,
                    'office_postal' => $row->office_postal,
                    'office_country' => $row->office_country,

                    'organization_name' => $row->organization_name,

                    'employee_job_id' => $row->id,

                ]);
            }

        });  
        
        $this->LogMessage('');
        $this->LogMessage('Total detail row created count : ' . $row_count  );

    }

    protected function SetEligibleEmployeeBUCount()
    {

        $total_count = 0;
        $created_count = 0;
        $updated_count = 0;

        $as_of_date = today()->format('Y-m-d');
        $year = today()->year;

        $bu_groups = EligibleEmployeeDetail::select('business_unit', 'business_unit_name', 'organization_code', 
                        'year', DB::raw('count(*) as ee_cnt'))
                    ->where('year',$year)
                    ->groupBy('business_unit', 'business_unit_name', 'organization_code', 'year')
                    ->get();

        if ($bu_groups->count() > 0) {
            // clean up the old data before insert if exists
            ElligibleEmployee::where('year', $year)->delete();   
        }

        $this->LogMessage('');
        $this->LogMessage('Summarize eligible employees by business unit');

        foreach($bu_groups as $row) {
            $total_count++;

            $ee = ElligibleEmployee::create([
                'as_of_date' => $as_of_date,
                'ee_count' => $row->ee_cnt,
                'business_unit' => $row->business_unit,
                'business_unit_name' => $row->business_unit_name,
                'cde' => $row->organization_code,
                'year' => $row->year,
            ]);

            $created_count++;
            $this->LogMessage('   (CREATED) => ' . $ee->toJson() );

            if (!$row->business_unit_name) {
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
