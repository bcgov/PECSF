@extends('adminlte::master')

@php( $dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home') )

@if (config('adminlte.use_route_url', false))
    @php( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' )
@else
    @php( $dashboard_url = $dashboard_url ? url($dashboard_url) : '' )
@endif

@section('adminlte_css')
    @stack('css')
    @yield('css')
@stop

@section('classes_body'){{ ($auth_type ?? 'login') . '-page' }}@stop

@section('body')
    <div class="{{ $auth_type ?? 'login' }}-box text-center" >

        {{-- Logo --}}
        <div class="{{ $auth_type ?? 'login' }}-logo text-center">
                {{-- <img src="{{ asset(config('adminlte.logo_img_xl')) }}" height="100px"> --}}
                <img class="mb-3 logo-image-header mx-auto d-block" src="{{ asset('img/brand/PECSF_Logo_Horiz_RGB.png') }}" alt="Provincial Employees Community Services Fund Logo" height="120px" >
                {!! config('adminlte.logo', '<b>Admin</b>LTE') !!}
        </div>

        @if ($setting->is_system_lockdown)
            <div class=" bg-warning rounded shadow mx-4 mb-4 p-2">
                <p class="h6 font-weight-bold text-center text-danger">Important: Planned Maintenance in Progress</p>
                <p class="h6 font-weight-bold text-center text-secondary">The PECSF is expected to be back on
                    </br>{{ $setting->system_lockdown_end->format('g:ia T \o\n l, M jS Y') }}.
                    </br>We apologize for any inconvenience.</p>
            </div>
        @endif


        {{-- Card Box --}}
        <div class="card {{ config('adminlte.classes_auth_card', 'card-outline card-primary') }} border-0">

            {{-- Card Header --}}
            @hasSection('auth_header')
                <div class="card-header {{ config('adminlte.classes_auth_header', '') }}">
                    <h3 class="card-title float-none text-center">
                        @yield('auth_header')
                    </h3>
                </div>
            @endif



            {{-- Card Body --}}
            <div class="card-body {{ $auth_type ?? 'login' }}-card-body {{ config('adminlte.classes_auth_body', '') }}">


                @yield('auth_body')
            </div>

            {{-- Card Footer --}}
            @hasSection('auth_footer')
                <div class="card-footer {{ config('adminlte.classes_auth_footer', '') }}">
                    @yield('auth_footer')
                </div>
            @endif

        </div>

    </div>
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')
@stop
