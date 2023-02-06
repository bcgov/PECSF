<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

class SettingsController extends Controller
{

    public function __construct()
    {
        if(empty(Auth::id())){
            redirect("/login");
        }
    }

    public function index(Request $requests){
        $settings = Setting::where("id","=",1)->first();

        return view('settings.index',compact('settings'));
    }

    public function volunteering(Request $requests){
        $settings = Setting::where("id","=",1)->first();

        return view('settings.volunteering',compact('settings'));
    }

    public function challenge(Request $requests){

        $settings = Setting::where("id","=",1)->first();

        return view('settings.challenge',compact('settings'));
    }

    public function updateSetting(Request $request){

        $settings = Setting::update([
            'id' => 1,
        ],[
            $request->name => $request->value,
        ]);

    }
}
