@extends('adminlte::page')

@section('content_header')




    <div class="d-flex mt-3">
        <h1>Settings</h1>
        <div class="flex-fill"></div>
    </div>
    <br>
    <br>
    @include('settings.partials.tabs')

@endsection
@section('content')




    <p>
        Settings Drop Page to be Built out
    </p>



    <div style="clear:both;float:none;"></div>
    <br>
    <br>
    <div class="card">
        <div class="card-body">

        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="pledgeModal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="">More Info</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Deposit Date</th>
                            <th>Deposit Amount</th>
                            <th>Description</th>
                            <th>Employment City</th>
                        </tr>
                        </thead>
                        <tbody id="more_info_pledge">

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


@endpush
