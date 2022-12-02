@extends('adminlte::page')

@section('content_header')

    <div class="d-flex mt-3 pb-1">
        <h2>Pledge Administration</h2>
        <div class="flex-fill"></div>
    </div>

    @include('admin-pledge.partials.tabs')

    <div class="d-flex mt-2">
        <div class="flex-fill"><p><a href="/administrators/dashboard">Back</a></p></div>

        <div class="d-flex">
            <div class="mr-2">
                <x-button class="btn-primary" :href="route('admin-pledge.campaign.create')">Create New Pledge</x-button>
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
            <div class="form-group col-md-1">
                <label for="tran_id">
                    Tran ID
                </label>
                <input name="tran_id" id="tran_id"  class="form-control" />
            </div>

            <div class="form-group col-md-3">
                <label for="organization_id">
                    Organization
                </label>
                <select name="organization_id" id="organization_id" value="" class="form-control">
                    <option value="">All</option>
                    @foreach( $organizations as $organization)
                    <option value="{{ $organization->id }}">{{ $organization->name }} ({{ $organization->code }})</option>
                    @endforeach 
                </select>
            </div>

            <div class="form-group col-md-2">
                <label for="emplid">
                    Empl ID
                </label>
                <input name="emplid" id="emplid"  class="form-control" />
            </div> 

            <div class="form-group col-md-2">
                <label for="pecsf_id">
                    PECSF ID
                </label>
                <input type="number" name="pecsf_id" id="pecsf_id"  class="form-control" 
                        onKeyPress="if(this.value.length==6) return false;" />
            </div> 

            <div class="form-group col-md-2">
                <label for="name">
                    Name
                </label>
                <input name="name" id="name"  class="form-control" />
            </div> 

            <div class="form-group col-md-2">
                <label for="city">
                    City
                </label>
                <select class="form-control" name="city" id="city" {{ isset($pledge) ? 'disabled' : '' }}>
                    <option value="">All</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city->city }}" >
                            {{ $city->city }}</option>
                    @endforeach
                </select>
            </div> 
        </div>

        <div class="form-row">
            <div class="form-group col-md-2">
                <label for="campaign_year">
                    Campaign Year
                </label>
                <select id="campaign_year_id" class="form-control" name="campaign_year_id">
                    <option value="">All</option>
                    @foreach ($campaign_years as $cy)
                        <option value="{{ $cy->id }}" {{ ($cy->calendar_year == date('Y')) ? 'selected' : '' }}>{{ $cy->calendar_year }}
                        </option>
                    @endforeach
                </select>
            </div> 

            <div class="form-group col-md-2">
                <label for="one_time_amt_from">One Time Amt (From)</label>
                <input class="form-control " type="number" id="one_time_amt_from" name="one_time_amt_from">
            </div>

            <div class="form-group col-md-2">
                <label for="one_time_amt_to">One Time Amt (To)</label>
                <input class="form-control " type="number" id="one_time_amt_to" name="one_time_amt_to">
            </div>

            <div class="form-group col-md-2">
                <label for="pay_period_amt_from">Bi-weekly Amt (From)</label>
                <input class="form-control " type="number" id="pay_period_amt_from" name="pay_period_amt_from">
            </div>

            <div class="form-group col-md-2">
                <label for="pay_period_amt_to">Bi-weekly Amt (To)</label>
                <input class="form-control " type="number" id="pay_period_amt_to" name="pay_period_amt_to">
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

		<table class="table table-bordered" id="campaign-table" style="width:100%">
			<thead>
				<tr>
                    <th>Tran ID</th>
                    <th>Org</th>
					<th>Empl ID</th>
                    <th>PECSF ID</th>
                    <th>Name</th>
                    <th>City</th>
                    <th>Campaign Year</th>
                    <th>FS Pool / Charities</th>
                    <th>One Time Amount</th>
                    <th>Bi Weekly Amount</th>
                    <th>Goal Amount</th>
                    <th>Action </th>
                    <th>Send to PSFT</th>
                    <th>Send At </th>
                    <th>Created At</th>
                    <th>Updated At</th>
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
    <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">

	<style>
	#campaign-table_filter label {
		text-align: right !important;
        padding-right: 10px;
	}
    
    #campaign-table_filter {
        display: none;
    }

    .dataTables_scrollBody {
        margin-bottom: 10px;
    }
</style>
@endpush

@push('js')
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>

    <script>

    $(function() {

        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': '{{csrf_token()}}'
            }
        });

        var oTable = $('#campaign-table').DataTable({
            "scrollX": true,
            retrieve: true,
            "searching": true,
            processing: true,
            serverSide: true,
            select: true,
            'order': [[0, 'desc']],
            "initComplete": function(settings, json) {
                    oTable.columns.adjust().draw();
            },
            ajax: {
                url: '{!! route('admin-pledge.campaign.index') !!}',
                type: "GET",
                data: function (data) {
                    data.tran_id  = $('#tran_id').val();
                    data.organization_id = $('#organization_id').val();
                    data.emplid = $('#emplid').val();
                    data.pecsf_id = $('#pecsf_id').val();
                    data.name = $('#name').val();
                    data.city = $('#city').val();
                    data.campaign_year_id = $('#campaign_year_id').val();
                    data.one_time_amt_from = $('#one_time_amt_from').val();
                    data.one_time_amt_to = $('#one_time_amt_to').val();
                    data.pay_period_amt_from = $('#pay_period_amt_from').val();
                    data.pay_period_amt_to = $('#pay_period_amt_to').val();
                }
            },
            columns: [
                {data: 'id',  className: "dt-nowrap"},
                {data: 'organization.code',  defaultContent: '', className: "dt-nowrap"},
                {data: 'user.primary_job.emplid', defaultContent: '' },
                {data: 'pecsf_id', defaultContent: '' },
                {data: 'user.primary_job.name', defaultContent: '', className: "dt-nowrap",
                    render: function ( data, type, row, meta ) {
                        if(row.pecsf_id) {
                            return row.last_name + ',' + row.first_name;
                        } else {
                            return data;
                        }
                    }
                },
                {data: 'user.primary_job.city', defaultContent: '', className: "dt-nowrap",
                    render: function ( data, type, row, meta ) {
                            if(row.pecsf_id) {
                                return row.city;
                            } else {
                                return data;
                            }
                        }
                },
                {data: 'campaign_year.calendar_year', className: "dt-nowrap", className: "dt-center"},
                {data: 'description', orderable: false, searchable: false, className: "dt-nowrap"},
                {data: 'one_time_amount', name: 'one_time_amount', 'className': 'dt-right', render: $.fn.dataTable.render.number(',', '.', 2, '')},
                {data: 'pay_period_amount', name: 'pay_period_amount',  'className': 'dt-right', render: $.fn.dataTable.render.number(',', '.', 2, '')},
                {data: 'goal_amount', name: 'goal_amount', 'className': 'dt-right', render: $.fn.dataTable.render.number(',', '.', 2, '') },
                {data: 'action', name: 'action', orderable: false, searchable: false, className: "dt-nowrap"},
                {data: 'ods_export_status', name: 'ods_export_status', className: "dt-nowrap",
                    render: function ( data, type, row, meta ) {
                            if( data == 'C') {
                                return 'Completed';
                            } else {
                                return '';
                            }
                        }
                },
                {data: 'ods_export_at', name: 'ods_export_at', className: "dt-nowrap" },
                {data: 'created_at', name: 'created_at', className: "dt-nowrap" },
                {data: 'updated_at', name: 'updated_at', className: "dt-nowrap" },
                {data: 'fund_supported_pool.region.name', defaultContent: '', visible: false, searchable: true},
                {data: 'distinct_charities.charity.charity_name', defaultContent: '', visible: false, searchable: true},

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


        function Toast( toast_title, toast_body, toast_class) {
            $(document).Toasts('create', {
                            class: toast_class,
                            title: toast_title,
                            autohide: true,
                            delay: 3000,
                            body: toast_body
            });
        }

        // Model -- Delete
        $(document).on("click", ".delete-pledge" , function(e) {
            e.preventDefault();

            id = $(this).attr('data-id');
            title = $(this).attr('data-code');

            Swal.fire( {
                title: 'Are you sure you want to delete the pledge "' + title + '" ?',
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
                    // Swal.fire('Saved!', '', '')
                    $.ajax({
                        method: "DELETE",
                        url:  '/admin-pledge/campaign/' + id,
                        success: function(data)
                        {
                            oTable.ajax.reload(null, false);	// reload datatables
                            Toast('Success', 'Pledge ' + title +  ' was successfully deleted.', 'bg-success' );
                        },
                        error: function(response) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.responseJSON.error,
                            })
                            console.log(response.responseJSON.error);
                        }
                    });
                } else if (result.isCancelledDenied) {
                    // Swal.fire('Changes are not saved', '', '')
                }
            })

        });



    });
    </script>
@endpush
