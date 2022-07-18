@extends('adminlte::page')

@section('content_header')

@include('admin-campaign.partials.tabs')

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
                <input type="button" id="reset-btn" value="Reset" class="form-control btn btn-secondary" />
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
                    <th>Message</th>
				</tr>
			</thead>
		</table>

	</div>
</div>


{{-- Modal Box  --}}

<div class="modal fade" id="audit-show-modal" tabindex="-1" role="dialog" aria-labelledby="auditModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
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
                <textarea class="form-control" id="modal-message" rows="15"></textarea>
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
    
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>

    <script>

    $(function() {
 
        // Datatables
        var oTable = $('#audit-table').DataTable({
            "scrollX": true,
            retrieve: true,
            "searching": true,
            processing: true,
            serverSide: true,
            // select: true,
            'order': [[ 0, 'desc']],
            
            ajax: {
                url: '{!! route('settings.schedule_job_audits') !!}',
                data: function (data) {
                    // data.term = $('#user').val();
                    data.start_time = $('#start_time').val();
                    data.end_time  = $('#end_time').val();
                }
            },
            columns: [
                {data: 'id', name: 'id', className: "dt-nowrap" },
                {data: 'job_name', name: 'job_name', className: "dt-nowrap" },
                {data: 'start_time', name: 'start_time', className: "dt-nowrap" },
                {data: 'end_time',  name: 'end_time',  className: "dt-nowrap" },
                {data: 'status',  name: 'status',  className: "dt-nowrap" },
                {data: 'message_text', name: 'message_text'},
            ],
            columnDefs: [
                    {
                        // width: '5em',
                        // targets: [0]
                    },
            ],

        });


        $('#job_name').on('keyup change', function () {
            oTable.columns( 'job_name:name' ).search( this.value ).draw();            
        });

        $('#status').on('change', function () {
            oTable.columns( 'status:name' ).search( this.value ).draw();            
        });

        $('.datetime-range-filter').on('change', function () {
            oTable.draw();
        });

        $('#reset-btn').on('click', function() {
            $('#user').val('');
            $('.datetime-range-filter').val('');
            $('#login_method').val('');

            oTable.search( '' ).columns().search( '' ).draw();
        });

        // Model -- Show
    	$(document).on("click", ".more-link" , function(e) {
			e.preventDefault();

            id =  $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url:  '/settings/schedule-job-audits/' + id,
                dataType: 'json',
                success: function(data)
                {
                    $('#auditModalLabel').html('Job : ' + data.id + ' (' + data.job_name + ')' );
                    //  started at ' + data.start_time);
                    $('#modal-status').html(data.status);
                    $('#modal-message').val(data.message);
                    $('#audit-show-modal').modal('show');
                },
                error: function(response) {
                    console.log('Error');
                }
            });
    	});

    });

    </script>
@endpush
