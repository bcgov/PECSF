<?php

namespace App\Http\Controllers\Admin;

use App\Models\AccessLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
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


            // $columns = ["code","name","status","created_at"];
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
                            ->select('access_logs.*', 'users.name', 'users.idir', 'users.emplid');
                            
//    return( [$access_logs->toSql(), $access_logs->getBindings() ]);                                

            return Datatables::of($access_logs)
                    ->addIndexColumn()
                    ->make(true);
        }

        return view('admin-campaign.access-logs.index',compact('request') );

    }

}
