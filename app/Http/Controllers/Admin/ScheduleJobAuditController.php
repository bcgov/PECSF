<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\ScheduleJobAudit;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class ScheduleJobAuditController extends Controller
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
     public function index(Request $request) {
    
        if($request->ajax()) {

            $audits = ScheduleJobAudit::select('schedule_job_audits.*')
                        ->when($request->start_time, function($query) use($request) {
                            $start_time = $request->start_time;
                            $end_time = $request->end_time ?? '2099-12-21';
                            return $query->whereBetween('start_time', [$start_time, $end_time])
                                            ->orWhereNull('start_time'); 
                        })
                        ->when($request->end_time, function($query) use($request) {
                            $start_time = $request->start_time ?? '1990-01-01';
                            $end_time = $request->end_time ;
                            return $query->whereBetween('end_time', [$start_time, $end_time])
                                            ->orWhereNull('end_time'); 
                        });
                            
//    return( [$access_logs->toSql(), $access_logs->getBindings() ]);                                

            return Datatables::of($audits)
                        ->addColumn('message_text', function ($audit) {
                            $more_link = '... <br><a class="more-link" data-id="'. $audit->id .'" >More</a>';
                            $maxlen = 100;
                            if (strlen($audit->message) > $maxlen) {
                                return nl2br( substr($audit->message, 0, $maxlen)) . $more_link;
                            } else {   
                                return nl2br( $audit->message);
                            }
                        })
                        ->rawColumns(['message_text'])
                        ->make(true);
        }

        $status_list = ScheduleJobAudit::JOB_STATUS;

        return view('admin-campaign.schedule-job-audits.index', compact('request', 'status_list') );

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

            $audit = ScheduleJobAudit::where('id', $id)->first();
            // $audit->created_by_name = $access_log->created_by ? $access_log->created_by->name : '';
            // $audit->updated_by_name = $access_log->updated_by ? $access_log->updated_by->name : '';
            // $audit->formatted_created_at = $access_log->created_at->format('Y-m-d H:i:s');
            // $audit->formatted_updated_at = $access_log->updated_at->format('Y-m-d H:i:s');
            // // $region->updated_at = date_timezone_set($region->updated_at, timezone_open('America/Vancouver'));
            // unset($access_log->created_by );
            // unset($access_log->updated_by );

            return response()->json($audit);
        }

    }

}
