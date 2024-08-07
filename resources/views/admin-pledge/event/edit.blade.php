
@extends('adminlte::page')

@section('content_header')

    @include('admin-pledge.partials.tabs')

    <h4 class="mx-1 mt-3">Edit an Event Pledge</h4>

    <div class="mx-1 pt-2">
        <button class="btn btn-outline-primary" onclick="window.location.href='{{ route('admin-pledge.maintain-event.index') }}'">
            Back
        </button>
    </div>


@endsection
@section('content')

<div class="card pb-4">
    <div class="card-body py-0">

        @include('admin-pledge.event.partials.form')

    </div>
</div>

@endsection
@push('css')
    
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
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
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>

    <script type="x-tmpl" id="organization-tmpl">
        @include('volunteering.partials.add-organization', ['index' => 'XXX', 'charity' => "YYY"] )
    </script>

    <script>

        $(function () {
            $('.closeModalBtn').on('click', function() {
                $('#regionalPoolModal').modal('hide');
            });
        });

    </script>

    @include('donate.partials.choose-charity-js')

@endpush
