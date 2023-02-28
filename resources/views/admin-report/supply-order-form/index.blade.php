@extends('adminlte::page')
@section('content_header')
    {{-- <h2>Reporting</h2> --}}
    @include('admin-report.partials.tabs')

@endsection
@section('content')
    <h1 style="main-blue">Supply Order Form Reporting</h1>
    <div class="btn-group mt-5" role="group" aria-label="Basic example">
        <button type="button" class="active btn btn-primary"><h2>Find a Report</h2></button>
        <button type="button" class="btn btn-outline-primary"><h2>Edit Supply Order Form</h2></button>
    </div>


    <div class="card mt-5">

        <div class="row">
            <div class="col-md-12 justify-content-start">
                <h3 class="main-blue">Filter Reports</h3>
            </div>
        </div>

        <div class=" row">

            <div class="col-md-4">

                <label>
Employee Name</label>
                    <input class="form-control" name="employee_name" id="employee_name"/>


            </div>

            <div class="col-md-4">
                <label>
              Organization Code </label>
                <input class="form-control" name="organization_code" id="organization_code"/>

            </div>
            <div class="col-md-2">
                <label>
                   Date Received </label>
                    <select class="form-control" name="month">
                   <option>Month</option>

                    </select>

            </div>
            <div class="col-md-2">
                <label>
                   Select a Year </label>
                <select class="form-control" name="month">
                    <option>Year</option>

                </select>
            </div>
        </div>

        <div class="row mt-4">

            <div class="col-md-2">
                <button class="form-control btn btn-primary">Search</button>
            </div>
            <div class="col-md-2">
                <button class="form-control btn btn-outline-primary">Clear</button>
            </div>
        </div>


    </div>

    <div class="card">
        <div class="row">
            <div class="col-md-12 justify-content-start">
                <h3 class="main-blue">Results</h3>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2 justify-content-start">
                <button class="form-control btn btn-danger">Delete (<span id="delete_count">0</span>)</button>
            </div>
            <div class="col-md-2 justify-content-start">
                <button class="form-control btn btn-primary">Export Data Set (<span id="export_count">0</span>)</button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 justify-content-center">
                <table class="table">

                    <thead>
                    <th>Select</th>
                    <th>Name</th>
                    <th>Date Received</th>
                    <th>Organization</th>
                    <th>Status</th>
                    <th>Options</th>
                    </thead>

                    @foreach($forms as $form)
                        <tbody>
                        <td><input class="form-control" type="checkbox" value="{{$form->id}}" name="supply_order_form_selection" /></td>
                        <td>{{$form->first_name." ".$form->last_name}}</td>
                        <td>{{gmdate("Y-m-d H:i:s",strtotime($form->created_at))}}</td>
                        <td>{{$form->name}}</td>
                        <td><select>
                                <option value="1">Active</option>
                                <option value="0">Disabled</option>
                            </select></td>
                        <td><button class="btn btn-primary">View / Edit</button></td>
                        </thead>

                        @endforeach
                </table>
            </div>

        </div>


    </div>


@endsection
@push('css')


    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/fixedheader/3.2.4/css/fixedHeader.dataTables.min.css" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">

    <style>
        #employee-table_filter label {
            display:none;
        }
        #employee-table_filter label {
            text-align: right !important;
            padding-right: 10px;
        }
        .dataTables_scrollBody {
            margin-bottom: 10px;
        }

        /* Blink */
        .blink {
            animation: blinker 0.6s linear infinite;
            /* color: #1c87c9;
            font-size: 30px;
            font-weight: bold;
            font-family: sans-serif; */
        }
        @keyframes blinker {
            50% {
                opacity: 0;
            }
        }
        .blink-one {
            animation: blinker-one 1s linear infinite;
        }
        @keyframes blinker-one {
            0% {
                opacity: 0;
            }
        }
        .blink-two {
            animation: blinker-two 1.4s linear infinite;
        }
        @keyframes blinker-two {
            100% {
                opacity: 0;
            }
        }

    </style>
@endpush


@push('js')

    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.2.4/js/dataTables.fixedHeader.min.js"></script>

    <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>

    <script>

        $(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                }
            });

        });
    </script>
@endpush
