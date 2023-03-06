<?php

namespace App\Http\Controllers\System;

use App\Models\Audit;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AuditingController extends Controller
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

            $audits = Audit::join('users', 'users.id', 'audits.user_id')
                ->where( function($query) use($request) {
                    return $query->when($request->audit_user, function($q) use($request) {
                            return $q->where('users.name','LIKE','%'.$request->audit_user.'%')
                                ->orWhere('users.idir','LIKE','%'.$request->audit_user.'%')
                                ->orWhere('users.emplid','LIKE','%'.$request->audit_user.'%');
                    });
                })
                ->when($request->event_type, function($query) use($request) {
                    return $query->where('audits.event', $request->event_type);
                })
                ->when($request->auditable_type, function($query) use($request) {
                    return $query->where('audits.auditable_type', "App\\Models\\" . $request->auditable_type);
                })
                ->when($request->auditable_id, function($query) use($request) {
                    return $query->where('audits.auditable_id', $request->auditable_id);
                })
                ->when($request->start_time || $request->end_time, function($query) use($request) {
                    $from = $request->start_time ?? '1990-01-01';
                    $to = $request->end_time ?? '2099-12-31';
                    return  $query->whereBetween('audits.created_at',[ $from, $to]);
                })
                ->when($request->old_values, function($query) use($request) {
                    return $query->where('audits.old_values', 'LIKE','%'.$request->old_values.'%');
                })
                ->when($request->new_values, function($query) use($request) {
                    return $query->where('audits.new_values', 'LIKE','%'.$request->new_values.'%');
                })
                ->select('audits.*')
                ->with(['audit_user']);

            return Datatables::of($audits)
                ->addColumn('auditable_type_name', function ($audit) {
                    return str_replace( ["App\\Models\\"], '',  $audit->auditable_type);
                })
                ->addColumn('audit_timestamp', function ($audit) {
                    return $audit->created_at->format('Y-m-d H:i:s');
                })
                ->make(true);
            
        }

        $auditable_types = Audit::select( DB::raw("REPLACE(auditable_type, 'App\\\\Models\\\\', '') as audit_type"))
                ->distinct('audit_type')->orderBy('audit_type')->pluck('audit_type');

        $event_types = Audit::EVENT_TYPES;

        return view('system-security.auditing.index', compact('event_types', 'auditable_types'));
        
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
