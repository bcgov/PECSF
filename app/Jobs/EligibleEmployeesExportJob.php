<?php

namespace App\Jobs;

// use App\Models\EmployeeJob;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Models\ScheduleJobAudit;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Exports\EligibleEmployeesExport;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Maatwebsite\Excel\Facades\Excel;

class EligibleEmployeesExportJob implements ShouldQueue, ShouldBeUnique
{
    use  Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $history_id;
    protected $filters;     // array of $this->filters['all() 
    protected $filename;
    protected $uploadFilePath;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($history_id, $filename, $filters)
    {
        //
        $this->history_id = $history_id;
        $this->filename = $filename;
        $this->filters = $filters;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        // (new EligibleEmployeesExport($this->history_id, $this->filters) )->store('public/'.$this->filename);
        Excel::store(new EligibleEmployeesExport($this->history_id, $this->filters), 'public/'.$this->filename);  

        //
        // $handle = fopen( storage_path('app/public/'.$this->filename), 'w');

        // $header = ['Emplid', 'Name', 'Status', 'Address1', 'Address2', 
        //             'City', 'Province', 'Postal', 'organization_name', 'business_unit',
        //             'Business Unit Name', 'Dept ID', 'Dept Name', 'Region', 'Region Name',
        //           ];

        // $fields = ['emplid', 'name', 'empl_status', 'office_address1', 'office_address2', 
        //            'office_city', 'office_stateprovince', 'office_postal', 'organization_name', 'business_unit',
        //            'business_unit_name', 'deptid', 'dept_name', 'tgb_reg_district', 'region_name'
        //         ];


        // // Get the last completed job 
        // $last_job = ScheduleJobAudit::where('job_name', 'command:ImportEmployeeJob')
        //                 ->where('status','Completed')
        //                 ->orderBy('end_time', 'desc')->first();     

        // // Export header
        // fputcsv($handle, ['Report Title     :  Eligible Employee Report'] );
        // fputcsv($handle, ['Report Run on    : ' . now() ] );
        // fputcsv($handle, ['Status update on : ' . ($last_job ? $last_job->start_time : '') ] );
        // fputcsv($handle, [''] );
        // fputcsv($handle, $header );

        // // export the data with filter selection
        // $filters = $this->filters;

        // $sql = EmployeeJob::with('organization','bus_unit','region')
        //                     ->where( function($query) {
        //                         $query->where('employee_jobs.empl_rcd', '=', function($q) {
        //                                 $q->from('employee_jobs as J2') 
        //                                     ->whereColumn('J2.emplid', 'employee_jobs.emplid')
        //                                     // ->where('J2.empl_status', 'A')
        //                                     ->selectRaw('min(J2.empl_rcd)');
        //                             })
        //                             ->orWhereNull('employee_jobs.empl_rcd');
        //                     })
        //                     ->when( $filters['emplid'], function($query) use($filters) {
        //                         $query->where('employee_jobs.emplid', 'like', '%'. $filters['emplid'] .'%');
        //                     })
        //                     ->when( $filters['name'], function($query) use($filters) {
        //                         $query->where('employee_jobs.name', 'like', '%'. $filters['name'] .'%');
        //                     })
        //                     ->when( $filters['empl_status'], function($query) use($filters) {
        //                         $query->where('employee_jobs.empl_status', $filters['empl_status']);
        //                     })
        //                     ->when( $filters['office_city'], function($query) use($filters) {
        //                         $query->where('employee_jobs.office_city', $filters['office_city']);
        //                     })
        //                     ->when( $filters['organization'], function($query) use($filters) {
        //                         $query->where('employee_jobs.organization', $filters['organization']);
        //                     })
        //                     ->when( $filters['business_unit'], function($query) use($filters) {
        //                         $query->where( function($q) use($request) {
        //                             $q->where('employee_jobs.business_unit', $filters['business_unit'])
        //                               ->orWhereExists(function ($q) use($filters) {
        //                                   $q->select(DB::raw(1))
        //                                     ->from('business_units')
        //                                     ->whereColumn('business_units.code', 'employee_jobs.business_unit')
        //                                     ->where('business_units.name', 'like', '%'. $filters['business_unit'] .'%');
        //                                 });
        //                         });
        //                     })
        //                     ->when( $filters['department'], function($query) use($filters) {
        //                         $query->where( function($q) use($request) {
        //                             return $q->where('employee_jobs.deptid', 'like', '%'. $filters['department'] .'%')
        //                                      ->orWhere('employee_jobs.dept_name', 'like', '%'. $filters['department'] .'%');
        //                         });
        //                     })
        //                     ->when( $filters['tgb_reg_district'], function($query) use($filters) {
        //                         // $query->where('employee_jobs.tgb_reg_district', $request->tgb_reg_district);
        //                         $query->where( function($q) use($request) {
        //                             $q->where('employee_jobs.tgb_reg_district', $filters['tgb_reg_district'])
        //                               ->orWhereExists(function ($q) use($filters) {
        //                                   $q->select(DB::raw(1))
        //                                     ->from('regions')
        //                                     ->whereColumn('regions.code', 'employee_jobs.tgb_reg_district')
        //                                     ->where('regions.name', 'like', '%'. $filters['tgb_reg_district'] .'%');
        //                                 });
        //                         });
        //                     })
        //                     ->select('employee_jobs.*');

        // // add 
        // $total_count = $sql->count();

        // \App\Models\ProcessHistory::UpdateOrCreate([
        //     'id' => $this->history_id,
        // ],[                    
        // 'status' => 'Processing',
        // 'original_filename' => $this->filename,
        // 'filename' => $this->filename,
        // 'total_count' => $total_count,
        // 'start_at' => now(),
        // ]);

        // // export the data with filter selection
        // $count = 0;
        // $sql->chunk(2000, function($employees) use ($handle, $fields, &$count) {

        //         // additional data 
        //         foreach( $employees as $employee) {
        //             $employee->business_unit_name = $employee->bus_unit->name;
        //             $employee->region_name = $employee->region->name;
        //         }

        //         $subset = $employees->map->only( $fields );

        //         // output to csv
        //         foreach($subset as $employee) {
        //             fputcsv($handle, $employee, ',', '"' );
        //         }

        //         // update done count
        //         $count = $count + count($employees);
        //         \App\Models\ProcessHistory::UpdateOrCreate([
        //             'id' => $this->history_id,
        //         ],[                    
        //         'status' => 'Processing',
        //         'done_count' => $count,
        //         ]);
 
        // });

        // fclose($handle);

        // \App\Models\ProcessHistory::UpdateOrCreate([
        //     'id' => $this->history_id,
        // ],[                    
        //    'status' => 'Completed',
        //    'end_at' => now(),
        // ]);
        
    }

    public function uniqueId()
    {
        return $this->history_id;
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware()
    {
        echo "The job (EligibleEmployeesExportJob) process with history id " . $this->history_id . " started at " . now() . PHP_EOL;
        // If you don’t want any overlapping jobs to be released back onto the queue, you can use the dontRelease method
        return [(new WithoutOverlapping($this->history_id))->dontRelease()];
    }

}
