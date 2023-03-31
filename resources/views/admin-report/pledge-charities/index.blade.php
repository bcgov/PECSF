@extends('adminlte::page')

@section('content_header')
    {{-- <h2>Reporting</h2> --}}
    @include('admin-report.partials.tabs')
    <div class="d-flex mt-3">
        <h4>Amount by charity report</h4>
        <div class="flex-fill"></div>
    </div>
@endsection
@section('content')

<p><a href="/administrators/dashboard" class="BC-Gov-SecondaryButton">Back</a></p>

<form id="pledge-form" method="post">
<div class="card search-filter">

    <div class="card-body pb-0 ">

        <h2>Report Criteria</h2>

        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ $message }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="year">
                    Calendar Year
                </label>
                <select name="year" value="" class="form-control">
                    {{-- <option value="">All</option> --}}
                    @foreach( $years as $year)
                        <option value="{{ $year }}" {{ today()->year == $year ? 'selected' : '' }}>{{ $year }} </option>
                    @endforeach 
                </select>
            </div>

            {{-- <div class="form-group col-md-1">
                <label for="search">
                    &nbsp;
                </label>
                <button type="button" id="refresh-btn" value="Refresh" class="form-control btn btn-primary" />Search</button>
            </div>
            <div class="form-group col-md-1">
                <label for="search">
                    &nbsp;
                </label>
                <button type="button" id="reset-btn" value="Reset" class="form-control btn btn-secondary">Reset</button>
            </div> --}}

        </div>    

        <div class="form-row">
            <div class="form-group col-md-3">
                <div id="export-section" class="">
                    <button type="button" id="export-btn" value="export" class="btn btn-primary">Export</button>
                    <span id="export-section-result"></span>
                </div>
            </div>
        </div>

    </div>    
</div>
</form>

    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-row-reverse">
                <button type="button" class="btn btn-outline-primary" id="refresh-btn">Refresh</button>
            </div>

        </div>
        <div class="card-body">

            <table class="table table-bordered" id="history-table" style="width:100%">
                <thead>
                    <tr>
                        <th>Process ID</th>
                        <th>Submitted At</th>
                        <th>Start At</th>
                        <th>End At</th>
                        <th>Status</th>
                        <th>File Name</th>                        
                        <th>Message</th>
                    </tr>
                </thead>
            </table>
        </div>

    </div>


{{-- Modal Box  --}}
<div class="modal fade" id="process-show-modal" tabindex="-1" role="dialog" aria-labelledby="processModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
            <h5 class="modal-title" id="processModalLabel">Existing details</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">


                    <p class="px-2"><b>Status : </b>
                    <span id="modal-status"></span>
                    </p>


                <div class="form-group px-2">
                    <label for="message">Log Message</label>
                    <pre id="modal-message" class="border" style="white-space:pre-line;"></pre>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>

        </div>
    </div>
</div>
@endsection


@push('css')


    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/fixedheader/3.2.4/css/fixedHeader.dataTables.min.css" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">

	<style>
  
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

        .dataTables_scroll {
            padding-bottom : 20px;
        }

        /* processing position */
        div.dataTables_wrapper div.dataTables_processing {
            top: 5%;
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

        // Datatables
        var oTable = $('#history-table').DataTable({
            "scrollX": true,
            retrieve: true,
            "searching": true,
            processing: true,
            serverSide: true,
            select: true,
            'order': [[0, 'desc']],
            fixedHeader: true,   
            "initComplete": function(settings, json) {
                    oTable.columns.adjust().draw();
            },
            ajax: {
                url: '{!! route('reporting.pledge-charities.index') !!}',
                data: function (data) {
                    data.year = $("select[name='year']").val();
                },
                error: function(xhr, resp, text) {
                        if (xhr.status == 401) {
                            { // session expired 
                                window.location.href = '/login'; 
                            }
                        }
                },
            },
            columns: [
                {data: 'id', className: "dt-nowrap"},
                {data: 'submitted_at',  className: "dt-nowrap"},
                {data: 'start_at', defaultContent: '', className: "dt-nowrap" },
                {data: 'end_at', defaultContent: '', className: "dt-nowrap"},
                {data: 'status', "className": "dt-center"},
                {data: 'download_file_link', className: "dt-nowrap", orderable: false, searchable: false},                
                {data: 'message_text', className: "dt-nowrap"},
            ],
            columnDefs: [
                {
                  
                },
                 
            ],
        });
        
        $("#refresh-btn").click(function() {
            oTable.ajax.reload( null, false );
        })

        // // Move the export button to the filter area
        // $('#pledge-table_filter').parent().append( $('#export-section') );

        $(window).keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                oTable.ajax.reload();
                return false;
            }
        });

         // Model -- Show
    	$(document).on("click", ".more-link , .show-process" , function(e) {
			e.preventDefault();

            id =  $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url:  '{!! route('reporting.pledge-charities.index') !!}/' + id,
                dataType: 'json',
                success: function(data)
                {
                    $('#processModalLabel').html('Process : ' + data.id + ' (' + data.process_name + ')' );
                    //  started at ' + data.start_time);
                    $('#modal-status').html(data.status);
                    $('#modal-message').html(data.message);
                    $('#process-show-modal').modal('show');
                },
                error: function(response) {
                    console.log('Error');
                }
            });
    	});

        $('#export-btn').on('click', function() {
            
            Swal.fire({
                text: 'Are you sure to export the data ?'  ,
                // icon: 'question',
                //showDenyButton: true,
                confirmButtonText: 'Export',
                showCancelButton: true,
            }).then((result) => {

                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {

                    // refresh data tables first
                    oTable.draw();
                    $('#export-btn').prop('disabled', true);
                    $('#export-section-result').html('Queued. Please wait.');

                    var form = $('#pledge-form');

                    // Use ajax call to submit
                    $.ajax({
                        method: "GET",
                        dataType: 'json',
                        url: '{!! route('reporting.pledge-charities.export2csv') !!}',
                        data: form.serialize(), // serializes the form's elements.
                        success: function(data) {
                            batch_id = data.batch_id;
                            console.log('export job submit');
                            intervalID = setInterval(exportProgress, 3000);
                        },
                        error: function(response) {
                            $('#export-btn').prop('disabled', false);
                            console.log('Error');
                        }
                    });
                }

            })

        })

        
        function exportProgress() {

            $.ajax({
                method: "GET",
                dataType: 'json',
                url:  '/reporting/pledge-charities/export-progress/' + batch_id,
                success: function(data)
                {
                    if (data.finished) {
                        clearInterval(intervalID);
                        $('#export-btn').prop('disabled', false);
                    }
                    $('#export-section-result').html(data.message);
                },
                error: function(response) {
                    if (response.status == 422) {
                        $('#export-btn').prop('disabled', false);
                        $('#export-section-result').html('');

                        Swal.fire({
                            title: 'Export failed!',
                            text: response.responseJSON.message,
                            icon: 'error',
                        })
                        clearInterval(intervalID);
                    }
                    console.log('export job error');
                }
                
            });

            oTable.ajax.reload(null, false);	// reload datatables
        }


    });
    </script>
@endpush
