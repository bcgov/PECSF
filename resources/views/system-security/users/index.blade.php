@extends('adminlte::page')

@section('content_header')

@include('system-security.partials.tabs')

    <div class="d-flex mt-3">
        <h4>Users</h4>
        <div class="flex-fill"></div>

    </div>
@endsection
@section('content')

<div class="card search-filter">

    <div class="card-body pb-0 ">
        <h2>Search Criteria</h2>

        <div class="form-row">
            <div class="form-group col-md-1">
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
            
            <div class="form-group col-md-2">
                <label for="user_name">
                    User Name
                </label>
                <input name="user_name" id="user_name"  class="form-control" />
            </div>

            <div class="form-group col-md-2">
                <label for="emplid">
                    Emplid
                </label>
                <input name="emplid" id="emplid"  class="form-control" />
            </div>

            <div class="form-group col-md-2">
                <label for="filter_allow_inapp_msg">
                    Locked
                </label>
                <select name="acctlock" id="acctlock" value="" class="form-control">
                    <option value="">Select a option</option>
                    <option value="1">Yes (locked)</option>
                    <option value="0">No (Unlocked)</option>
                </select>
            </div>
        </div>    

        <div class="form-row">
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

            <div class="form-group col-md-2">
                <label for="business_unit">
                    Business Unit
                </label>
                <input name="business_unit" id="business_unit"  class="form-control" />
            </div>
            <div class="form-group col-md-2">
                <label for="deptid">
                    Department
                </label>
                <input name="deptid" id="deptid"  class="form-control" />
            </div>
            <div class="form-group col-md-2">
                <label for="tgb_reg_district">
                    Regional District
                </label>
                <input name="tgb_reg_district" id="tgb_reg_district"  class="form-control" />
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
                <button type="button" id="refresh-btn" value="Refresh" class="form-control btn btn-primary" />Search</button>
            </div>
            <div class="form-group col-md-1">
                <label for="search">
                    &nbsp;
                </label>
                <button type="button" id="reset-btn" value="Reset" class="form-control btn btn-secondary">Reset</button>
            </div>

        </div>

    </div>    

</div>

<div class="card">
    
    <div class="px-4"></div>

	<div class="card-body">

		<table class="table table-bordered" id="user-table" style="width:100%">
			<thead>
				<tr>
                    <th>User ID </th>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>IDIR</th>
                    <th>Organization</th>                    
                    <th>Emplid</th>
                    <th>Status</th>
                    <th>Sign On Count</th>
                    <th>Active Job Count</th>

                    <th>Empl Status</th>
                    <th>Hire Date</th>
                    <th>Date Update</th>
                    <th>Date Delete</th>

                    <th>Bus Unit</th>
                    <th>Dept ID</th>
                    <th>Dept Name</th>
                    <th>Region</th>
                    <th>Region Name</th>
                    <th>Office City</th>

                    <th>Address 1</th>
                    <th>Address 2</th>
                    <th>City</th>
                    <th>Province</th>
                    <th>Country</th>
                    <th>Postal</th>

                    <th>Last Signon at</th>
                    <th>Last Sync at</th>
                    <th>Created at</th>
                    <th>Updated at</th>
                    {{-- <th>Action</th> --}}
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

    <link href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/datatables-plugins/fixedheader/css/fixedHeader.bootstrap4.min.css') }}" rel="stylesheet">
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

    div.dataTables_wrapper div.dataTables_processing {
      top: 5%;
    }

</style>
@endpush


@push('js')

    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}" ></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}" ></script>
    <script src="{{ asset('vendor/datatables-plugins/fixedheader/js/dataTables.fixedHeader.min.js') }}" ></script>
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
            "language": {
               processing: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw text-info"></i><span class="sr-only">Loading...</span>'
            },
            serverSide: true,
            // select: true,
            'order': [[0, 'asc']],
            fixedHeader: true,
            "initComplete": function(settings, json) {
                    oTable.columns.adjust().draw(false);
            },            
            ajax: {
                url: '{!! route('system.users.index') !!}',
                data: function (data) {
                    // data.term = $('#user').val();
                    data.user_name = $('#user_name').val();
                    data.emplid = $('#emplid').val();
                    data.acctlock = $('#acctlock').val();
                    data.source_type = $('#source_type').val();
                    data.organization_id = $('#organization_id').val();
                    data.business_unit = $('#business_unit').val();
                    data.deptid = $('#deptid').val();
                    data.tgb_reg_district = $('#tgb_reg_district').val();
                    data.last_signon_from = $('#last_signon_from').val();
                    data.last_signon_to  = $('#last_signon_to').val();
                    data.last_sync_from = $('#last_sync_from').val();
                    data.last_sync_to  = $('#last_sync_to').val();
                },
                complete: function(xhr, resp) {
                    min_height = $(".wrapper").outerHeight();
                    $(".main-sidebar").css('min-height', min_height - 240);
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
                {data: 'source_type', name: 'source_type', className: "dt-nowrap" },
                {data: 'name', name: 'name', className: "dt-nowrap" },
                {data: 'email', name: 'email', defaultContent: '', className: "dt-nowrap" },
                {data: 'idir', name: 'idir', defaultContent: '', className: "dt-nowrap" },
                {data: 'organization.code',  name: 'organization.code', defaultContent: '', className: "dt-nowrap" },
                {data: 'emplid', name: 'emplid', defaultContent: '', className: "dt-nowrap" },
                {data: 'acctlock', render: function ( data, type, row, meta ) {
                        icon_name = (data == 0) ? 'fa-user-check' : 'fa-user-times';
                        icon_color = (data == 0) ? 'text-primary' : 'text-danger';
                        return '<button type="button" class="btn"><span class="toggle_user" data-id="' + row.id + 
                                   '" data-locked="' + data + 
                                   '" data-name="' + row.name + 
                                   '"><i class="fa ' + icon_name + ' fa-lg ' + icon_color + '"> </i></span></button>';
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
                {data: 'active_employee_jobs_count', name: 'active_employee_jobs_count', className: "dt-nowrap",
                     render: function ( data, type, row, meta ) {
                        if(data > 0) {
                            // TO DO -- Drill Down
                            // return '<a href="{{ route('system.access-logs') . '?emplid=' }}' + row.emplid + '">' + data + '</a>';
                            return data;
                        } else {
                            return data;
                        }
                    }
                },
                {data: 'empl_status', name: 'employee_jobs.empl_status', defaultContent: '', className: "dt-nowrap" },
                {data: 'hire_dt', name: 'employee_jobs.hire_dt', defaultContent: '', className: "dt-nowrap" },
                {data: 'date_updated', name: 'employee_jobs.date_updated', defaultContent: '', className: "dt-nowrap" },
                {data: 'date_deleted', name: 'employee_jobs.date_deleted', defaultContent: '', className: "dt-nowrap" },
                {data: 'business_unit', name: 'employee_jobs.business_unit', defaultContent: '', className: "dt-nowrap" },
                {data: 'deptid', name: 'employee_jobs.deptid', defaultContent: '', className: "dt-nowrap" },
                {data: 'dept_name', name: 'employee_jobs.dept_name', defaultContent: '', className: "dt-nowrap" },
                {data: 'tgb_reg_district', name: 'regions.code', defaultContent: '', className: "dt-nowrap" },
                {data: 'region_name', name: 'regions.name', defaultContent: '', className: "dt-nowrap" },
                {data: 'office_city', name: 'employee_jobs.office_city', defaultContent: '', className: "dt-nowrap" },                
                {data: 'address1', name: 'employee_jobs.address1', defaultContent: '', className: "dt-nowrap" },
                {data: 'address2', name: 'employee_jobs.address2', defaultContent: '', className: "dt-nowrap" },
                {data: 'city', name: 'employee_jobs.city', defaultContent: '', className: "dt-nowrap" },
                {data: 'stateprovince', name: 'employee_jobs.stateprovince', defaultContent: '', className: "dt-nowrap" },
                {data: 'country', name: 'employee_jobs.country', defaultContent: '', className: "dt-nowrap" },
                {data: 'postal', name: 'employee_jobs.postal', defaultContent: '', className: "dt-nowrap" },
                {data: 'last_signon_at', name: 'last_signon_at', orderable: false, searchable: false, className: "dt-nowrap"},
                {data: 'last_sync_at', name: 'last_sync_at', orderable: false, searchable: false, className: "dt-nowrap"},
                {data: 'created_at', name: 'created_at', orderable: false, searchable: false, className: "dt-nowrap"},
                {data: 'updated_at', name: 'updated_at', orderable: false, searchable: false, className: "dt-nowrap"},
                // {data: 'action', name: 'action', orderable: false, searchable: false, className: "dt-nowrap"},
                
            ],
            columnDefs: [
                    {
                        // width: '5em',
                        // targets: [0]
                    },
            ],

        });

        $(document).on('keydown', '.search-filter input', function (e) {
            if(event.keyCode == 13) {
                event.preventDefault();
                oTable.ajax.reload();
                return false;
            }
        });

        $('#refresh-btn').on('click', function() {
            // oTable.ajax.reload(null, true);
            oTable.draw();
        });

        $('#reset-btn').on('click', function() {
           
            $('.search-filter input').map( function() {$(this).val(''); });
            $('.search-filter select').map( function() { return $(this).val(''); })

            oTable.search( '' ).columns().search( '' ).draw();
        });

        function Toast( toast_title, toast_body, toast_class) {
            Swal.fire({
                    position: 'top-end',
                    icon: (toast_class.includes("bg-success") ? 'success' : 'warning'),
                    title: toast_title,
                    text: toast_body,
                    showConfirmButton: false,
                    showCloseButton: true,
                    timer: 5000
            })
        }

        $('body').on('click', 'span.toggle_user', function(e) {
            e.preventDefault();

            id = $(this).attr('data-id');
            locked = $(this).attr('data-locked');
            name = $(this).attr('data-name');
   
            title = 'Are you sure you want to lock the user "' + name + '" ?';
            url = '/system/users/' + id + '/lock';
            button_text = 'Lock';
            if (locked == 1) {
                title = 'Are you sure you want to unlock the user "' + name + '" ?';
                url = '/system/users/' + id + '/unlock';
                button_text = 'Unlock';
            }

            Swal.fire( {
                    title: title,
                    text: '',
                    // icon: 'question',
                    //showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: button_text,
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
                            method: "POST",
                            url: url,
                            success: function(data)
                            {
                                oTable.ajax.reload(null, false);	// reload datatables
                                text = 'locked';
                                if (locked == 1) {
                                    text = 'unlocked';
                                }
                                Toast('Success', 'User  "' + name +  '" was successfully ' + text + '.', 'bg-success' );
                            },
                            error: function (data) {
                                    Swal.fire({
                                            icon: 'error',
                                            title: data.responseJSON.title, // data.responseJSON.title,
                                            text: data.responseJSON.message,
                                    });
                                    console.log(data.responseJSON.message);
                            }
                        });
                    } else if (result.isCancelledDenied) {
                        // Swal.fire('Changes are not saved', '', '')
                    }
                });
              
        });

    });

    </script>
@endpush
