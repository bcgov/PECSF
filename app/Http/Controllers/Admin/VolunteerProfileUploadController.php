<?php

namespace App\Http\Controllers\Admin;

use App\Models\Jobs;
use App\Models\FailedJobs;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Models\CompletedJobs;
use App\Models\ProcessHistory;
use App\Imports\VolunteerProfilesImport;
use App\Jobs\VolunteerProfilesImportJob;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class VolunteerProfileUploadController extends Controller
{

    protected $process_name = 'VolunteerProfilesImportJob';
//
    function __construct()
    {
        $this->middleware('permission:setting');
        $this->donation_file_folder = "uploads";
    }

    public function dashboard()
    {
        return view('admin.dashboard.index');
    }
 
    /**
     * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {

        if($request->ajax()) {

            // check the unfinished process and update the status
            $failed_processes = ProcessHistory::where('process_histories.process_name', $this->process_name)
            ->whereNotIn('process_histories.status', ['Completed', 'Warning', 'Error'])
            ->get();

            foreach( $failed_processes as $process_history) {
                $batch = Bus::findBatch( $process_history->batch_id);
                if ( $batch && ($batch->cancelledAt) && ($batch->totalJobs == $batch->failedJobs) && ($batch->failedJobs > 0) ) {

                    $failed_job = DB::table('failed_jobs')->where('uuid', $batch->failedJobIds[0])->first();

                    $process_history->status = 'Error';
                    $process_history->message = $failed_job ? $failed_job->exception : '';
                    $process_history->end_at = $batch->finishedAt;
                    $process_history->save();
                }
            }


            // Prepare for the datatables
            $processes = ProcessHistory::where('process_name', $this->process_name);

            return Datatables::of($processes)
                // ->addColumn('short_message', function ($process) {
                //     return substr($process->message, 0, 255);
                // })
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
                ->rawColumns(['message_text', 'action'])
                ->make(true);

        }

        // $organizations = Organization::where('status', 'A')
        //                         ->whereNotIn('code', ['GOV'])->orderBy('code')->get();
        $year_list = range(2024, today()->year);

        $jobs = count(Jobs::all()) > 0 ? Jobs::all() : [];
        $completed_jobs = CompletedJobs::all();
        $failed_jobs = FailedJobs::all();

       

        // load the view and pass the sharks
        return view('admin-volunteering.upload-profile.index',compact('year_list'));
    }
 
     public function store(Request $request)
     {

        $org_filename = $request->file('donation_file') ? $request->file('donation_file')->getClientOriginalName() : '';

         $validator = Validator::make(request()->all(), [
             'campaign_year'          => 'required',
             'org_type'               => 'required|in:1,2',
             'donation_file'          => 'required|max:102400|mimes:xls,xlsx',
         ],[
             'donation_file.required' => 'Please upload an xls xlsx Excel File', 
             'donation_file.mimes' => 'The donation file must be a file of type: xls, xlsx, your uploaded file is called ' . $org_filename,
             'donation_file.max' => 'Sorry! Maximum allowed size for an image is 10MB, your uploaded file is called ' . $org_filename,
         ]);
 
        //run validation which will redirect on failure
        $validated = $validator->validate();

        //  if ($validator->fails()) {
        //      return redirect()->route('reporting.donation-upload.index')
        //          ->withErrors($validator)
        //          ->withInput();
        //  }

        // $organization = Organization::where('id', $request->organization_id )->first();
        $campaign_year = $request->campaign_year;
        $org_type = $request->org_type;

        $upload_file = $request->file('donation_file') ?? null;
        //  $filesize = $upload_file->getSize();
        $original_filename = $upload_file->getClientOriginalName();
        $filename=now()->format('YmdHisu').'_'. str_replace(' ', '_', $original_filename );
        $filePath = $upload_file->storeAs(  $this->donation_file_folder , $filename);

        $parameters = [
            'Campaign Year' => $campaign_year,
            'Organization Type' => $org_type,
            'File Name' => $original_filename, 
        ];

        // Submit a Job
        $history = \App\Models\ProcessHistory::create([
            'batch_id' => 0,
            'process_name' => 'VolunteerProfilesImportJob',
            'parameters' => json_encode( $parameters ),
            'status'  => 'Queued',
            'submitted_at' => now(),
            'original_filename' => $original_filename,
            'filename' => $filePath,
            'total_count' => 0,
            'done_count' => 0,
            'created_by_id' => Auth::Id(),
            'updated_by_id' => Auth::Id(),

        ]);

        $batch = Bus::batch([
            new VolunteerProfilesImportJob( $filePath, $history->id, $campaign_year, $org_type),
        ])->dispatch();

        // $this->batchId = $batch->id;

        $history->batch_id = $batch->id;
        $history->save();

        //  ProcessCharityList::dispatch(public_path( $this->donation_file_folder)."/".$filename,$filename,$filesize);
        return response()->json([
                'success' => 'File ' . $original_filename . ' for campaign year ' . $campaign_year . ' and Organization Type ' . $org_type . 
                    ' was successfully uploaded and added to the process queue.'
            ]);

        //  return redirect()->route('reporting.donation-upload.index')
        //     ->withInput()
        //     ->with('success','File ' . $original_filename . ' for organization ' . $organization->code . ' was successfully uploaded and added to the process queue.');
 
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
 
     
}
