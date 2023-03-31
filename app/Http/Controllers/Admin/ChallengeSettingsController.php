<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChallengeSettingsController extends Controller
{
    public function __construct()
{
    if(empty(Auth::id())){
        redirect("/login");
    }
}

    public function index(Request $requests){
    $settings = Setting::where("id","=",1)->first();

    return view('admin-campaign.challenge.index',compact('settings'));
}

    public function store(Request $request){

    Setting::updateOrCreate([
        'id' => 1,
    ],[
        $request->name => Carbon::parse($request->value),
    ]);
    return json_encode([true]);
}
}
