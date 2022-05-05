@extends('adminlte::page')
@section('content_header')
    <div class="d-flex mt-3">
        <h1>PECSF Campaign Year</h1>
        <div class="flex-fill"></div>

        <div class="d-flex">
            <div class="mr-2">
                <x-button :href="route('campaignyears.create')">Add a New Value</x-button>        
            </div>
        </div>
    </div>
@endsection
@section('content')


<p><a href="/administrators/dashboard">Back</a></p>


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
            serverSide: true,
            select: true,
            'order': [[0, 'desc']],
            ajax: {
                url: '{!! route('campaignyears.index') !!}',
                data: function (d) {
                }
            },
            columns: [
                {data: 'calendar_year', name: 'calendar_year'},
                {data: 'status', name: 'status'},
                {data: 'number_of_periods', name: 'number_of_periods'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
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
