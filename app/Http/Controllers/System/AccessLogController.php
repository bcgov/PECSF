<?php

namespace App\Http\Controllers\System;

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
                            // ->where( function($query) use($request) {
                            //         return $query->where('users.name','LIKE','%'.$request->term.'%')
                            //             ->orWhere('users.idir','LIKE','%'.$request->term.'%')
                            //             ->orWhere('users.emplid','LIKE','%'.$request->term.'%');
                            // })
                            ->when($request->user_id, function($query) use($request) {
                                return $query->where('users.id', $request->user_id);
                            })
                            ->when($request->term , function($query) use($request) {
                                return $query->where('users.name','LIKE','%'.$request->term.'%')
                                        ->orWhere('users.idir','LIKE','%'.$request->term.'%')
                                        ->orWhere('users.emplid','LIKE','%'.$request->term.'%');
                            })
                            ->when($request->login_at_from || $request->login_at_to, function($query) use($request) {
                                $from = $request->login_at_from ?? '1990-01-01';
                                $to = $request->login_at_to ?? '2099-12-31';
                                return  $query->whereBetween('login_at',[ $from, $to]);
                            })
                            ->when($request->login_method, function($query) use($request) {
                                return $query->where('login_method', $request->login_method); 
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
        } else {

            // Non Ajax call
            if ($request->user_id) {
                session()->put('_old_input', [
                    'user_id' => $request->user_id,
                ]);

                $selected_user = User::where('id', $request->user_id)->first() ?? null;
                $request->session()->flash('selected_user', $selected_user);
            }
    
        }

        return view('system-security.access-logs.index',compact('request') );

    }

    public function show(Request $request, $id) {

        $user = User::where('id', $id)->with('primary_job')->first();

        return view('system-security.access-logs.partials.user-detail', compact('user') )->render();

    }

    public function getUsers(Request $request)
    {

        $term = trim($request->q);

        $users = User::orderby('name','asc')->select('id','name', 'emplid')->limit(100)
                        ->when( $term, function($query) use($term) {
                            return $query->where('name', 'like', '%' .$term . '%')
                                         ->orWhere('emplid', 'like', '%' .$term . '%');
                        })
                        ->get();
   
         $formatted_users = [];
         foreach ($users as $user) {
            $text = $user->name;
            $text .= $user->emplid ? ' (' . $user->emplid . ')' : '';
            $formatted_users[] = ['id' => $user->id, 'text' => $text];
        }

        return response()->json($formatted_users);

    }

}
