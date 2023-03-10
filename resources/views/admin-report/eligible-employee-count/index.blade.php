@extends('adminlte::page')

@section('content_header')
    {{-- <h2>Reporting</h2> --}}
    @include('admin-report.partials.tabs')
    <div class="d-flex mt-3">
        <h4>Eligible Employee Summary </h4>
        <div class="flex-fill"></div>
    </div>
@endsection
@section('content')

<p><a href="/administrators/dashboard" class="BC-Gov-SecondaryButton">Back</a></p>

<form id="eligible-employee-form" method="post">
<div class="card search-filter">

    <div class="card-body pb-0 ">
        <h2>Search Criteria</h2>

        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="year">
                    Year
                </label>
                <select name="year" value="" class="form-control">
                    {{-- <option value="">All</option> --}}
                    @foreach( $years as $year => $as_of_date)
                        <option value="{{ $year }}" {{ today()->year == $year ? 'selected' : '' }}>{{ $year }} [Snapshot on : {{ $as_of_date }}]</option>
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

    </div>    
</div>
</form>

<div class="card">
    <div class="card-body">

        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ $message }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- <div id="export-section" class="px-3 float-right">
            <button type="button" id="export-btn" value="export" class="btn btn-primary">Export</button>
            <span id="export-section-result"></span>
        </div> --}}

        <table class="table table-bordered" id="eligible-employee-table" style="width:100%">
            <thead>
                <tr>
                    <th>Year</th>
                    <th>Business Unit</th>
                    <th>Name</th>
                    <th>Count</th>
                </tr>
            </thead>
        </table>

    </div>
</div>

@endsection


@push('css')


    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/fixedheader/3.2.4/css/fixedHeader.dataTables.min.css" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">

	<style>
    #eligible-employee-table_filter label {
        display:none;
    }
	#eligible-employee-table_filter label {
		text-align: right !important;
        padding-right: 10px;
	}
    .dataTables_scrollBody {
        margin-bottom: 10px;
    }

    /* Blink */
    .blink {
        animation: blinker 0.6s linear infinite;
        /* color: #1c87c9;
        font-size: 30px;
        font-weight: bold;
        font-family: sans-serif; */
    }
    @keyframes blinker {
        50% {
            opacity: 0;
        }
    }
    .blink-one {
      animation: blinker-one 1s linear infinite;
    }
    @keyframes blinker-one {
        0% {
            opacity: 0;
        }
    }
    .blink-two {
      animation: blinker-two 1.4s linear infinite;
    }
    @keyframes blinker-two {
        100% {
            opacity: 0;
        }
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
        var oTable = $('#eligible-employee-table').DataTable({
            "scrollX": true,
            retrieve: true,
            "searching": true,
            processing: true,
            serverSide: true,
            select: true,
            'order': [[0, 'asc']],
            fixedHeader: true,   
            "initComplete": function(settings, json) {
                    oTable.columns.adjust().draw();
            },
            ajax: {
                url: '{!! route('reporting.eligible-employee-count.index') !!}',
                data: function (data) {
                    // data.term = $('#user').val();
                    data.year = $("select[name='year']").val();
                }
            },
            columns: [
                {data: 'year', name: 'year', className: "dt-nowrap" },                
                {data: 'business_unit', name: 'business_unit', className: "dt-nowrap" },
                {data: 'business_unit_name', name: 'business_unit_name', className: "dt-nowrap" },
                {data: 'ee_count', name: 'ee_count', className: "dt-nowrap" },
            ],
            columnDefs: [
                    {
                        // width: '5em',
                        // targets: [0]
                    },
            ]
        });
        
        // Move the export button to the filter area
        $('#eligible-employee-table_filter').parent().append( $('#export-section') );

        $(window).keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                oTable.ajax.reload();
                return false;
            }
        });

        $("select[name='year']").on('change', function() {
            // oTable.ajax.reload(null, true);
            oTable.draw();
        });

        // $('#refresh-btn').on('click', function() {
        //     // oTable.ajax.reload(null, true);
        //     oTable.draw();
        // });

        // $('#reset-btn').on('click', function() {
           
        //     $('.search-filter input').map( function() {$(this).val(''); });
        //     $('.search-filter select').map( function() { return $(this).val(''); })

        //     oTable.search( '' ).columns().search( '' ).draw();
        // });


    });
    </script>
@endpush
