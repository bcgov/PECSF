<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\AccessLog;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class AccessLogController extends Controller
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

            $access_logs = AccessLog::join('users', 'users.id', 'access_logs.user_id')
                            ->where( function($query) use($request) {
                                return $query->when($request->term, function($q) use($request) {
                                         return $q->where('users.name','LIKE','%'.$request->term.'%')
                                            ->orWhere('users.idir','LIKE','%'.$request->term.'%')
                                            ->orWhere('users.emplid','LIKE','%'.$request->term.'%');
                                });
                            })
                            ->when($request->login_at_from, function($query) use($request) {
                                return $query->where('login_at', '>=', $request->login_at_from); 
                            })
                            ->when($request->login_at_to, function($query) use($request) {
                                return $query->where('login_at', '<=', $request->login_at_to); 
                            })
                            ->select('access_logs.*', 'users.name', 'users.idir', 'users.emplid')
                            ->with('user','user.primary_job');
                            
            return Datatables::of($access_logs)
                    ->addColumn('user_detail_link', function ($access_log) {
                            return '<a class="ml-2 user-detail-link" data-id="'. $access_log->user_id .
                            '" data-name="'. $access_log->user->name . '">'.$access_log->user->name .' </a>';
                    })
                    ->rawColumns(['user_detail_link'])
                    ->make(true);
        }

        return view('system-security.access-logs.index',compact('request') );

    }

    public function show(Request $request, $id) {

        $user = User::where('id', $id)->with('primary_job')->first();

        return view('system-security.access-logs.partials.user-detail', compact('user') )->render();

    }

}
