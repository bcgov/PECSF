<?php

namespace App\Http\Controllers\Admin;

use App\Models\Jobs;
use App\Models\FailedJobs;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Models\CompletedJobs;
use App\Models\ProcessHistory;
use App\Imports\DonationsImport;
use App\Jobs\DonationsImportJob;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class DonationUploadController extends Controller
{

    protected $process_name = 'DonationsImportJob';
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
                if ( ($batch->cancelledAt) && ($batch->totalJobs == $batch->failedJobs) && ($batch->failedJobs > 0) ) {

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
                ->addColumn('short_message', function ($process) {
                    return substr($process->message, 0, 255);
                })
                ->addColumn('action', function ($process) {
                    return '<a class="btn btn-info btn-sm" href="' . route('admin-pledge.campaign.show',$process->id) . '">Show</a>' .
                        '<a class="btn btn-primary btn-sm ml-2" href="' . route('admin-pledge.campaign.edit',$process->id) . '">Edit</a>';
                })->rawColumns(['action'])
                ->make(true);

        }

        $organizations = Organization::whereNotIn('code', ['GOV'])->orderBy('code')->get();

        $jobs = count(Jobs::all()) > 0 ? Jobs::all() : [];
        $completed_jobs = CompletedJobs::all();
        $failed_jobs = FailedJobs::all();


        // load the view and pass the sharks
        return view('admin-report.donation-upload.index',compact('organizations'));
    }
 
     public function store(Request $request)
     {

        $org_filename = $request->file('donation_file') ? $request->file('donation_file')->getClientOriginalName() : '';

         $validator = Validator::make(request()->all(), [
             'donation_file'          => 'required|max:102400|mimes:xlsx',
         ],[
             'donation_file.required' => 'Please upload an xls xlsx Excel File', 
             'donation_file.mimes' => 'The donation file must be a file of type: xls, xlsx, your uploaded file is called ' . $org_filename,
             'donation_file.max' => 'Sorry! Maximum allowed size for an image is 10MB, your uploaded file is called ' . $org_filename,
         ]);
 
         if ($validator->fails()) {
             return redirect()->route('reporting.donation-upload.index')
                 ->withErrors($validator)
                 ->withInput();
         }
 
        $organization = Organization::where('id', $request->organization_id )->first();

        $upload_file = $request->file('donation_file') ?? null;
        //  $filesize = $upload_file->getSize();
        $original_filename = $upload_file->getClientOriginalName();
        $filename=date('YmdHis').'_'. str_replace(' ', '_', $original_filename );
        $filePath = $upload_file->storeAs(  $this->donation_file_folder , $filename);

        // Submit a Job
        $history = \App\Models\ProcessHistory::create([
            'batch_id' => 0,
            'process_name' => 'DonationsImportJob',
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
            new DonationsImportJob( $filePath, $history->id, $organization->code),
        ])->dispatch();

        // $this->batchId = $batch->id;

        $history->batch_id = $batch->id;
        $history->save();

        //  ProcessCharityList::dispatch(public_path( $this->donation_file_folder)."/".$filename,$filename,$filesize);
 
         return redirect()->route('reporting.donation-upload.index')
            ->withInput()
            ->with('success','File ' . $original_filename . ' for organization ' . $organization->code . ' was successfully uploaded and added to the process queue.');
             
 
     }
 
     
}
