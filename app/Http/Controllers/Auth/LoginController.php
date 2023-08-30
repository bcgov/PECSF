<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm(Request $request) 
    {
        $setting = Setting::first();
        $system_lockdown = $setting->is_system_lockdown;

        if ( ($request->path() != 'admin/login') && $system_lockdown) {
            return view('system-security.lockdown.index', compact('setting'));
        }

        return view('vendor.adminlte.auth.login', compact('setting'));
    }

    public function credentials(Request $request)
    {
        return [
            'email' => request()->email,
            'password' => request()->password,
            'acctlock' => 0,                        //  check additional field during login 
            'source_type' => 'LCL',                 //  check additional field during login 
        ];
    } 

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {

        if (!($this->isAllowLoginDuringMaintenance($user))) {
            // same as AuthenticatedSessionController::logout();
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->with('error-psft', 'Maintenace In Progress.');
        }

        // Set Special Campaign Banner when activated special campaign
        // $banner_texts = \App\Models\SpecialCampaign::activeBannerText();
        // if (count($banner_texts) > 0 ) {
        //     session()->put('special-campaign-banner-text', $banner_texts );
        // }

        if ( \App\Models\SpecialCampaign::hasActiveSpecialCampaign() ) {
            session()->put('has-active-special-campaign', 'YES' );
        }

        // Add a flash to display annoucement if required
        $hasAnnouncement = \App\Models\Announcement::hasAnnouncement();
        if ($hasAnnouncement) {
            $request->session()->flash('has-announcement', 'YES');
        }

        // Update the last signon datetime
        $user->last_signon_at = Carbon::now();
        $user->save();

        // Write to access log
        \App\Models\AccessLog::create([
            'user_id' => $user->id,
            'login_at' => Carbon::now(), 
            'login_ip' => $request->getClientIp(),
            'login_method' => 'Laravel UI',
       ]);
    }

    
    private function isAllowLoginDuringMaintenance($login_user) 
    {
        $bAllow = true;

        $setting = Setting::first();
        if ($setting->is_system_lockdown) {
            if ($login_user->hasRole(['admin'])) {
                // allow to sign if administrator
            } else {
                $bAllow = false;
            }
        }
        return $bAllow;
    }

}
