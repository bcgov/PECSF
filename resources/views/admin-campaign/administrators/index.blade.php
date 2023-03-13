@extends('adminlte::page')
@section('content_header')

    @include('admin-campaign.partials.tabs')
    <div class="d-flex mt-3">
        <h4>Security - PECSF Administrators</h4>
        <div class="flex-fill"></div>
    </div>
@endsection
@section('content')
<div class="card">
    <div class="card-body">
        
        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" style="text-decoration: none;"  data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Success!</strong> {{ $message }}
            </div>
        @endif

        @error('user')
            <div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" style="text-decoration: none;"  data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Error!</strong> {{  $message }}
            </div>    
        @enderror

        <div class="card">
    
            <div class="d-flex mt-3">
                <h4></h4>
                <div class="px-4">
                    
                    <form action="{{ route('settings.administrators.store') }}" class="form-inline" method="post">
                        @csrf
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                              <label  class="col-form-label">Assign User</label>
                            </div>
                            <div class="col-auto">
                              
                              <select class="form-control select2" style="height: 28px; width:300px;" name="user_id" id="user_id">
                              </select>
        
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-outline-secondary"  type="submit" >Add</button>
                            </div>
                        </div>
                    </form>
        
                </div>    
                <div class="flex-fill"></div>
                <div class="px-4">
                    
                </div>
            </div>
        
            <div class="card-body">
                <table class="table table-bordered" id="administrator-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>User Name</th>
                            <th>Email</th>
                            <th>IDIR</th>
                            <th>Organization</th>
                            <th>Employee ID</th>
                            <th>Status </th>
                            <th>Role Name</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>
</div>


<form id="delete-administrator-form" action="" class="form-inline" method="post">
    @csrf
    @method('delete')
</form>


@endsection

@push('css')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
<link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">

<style>
	#administrator-table_filter label {
		text-align: right !important;
        padding-right: 10px;
	} 
    .dataTables_scrollBody {
        margin-bottom: 10px;
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>

    <script>
    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove(); 
        });
    }, 3000);

    $(function() {

        var oTable = $('#administrator-table').DataTable({
            "scrollX": true,
            retrieve: true,
            "searching": true,
            processing: true,
            serverSide: true,
            select: true,
            'order': [[1, 'asc']],
            ajax: {
                url: '{!! route('settings.administrators.store') !!}',
                data: function (d) {
                }
            },
            columns: [
                {data: 'source_type', name: 'source_type', className: 'dt-nowrap'},
                {data: 'name', name: 'name', className: 'dt-nowrap'},
                {data: 'employee_email', className: 'dt-nowrap', orderable: false, searchable: false },
                {data: 'idir', className: 'dt-nowrap'},
                {data: 'organization.code', name: 'organization.code', className: 'dt-nowrap'},
                {data: 'emplid', name: 'emplid', className: 'dt-nowrap'},
                {data: 'acctlock',  orderable: false, searchable: false, render: function ( data, type, row, meta ) {
                        icon_name = (data == 0) ? 'fa-user-check' : 'fa-user-times';
                        icon_color = (data == 0) ? 'text-primary' : 'text-danger';
                        return '<span><i class="fa ' + icon_name + ' fa-lg ' + icon_color + '"> </i></span>';
                    }
                },                
                {data: 'rolename', name: 'rolename'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            columnDefs: [
                {
                },
            ]
        });

        $('#user_id').select2({
            ajax: {
                url: '/settings/administrators/users'
                , dataType: 'json'
                , delay: 250
                , data: function(params) {
                    var query = {
                        'q': params.term
                    , }
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

        // Model -- Delete
        $(document).on("click", ".delete-administrator" , function(e) {
            e.preventDefault();

            id = $(this).attr('data-id');
            name = $(this).attr('data-name');

            Swal.fire( {
                title: 'Are you sure you want to delete administrator "' + name + '" ?',
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
                    $('#delete-administrator-form').attr('action', '/settings/administrators/' + id );
                    $('#delete-administrator-form').submit();
                } else if (result.isCancelledDenied) {
                    // Do nothing
                }
            })
        });


    });
    </script>
@endpush
