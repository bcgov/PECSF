@extends('adminlte::page')

@section('content_header')
    <div class="d-flex mt-3">
        <h1>Forms</h1>
        <div class="flex-fill"></div>
    </div>
@endsection



@section('content')

    @include('volunteering.partials.form_tabs')




            @include('volunteering.partials.form')


    @push('css')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


        <style>
            .select2 {
                width:100% !important;
            }
            .select2-selection--multiple{
                overflow: hidden !important;
                height: auto !important;
                min-height: 38px !important;
            }

            .select2-container .select2-selection--single {
                height: 38px !important;
            }
            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 38px !important;
            }

            table tr{
                background:#fff;
            }

        </style>
        <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">

    @endpush


    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>

        <script type="x-tmpl" id="organization-tmpl">
            @include('volunteering.partials.add-organization', ['index' => 'XXX','charity' => "YYY"] )
        </script>
            @include('volunteering.partials.add-event-js')
            @include('donate.partials.choose-charity-js')
    @endpush
@endsection
