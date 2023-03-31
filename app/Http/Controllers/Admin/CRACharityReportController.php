<?php

namespace App\Http\Controllers\Admin;

use App\Models\Charity;
use Illuminate\Http\Request;
use App\Models\ProcessHistory;
use App\Jobs\CharitiesExportJob;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CRACharityReportController extends Controller
{
    //
    protected $process_name = 'CharitiesExportJob';

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

            // check the unfinished process and update the status
            $failed_processes = ProcessHistory::where('process_histories.process_name', $this->process_name)
                                    ->whereNotIn('process_histories.status', ['Completed', 'Warning', 'Error'])
                                    ->get();

            foreach( $failed_processes as $process_history) {
                $batch = Bus::findBatch( $process_history->batch_id);

                if ($batch) {

                    if (($batch->cancelledAt) && ($batch->totalJobs == $batch->failedJobs) && ($batch->failedJobs > 0) ) {

                        $failed_job = DB::table('failed_jobs')->where('uuid', $batch->failedJobIds[0])->first();

                        $process_history->status = 'Error';
                        $process_history->message = $failed_job ? $failed_job->exception : '';
                        $process_history->end_at = $batch->finishedAt;
                        $process_history->save();
                    }
                }
            }

            // Prepare for the datatables
            $processes = ProcessHistory::where('process_name', $this->process_name);

            return Datatables::of($processes)
                // ->addColumn('short_message', function ($process) {
                //     return substr($process->message, 0, 255);
                // })
                ->addColumn('download_file_link', function ($process) {
                    if ($process->status == 'Completed') {
                        $url = route('reporting.cra-charities.download-export-file', $process->id);
                        $link = '<a class="" href="'.$url.'">'. $process->original_filename . '</a>';
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
                    return '<a class="btn btn-info btn-sm  show-process" data-id="'. $process->id .'" >Show</a>';
                })
                // ->addColumn('action', function ($process) {
                //     return '<a class="btn btn-info btn-sm" href="' . route('admin-pledge.campaign.show',$process->id) . '">Show</a>' .
                //         '<a class="btn btn-primary btn-sm ml-2" href="' . route('admin-pledge.campaign.edit',$process->id) . '">Edit</a>';
                // })
                ->rawColumns(['download_file_link','message_text', 'action'])
                ->make(true);


        }

        $as_of_date = today()->format('Y-m-d');

//         $sql = Charity::leftJoin('f_s_pool_charities', 'f_s_pool_charities.charity_id','charities.id')
//                     ->leftJoin('f_s_pools', 'f_s_pools.id', 'f_s_pool_charities.f_s_pool_id')
//                     ->leftJoin('regions', 'f_s_pools.region_id', 'regions.id')
//                     ->where( function($query) use($as_of_date) {
//                         $query->where('f_s_pools.start_date', '=', function ($q) use($as_of_date) {
//                             $q->selectRaw('max(start_date)')
//                                 ->from('f_s_pools as A')
//                                 ->whereColumn('A.region_id', 'f_s_pools.region_id')
//                                 ->whereNull('A.deleted_at')
//                                 ->where('A.start_date', '<=', $as_of_date );
//                         });
//                         // ->orWhereNull('f_s_pools.start_date');
//                     })
//                     ->whereNull('f_s_pool_charities.deleted_at')
//                     ->whereNull('f_s_pools.deleted_at')
//                     ->whereNull('regions.deleted_at')
//                     ->select('charities.*', 'regions.code as region_code', 'regions.name as region_name',
//                              'f_s_pool_charities.percentage', 'f_s_pool_charities.name as supported_program',
//                              'f_s_pool_charities.contact_title', 'f_s_pool_charities.contact_name', 'f_s_pool_charities.contact_email',
//                              'f_s_pool_charities.notes',
//                                 DB::Raw("case when f_s_pool_id is null then 'No' else 'Yes' end as f_s_pool_flag")
                    
//                 )->limit(10)->get();

// dd($sql);                    


        return view('admin-report.cra-charities.index', compact('as_of_date'));

    }

    public function export2csv(Request $request) {

        if($request->ajax()) {

            $filters = $request->all(); 

            $filename = 'cra_charities_'.now()->format("Y-m-d-his").".csv";

            // Submit a Job
            $history = \App\Models\ProcessHistory::create([
                'batch_id' => 0,
                'process_name' => $this->process_name,
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
                new CharitiesExportJob($history->id, $filename, $filters),
            ])->dispatch();

            // dd ($batch->id);
            $history->batch_id = $batch->id;
            $history->save();

            return response()->json([
                    'batch_id' => $history->id,
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
                // $history->status = 'Completed';
                // $history->message = 'Exported completed';
                // $history->end_at = now();
                // $history->save();
                
                $link = route('reporting.cra-charities.download-export-file', $history->id);
                $message = 'Done. Download file <a class="" href="'.$link.'">here</a>';
                
            } else if ($history->status == 'Queued') {
                $message = 'Queued, please wait.';
            } else if ($history->status == 'Processing') {
                $progress = $history->total_count > 0 ? round(($history->done_count / $history->total_count) * 100,0) : 0 ;
                $message = 'Processing... ('. $progress .'%) , please wait.';
                // $message = '<span class="blink-two">Processing... , please wait.</span>';
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


}
