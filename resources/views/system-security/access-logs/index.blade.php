@extends('adminlte::page')

@section('content_header')

@include('system-security.partials.tabs')

    <div class="d-flex mt-3">
        <h4>Access Log</h4>
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
                <label for="user">
                    User Name / IDIR / Employee ID
                </label>
                <input name="user" id="user"  class="form-control" />
            </div>

            <div class="form-group col-md-2">
                <label for="login_at_from">Login at (From)</label>
                <input class="form-control date-range-filter" type="date" id="login_at_from" name="login_at_from">
            </div>

            <div class="form-group col-md-2">
                <label for="login_at_to">Login at (To)</label>
                <input class="form-control date-range-filter" type="date" id="login_at_to" name="login_at_to">
            </div>

            <div class="form-group col-md-2">
                <label for="login_method">
                    Login Method
                </label>
                <select name="login_method" id="login_method" value="" class="form-control">
                    <option value="">Select a method</option>
                    <option value="Laravel UI">Laravel</option>
                    <option value="Keycloak">IDIR (Keycloak)</option>
                </select>
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

		<table class="table table-bordered" id="accesslog-table" style="width:100%">
			<thead>
				<tr>
                    <th>Tran ID </th>
                    <th>Login at </th>
                    <th>User link</th>
                    <th>IDIR</th>
                    <th>Employee ID</th>
                    <th>User ID</th>
                    <th>Login Method</th>
                    <th>Identity Provider</th>
                    <th>Login IP</th>
                    <th>Logout at</th>
                    <th>Business Unit</th>
                    <th>Department</th>
                    <th>Organization</th>
				</tr>
			</thead>
		</table>

	</div>
</div>

{{-- Modal Box  --}}

<div class="modal fade" id="user-detail-modal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
	  <div class="modal-content">
		<div class="modal-header bg-primary">
		  <h5 class="modal-title" id="userModalLabel">User detail</h5>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">

            
            
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
	/* #accesslog-table_filter label {
		text-align: right !important;
        padding-right: 10px;
	} */
    
    #accesslog-table_filter {
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
        var oTable = $('#accesslog-table').DataTable({
            "scrollX": true,
            retrieve: true,
            "searching": true,
            processing: true,
            serverSide: true,
            // select: true,
            'order': [[ 0, 'desc']],
            
            ajax: {
                url: '{!! route('settings.access_logs') !!}',
                data: function (data) {
                    data.term = $('#user').val();
                    data.login_at_from = $('#login_at_from').val();
                    data.login_at_to  = $('#login_at_to').val();
                }
            },
            columns: [
                {data: 'id', name: 'id', className: "dt-nowrap" },
                {data: 'login_at', name: 'login_at', className: "dt-nowrap" },
                {data: 'user_detail_link',  searchable: false, orderable: false,  className: "dt-nowrap" },
                {data: 'idir',  name: 'users.idir',  className: "dt-nowrap" },
                {data: 'emplid',  name: 'users.emplid',  className: "dt-nowrap" },
                {data: 'user_id',  name: 'user_id',  className: "dt-nowrap" },
                {data: 'login_method', name: 'login_method'},
                {data: 'identity_provider', name: 'identity_provider'},
                {data: 'login_ip', name: 'login_ip'},
                {data: 'logout_at', name: 'logout_at', className: "dt-nowrap"},
                {data: 'user.primary_job.business_unit', searchable: false, orderable: false, className: "dt-nowrap"},
                {data: 'user.primary_job.dept_name', searchable: false, orderable: false, className: "dt-nowrap"},
                {data: 'user.primary_job.organization_name', searchable: false, orderable: false, className: "dt-nowrap"},
                
            ],
            columnDefs: [
                    {
                        // width: '5em',
                        // targets: [0]
                    },
            ],


        });



        $('#user').on('keyup change', function () {
            oTable.draw();
        });

        $('#login_method').on('change', function () {
            oTable.columns( 'login_method:name' ).search( this.value ).draw();            
        });

        $('.date-range-filter').on('change', function () {
            oTable.draw();
        });

        $('#reset-btn').on('click', function() {
            $('#user').val('');
            $('.date-range-filter').val('');
            $('#login_method').val('');

            oTable.search( '' ).columns().search( '' ).draw();
        });


        // Model -- Show
    	$(document).on("click", ".user-detail-link" , function(e) {
			e.preventDefault();

            id =  $(this).attr('data-id');
            title = $(this).attr('data-name');
            $.ajax({
                method: "GET",
                url:  '/settings/access-logs-user-detail/' + id,
                dataType: 'html',
                success: function(data)
                {
                    $('#userModalLabel').html('User : ' + title );

                    $('#user-detail-modal div.modal-body').html(data); 
                    //  started at ' + data.start_time);
                    // $('#modal-status').html(data.status);
                    // $('#modal-message').val(data.message);
                    $('#user-detail-modal').modal('show');
                },
                error: function(response) {
                    console.log('Error');
                }
            });
    	});

    });

    </script>
@endpush
