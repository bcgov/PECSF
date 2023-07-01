<?php

namespace App\Http\Controllers\System;

use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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

        return view('system-security.settings.index',compact('setting'));
    }

    public function store(Request $request){

        $validator = Validator::make(request()->all(), [
            'system_lockdown_start'      => 'required|date_format:Y-m-d\TH:i|after:' . date(DATE_ATOM, strtoTime("-10 min")),
            'system_lockdown_end'        => 'required|date_format:Y-m-d\TH:i|after:system_lockdown_start',
        ],[

        ]);

        //run validation which will redirect on failure
        $validator->validate();

        $setting = Setting::first();
    
        $setting->system_lockdown_start = $request->system_lockdown_start;
        $setting->system_lockdown_end   = $request->system_lockdown_end;

        $setting->save();
        
        return response()->noContent();
    
    }

}
