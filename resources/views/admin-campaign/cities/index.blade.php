@extends('adminlte::page')

@section('content_header')

@include('admin-campaign.partials.tabs')

    <h4 class="mx-1 mt-3">Cities</h4>

    <div class="d-flex mt-3">
        <div class="flex-fill">
            <p><button class="ml-2 btn btn-outline-primary" onclick="window.location.href='/administrators/dashboard'">
                Back    
            </button></p>
        </div>

    </div>

@endsection
@section('content')

<div class="card">
	<div class="card-body">

		<table class="table table-bordered" id="city-table" style="width:100%">
			<thead>
				<tr>
                    <th>City</th>
					<th>Description</th>
                    <th>Province</th>
                    <th>Country</th>
                    <th>Region Code</th>
                    <th>Regioin Name</th>
				</tr>
			</thead>
		</table>

	</div>    
</div>   

@endsection


@push('css')

    <link href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">    

	<style>
	#city-table_filter label {
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
 
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}" ></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}" ></script>

    <script>
    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove(); 
        });
    }, 3000);

    $(function() {
 
        // Datatables
        var oTable = $('#city-table').DataTable({
            "scrollX": true,
            retrieve: true,
            "searching": true,
            processing: true,
            "language": {
               processing: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw text-info"></i><span class="sr-only">Loading...</span>'
            },
            serverSide: true,
            select: true,
            'order': [[0, 'asc']],
            ajax: {
                url: '{!! route('settings.cities.index') !!}',
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
                {data: 'city',  className: "dt-nowrap" },
                {data: 'DescrShort',  className: "dt-nowrap" },
                {data: 'province',  className: "dt-nowrap" },
                {data: 'country',  className: "dt-nowrap"},
                {data: 'region_code', name: "regions.code",  className: "dt-nowrap"},
                {data: 'region_name', name: "regions.name",  className: "dt-nowrap"}
            ],
            columnDefs: [
                    {
                        
                    },
            ]
        });


    });
    </script>
@endpush
