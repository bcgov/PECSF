@extends('adminlte::page')

@section('content_header')
    
    @include('admin-report.partials.tabs')

    <h4 class="mx-1 mt-3">Organization Participation Trackers</h4>
    
    <div class="d-flex mt-3">
        <div class="flex-fill">
            <p><button class="ml-2 btn btn-outline-primary" onclick="window.location.href='/administrators/dashboard'">
                Back    
            </button></p>    
        </div>

        <div class="d-flex">
            <div class="mr-2">
            </div>
        </div>
    </div>

@endsection
@section('content')

<form id="export-report-form" method="post">
<div class="card search-filter">
    <div class="card-header">
        <div class="row">
            <h4 class="font-weight-bold">Report Criteria</h4>
        </div>
    </div>

    <div class="card-body pb-0 ">

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
                {{-- @if ($last_date) 
                    <p><i>Notes: The latest eligible employee process was successfully ran on {{ $last_date }}</i></p>
                @endif --}}
                <label for="yearcd">
                    Campaign Year
                </label>
                <select name="yearcd" value="" class="form-control">
                    @foreach( $date_options as $yearcd)
                        <option value="{{ $yearcd }}">{{ $yearcd }} </option>
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
            <div class="form-group col-md-12">
                <div id="export-section" class="">
                    <button type="button" id="export-btn" value="export" class="btn btn-primary">Generate</button>
                    <span id="export-section-result"></span>
                </div>
            </div>
        </div>

    </div>    
</div>
</form>

    <div class="card">
        <div class="card-header">
            <div class="row">
                <h4 class="font-weight-bold">History of Processes</h4>
            </div>
        </div>
    
        <div class="card-body">

            <div class="row">
                <div class="col-6"><button type="button" class="btn btn-primary" id="download-zip-btn" disabled>Download Selected Files as Zip</button></div>
                <div class="col-6"><span class="float-right"><button type="button" class="btn btn-outline-primary" id="refresh-btn">Reload</button></span></div>
            </div>
            <div class="p-3"></div>
           
            <table class="table table-bordered" id="history-table" style="width:100%">
                <thead>
                    <tr>
                        <th><input name="select_all" value="1" id="history-list-select-all" type="checkbox" /></th>
                        <th>Process ID</th>
                        <th>Submitted At</th>
                        <th>Submitted By</th>
                        <th>Start At</th>
                        <th>End At</th>
                        <th>Status</th>
                        <th>File Name</th>                        
                        <th>Action</th>
                        {{-- <th>Message</th> --}}
                        <th>Filename</th>
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

    <link href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/datatables-plugins/fixedheader/css/fixedHeader.bootstrap4.min.css') }}" rel="stylesheet">
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

        div.dataTables_wrapper div.dataTables_processing {
             top: 5%;
        }

</style>
@endpush


@push('js')

    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}" ></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}" ></script>
    <script src="{{ asset('vendor/datatables-plugins/fixedheader/js/dataTables.fixedHeader.min.js') }}" ></script>

    {{-- <script src="{{ asset('vendor/datatables-plugins/buttons/js/dataTables.buttons.min.js') }}" ></script>
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/buttons.html5.min.js') }}" ></script> --}}

    <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>

    <script>

    $(function() {

        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': '{{csrf_token()}}'
            }
        });

        // selection
        let g_matched_processes = {!!json_encode($matched_process_ids)!!};
        let g_selected_processes = {!!json_encode($old_selected_process_ids)!!};  
        let g_batch_ids = [];

        // Datatables
        var oTable = $('#history-table').DataTable({
            // dom: 'Blfrtip',
            // buttons: [
            //     { extend: 'csv',  className: 'btn btn-primary', 
            //         text: 'Current page to CSV',
            //         filename: function() {
            //                 var date = $('#yearcd').val();
            //                 return 'Challenge_Page_Data_on_' + date;
            //         },
            //     },
            //     { extend: 'excel', className: 'btn btn-primary',
            //         text: 'Current page to Excel',
            //         // messageTop: 'Year : ' + $('#year').val() + '  and As of date : ' + $('#yearcd').val(),
            //         filename: function() {
            //                 var date = $('#yearcd').val();
            //                 return 'Challenge_Page_Data_on_' + date;
            //         },
            //     },
            // ], 
            "scrollX": true,
            retrieve: true,
            "searching": true,
            processing: true,
            "language": {
               processing: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw text-info"></i><span class="sr-only">Loading...</span>'
            },
            serverSide: true,
            searchDelay: 400,
            select: true,
            'order': [[1, 'desc']],
            fixedHeader: true,   
            "initComplete": function(settings, json) {
                    oTable.columns.adjust().draw();
            },
            ajax: {
                url: '{!! route('reporting.org-partipation-tracker.index') !!}',
                data: function (data) {
                    // data.year = $("select[name='year']").val();
                },
                complete: function(xhr, resp) {
                    min_height = $(".wrapper").outerHeight();
                    $(".main-sidebar").css('min-height', min_height);
                },
                error: function(xhr, resp, text) {
                        if (xhr.status == 401) {
                            { // session expired 
                                window.location.href = '/login'; 
                            }
                        }
                },
            },
            "fnDrawCallback": function() {

                list = ( $('#history-table input:checkbox') );

                $.each(list, function( index, item ) {
                    var pos = $.inArray( parseInt(item.value) , g_selected_processes);
                    // console.log( pos + ' - ' + item.value + ' - ' + g_selected_employees);
                    if ( pos === -1 ) {
                        $(item).prop('checked', false); // unchecked
                    } else {
                        $(item).prop('checked', true);  // checked 
                    }
                });

                // update the check all checkbox status 
                if (g_selected_processes.length == 0) {
                    $('#history-list-select-all').prop("checked", false);
                    $('#history-list-select-all').prop("indeterminate", false);   
                } else if (g_selected_processes.length == g_matched_processes.length) {
                    $('#history-list-select-all').prop("checked", true);
                    $('#history-list-select-all').prop("indeterminate", false);   
                } else {
                    $('#history-list-select-all').prop("checked", false);
                    $('#history-list-select-all').prop("indeterminate", true);    
                }

            },
            columns: [
                {data: 'select_process', name: 'select_process', orderable: false, searchable: false},
                {data: 'id', className: "dt-nowrap"},
                {data: 'submitted_at',  className: "dt-nowrap"},
                {data: 'created_by.name',  defaultContent: '', className: "dt-nowrap"},
                {data: 'start_at', defaultContent: '', className: "dt-nowrap" },
                {data: 'end_at', defaultContent: '', className: "dt-nowrap"},
                {data: 'status', "className": "dt-center"},
                {data: 'download_file_link', className: "dt-nowrap", orderable: false},                
                {data: 'action', className: "dt-nowrap", orderable: false, searchable: false},                
                // {data: 'message_text', className: "dt-nowrap"},
                {data: 'filename', className: "dt-nowrap", visible: false},                
            ],
            columnDefs: [
                {
                  
                },
                 
            ],
        });
        
        let refreshing = false;
        oTable.on('search.dt', function () {

            if (refreshing) {
                // refresh button click -- ignore (no action)
            } else {
                // clear up current selection
                var value = $('.dataTables_filter input').val();

                if (g_selected_processes.length > 0) {
                    $('#history-list-select-all').trigger('click');
                }

                // Update the g_matched_process
                $.ajax({
                    method: "GET",
                    url:  '{!! route('reporting.org-partipation-tracker.filtered-ids') !!}',
                    data: {
                        'term': value,
                    },
                    dataType: 'json',
                    success: function(data)
                    {
                        g_matched_processes = data;
                        // $('#processModalLabel').html('Process : ' + data.id + ' (' + data.process_name + ')' );
                        // //  started at ' + data.start_time);
                        // $('#modal-status').html(data.status);
                        // $('#modal-message').html(data.message);
                        // $('#process-show-modal').modal('show');
                    },
                    error: function(response) {
                        console.log('Error');
                    }
                });

            }

            refreshing = false;
        });

        $("#refresh-btn").click(function() {
            refreshing = true;
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

        // Select/Unselect handling
        $('#history-table tbody').on( 'click', 'input:checkbox', function () {

            // if the input checkbox is selected 
            var id = Number(this.value);
            var index = $.inArray(id, g_selected_processes);
            if(this.checked) {
                g_selected_processes.push( id );
            } else {
                g_selected_processes.splice( index, 1 );
            }

            // update the check all checkbox status 
            if (g_selected_processes.length == 0) {
                $('#history-list-select-all').prop("checked", false);
                $('#history-list-select-all').prop("indeterminate", false);   
            } else if (g_selected_processes.length == g_matched_processes.length) {
                $('#history-list-select-all').prop("checked", true);
                $('#history-list-select-all').prop("indeterminate", false);   
            } else {
                $('#history-list-select-all').prop("checked", false);
                $('#history-list-select-all').prop("indeterminate", true);    
            }

            // Enable/Disable Download Zip files
            if (g_selected_processes.length > 0) {
                $('#download-zip-btn').prop('disabled', false);
            } else {
                $('#download-zip-btn').prop('disabled', true);
            }

        });

            // Handle click on "Select all" control
        $('#history-list-select-all').on('click', function() {

            //g_selected_processes = g_matched_processes.map((x) => x);

            // Check/uncheck all checkboxes in the table
            $('#history-table tbody input:checkbox').prop('checked', this.checked);
            if (this.checked) {
                g_selected_processes = g_matched_processes.map((x) => x);
                $('#history-list-select-all').prop("checked", true);
                $('#history-list-select-all').prop("indeterminate", false);    
            } else {
                g_selected_processes = [];
                $('#history-list-select-all').prop("checked", false);
                $('#history-list-select-all').prop("indeterminate", false);    
            }    
            
            // Enable/Disable Download Zip files
            if (g_selected_processes.length > 0) {
                $('#download-zip-btn').prop('disabled', false);
            } else {
                $('#download-zip-btn').prop('disabled', true);
            }

        });


         // Model -- Show
    	$(document).on("click", ".more-link , .show-process" , function(e) {
			e.preventDefault();

            id =  $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url:  '{!! route('reporting.org-partipation-tracker.index') !!}/' + id,
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

                    // clean up search filter
                    $('#history-table_filter input[type=search]').val('');
                    oTable.search('').columns().search('').draw();

                    var form = $('#export-report-form');

                    // Use ajax call to submit
                    $.ajax({
                        method: "GET",
                        dataType: 'json',
                        url: '{!! route('reporting.org-partipation-tracker.export2csv') !!}',
                        data: form.serialize(), // serializes the form's elements.
                        success: function(data) {

                            g_batch_ids = JSON.parse( data.batch_ids );
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

            ids = JSON.stringify( g_batch_ids );

            $.ajax({
                method: "GET",
                dataType: 'json',
                url:  '/reporting/org-partipation-tracker/export-progress',
                data: {
                    'batch_ids': ids,
                },
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


        $("#download-zip-btn").click(function() {

            // checking and confirmation before pass to server 
            Swal.fire({
                text: 'Are you sure to dowload the selected ' + g_selected_processes.length + ' files as a zip archive ?',
                // icon: 'question',
                //showDenyButton: true,
                confirmButtonText: 'Download',
                showCancelButton: true,
            }).then((result) => {

                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {


                    ids = encodeURIComponent(JSON.stringify( g_selected_processes ));
                    window.location.href = '{!! route('reporting.org-partipation-tracker.download-export-files-in-zip') !!}?ids=' + ids ; 

                    // to reset previous selected files
                    $('#history-list-select-all').trigger('click');

                }

            });

        })

    });
    </script>
@endpush
