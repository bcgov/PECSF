@extends('adminlte::page')

@section('content_header')

@include('admin-campaign.partials.tabs')

    <h4 class="mx-1 mt-3">Campaign Years</h4>

    <div class="d-flex mt-3">
        <div class="flex-fill">
            <p><button class="ml-2 btn btn-outline-primary" onclick="window.location.href='/administrators/dashboard'">
                Back    
            </button></p>
        </div>

        <div class="d-flex">
            <div class="mr-2">
                <x-button class="btn-primary" :href="route('settings.campaignyears.create')">Create New Campaign Year</x-button>
            </div>
        </div>
    </div>

@endsection
@section('content')

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
            
		<table class="table table-bordered" id="campaignyear-table" style="width:100%">
			<thead>
				<tr>
                    <th>Calendar Year</th>
					<th>Status</th>
                    <th>Number of periods</th>                    
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Closed Date</th>
                    <th>Action </th>
				</tr>
			</thead>
		</table>

	</div>    
</div>   

@endsection


@push('css')

    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
	<style>
	#campaignyear-table_filter label {
		text-align: right !important;
        padding-right: 10px;
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
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>

    <script>
    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove(); 
        });
    }, 3000);

    $(function() {

        var oTable = $('#campaignyear-table').DataTable({
            "scrollX": true,
            retrieve: true,
            "searching": true,
            processing: true,
            "language": {
               processing: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw text-info"></i><span class="sr-only">Loading...</span>'
            },
            stateSave: true,            
            serverSide: true,
            select: true,
            'order': [[0, 'desc']],
            "initComplete": function(settings, json) {
                oTable.columns.adjust().draw(false);

                @if (!(str_contains( url()->previous(), 'settings/campaignyears')))
                    oTable.table().search("").draw();
                @endif
        
            },
            ajax: {
                url: '{!! route('settings.campaignyears.index') !!}',
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
                {data: 'calendar_year', name: 'calendar_year'},
                {data: 'status', name: 'status'},
                {data: 'number_of_periods', name: 'number_of_periods', 'className': 'dt-center'},                
                {data: 'start_date', name: 'start_date'},
                {data: 'end_date', name: 'end_date'},
                {data: 'close_date', name: 'close_date'},

                {data: 'action', name: 'action', orderable: false, searchable: false, className: "dt-nowrap"}
            ],
            columnDefs: [
                    {
                        render: function (data, type, full, meta) {
                            if (data == 'A') {
                                return 'Active';
                            } else {
                                return 'Inactive';
                            }
                        },
                        targets: 1
                    },
                    {
                        className: "dt-nowrap",
                        targets: [0,1,2]
                    },
            ]
        });

    });
    </script>
@endpush

