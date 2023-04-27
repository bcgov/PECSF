<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
// use App\Models\EmployeeJob;
use Illuminate\Http\Request;
use App\Models\ProcessHistory;
use App\Models\ScheduleJobAudit;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\EligibleEmployeeDetail;

use Illuminate\Support\Facades\Storage;
use App\Exports\EligibleEmployeesExport;
use App\Jobs\EligibleEmployeesExportJob;

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
        // $empl_status_List = EligibleEmployeeDetail::EMPL_STATUS_LIST;
        $office_cities = EligibleEmployeeDetail::office_city_list();
        $organizations = EligibleEmployeeDetail::organization_list();
        $years = EligibleEmployeeDetail::distinct('year','as_of_date')->orderBy('year')->pluck('as_of_date','year');


        // load the view and pass data
        return view('admin-report.eligible-employee.index', compact('office_cities','organizations', 'years'));

    }

    public function export2csv(Request $request) {

        if($request->ajax()) {

            $filters = $request->all(); 

            $filename = 'export_eligible_employees_'.now()->format("Y-m-d-his").".xlsx";

            // Submit a Job
            $history = \App\Models\ProcessHistory::create([
                'batch_id' => 0,
                'process_name' => 'EligibleEmployeesExportJob',
                'parameters' => json_encode( $filters ),
                'status'  => 'Queued',
                'submitted_at' => now(),
                'original_filename' => $filename,
                'filename' => $filename,
                'total_count' => 0,
                'done_count' => 0,
                'created_by_id' => Auth::Id(),
                'updated_by_id' => Auth::Id(),
            ]);
       
            // Submit a job 
            $batch = Bus::batch([
                new EligibleEmployeesExportJob($history->id, $filename, $filters),
            ])->dispatch();

            // dd ($batch->id);
            $history->batch_id = $batch->id;
            $history->save();

            return response()->json([
                    'batch_id' => $history->id,
            ], 200);

        }

    }

    public function exportProgress(Request $request, $id) {

        // storage batch id in session
        $history = ProcessHistory::where('id', $id)->first();

        if ($history) {

            // $batch_id = session()->get('charities-export-batch-id');

            $batch = Bus::findBatch($history->batch_id);
            // TODO -- how to check failed
            if ($batch->failedJobs) {
                return response()->json([
                    'finished' => false,
                    'message' => 'Job failed, please contact system administrtator.',
                ], 422);
                
            }

            $finished = false;
            $message = 'Procsssing..., please wait.' . now();

            if ($batch->finished() ) {
                $finished = true;

                // Update 
                $history->status = 'Completed';
                $history->message = 'Exported completed';
                $history->end_at = now();
                $history->save();
                
                $link = route('reporting.eligible-employees.download-export-file', $history->id);
                $message = 'Done. Download file <a class="" href="'.$link.'">here</a>';
                
            } else if ($history->status == 'Queued') {
                $message = 'Queued, please wait.';
            } else if ($history->status == 'Processing') {
                // $progress = round(($history->done_count / $history->total_count) * 100,0);
                // $message = 'Processing... ('. $progress .'%) , please wait.';
                $message = '<span class="blink-two">Processing... , please wait.</span>';
            } else {
                // others
            }

            return response()->json([
                'finished' => $finished,
                'message' => $message,
            ], 200);
        }   

    }

    public function downloadExportFile(Request $request, $id) {

        $history = ProcessHistory::where('id', $id)->first();
        // $path = Student::where("id", $id)->value("file_path");
    
        $filepath = $history->filename; 

        $headers = [
            'Content-Description' => 'File Transfer',
            'Content-Type' => 'application/csv',
            "Content-Transfer-Encoding: UTF-8",
        ];

        // return Storage::download($path);
        return Storage::disk('public')->download($filepath, $filepath, $headers); 

    }        

    function getEmployeeJobQuery(Request $request) {

        $sql = EligibleEmployeeDetail::when( $request->year, function($query) use($request) {
                                $query->where('eligible_employee_details.year', $request->year);
                            })
                            ->when( $request->emplid, function($query) use($request) {
                                $query->where('eligible_employee_details.emplid', 'like', '%'. $request->emplid .'%');
                            })
                            ->when( $request->name, function($query) use($request) {
                                $query->where('eligible_employee_details.name', 'like', '%'. $request->name .'%');
                            })
                            ->when( $request->empl_status, function($query) use($request) {
                                $query->where('eligible_employee_details.empl_status', $request->empl_status);
                            })
                            ->when( $request->office_city, function($query) use($request) {
                                $query->where('eligible_employee_details.office_city', $request->office_city);
                            })
                            ->when( $request->organization, function($query) use($request) {
                                $query->where('eligible_employee_details.organization_name', $request->organization);
                            })
                            ->when( $request->business_unit, function($query) use($request) {
                                $query->where( function($q) use($request) {
                                    $q->where('eligible_employee_details.business_unit', 'like', '%'. $request->business_unit .'%')
                                      ->orWhere('eligible_employee_details.business_unit_name', 'like', '%'. $request->business_unit .'%');
                                });
                            })
                            ->when( $request->department, function($query) use($request) {
                                $query->where( function($q) use($request) {
                                    return $q->where('eligible_employee_details.deptid', 'like', '%'. $request->department .'%')
                                             ->orWhere('eligible_employee_details.dept_name', 'like', '%'. $request->department .'%');
                                });
                            })
                            ->when( $request->tgb_reg_district, function($query) use($request) {
                                // $query->where('employee_jobs.tgb_reg_district', $request->tgb_reg_district);
                                $query->where( function($q) use($request) {
                                    $q->where('eligible_employee_details.tgb_reg_district', $request->tgb_reg_district)
                                      ->orWhereExists(function ($q) use($request) {
                                          $q->select(DB::raw(1))
                                            ->from('regions')
                                            ->whereColumn('regions.code', 'eligible_employee_details.tgb_reg_district')
                                            ->where('regions.name', 'like', '%'. $request->tgb_reg_district .'%');
                                        });
                                });
                            })
                            ->select('eligible_employee_details.*');

        return $sql;
    }

}
