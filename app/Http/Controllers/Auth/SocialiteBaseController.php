<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;

abstract class SocialiteBaseController extends Controller {
    protected $provider;

    public function __construct($provider) {
        $this->provider = $provider;
    }
    public function login() {
        return Socialite::driver($this->provider)->redirect();
    }
    abstract public function handleCallback();
}