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
    @if( session()->has('has-active-special-campaign') and !str_contains( Route::current()->getName(), 'special-campaign.' ) )

        {{-- <div class="top-message-bar pt-2 pb-1 text-center bg-warning d-flex justify-content-center align-items-center XXsticky-top"> --}}
        <div class="top-message-bar p-2  bg-warning   XXsticky-top">
            <div class="row  justify-content-center ">
                <div class="col-sm-12 col-md-8 col-lg-10">
                    <div class="float-right">
                        {{-- <span class="h6">One of three columns</span> --}}
                        <div class="special-campaign-container">
                            <div class="row pr-1">
                                <div class="v-slider-frame">
                                    <ul class="v-slides">
                                        @foreach ( \App\Models\SpecialCampaign::activeBannerText() as $text )
                                            <li class="v-slide">{{  $text }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4 col-lg-2">
                    <div class="float-left">
                        <span class="h6 mx-2">|</span>
                        <span class="h6 text-primary font-weight-bold special-campaign">
                            <u><a href="{{ route('special-campaign.index') }}" class="text-danger mx-2">Make a Donation</a></u>
                        </span>
                    </div>
                    <div class="form-inline float-right align-middle pr-2">
                        <button type="submit" class="close">
                            <span aria-hidden="true" class="h6 font-weight-bold">X</span>
                        </button>
                    </div>
                </div>
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
    @if(!request()->is('login') && !request()->is('register') && !request()->is('password/*') && !request()->is('admin/login'))
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

    @if( session()->has('has-active-special-campaign') and !str_contains( Route::current()->getName(), 'special-campaign.' ) )
        <style>
            .v-slider-frame {
                border: none;
                height: 30px;
                overflow: hidden;
                text-align: right;
            }
            ul.v-slides {
                list-style-type: none;
                transform: translateY(30px);
                padding:0;
            }
            .v-slide {
                font-size: 1.0em;
                line-height: 30px;
                color: #1a5a96;
            }
        </style>
        <script src="//cdnjs.cloudflare.com/ajax/libs/gsap/1.14.2/TweenMax.min.js"></script>
        <script>
            var vsOpts = {
                $slides: $('.v-slide'),
                $list: $('.v-slides'),
                duration: 8,
                lineHeight: 30
            }

            var vSlide = new TimelineMax({
                paused: true,
                repeat: -1
            })

            vsOpts.$slides.each(function(i) {
                vSlide.to(vsOpts.$list, vsOpts.duration / vsOpts.$slides.length, {
                    y: i * -1 * vsOpts.lineHeight,
                    ease: Elastic.easeOut.config(1, 0.7)
                })
            })
            vSlide.play();
        </script>
        <script>
            $(function() {
                $('div.top-message-bar span.special-campaign').on('click', function(e) {
                    $('div.top-message-bar').removeClass('d-flex');
                    $('div.top-message-bar').fadeOut(1000);
                });

                $('div.top-message-bar .close').on('click', function(e) {
                    $(this).hide();
                    $('div.top-message-bar').removeClass('d-flex');
                    $('div.top-message-bar').fadeOut(1000);
                    
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
    {{-- Global AjaxError event to redirect to login page when the session was expired --}}
    <script>
        $(function() {
            $(document).ajaxError(function(event, jqxhr, settings, exception) {
                if (jqxhr.status == 401 || jqxhr.status == 419) {
                   // session expired 
                   window.location.href = '/login'; 
                }
                console.log('global ajaxError handler -- status ' + jqxhr.status + ' | ' + exception);
            });
        });
    </script>

    {{-- Accessibility features --}}
    @include('aria-accessibility.menu-button-links-js')
    <script>
        $(function() {
            {{-- Sidebar open/close button --}}
            $(document).on('collapsed.lte.pushmenu', function () {
			    $('.main-sidebar a').attr('tabindex', -1);
                $('a.nav-link[data-widget="pushmenu"]').attr('aria-label', 'This button will display the left menu bar');
		    }).on('shown.lte.pushmenu', function () {
			    $('.main-sidebar a').removeAttr('tabindex');
                $('a.nav-link[data-widget="pushmenu"]').attr('aria-label', 'This button will hide the left menu bar');
            });

            // Initialize menu buttons
            $('li.user-menu, li.has-treeview').each( function() {
                new MenuButtonLinks( $(this).get(0) );
            });
  
        });

    </script>

    <script>
        // <!-- Snowplow starts plowing - Standalone vE.2.14.0 -->
        
        ;(function(p,l,o,w,i,n,g){if(!p[i]){p.GlobalSnowplowNamespace=p.GlobalSnowplowNamespace||[];
        p.GlobalSnowplowNamespace.push(i);p[i]=function(){(p[i].q=p[i].q||[]).push(arguments)
        };p[i].q=p[i].q||[];n=l.createElement(o);g=l.getElementsByTagName(o)[0];n.async=1;
        n.src=w;g.parentNode.insertBefore(n,g)}}(window,document,"script","https://www2.gov.bc.ca/StaticWebResources/static/sp/sp-2-14-0.js","snowplow"));
        
        var collector = '{{ env('SNOWPLOW_COLLECTOR') }}';
        window.snowplow('newTracker','rt',collector, {
        appId: 'Snowplow_standalone_PSA',
        cookieLifetime: 86400 * 548,
        platform: 'web',
        post: true,
        forceSecureTracker: true,
        contexts: {
        webPage: true,
        performanceTiming: true
        }
        });
        window.snowplow('enableActivityTracking', 30, 30); // Ping every 30 seconds after 30 seconds
        window.snowplow('enableLinkClickTracking');
        window.snowplow('trackPageView');
        
        // <!-- Snowplow stops plowing –>
    </script>
</body>

</html>
