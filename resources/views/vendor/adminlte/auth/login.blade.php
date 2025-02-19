@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <style>
        .card.card-outline.card-primary {
            border: none;
        }

        .login-box {
            width: 530px !important;
        }
    </style>
@stop

@php( $login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login') )
@php( $register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register') )
@php( $password_reset_url = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset') )

@if (config('adminlte.use_route_url', false))
    @php( $login_url = $login_url ? route($login_url) : '' )
    @php( $register_url = $register_url ? route($register_url) : '' )
    @php( $password_reset_url = $password_reset_url ? route($password_reset_url) : '' )
@else
    @php( $login_url = $login_url ? url($login_url) : '' )
    @php( $register_url = $register_url ? url($register_url) : '' )
    @php( $password_reset_url = $password_reset_url ? url($password_reset_url) : '' )
@endif

{{-- @section('auth_header', __('adminlte::adminlte.login_message')) --}}

@section('auth_body')

    @php( $has_admin_error = ($errors->has('email') || $errors->has('password') ))

    @if ($message = Session::get('error-psft'))
    <div class="alert alert-danger alert-dismissible">
        <a href="#" class="close" style="text-decoration: none;"  data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Login error!</strong> {{ $message }}
    </div>
    @endif

    <div id="idir-login" style="{{  $errors->has('email')  ? 'display:none;' : '' }}" >
        <div class="text-center py-3">
                <h3 class="font-weight-bold">Log in to start your session<h1>
                    <p class="my-4 ">
                        <form action="{{ '/login/keycloak' }}" method="get">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg">Login with Your BC Govt login ID </button>
                        </form>
                    </p>
        </div>
        <div class="py-2 border-top text-left ">
            <div class="pt-4 h6 font-weight-bold">Need Help?</div>
            <div class="">Contact your IDIR security administrator or the 7-7000 Service Desk at:</div>
            <div class="pt-2">Phone: <a style="text-decoration:underline;" href="tel:0612345678">250-387-7000</a></div>
            <div>Email: <a style="text-decoration:underline;" href="mailto:77000@gov.bc.ca" target="_blank" >77000@gov.bc.ca</a></div>

            {{-- @if (!str_contains(Request::url(), 'pecsf-test.apps.silver.devops.gov.bc.ca'))   --}}
            @if ((Request::is('admin/login')) || (in_array(env('APP_ENV'), ['dev', 'local', 'TEST', 'demo'])))
                <div class="py-4 small"><a class="sysadmin-login" href="">Log in as a System Administrator</a></div>
            @endif
            {{-- @endif --}}

        </div>
    </div>

    <div id="admin-login" style="{{  Session::get('error-psft') || $has_admin_error == false ? 'display:none;' : '' }}">
        <div class="text-center py-3">
            <h3 class="font-weight-bold">Log in to start your session</h3>
        </div>

        <form action="{{ $login_url }}" method="post">
            @csrf

            {{-- Email field --}}
            <div class="input-group mb-3">
                <input type="email" title="PECSF email login field" name="email" class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}" autofocus>

                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                    </div>
                </div>

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- Password field --}}
            <div class="input-group mb-3">
                <input type="password" title="PECSF password login field" name="password" class="form-control @error('password') is-invalid @enderror"
                    placeholder="{{ __('adminlte::adminlte.password') }}">

                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                    </div>
                </div>

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- Login field --}}
            <div class="row">
                <div class="col-7">
                    <div class="icheck-primary" title="{{ __('adminlte::adminlte.remember_me_hint') }}">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                        <label for="remember">
                            {{ __('adminlte::adminlte.remember_me') }}
                        </label>
                    </div>
                </div>

                <div class="col-5">
                    <button type=submit class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
                        <span class="fas fa-sign-in-alt"></span>
                        {{ __('adminlte::adminlte.sign_in') }}
                    </button>
                </div>
            </div>

        </form>

        <div class="py-3"><button style="background:none;border:none;"><a class="idir-login" >Back</a></button></div>

    </div>
@stop

@section('auth_footer')

    {{-- Login with Azure Active Directory --}}
    {{-- <p class="my-4">
        <form action="{{ '/login/microsoft' }}" method="get">
            <button type="submit" class="btn btn-success">Signin with Your BC Govt login ID </button>
        </form>
    </p> --}}

    {{-- Password reset link --}}
    {{-- @if($password_reset_url)
        <p class="my-0">
            <a href="{{ $password_reset_url }}">
                {{ __('adminlte::adminlte.i_forgot_my_password') }}
            </a>
        </p>
    @endif --}}

    {{-- Register link --}}
    {{-- @if($register_url)
        <p class="my-0">
            <a href="{{ $register_url }}">
                {{ __('adminlte::adminlte.register_a_new_membership') }}
            </a>
        </p>
    @endif --}}
@stop

@push('js')
<script>
    $(function() {
        console.log( "ready!" );

        $(document).on("click",".sysadmin-login",function(event) {
            event.preventDefault();
            $('#idir-login').hide();
            $('#admin-login').show();
        });

        $(document).on("click",".idir-login",function(event) {
            event.preventDefault();
            $('#idir-login').show();
            $('#admin-login').hide();
        });

    });

    </script>
@endpush
