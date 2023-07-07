<?php

namespace App\Http\Controllers\System;

use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SystemSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:setting');
    }

    public function index(Request $requests){

        $setting = Setting::first();

        $setting->system_lockdown_start = $setting->system_lockdown_start ?? now();
        $setting->system_lockdown_end = $setting->system_lockdown_end ?? now();

        $allow_signout_all = (now() >= $setting->system_lockdown_start && now() <= $setting->system_lockdown_end) ? true : false;

        return view('system-security.settings.index',compact('setting', 'allow_signout_all'));
    }

    public function store(Request $request){

   
        if ($request->has('signout_all')) {

            
            $filesnames = Storage::disk('sessions')->files();
            if (($key = array_search( '.gitignore', $filesnames)) !== false) {
                unset($filesnames[$key]);
            }
            if (($key = array_search( Session::getId() , $filesnames)) !== false) {
                unset($filesnames[$key]);
            }
            Storage::disk('sessions')->delete( $filesnames );

            return redirect()->route('system.settings.index')
                    ->with('success','The current logged in users were successfully forced to signout.');

        } else {

            $validator = Validator::make(request()->all(), [
                // 'system_lockdown_start'      => 'required|date_format:Y-m-d\TH:i|after:' . date(DATE_ATOM, strtoTime("-10 min")),
                'system_lockdown_start'      => 'required|date_format:Y-m-d\TH:i|after:' . now()->format('Y-m-d 00:00'),
                'system_lockdown_end'        => 'required|date_format:Y-m-d\TH:i|after:system_lockdown_start',
            ],[

            ]);

            //run validation which will redirect on failure
            $validator->validate();

            $setting = Setting::first();
        
            $setting->system_lockdown_start = $request->system_lockdown_start;
            $setting->system_lockdown_end   = $request->system_lockdown_end;

            $setting->save();

            return redirect()->route('system.settings.index')
                ->with('success','The setting was successfully saved.');

        }
        
    }

}
