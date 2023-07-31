@extends('adminlte::page')

@section('content_header')
    
    @include('system-security.partials.tabs')

@endsection
@section('content')

    <div class="d-flex p-2 ">
        <h4>Log Files</h4>
        <div class="flex-fill"></div>
    </div>

    <div class="card">
      
        <div class="card-body">

            <table class="table table-bordered" id="bu-table" style="width:100%">
                <thead>
                  <tr>
                    <th >#</th>
                    <th >File name</th>
                    <th >File type</th>
                    <th >File size (in KB)</th>
                    <th >Last modified</th>
                  </tr>
                </thead>
            </table>
            
    </div>


</div>
@endsection



@push('css')

    <link href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">
    
@endpush

@push('js')
   
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}" ></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}" ></script>

    <script>

    $(function() {

        // Datatables
        var oTable = $('#bu-table').DataTable({
            "scrollX": true,
            retrieve: true,
            "searching": true,
            processing: true,
            "language": {
               processing: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw text-info"></i><span class="sr-only">Loading...</span>'
            },
            serverSide: true,
            select: true,
            'pageLength': 25,
            'order': [[1, 'asc']],
            ajax: {
                url: '{!! route('system.log-files.index') !!}',
                data: function (d) {
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
                {data: 'id', render: function (data, type, row, meta) {
                                    return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {data: 'filename', name: 'filename', className: "dt-nowrap", 
                            render: function (data, type, row, meta) {
                                    return '<a href="/system/log-files/' + row.filename + '">' + row.filename + '</a>';
                            }
                },
                {data: 'type', name: 'type', className: "dt-nowrap" },
                {data: 'size', name: 'size', className: "dt-nowrap text-right" },
                {data: 'last_modified', name: 'last_modified', className: "dt-nowrap" },
            ],
            columnDefs: [
                    {
                        width: '5em',
                        targets: [0]
                    },
            ]
        });

    });

    </script>
@endpush
