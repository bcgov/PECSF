<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use App\Models\ExportAuditLog;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ExportAuditLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     function __construct()
     {
          $this->middleware('permission:setting');
     }
 
     public function index(Request $request)
     {
         //
         if($request->ajax()) {
 
             $audits = ExportAuditLog::when($request->schedule_job_id, function($query) use($request) {
                    return $query->where('export_audit_logs.schedule_job_id', $request->schedule_job_id);
                })
                ->when($request->to_application, function($query) use($request) {
                    return $query->where('export_audit_logs.to_application', $request->to_application);
                })
                ->when($request->table_name, function($query) use($request) {
                     return $query->where('export_audit_logs.table_name', $request->table_name);
                })
                ->when($request->row_id, function($query) use($request) {
                    return $query->where('export_audit_logs.table_name', $request->row_id);
                })
                ->when($request->start_time || $request->end_time, function($query) use($request) {
                     $from = $request->start_time ?? '1990-01-01';
                     $to = $request->end_time ?? '2099-12-31';
                     return  $query->whereBetween('export_audit_logs.created_at',[ $from, $to]);
                })
                ->when($request->row_values, function($query) use($request) {
                     return $query->where('export_audit_logs.row_values', 'LIKE','%'.$request->row_values.'%');
                })
                ->select('export_audit_logs.*')
                ->with(['schedule_job']);
 
             return Datatables::of($audits)
                 ->addColumn('audit_timestamp', function ($audit) {
                     return $audit->created_at->format('Y-m-d H:i:s');
                 })
                 ->make(true);
             
         }
 
        $table_name_options = ExportAuditLog::table_name_options();
        $to_application_options = ExportAuditLog::to_application_options();
 
        return view('system-security.export-audit-log.index', compact('table_name_options',
                        'to_application_options'));
         
     }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
