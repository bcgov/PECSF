@extends('adminlte::page')

@section('content_header')

@include('admin-campaign.partials.tabs')

    <div class="d-flex mt-3">
        <h4>Pay Calendars</h4>
        <div class="flex-fill"></div>

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
    
		<table class="table table-bordered" id="pay-calendar-table" style="width:100%">
			<thead>
				<tr>
                    <th>Tran ID</th>
                    <th>Pay Begin Date</th>
					<th>Pay End Date</th>
                    <th>Check Date</th>
				</tr>
			</thead>
		</table>

	</div>    
</div>   

@endsection


@push('css')

    
    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">

	<style>
	#pay-calendar-table_filter label {
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

    $(function() {

        // Datatables
        var oTable = $('#pay-calendar-table').DataTable({
            "scrollX": true,
            retrieve: true,
            "searching": true,
            processing: true,
            serverSide: true,
            "pageLength": 50,
            select: true,
            'order': [[0, 'desc']],
            ajax: {
                url: '{!! route('settings.pay-calendars.index') !!}',
                data: function (d) {
                }
            },
            columns: [
                {data: 'id', width: '2em'},
                {data: 'pay_begin_dt', name: 'pay_begin_dt', className: "dt-nowrap", width: '5em' },
                {data: 'pay_end_dt', name: 'pay_end_dt', className: "dt-nowrap",  width: '5em' },
                {data: 'check_dt', name: 'check_dt', className: "dt-nowrap",  width: '5em' },

            ],
            columnDefs: [
                    // {
                    //     render: function (data, type, full, meta) {
                    //         if (data == 'A') {
                    //             return 'Active';
                    //         } else {
                    //             return 'Inactive';
                    //         }
                    //     },
                    //     targets: 2
                    // },
                    // {
                    //     width: '5em',
                    //     targets: [0,3]
                    // },
            ]
        });

        

    });
    </script>
@endpush
