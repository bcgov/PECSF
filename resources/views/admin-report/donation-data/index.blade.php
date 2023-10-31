@extends('adminlte::page')

@section('content_header')

    @include('admin-report.partials.tabs')

    <h4 class="mx-1 mt-3">Review Donation Data</h4>
    
    <div class="d-flex mt-3">
        <div class="flex-fill">
            <p><button class="ml-2 btn btn-outline-primary" onclick="window.location.href='/administrators/dashboard'">
                Back    
            </button></p>    
        </div>

        <div class="d-flex">
            <div class="mr-2">
            </div>
        </div>
    </div>

    @endsection
@section('content')

<div class="card">
<form class="filter">
    <div class="card-body pb-0 search-filter">
        <h2>Search Criteria</h2>

        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="org_code">
                    Organization
                </label>
                <select name="org_code" value="" class="form-control">
                    <option value="">All</option>
                    @foreach( $organizations as $organization)
                    <option value="{{ $organization->code }}">{{ $organization->code }} ({{ $organization->name }})</option>
                    @endforeach 
                </select>
            </div>

            <div class="form-group col-md-2">
                <label for="pecsf_id">
                    PECSF ID
                </label>
                <input name="pecsf_id" id="pecsf_id"  class="form-control" />
            </div> 

            <div class="form-group col-md-2">
                <label for="name">
                    Name
                </label>
                <input name="name" id="name"  class="form-control" />
            </div> 
        </div>

        <div class="form-row">
            <div class="form-group col-md-2">
                <label for="yearcd">
                    Year
                </label>
                <select id="yearcd" class="form-control" name="yearcd">
                    <option value="">All</option>
                    @foreach ($years as $year)
                        <option value="{{ $year }}">{{ $year }}
                        </option>
                    @endforeach
                </select>
            </div> 

            <div class="form-group col-md-2">
                <label for="source_type">Source Type</label>
                <select id="source_type" class="form-control" name="source_type">
                    <option value="">All</option>
                    @foreach ($source_type_list as $key => $source_type)
                        <option value="{{ $key }}">{{ $source_type }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-2">
                <label for="frequency">Frequency</label>
                <select id="frequency" class="form-control" name="frequency">
                    <option value="">All</option>
                    @foreach ($frequencies as $frequency)
                        <option value="{{ $frequency }}">{{ $frequency }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-2">
                <label for="amount_from">Amount (From)</label>
                <input class="form-control " type="number" id="amount_from" name="amount_from">
            </div>

            <div class="form-group col-md-2">
                <label for="amount_to">Amount (To)</label>
                <input class="form-control " type="number" id="amount_to" name="amount_to">
            </div>

            <div class="form-group col-md-1">
                <label for="search">
                    &nbsp;
                </label>
                <button type="button" id="refresh-btn" value="Refresh" class="form-control btn-primary">Refresh</button>
            </div>
            <div class="form-group col-md-1">
                <label for="search">
                    &nbsp;
                </label>
                <button type="button" id="reset-btn" value="Reset" class="form-control  btn-secondary" >Reset</button>
            </div>

        </div>

    </div> 
</form>  
</div>   

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

		<table class="table table-bordered" id="donation-table" style="width:100%">
			<thead>
				<tr>
                    <th>ID</th>
                    <th>Org</th>
                    <th>PECSF ID</th>
                    <th>Name</th>
                    <th>Year</th>
                    <th>Pay End Date</th>
                    <th>Source Type</th>
                    <th>Frequency</th>
                    <th>Amount</th>
                    <th>Process ID</th>
                    <th>Process Status</th>
                    <th>Process End Time</th>
                    <th>Created by</th>
                    <th>Created at</th>
                    <th>Updated by</th>
                    <th>Updated at</th>
				</tr>
			</thead>
		</table>

	</div>
</div>

@endsection


@push('css')

    <link href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">

	<style>
	#donation-table_filter label {
		text-align: right !important;
        padding-right: 10px;
	}
    
    #donation-table_filter {
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
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>

    <script>

    $(function() {

        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': '{{csrf_token()}}'
            }
        });

        var oTable = $('#donation-table').DataTable({
            "scrollX": true,
            retrieve: true,
            "searching": true,
            processing: true,
            "language": {
               processing: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw text-info"></i><span class="sr-only">Loading...</span>'
            },
            serverSide: true,
            select: true,
            'order': [[0, 'desc']],
            "initComplete": function(settings, json) {
                    oTable.columns.adjust().draw();
            },
            ajax: {
                url: '{!! route('reporting.donation-data.index') !!}',
                type: "GET",
                data: function (data) {
                    data.org_code = $("select[name='org_code']").val();
                    data.pecsf_id = $("input[name='pecsf_id']").val();
                    data.name = $("input[name='name']").val();
                    data.yearcd  = $("select[name='yearcd']").val();
                    data.source_type = $("select[name='source_type']").val();
                    data.frequency = $("select[name='frequency']").val();
                    data.amount_from = $("input[name='amount_from']").val();
                    data.amount_to = $("input[name='amount_to']").val();
                },
                complete: function(xhr, resp) {
                    min_height = $(".wrapper").outerHeight();
                    $(".main-sidebar").css('min-height', min_height);
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
                {data: 'id',  className: "dt-nowrap"},
                {data: 'org_code',  defaultContent: '', className: "dt-nowrap"},
                {data: 'pecsf_id', defaultContent: '' },
                {data: 'name', defaultContent: '' },
                {data: 'yearcd', defaultContent: '' },
                {data: 'pay_end_date', defaultContent: '', className: "dt-nowrap", },
                {data: 'source_type_descr', name: 'source_type', defaultContent: '', className: "dt-nowrap", },
                {data: 'frequency', defaultContent: '', className: "dt-nowrap", },
                {data: 'amount', name: 'amount', defaultContent: '', 'className': 'dt-right',
                         render: $.fn.dataTable.render.number(',', '.', 2, '')
                },
                {data: 'process_history_id', defaultContent: '', name: 'process_history_id', className: "dt-nowrap" },
                {data: 'process_history.status', defaultContent: '', name: 'process_status', className: "dt-nowrap" },
                {data: 'process_history.end_at', defaultContent: '', name: 'process_date', className: "dt-nowrap" },
                {data: 'process_history.created_by.name', defaultContent: '',name: 'process_history.created_by.name', className: "dt-nowrap" },
                {data: 'created_at', defaultContent: '', name: 'created_at', className: "dt-nowrap" },
                {data: 'process_history.updated_by.name', defaultContent: '', name: 'process_history.updated_by.name', className: "dt-nowrap" },
                {data: 'updated_at', defaultContent: '', name: 'updated_at', className: "dt-nowrap" },
            ],
            columnDefs: [
                    {

                    },
            ]
        });

        $('#refresh-btn').on('click', function() {
            // oTable.ajax.reload(null, true);
            oTable.draw();
        });

        $('#reset-btn').on('click', function() {

            // Reset filter fields value
            $('.search-filter input').map( function() {$(this).val(''); });
            $('.search-filter select').map( function() { return $(this).val(''); })

            oTable.search( '' ).columns().search( '' ).draw();
        });

    });
    </script>
@endpush
