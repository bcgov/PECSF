<?php

namespace App\Http\Controllers\Admin;

use ZipArchive;
use App\Models\BusinessUnit;
use Illuminate\Http\Request;
use App\Models\ProcessHistory;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use App\Http\Controllers\Controller;
use App\Models\EligibleEmployeeByBU;
use Illuminate\Support\Facades\Auth;
use App\Models\EligibleEmployeeDetail;
use Illuminate\Support\Facades\Storage;
use App\Jobs\OrgPartipationTrackersExportJob;

class OrgPartipationTractorReportController extends Controller
{
    //
    protected $process_name = 'OrgPartipationTractor';

    /**
     * create a new instance of the class
     *
     * @return void
     */
    function __construct()
    {
        $this->middleware('permission:setting');


        // check the unfinished process and update the status
        $failed_processes = ProcessHistory::where('process_histories.process_name', $this->process_name)
                                ->whereNotIn('process_histories.status', ['Completed', 'Warning', 'Error'])
                                ->get();

        foreach( $failed_processes as $process_history) {
            $batch = Bus::findBatch( $process_history->batch_id);

            if ($batch) {
                // if ($batch->finished()) {
                //     // Update 
                //     $process_history->status = 'Completed';
                //     $process_history->message = 'Exported completed';
                //     $process_history->end_at = now();
                //     $process_history->save();
                // }

                if (($batch->cancelledAt) && ($batch->totalJobs == $batch->failedJobs) && ($batch->failedJobs > 0) ) {

                    $failed_job = DB::table('failed_jobs')->where('uuid', $batch->failedJobIds[0])->first();

                    $process_history->status = 'Error';
                    $process_history->message = $failed_job ? $failed_job->exception : '';
                    $process_history->end_at = $batch->finishedAt;
                    $process_history->save();
                }
            }
        }

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

            // check the unfinished process and update the status
            // $failed_processes = ProcessHistory::where('process_histories.process_name', $this->process_name)
            //                         ->whereNotIn('process_histories.status', ['Completed', 'Warning', 'Error'])
            //                         ->get();

            // foreach( $failed_processes as $process_history) {
            //     $batch = Bus::findBatch( $process_history->batch_id);

            //     if ($batch) {
            //         // if ($batch->finished()) {
            //         //     // Update 
            //         //     $process_history->status = 'Completed';
            //         //     $process_history->message = 'Exported completed';
            //         //     $process_history->end_at = now();
            //         //     $process_history->save();
            //         // }

            //         if (($batch->cancelledAt) && ($batch->totalJobs == $batch->failedJobs) && ($batch->failedJobs > 0) ) {

            //             $failed_job = DB::table('failed_jobs')->where('uuid', $batch->failedJobIds[0])->first();

            //             $process_history->status = 'Error';
            //             $process_history->message = $failed_job ? $failed_job->exception : '';
            //             $process_history->end_at = $batch->finishedAt;
            //             $process_history->save();
            //         }
            //     }
            // }
// dd($request->search['value']);
            // Prepare for the datatables
            $processes = ProcessHistory::where('process_name', $this->process_name)
                            ->with('created_by');


            return Datatables::of($processes)
                // ->addColumn('short_message', function ($process) {
                //     return substr($process->message, 0, 255);
                // })
                ->addColumn('download_file_link', function ($process) {
                    $retention_days = env('REPORT_RETENTION_DAYS') ?: 14;
                    if ($process->status == 'Completed') {
                        if ($process->updated_at >= today()->subdays($retention_days) ) {
                            $url = route('reporting.pledges.download-export-file', $process->id);
                            $link = '<a class="" href="'.$url.'">'. $process->original_filename . '</a>';
                        } else {
                            $link = $process->original_filename;    
                        }
                    } else {
                        // $link = $process->original_filename;
                        $link = '';
                    }
                    return $link;
                })
                ->addColumn('message_text', function ($process) {
                    $more_link = ' ... <br><a class="more-link text-danger" data-id="'. $process->id .'" >click here for more detail</a>';
                    $maxline = 3;
                    $lines = preg_split('#\r?\n#', $process->message);
                    if ( count($lines) > $maxline) {
                        // return nl2br( substr($audit->message, 0, $maxline)) . $more_link;
                        return nl2br( implode( PHP_EOL , array_slice( $lines, 0, 3) ) . $more_link );
                    } else {   
                        return nl2br( $process->message);
                    }
                })
                ->addColumn('action', function ($process) {
                    return '<a class="btn btn-info btn-sm  show-process" data-id="'. $process->id .'" >Details</a>';
                })
                ->addColumn('select_process', static function ($process) {
                    $html = '';
                    if ($process->status == 'Completed') {
                        $retention_days = env('REPORT_RETENTION_DAYS') ?: 14;
                        if ($process->updated_at >= today()->subdays($retention_days) ) {
                            $html = '<input pid="1335" type="checkbox" id="userCheck'. 
                                    $process->id .'" name="userCheck[]" value="'. $process->id .'" class="dt-body-center">';
                        } 
                    }
                    return $html;
                })
                // ->addColumn('action', function ($process) {
                //     return '<a class="btn btn-info btn-sm" href="' . route('admin-pledge.campaign.show',$process->id) . '">Show</a>' .
                //         '<a class="btn btn-primary btn-sm ml-2" href="' . route('admin-pledge.campaign.edit',$process->id) . '">Edit</a>';
                // })
                ->rawColumns(['download_file_link','message_text', 'select_process', 'action'])
                ->make(true);


        }

        // get all the record for select options 
        // $years = Pledge::join('campaign_years', 'campaign_years.id', 'pledges.campaign_year_id')
        //                 ->whereNull('cancelled')
        //                 ->distinct('campaign_years.calendar_year')->orderBy('campaign_years.calendar_year')->pluck('campaign_years.calendar_year');
        $date_options = EligibleEmployeeByBU::where('organization_code', 'GOV')
                                    ->select('campaign_year')
                                    ->orderBy('campaign_year', 'desc')
                                    ->distinct()
                                    ->pluck('campaign_year');

        // selection 
        // $retention_days = env('REPORT_RETENTION_DAYS') ?: 14;
        // $start_dt = today()->subdays($retention_days);
        // $term = strtolower('%'. ($request->search ? $request->search['value'] : '') .'%');

        // $processes = ProcessHistory::where('process_name', $this->process_name)
        //                             ->where('status','Completed')
        //                             ->where('updated_at', '>=', $start_dt)
        //                             ->where(function ($query) use($term) {
        //                                 $query->whereRaw( 'LOWER(`id`) like ?', array( $term ) )
        //                                     ->orWhereRaw( 'LOWER(`submitted_at`) like ?', array( $term ) )
        //                                     ->orWhereRaw( 'LOWER(`start_at`) like ?', array( $term ) )
        //                                     ->orWhereRaw( 'LOWER(`end_at`) like ?', array( $term ) )
        //                                     ->orWhereRaw( 'LOWER(`status`) like ?', array( $term ) )
        //                                     ->orWhereRaw( 'LOWER(`filename`) like ?', array( $term ) );
        //                             });

        $processes = $this->get_filtered_ids_sql( $request->term );
        $matched_process_ids = $processes->select(['id'])->pluck('id');
        $old_selected_process_ids = isset($old['selected_process_ids']) ? json_decode($old['selected_process_ids']) : [];

        // load the view and pass data
        return view('admin-report.org-partipation-tracker.index', compact('date_options', 'matched_process_ids', 'old_selected_process_ids'));

    }

    public function export2csv(Request $request) {

        if($request->ajax()) {

            $EE_BUs = EligibleEmployeeByBU::where('organization_code', 'GOV')
                            ->where('campaign_year', $request->yearcd)
                            ->where('ee_count', '>', 0)
                            ->orderBy('business_unit_code')->get();

            $submitted_at = now();

            $process_list = [];
            foreach($EE_BUs as $row) {

                // For Testing purpose
                // if ($row->business_unit_code <> 'BC003') {
                //     continue;
                // }

                $as_of_date = $row->as_of_date;
                $bu = $row->business_unit_code;

                $filters = $request->all(); 
                $filters['as_of_date'] = $row->as_of_date;
                $filters['business_unit_code'] = $bu;
                $filters['year'] = $row->campaign_year;
                $filters['title'] = $row->business_unit_name . ' ('  . $row->business_unit_code . ')';

                $business_unit = BusinessUnit::where('code', $row->business_unit_code)->first();
                $t1 = ($business_unit && $business_unit->acronym) ? $business_unit->acronym :  $row->business_unit_code;

                $filename = 'OrgPartipationTracker_'.  $row->campaign_year . '_' . $t1 . '_' . $as_of_date->format('Y-m-d') .".xlsx";

                // Submit a Job
                $history = \App\Models\ProcessHistory::create([
                    'batch_id' => 0,
                    'process_name' => $this->process_name,
                    'parameters' => json_encode( $filters ),
                    'status'  => 'Queued',
                    'submitted_at' => $submitted_at,
                    'original_filename' => $filename,
                    'filename' => $filename,
                    'total_count' => 0,
                    'done_count' => 0,
                    'created_by_id' => Auth::Id(),
                    'updated_by_id' => Auth::Id(),
                ]);
        
                // Submit a job 
                $batch = Bus::batch([
                    new OrgPartipationTrackersExportJob($history->id, $filename, $filters),
                ])->dispatch();

                // dd ($batch->id);
                $history->batch_id = $batch->id;
                $history->save();

                array_push($process_list, $history->id);

            }

            return response()->json([
                    'batch_id' => $history->id,
                    'batch_ids' => json_encode($process_list),
            ], 200);

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */
    public function show(Request $request, $id)
    {

        if ($request->ajax()) {

            $process = \App\Models\ProcessHistory::where('id', $id)->first();

            return response()->json($process);
        }

    }


    public function exportProgress(Request $request) {

        $batch_ids = json_decode($request->batch_ids) ?? [];

        $histories = ProcessHistory::whereIn('id', $batch_ids ) 
                        ->select('status', DB::raw('count(*) as count'))
                        ->groupBy('status')
                        ->orderBy('status');

        $result = $histories->pluck('count', 'status') ;

        $finished = false;
        if ($result->get('Error') > 0) {
            return response()->json([
                'finished' => false,
                'message' => 'Job failed, please contact system administrtator.',
            ], 422);
        } else if ( $result->get('Completed') == count($batch_ids)) {
            $finished = true;
            $message = 'Done. All files have been successfully created.';
        } else if ($result->get('Processing') > 0) {
            $message = '<span class="blink-two">Processing... , please wait.</span>';
        } else if ($result->get('Queued') == count($batch_ids)) {
            $message = 'Queued, please wait.';
        } else {
            $message = 'Procsssing..., please wait.';
        }

        return response()->json([
            'finished' => $finished,
            'message' => $message,
        ], 200);

    }

    public function downloadExportFile(Request $request, $id) {

        $history = ProcessHistory::where('id', $id)->first();
        // $path = Student::where("id", $id)->value("file_path");
    
        if ($history) {

            $filepath = $history->filename; 

            $headers = [
                'Content-Description' => 'File Transfer',
                'Content-Type' => 'application/csv',
                "Content-Transfer-Encoding: UTF-8",
            ];

            // return Storage::download($path);
            return Storage::disk('public')->download($filepath, $filepath, $headers); 

        } else {
            abort(404);
        }

    }    

    public function downloadExportFilesInZip(Request $request) {

        // dd($request-ids);
        $ids = json_decode($request->ids) ?? [];
        $zip_filename = 'OrgPartipationTracker_'. now()->format("Y-m-d-his") .'.zip';

        $zip_file = Storage::disk('public')->path(  $zip_filename ) ; // Name of our archive to download

        // Initializing PHP class
        $zip = new ZipArchive();
        $zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        
        $histories = ProcessHistory::whereIn('id', $ids)->get();

        foreach( $histories as $history) {
            $filename = $history->filename; 

            // Adding file: second parameter is what will the path inside of the archive
            // So it will create another folder called "storage/" inside ZIP, and put the file there.
            if (Storage::disk('public')->exists($filename)) {
                $zip->addFile( Storage::disk('public')->path($filename), $filename);
            }
        }
                    
        $zip->close();
                    
        // We return the file immediately after download
        return response()->download($zip_file);

    }

    public function filteredIds(Request $request) {

        if($request->ajax()) {

            $processes = $this->get_filtered_ids_sql( $request->term );
            $matched_process_ids = $processes->select(['id'])->pluck('id');

            return json_encode($matched_process_ids);
        }
        
    }

    private function get_filtered_ids_sql($term) {
        
        $retention_days = env('REPORT_RETENTION_DAYS') ?: 14;
        $start_dt = today()->subdays($retention_days);
        $term = strtolower('%'. $term .'%');

        return ProcessHistory::where('process_name', $this->process_name)
                                    ->where('status','Completed')
                                    ->where('updated_at', '>=', $start_dt)
                                    ->where(function ($query) use($term) {
                                        $query->whereRaw( 'LOWER(`id`) like ?', array( $term ) )
                                              ->orWhereRaw( 'LOWER(`submitted_at`) like ?', array( $term ) )
                                              ->orWhereRaw( 'LOWER(`start_at`) like ?', array( $term ) )
                                              ->orWhereRaw( 'LOWER(`end_at`) like ?', array( $term ) )
                                              ->orWhereRaw( 'LOWER(`status`) like ?', array( $term ) )
                                              ->orWhereRaw( 'LOWER(`filename`) like ?', array( $term ) );
                                });

    }

}
