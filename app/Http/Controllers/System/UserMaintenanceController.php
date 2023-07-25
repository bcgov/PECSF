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

            $users = User::select('users.*',
                    'employee_jobs.empl_status',
                    'employee_jobs.date_updated',
                    'employee_jobs.date_deleted',
                    'employee_jobs.business_unit',
                    'employee_jobs.deptid',
                    'employee_jobs.dept_name',
                    'regions.code as tgb_reg_district',
                    'regions.name as region_name',
                    'employee_jobs.office_city')
                        ->leftJoin('employee_jobs', function($join) {
                            $join->on('employee_jobs.organization_id', '=', 'users.organization_id')
                                 ->on('employee_jobs.emplid', '=', 'users.emplid');
                        })
                        ->where( function($query) {
                            $query->where('employee_jobs.empl_rcd', '=', function($q) {
                                    $q->from('employee_jobs as J2') 
                                        ->whereColumn('J2.emplid', 'employee_jobs.emplid')
                                        ->selectRaw('min(J2.empl_rcd)');
                                })
                                ->orWhereNull('employee_jobs.empl_rcd');
                        })
                        ->leftJoin('cities', 'employee_jobs.office_city', '=', 'cities.city')
                        ->leftJoin('regions', 'cities.tgb_reg_district', '=', 'regions.code')
                        ->when($request->source_type, function($query) use($request) {
                            return $query->where('users.source_type',  $request->source_type);
                        })
                        ->when($request->user_name, function($query) use($request) {
                            return $query->where( function($q) use($request) {
                                return $q->where('users.name', 'like', '%'.$request->user_name.'%');
                            });
                        })
                        ->when($request->emplid, function($query) use($request) {
                            return $query->where('employee_jobs.emplid', 'like', $request->emplid.'%');
                        })
                        ->when($request->acctlock != '' , function($query) use($request) {
                            return $query->where('users.acctlock', $request->acctlock);
                        })
                        ->when($request->organization_id, function($query) use($request) {
                            return $query->where('users.organization_id', $request->organization_id);
                        })
                        ->when($request->business_unit, function($query) use($request) {
                            return $query->where('employee_jobs.business_unit', 'like', '%'. $request->business_unit .'%');
                        })
                        ->when($request->deptid, function($query) use($request) {
                            return $query->where( function($q) use($request) {
                                return $q->where('employee_jobs.deptid',  'like', '%'. $request->deptid .'%')
                                         ->orWhere('employee_jobs.dept_name',  'like', '%'. $request->deptid .'%');
                            });
                        })
                        ->when($request->tgb_reg_district, function($query) use($request) {
                            return $query->where( function($q) use($request) {
                                return $q->where('employee_jobs.tgb_reg_district',  'like', '%'. $request->tgb_reg_district .'%')
                                         ->orWhere('regions.name',  'like', '%'. $request->tgb_reg_district .'%');
                            });
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
                        // ->with('organization', 'primary_job','primary_job.region')
                        ->with('organization')
                        ->withCount('access_logs')
                        ->withCount('active_employee_jobs')
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
                        ->editColumn('created_at', function ($user) {
                            return $user->created_at ? $user->created_at->format('Y-m-d H:m:s') : null; // human readable format
                        })
                        ->editColumn('updated_at', function ($user) {
                            return $user->updated_at ? $user->updated_at->format('Y-m-d H:m:s') : null; // human readable format
                        })
                        ->rawColumns(['action'])
                        ->make(true);
        }

        $source_type_options = User::source_type_options();
        $organizations = Organization::get();

        return view('system-security.users.index', compact('request', 'source_type_options', 'organizations') );

    }

    public function lockUser(Request $request, $id) {
    
        if($request->ajax()) {
            $user = User::where('id', $id)->first();

            $user->acctlock = 1;
            $user->save();
            
            return response()->noContent();

        } else {
            abort(404);
        }

    }

    public function unlockUser(Request $request, $id) {
    
        if($request->ajax()) {
            $user = User::where('id', $id)->first();

            $user->acctlock = 0;
            $user->save();
            
            return response()->noContent();

        } else {
            abort(404);
        }

    }


}
