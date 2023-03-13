@extends('adminlte::page')

@section('content_header')

@include('system-security.partials.tabs')

    <div class="d-flex mt-3">
        <h4>Schedule Job Audit</h4>
        <div class="flex-fill"></div>

    </div>
@endsection
@section('content')

<p><a href="/administrators/dashboard">Back</a></p>
<div class="card">

    <div class="card-body pb-0">
        <h2>Search Criteria</h2>

        <div class="form-row">
            <div class="form-group col-md-1">
                <label for="tran_id">
                    Tran Id
                </label>
                <input name="tran_id" id="tran_id" value="{{ $request->tran_id ? $request->tran_id : '' }}" class="form-control" />
            </div>

            <div class="form-group col-md-3">
                <label for="job_name">
                    Job Name
                </label>
                <input name="job_name" id="job_name"  class="form-control" />
            </div>

            <div class="form-group col-md-2">
                <label for="start_time">Start Time</label>
                <input class="form-control datetime-range-filter" type="datetime-local" id="start_time" name="start_time">
            </div>

            <div class="form-group col-md-2">
                <label for="end_time">End Time</label>
                <input class="form-control datetime-range-filter" type="datetime-local" id="end_time" name="end_time">
            </div>

            <div class="form-group col-md-2">
                <label for="status">
                    Status
                </label>
                <select name="status" id="status" value="" class="form-control">
                    <option value="">Select a status</option>
                    @foreach( $status_list as $status)
                    <option value="{{ $status }}">{{ $status }}</option>
                    @endforeach 
                    {{-- <option value="Keycloak">IDIR (Keycloak)</option> --}}
                </select>
            </div>

            <div class="form-group col-md-1">
                <label for="search">
                    &nbsp;
                </label>
                <input type="button" id="refresh-btn" value="Refresh" class="form-control btn btn-primary" />
            </div>
            <div class="form-group col-md-1">
                <label for="search">
                    &nbsp;
                </label>
                <input type="button" id="reset-btn" value="Reset" class="form-control btn btn-secondary" />
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="include_trashed" name="include_trashed">
                    <label class="form-check-label" for="include_trashed">
                    Include Trashed Schedule Job Audit
                    </label>
                </div>
            </div>
        </div>

    </div>    
    
    <div class="px-4"></div>

	<div class="card-body">

		<table class="table table-bordered" id="audit-table" style="width:100%">
			<thead>
				<tr>
                    <th>Tran ID </th>
                    <th>Job Name </th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Status</th>
                    <th>Action</th>
                    <th>Message</th>
                    <th>Delete by</th>
                    <th>Delete at</th>
				</tr>
			</thead>
		</table>

	</div>
</div>


{{-- Modal Box  --}}

<div class="modal fade" id="audit-show-modal" tabindex="-1" role="dialog" aria-labelledby="auditModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
	  <div class="modal-content">
		<div class="modal-header bg-primary">
		  <h5 class="modal-title" id="auditModalLabel">Existing details</h5>
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
                <pre id="modal-message" class="border"></pre>
            </div>
            
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
    
    </div>
</div>

@endsection


@push('css')


    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/fixedheader/3.2.4/css/fixedHeader.dataTables.min.css" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">

	<style>
	/* #audit-table_filter label {
		text-align: right !important;
        padding-right: 10px;
	} */
    
    #audit-table_filter {
        display: none;
    }

    .dataTables_scrollBody {
        margin-bottom: 10px;
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
        var oTable = $('#audit-table').DataTable({
            "scrollX": true,
            retrieve: true,
            "searching": true,
            processing: true,
            serverSide: true,
            // select: true,
            'order': [[ 0, 'desc']],
            fixedHeader: true,            
            ajax: {
                url: '{!! route('system.schedule-job-audits.index') !!}',
                data: function (data) {
                    // data.term = $('#user').val();
                    data.tran_id  = $('#tran_id').val();
                    data.job_name = $('#job_name').val();
                    data.status = $('#status').val();
                    data.start_time = $('#start_time').val();
                    data.end_time  = $('#end_time').val();
                    data.include_trashed = $('#include_trashed').prop("checked") ? '1' : '0';
                }
            },
            columns: [
                {data: 'id', name: 'id', className: "dt-nowrap" },
                {data: 'job_name', name: 'job_name',  },
                {data: 'start_time', name: 'start_time', className: "dt-nowrap" },
                {data: 'end_time',  name: 'end_time',  className: "dt-nowrap" },
                {data: 'status',  name: 'status',  className: "dt-nowrap" },
                {data: 'action', name: 'action', orderable: false, searchable: false, className: "dt-nowrap"},
                {data: 'message_text', name: 'message_text', },
                {data: 'deleted_by', name: 'delete_by', orderable: false, searchable: false, className: "dt-nowrap"},
                {data: 'deleted_at', name: 'delete_at', orderable: false, searchable: false, className: "dt-nowrap"},
                
            ],
            columnDefs: [
                    {
                        // width: '5em',
                        // targets: [0]
                    },
            ],

        });
     

        $('#refresh-btn').on('click', function() {
            // oTable.ajax.reload(null, true);
            oTable.draw();
        });

        $('#reset-btn').on('click', function() {
            $('#job_name').val('');
            $('.datetime-range-filter').val('');
            $('#status').val('');
            $('#include_trashed').prop('checked', false);

            oTable.search( '' ).columns().search( '' ).draw();
        });

        // Model -- Show
    	$(document).on("click", ".more-link , .show-audit" , function(e) {
			e.preventDefault();

            id =  $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url:  '/system/schedule-job-audits/' + id,
                dataType: 'json',
                success: function(data)
                {
                    $('#auditModalLabel').html('Job : ' + data.id + ' (' + data.job_name + ')' );
                    //  started at ' + data.start_time);
                    $('#modal-status').html(data.status);
                    $('#modal-message').html(data.message);
                    $('#audit-show-modal').modal('show');
                },
                error: function(response) {
                    console.log('Error');
                }
            });
    	});


        // Model -- Delete
        $(document).on("click", ".delete-audit" , function(e) {
            e.preventDefault();

            id = $(this).attr('data-id');
            title = $(this).attr('data-code');

            Swal.fire( {
                title: 'Are you sure you want to delete schedule job "' + title + '" ?',
                text: 'This action cannot be undone.',
                // icon: 'question',
                //showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Delete',
                buttonsStyling: false,
                //confirmButtonClass: 'btn btn-danger',
                customClass: {
                	confirmButton: 'btn btn-danger', //insert class here
                    cancelButton: 'btn btn-secondary ml-2', //insert class here
                }
                //denyButtonText: `Don't save`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    // Swal.fire('Saved!', '', '')
                    $.ajax({
                        method: "DELETE",
                        url:  '/system/schedule-job-audits/' + id,
                        success: function(data)
                        {
                            oTable.ajax.reload(null, false);	// reload datatables
                            Toast('Success', 'Schedule Job Audit ' + title +  ' was successfully deleted.', 'bg-success' );
                        },
                        error: function(response) {
                            console.log('Error');
                        }
                    });
                } else if (result.isCancelledDenied) {
                    // Swal.fire('Changes are not saved', '', '')
                }
            })
        });


    });

    </script>
@endpush
