@extends('adminlte::page')

@section('content_header')


    <div class="d-flex mt-3">
        <h4>Pledge Administration</h4>
        <div class="flex-fill"></div>

        <div class="d-flex">
            <div class="mr-2">
                <x-button class="btn-success" :href="route('admin-pledge.campaign.create')">Create New Pledge</x-button>
            </div>
        </div>
    </div>
<br>
    <br>
    @include('admin-pledge.partials.tabs')

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

		<table class="table table-bordered" id="campaign-table" style="width:100%">
			<thead>
				<tr>
                    <th>ID</th>
                    <th>Organization</th>
					<th>Empl ID</th>
                    <th>Name</th>
                    <th>Campaign Year</th>
                    <th>FS Pool / Charities</th>
                    <th>One Time Amount</th>
                    <th>Bi Weekly Amount</th>
                    <th>Goal Amount</th>
                    <th>Action </th>
                    <th>Pool Name</th>
                    <th>Charites Name</th>

				</tr>
			</thead>
		</table>

	</div>
</div>

@endsection


@push('css')

    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
	<style>
	#campaign-table_filter label {
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
    // window.setTimeout(function() {
    //     $(".alert").fadeTo(500, 0).slideUp(500, function(){
    //         $(this).remove();
    //     });
    // }, 3000);

    $(function() {

        var oTable = $('#campaign-table').DataTable({
            "scrollX": true,
            retrieve: true,
            "searching": true,
            processing: true,
            serverSide: true,
            select: true,
            'order': [[0, 'desc']],
            ajax: {
                url: '{!! route('admin-pledge.campaign.index') !!}',
                data: function (d) {
                }
            },
            columns: [
                {data: 'id',  className: "dt-nowrap"},
                {data: 'organization.name',  className: "dt-nowrap"},
                {data: 'user.primary_job.emplid', defaultContent: '' },
                {data: 'user.name', defaultContent: '', className: "dt-nowrap"},
                {data: 'campaign_year.calendar_year', "className": "dt-center"},
                {data: 'description'},
                {data: 'one_time_amount', name: 'one_time_amount', 'className': 'dt-right', render: $.fn.dataTable.render.number(',', '.', 2, '')},
                {data: 'pay_period_amount', name: 'pay_period_amount',  'className': 'dt-right', render: $.fn.dataTable.render.number(',', '.', 2, '')},
                {data: 'goal_amount', name: 'goal_amount', 'className': 'dt-right', render: $.fn.dataTable.render.number(',', '.', 2, '') },
                {data: 'action', name: 'action', orderable: false, searchable: false, className: "dt-nowrap"},
                {data: 'fund_supported_pool.region.name', defaultContent: '', visible: false, searchable: true},
                {data: 'distinct_charities.charity.charity_name', defaultContent: '', visible: false, searchable: true}
            ],
            columnDefs: [
                    {

                    },
            ]
        });

    });
    </script>
@endpush
