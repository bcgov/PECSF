@extends('adminlte::page')
@section('content_header')
    <div class="d-flex mt-3">
        <h1>Security - PECSF Administrators</h1>
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
                    
                    <form action="{{ route('admin.store') }}" class="form-inline" method="post">
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
                            <th>User Name</th>
                            <th>User Email</th>
                            <th>Role Name</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>
</div>

@endsection

@push('css')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
{{-- <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.1.1/dist/select2-bootstrap-5-theme.min.css" /> --}}

<link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<style>
	#administrator-table_filter label {
		text-align: right !important;
        padding-right: 10px;
	} 
    .dataTables_scrollBody {
        margin-bottom: 10px;
    }

    .select2-container .select2-selection--single 
    {
            height: 38px;  !important;
    }
     

</style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>

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
            //'order': [[0, 'desc']],
            ajax: {
                url: '{!! route('admin.index') !!}',
                data: function (d) {
                }
            },
            columns: [
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'rolename', name: 'rolename'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            columnDefs: [
                    {
                        className: "dt-nowrap",
                        targets: [0,1,2]
                    },
            ]
        });

        $('#user_id').select2({
            ajax: {
                url: '/administrators/users'
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

    });
    </script>
@endpush
