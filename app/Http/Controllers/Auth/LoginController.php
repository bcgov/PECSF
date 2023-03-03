<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

        // Set Special Campaign Banner when activated special campaign
        // $banner_texts = \App\Models\SpecialCampaign::activeBannerText();
        // if (count($banner_texts) > 0 ) {
        //     session()->put('special-campaign-banner-text', $banner_texts );
        // }

        if ( \App\Models\SpecialCampaign::hasActiveSpecialCampaign() ) {
            session()->put('has-active-special-campaign', 'YES' );
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

    
}
