
@extends('adminlte::page')

@section('content_header')


    <div class="d-flex mt-3">
        <h1>Pledge Administration</h1>
        <div class="flex-fill"></div>
    </div>
    <br>
    <br>
    @include('admin-pledge.partials.tabs')
@endsection
@section('content')
    <a href="/admin-pledge/maintain-event">Event Pledge List</a><span style="font-weight:bold;"> > Add New Event Pledge</span>
    <br>
    <br>
    <a href="/admin-pledge/maintain-event"><button class="btn btn-outline-primary" >Back To List</button></a>
@include('volunteering.partials.form')
    <a href="/admin-pledge/maintain-event"><button class="btn btn-outline-primary" >Back To List</button></a>
@endsection
@push('css')
    
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">
    
    <style>
        #campaign-table_filter label {
            text-align: right !important;
            padding-right: 10px;
        }
        .dataTables_scrollBody {
            margin-bottom: 10px;
        }

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

@endpush
@push('js')

    <script src="{{ asset('vendor/select2/js/select2.min.js') }}" ></script>
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}" ></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}" ></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>

    <script type="x-tmpl" id="organization-tmpl">
        @include('volunteering.partials.add-organization', ['index' => 'XXX', 'charity' => "YYY"] )
    </script>

    <script type="x-tmpl" id="attachment-tmpl">
        @include('volunteering.partials.add-attachment', ['index' => 'XXX'] )
    </script>
    <script>

        $(document).on("click",".more-info-pledge", function(e){
            e.preventDefault();
            $("#more_info_pledge").html($("#"+$(this).attr("data-id")).clone());
            $("#more_info_pledge").find("tr").css("display","");
            $('#pledgeModal').modal('show');
        });

        $(document).on("click", ".add-event-modal" , function(e) {
            e.preventDefault();
            $('#add-event-modal').modal('show');
        });
        $("select").select2();

    </script>
    @include('volunteering.partials.add-event-js')
    @include('donate.partials.choose-charity-js')
@endpush
