<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    {{-- Base Meta Tags --}}
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Custom Meta Tags --}}
    @yield('meta_tags')

    {{-- Title --}}
    <title>
        @yield('title_prefix', config('adminlte.title_prefix', ''))
        @yield('title', config('adminlte.title', 'AdminLTE 3'))
        @yield('title_postfix', config('adminlte.title_postfix', ''))
    </title>

    {{-- Custom stylesheets (pre AdminLTE) --}}
    @yield('adminlte_css_pre')

    {{-- Base Stylesheets --}}
    @if(!config('adminlte.enabled_laravel_mix'))
        <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">

        {{-- Configured Stylesheets --}}
        @include('adminlte::plugins', ['type' => 'css'])

        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    @else
        <link rel="stylesheet" href="{{ mix(config('adminlte.laravel_mix_css_path', 'css/app.css')) }}">
    @endif

    {{-- Livewire Styles --}}
    @if(config('adminlte.livewire'))
        @if(app()->version() >= 7)
            @livewireStyles
        @else
            <livewire:styles />
        @endif
    @endif

    {{-- Custom Stylesheets (post AdminLTE) --}}
    @yield('adminlte_css')

    {{-- Favicon --}}
    @if(config('adminlte.use_ico_only'))
        <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}" />
    @elseif(config('adminlte.use_full_favicon'))
        <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}" />
        <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('favicons/apple-icon-57x57.png') }}">
        <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('favicons/apple-icon-60x60.png') }}">
        <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('favicons/apple-icon-72x72.png') }}">
        <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('favicons/apple-icon-76x76.png') }}">
        <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('favicons/apple-icon-114x114.png') }}">
        <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('favicons/apple-icon-120x120.png') }}">
        <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('favicons/apple-icon-144x144.png') }}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('favicons/apple-icon-152x152.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicons/apple-icon-180x180.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicons/favicon-16x16.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicons/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicons/favicon-96x96.png') }}">
        <link rel="icon" type="image/png" sizes="192x192"  href="{{ asset('favicons/android-icon-192x192.png') }}">
        <link rel="manifest" href="{{ asset('favicons/manifest.json') }}">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="{{ asset('favicon/ms-icon-144x144.png') }}">
    @endif

</head>

<body class="@yield('classes_body')" @yield('body_data')>
    @if( session()->has('special-campaign-banner-text') and !str_contains( Route::current()->getName(), 'special-campaign.' ) )

    <div class="top-message-bar p-3 text-center bg-warning d-flex justify-content-center align-items-center XXsticky-top">
        <span class="flex-fill"></span>
        <span class="mx-4 h6 text-primary font-weight-bold">
            {{-- <i class="icon fas fa-exclamation-circle"></i> --}}
            {{-- <span class>{{ session()->get('special-campaign-banner-text') }}<span class="ml-2 ">|</span></span> --}}

            <div class="special-campaign-container">
                <ul>
                    @foreach ( session()->get('special-campaign-banner-text') as $text )
                       <li>{{  $text }}</li>
                    @endforeach
                </ul>
            </div>
            
        </span>
        <span class="h6 mx-2">|</span>
        <span class="h6 text-primary font-weight-bold special-campaign">
            <u><a href="{{ route('special-campaign.index') }}" class="text-danger mx-2">Make a Donation</a></u>
        </span>
        <span class="flex-fill"></span>

        <div class="form-inline" style="position:absolute; right:20px">
            {{-- <x-button :href="route('home')" size="sm" style="light" class="mx-2">Return to my profile</x-button> --}}
            <button type="submit" class="close">
                <span aria-hidden="true" class="h2 font-weight-bold">×</span>
            </button>
        </div>
    </div>
    @endif

    {{-- Body Content --}}
    @yield('body')

    {{-- Base Scripts --}}
    @if(!config('adminlte.enabled_laravel_mix'))
        <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>

        {{-- Configured Scripts --}}
        @include('adminlte::plugins', ['type' => 'js'])

        <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    @else
        <script src="{{ mix(config('adminlte.laravel_mix_js_path', 'js/app.js')) }}"></script>
    @endif

    {{-- Livewire Script --}}
    @if(config('adminlte.livewire'))
        @if(app()->version() >= 7)
            @livewireScripts
        @else
            <livewire:scripts />
        @endif
    @endif
    {{-- Add Footer here --}}
    @if(!request()->is('login') && !request()->is('register') && !request()->is('password/*'))
    <div class="d-flex align-items-center" style="background:#fff;padding:50px;">
        <img src="{{asset('img/brand/1.png')}}" alt="" class="p-3" style="height:140px;">
        <img src="{{asset('img/brand/2.png')}}" alt="" class="p-3" style="height:140px;">
        <img src="{{asset('img/brand/3.png')}}" alt="" class="p-3" style="height:140px;">
        <div class="flex-fill"></div>
        <strong class="float-right p-3 h5">Charity Registration no.: 889407466 RR0001</strong>
    </div>
    @endif
    {{-- Custom Scripts --}}
    @yield('adminlte_js')

    @if( session()->has('special-campaign-banner-text') and !str_contains( Route::current()->getName(), 'special-campaign.' ) )
    <style>
        .special-campaign-container ul, .special-campaign-container ul li {
			padding: 0;
			margin: 0;
			list-style: none;
			text-align: center;
		}
		.special-campaign-container {
		    
		    height: 30px;
		    line-height: 30px;
            overflow: Hidden;

            /* width: 360px;
		    border: 3px solid #E74C3C;
			border-radius:5px;
			background-color:#34495E;
			color:#fff;
			padding: 5px 0;
			margin: 30px auto; */
		}
    </style>
    <script type="text/javascript" src="{{ asset('js/jQuery.scrollText.js') }}"></script>
    <script>
        $(function() {

            @if (count(session()->get('special-campaign-banner-text')) > 1 )
			$(".special-campaign-container").scrollText({
				'duration': 3000
			});
            @endif

            $('div.top-message-bar span.special-campaign').on('click', function(e) {
                // $('div.top-message-bar').removeClass('d-flex');
                // $('div.top-message-bar').fadeOut(1000);
                $('div.top-message-bar').fadeOut(1000,  function () {
                    $('div.top-message-bar').remove();
                });
            });

            $('div.top-message-bar .close').on('click', function(e) {
                $(this).hide();
                $('div.top-message-bar').fadeOut(1000,  function () {
                    $('div.top-message-bar').remove();
                });
                
                $.ajax({
                    method: "POST",
                    url:  "{{ route('special-campaign-banner.dismiss')  }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                    },
                });

            });
            
        });
    </script>
    @endif
</body>

</html>
