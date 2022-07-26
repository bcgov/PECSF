@extends('adminlte::page')

@section('content_header')

@include('system-security.partials.tabs')

    <div class="d-flex mt-3">
        <h4>Schedule Job user</h4>
        <div class="flex-fill"></div>

    </div>
@endsection
@section('content')

<p><a href="/administrators/dashboard">Back</a></p>
<div class="card">

    <div class="card-body pb-0">
        <h2>Search Criteria</h2>

        <div class="form-row">
            <div class="form-group col-md-2">
                <label for="source_type">
                    Type
                </label>
                <select name="source_type" id="source_type" value="" class="form-control">
                    <option value="">All</option>
                    @foreach( $source_type_options as $source_type)
                    <option value="{{ $source_type }}">{{ $source_type }}</option>
                    @endforeach 
                </select>
            </div>
            
            <div class="form-group col-md-3">
                <label for="user_name">
                    User Name
                </label>
                <input name="user_name" id="user_name"  class="form-control" />
            </div>

            <div class="form-group col-md-3">
                <label for="organization_id">
                    Organization
                </label>
                <select name="organization_id" id="organization_id" value="" class="form-control">
                    <option value="">All</option>
                    @foreach( $organizations as $organization)
                    <option value="{{ $organization->id }}">{{ $organization->code }} ({{ $organization->name }})</option>
                    @endforeach 
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-2">
                <label for="last_signon_from">Last Sign On (From)</label>
                <input class="form-control datetime-range-filter" type="datetime-local" id="last_signon_from" name="last_signon_from">
            </div>

            <div class="form-group col-md-2">
                <label for="last_signon_to">Last Sign On (To)</label>
                <input class="form-control datetime-range-filter" type="datetime-local" id="last_signon_to" name="last_signon_to">
            </div>

            <div class="form-group col-md-2">
                <label for="last_sync_from">Last Sync at (From)</label>
                <input class="form-control datetime-range-filter" type="datetime-local" id="last_sync_from" name="last_sync_from">
            </div>

            <div class="form-group col-md-2">
                <label for="last_sync_to">Last Sync at  (To)</label>
                <input class="form-control datetime-range-filter" type="datetime-local" id="last_sync_to" name="last_sync_to">
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

    </div>    
    
    <div class="px-4"></div>

	<div class="card-body">

		<table class="table table-bordered" id="user-table" style="width:100%">
			<thead>
				<tr>
                    <th>User ID </th>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Organization</th>
                    <th>Locked </th>
                    <th>Sign On Count</th>
                    <th>Last Signon at</th>
                    <th>Last Sync at</th>
                    <th>Created at</th>
                    <th>Updated at</th>
                    <th>Action</th>
				</tr>
			</thead>
		</table>

	</div>
</div>


{{-- Modal Box  --}}

<div class="modal fade" id="user-show-modal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
	  <div class="modal-content">
		<div class="modal-header bg-primary">
		  <h5 class="modal-title" id="userModalLabel">Existing details</h5>
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
	/* #user-table_filter label {
		text-align: right !important;
        padding-right: 10px;
	} */
    
    #user-table_filter {
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
        var oTable = $('#user-table').DataTable({
            "scrollX": true,
            retrieve: true,
            "searching": true,
            processing: true,
            serverSide: true,
            // select: true,
            'order': [[0, 'asc']],
            //fixedHeader: true,            
            ajax: {
                url: '{!! route('system.users.index') !!}',
                data: function (data) {
                    // data.term = $('#user').val();
                    data.user_name = $('#user_name').val();
                    data.source_type = $('#source_type').val();
                    data.organization_id = $('#organization_id').val();
                    data.last_signon_from = $('#last_signon_from').val();
                    data.last_signon_to  = $('#last_signon_to').val();
                    data.last_sync_from = $('#last_sync_from').val();
                    data.last_sync_to  = $('#last_sync_to').val();

                }
            },
            columns: [
                {data: 'id', name: 'id', className: "dt-nowrap" },
                {data: 'source_type', name: 'source_type', className: "dt-nowrap" },
                {data: 'name', name: 'name', className: "dt-nowrap" },
                {data: 'primary_job.email', name: 'email', defaultContent: '', className: "dt-nowrap" },
                {data: 'organization.code',  name: 'code', defaultContent: '', className: "dt-nowrap" },
                {data: 'acctlock', render: function ( data, type, row, meta ) {
                        if(data == 0) {
                            return '<i class="fa fa-user-check fa-lg text-primary"> </i>';
                        } else {
                            return '<i class="fa fa-user-times fa-lg text-danger"> </i>';
                        }
                    }
                },
                {data: 'access_logs_count', name: 'access_logs_count', className: "dt-nowrap",
                     render: function ( data, type, row, meta ) {
                        if(data > 0) {
                            return '<a href="{{ route('system.access-logs') . '?user_id=' }}' + row.id + '">' + data + '</a>';
                        } else {
                            return data;
                        }
                    }
                },
                {data: 'last_signon_at', name: 'last_signon_at', className: "dt-nowrap"},
                {data: 'last_sync_at', name: 'last_sync_at', className: "dt-nowrap"},
                {data: 'created_at', name: 'created_at', orderable: false, searchable: false, className: "dt-nowrap"},
                {data: 'updated_at', name: 'updated_at', orderable: false, searchable: false, className: "dt-nowrap"},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: "dt-nowrap"},
                
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
            $('#user_name').val('');
            $('.datetime-range-filter').val('');
            $('#source_type').val('');
            $('#include_trashed').prop('checked', false);

            oTable.search( '' ).columns().search( '' ).draw();
        });

        // // Model -- Show
    	// $(document).on("click", ".more-link , .show-user" , function(e) {
		// 	e.preventDefault();

        //     id =  $(this).attr('data-id');
        //     $.ajax({
        //         method: "GET",
        //         url:  '/system/users/' + id,
        //         dataType: 'json',
        //         success: function(data)
        //         {
        //             $('#userModalLabel').html('Job : ' + data.id + ' (' + data.user_name + ')' );
        //             //  started at ' + data.start_time);
        //             $('#modal-source_type').html(data.status);
        //             $('#modal-message').html(data.message);
        //             $('#user-show-modal').modal('show');
        //         },
        //         error: function(response) {
        //             console.log('Error');
        //         }
        //     });
    	// });





    });

    </script>
@endpush
