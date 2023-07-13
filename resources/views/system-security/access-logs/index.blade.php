@extends('adminlte::page')

@section('content_header')

@include('system-security.partials.tabs')

    <div class="d-flex mt-3">
        <h4>Access Log</h4>
        <div class="flex-fill"></div>

    </div>
@endsection
@section('content')

<div class="card">

    <div class="card-body pb-0">
        <h2>Search Criteria</h2>

        <div class="form-row">

            <div class="form-group col-md-4">
                <label for="user_id">
                    User 
                </label>
                <select class="form-control select2" style="width:100%;" name="user_id" id="user_id">
                    @if ( old('user_id') && session()->get('selected_user') )
                        <option value="{{ old('user_id') }}">{{ session()->get('selected_user')->name }}</option>
                    @endif
                   {{-- <option value="" selected>-- choose user --</option> --}}
                </select>
            </div>

            <div class="form-group col-md-4">
                <label for="term">
                    IDIR / Employee ID
                </label>
                <input name="term" id="term"  class="form-control" />
            </div>

        </div>
        <div class="form-row">

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
                <input type="button" id="refresh-btn" value="Refresh" class="form-control btn btn-primary" />
            </div>
            <div class="form-group col-md-1">
                <label for="Reset">
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
                    <th>User</th>
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

    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">    
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

    div.dataTables_wrapper div.dataTables_processing {
      top: 5%;
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

</style>
@endpush


@push('js')

    <script src="{{ asset('vendor/select2/js/select2.min.js') }}" ></script>
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}" ></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}" ></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>

    <script>

    $(function() {
 
        // Datatables
        var oTable = $('#accesslog-table').DataTable({
            "scrollX": true,
            retrieve: true,
            "searching": true,
            processing: true,
            "language": {
               processing: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw text-info"></i><span class="sr-only">Loading...</span>'
            },
            serverSide: true,
            // select: true,
            'order': [[ 0, 'desc']],
            "initComplete": function(settings, json) {
                    oTable.columns.adjust().draw(false);

                    min_height = $(".wrapper").outerHeight();
                    $(".main-sidebar").css('height', min_height - 240);
            },
            ajax: {
                url: '{!! route('system.access-logs') !!}',
                data: function (data) {
                    data.user_id = $('#user_id').val();
                    data.term = $('#term').val();
                    data.login_at_from = $('#login_at_from').val();
                    data.login_at_to  = $('#login_at_to').val();
                    data.login_method  = $('#login_method').val();
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

        // Filter by User ID
        $('#user_id').select2({
            allowClear: true,
            placeholder: "Select a user",
            ajax: {
                url: '{{ route('system.access-logs.users') }}'
                , dataType: 'json'
                , delay: 250
                , data: function(params) {
                    var query = {
                        'q': params.term,
                    }
                    return query;
                }
                , processResults: function(data) {
                    return {
                        results: data
                        };
                }
                , cache: false
            }
        });


        $('#refresh-btn').on('click', function() {
            // oTable.ajax.reload(null, true);
            oTable.draw();
        });

        $('#reset-btn').on('click', function() {
            $('#term').val('');
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
                url:  '/system/access-logs-user-detail/' + id,
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
