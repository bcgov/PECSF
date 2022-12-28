<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use App\Models\EmployeeJob;
use Illuminate\Http\Request;
use App\Models\ScheduleJobAudit;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class EligibleEmployeeReportController extends Controller
{
    /**
     * create a new instance of the class
     *
     * @return void
     */
    function __construct()
    {
         $this->middleware('permission:setting');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        //
        if($request->ajax()) {

            $employees = $this->getEmployeeJobQuery($request); 
           
            return Datatables::of($employees)
                // ->editColumn('created_at', function ($donation) {
                //     return $donation->process_history->created_at->format('Y-m-d H:m:s'); // human readable format
                // })
                // ->editColumn('updated_at', function ($donation) {
                //     return $donation->process_history->updated_at->format('Y-m-d H:m:s'); // human readable format
                // })                        
                // ->rawColumns(['action','description'])
                ->make(true);

        }

        // get all the record for select options 
        $empl_status_List = EmployeeJob::EMPL_STATUS_LIST;
        $office_cities = EmployeeJob::office_city_list();
        $organizations = EmployeeJob::organization_list();

        // load the view and pass data
        return view('admin-report.eligible-employee.index', compact('empl_status_List','office_cities','organizations'));

    }


    public function export2csv(Request $request) {

        $filename = 'export_'.date("Y-m-d").".csv";
        $handle = fopen( storage_path('app/public/'.$filename), 'w');

        $header = ['Emplid', 'Name', 'Status', 'Address1', 'Address2', 
                    'City', 'Province', 'Postal', 'organization_name', 'business_unit',
                    'Business Unit Name', 'Dept ID', 'Dept Name', 'Region', 'Region Name',
                  ];

        $fields = ['emplid', 'name', 'empl_status', 'office_address1', 'office_address2', 
                   'office_city', 'office_stateprovince', 'office_postal', 'organization_name', 'business_unit',
                   'business_unit_name', 'deptid', 'dept_name', 'tgb_reg_district', 'region_name'
                ];

        $last_job = ScheduleJobAudit::where('job_name', 'command:ImportEmployeeJob')
                        ->where('status','Completed')
                        ->orderBy('end_time', 'desc')->first();        

        // Export header
        fputcsv($handle, ['Report Title     :  Eligible Employee Report'] );
        fputcsv($handle, ['Report Run on    : ' . now() ] );
        fputcsv($handle, ['Status update on : ' . ($last_job ? $last_job->start_time : '') ] );
        fputcsv($handle, [''] );
        fputcsv($handle, $header );

        // export the data with filter selection
        $sql = $this->getEmployeeJobQuery($request); 

        $sql->chunk(2000, function($employees) use ($handle, $fields) {

                // additional data 
                foreach( $employees as $employee) {
                    $employee->business_unit_name = $employee->bus_unit->name;
                    $employee->region_name = $employee->region->name;
                }

                $subset = $employees->map->only( $fields );

                // output to csv
                foreach($subset as $employee) {
                    fputcsv($handle, $employee, ',', '"' );
                }
        });

        fclose($handle);

        $headers = [
            'Content-Description' => 'File Transfer',
            'Content-Type' => 'application/csv',
            "Content-Transfer-Encoding: UTF-8",
        ];

        return Storage::disk('public')->download($filename, $filename, $headers); 

    }

    function getEmployeeJobQuery(Request $request) {

        $sql = EmployeeJob::with('organization','bus_unit','region')
                            ->where( function($query) {
                                $query->where('employee_jobs.empl_rcd', '=', function($q) {
                                        $q->from('employee_jobs as J2') 
                                            ->whereColumn('J2.emplid', 'employee_jobs.emplid')
                                            // ->where('J2.empl_status', 'A')
                                            ->selectRaw('min(J2.empl_rcd)');
                                    })
                                    ->orWhereNull('employee_jobs.empl_rcd');
                            })
                            ->when( $request->emplid, function($query) use($request) {
                                $query->where('employee_jobs.emplid', 'like', '%'. $request->emplid .'%');
                            })
                            ->when( $request->name, function($query) use($request) {
                                $query->where('employee_jobs.name', 'like', '%'. $request->name .'%');
                            })
                            ->when( $request->empl_status, function($query) use($request) {
                                $query->where('employee_jobs.empl_status', $request->empl_status);
                            })
                            ->when( $request->office_city, function($query) use($request) {
                                $query->where('employee_jobs.office_city', $request->office_city);
                            })
                            ->when( $request->organization, function($query) use($request) {
                                $query->where('employee_jobs.organization', $request->organization);
                            })
                            ->when( $request->business_unit, function($query) use($request) {
                                $query->where( function($q) use($request) {
                                    $q->where('employee_jobs.business_unit', $request->business_unit)
                                      ->orWhereExists(function ($q) use($request) {
                                          $q->select(DB::raw(1))
                                            ->from('business_units')
                                            ->whereColumn('business_units.code', 'employee_jobs.business_unit')
                                            ->where('business_units.name', 'like', '%'. $request->business_unit .'%');
                                        });
                                });
                            })
                            ->when( $request->department, function($query) use($request) {
                                $query->where( function($q) use($request) {
                                    return $q->where('employee_jobs.deptid', 'like', '%'. $request->department .'%')
                                             ->orWhere('employee_jobs.dept_name', 'like', '%'. $request->department .'%');
                                });
                            })
                            ->when( $request->tgb_reg_district, function($query) use($request) {
                                // $query->where('employee_jobs.tgb_reg_district', $request->tgb_reg_district);
                                $query->where( function($q) use($request) {
                                    $q->where('employee_jobs.tgb_reg_district', $request->tgb_reg_district)
                                      ->orWhereExists(function ($q) use($request) {
                                          $q->select(DB::raw(1))
                                            ->from('regions')
                                            ->whereColumn('regions.code', 'employee_jobs.tgb_reg_district')
                                            ->where('regions.name', 'like', '%'. $request->tgb_reg_district .'%');
                                        });
                                });
                            })
                            ->select('employee_jobs.*');

        return $sql;
    }

}
