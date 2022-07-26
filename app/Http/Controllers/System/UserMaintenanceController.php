<?php

namespace App\Http\Controllers\System;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class UserMaintenanceController extends Controller
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

            $users = User::select('users.*')
                        ->when($request->source_type, function($query) use($request) {
                            return $query->where('source_type',  $request->source_type);
                        })
                        ->when($request->user_name, function($query) use($request) {
                            return $query->where('name', 'like', '%'.$request->user_name.'%');
                        })
                        ->when($request->organization_id, function($query) use($request) {
                            return $query->where('organization_id', $request->organization_id);
                        })
                        // ->when($request->status, function($query) use($request) {
                        //     return $query->where('status', $request->status);
                        // })
                        ->when($request->last_signon_from || $request->last_signon_to, function($query) use($request) {
                            $from = $request->last_signon_from ?? '1990-01-01';
                            $to = $request->last_signon_to ?? '2099-12-31';
                            return  $query->whereBetween('last_signon_at',[ $from, $to]);
                        })
                        ->when($request->last_sync_from || $request->last_sync_to, function($query) use($request) {
                            $from = $request->last_sync_from ?? '1990-01-01';
                            $to = $request->last_sync_to ?? '2099-12-31';
                            return  $query->whereBetween('last_sync_at',[ $from, $to]);
                        })
                        ->with('primary_job', 'organization')
                        ->withCount('access_logs')
                        // ->having('access_logs_count', '>', 3)
                        ;
                            
            return Datatables::of($users)
                       
                        ->addColumn('action', function ($user) {
                            return '<a class="btn btn-info btn-sm  show-user" data-id="'. $user->id .'" >Show</a>' .
                                   '<a class="btn btn-danger btn-sm ml-2 delete-user" data-id="'. $user->id .
                                   '" data-title="'. $user->job_name . ' - ' . $user->start_time . '">Delete</a>';
                        })
                        // ->addColumn('deleted_by', function ($user) {
                        //     return $user->deleted_at ? $user->updated_by->name : '';       
                        // })
                        ->rawColumns(['action'])
                        ->make(true);
        }

        $source_type_options = User::source_type_options();
        $organizations = Organization::get();

        return view('system-security.users.index', compact('request', 'source_type_options', 'organizations') );

    }

}
