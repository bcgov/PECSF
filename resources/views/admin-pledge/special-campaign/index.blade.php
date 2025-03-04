@extends('adminlte::page')

@section('content_header')


    <div class="d-flex mt-3">
        <h2>Pledge Administration</h2>
        <div class="flex-fill"></div>
    </div>

    @include('admin-pledge.partials.tabs')

    <div class="d-flex mt-3">
        <div class="flex-fill">
            <p><button class="ml-2 btn btn-outline-primary" onclick="window.location.href='/administrators/dashboard'">
                Back    
            </button></p>    
        </div>

        <div class="d-flex">
            <div class="mr-2">
                <x-button class="btn-primary" :href="route('admin-pledge.special-campaign.create')">Create New Pledge</x-button>
            </div>
        </div>
    </div>

@endsection
@section('content')

{{-- @if ($message = Session::get('success'))
    <div class="mx-1 my-2">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
@endif --}}

<div class="card">
<form class="filter">
    <div class="card-body pb-0 search-filter">
        <h2>Search Criteria</h2>
        <p>Enter any informartion you have and click Search. Leave fields blank for a list of all values.</p>

        <div class="form-row">
            <div class="form-group col-md-1">
                <label for="tran_id">
                    Tran ID
                </label>
                <input type="number" name="tran_id" id="tran_id"  class="form-control" 
                        value="{{ isset($filter['tran_id']) ? $filter['tran_id'] : '' }}" />
            </div>
            <div class="form-group col-md-3">
                <label for="organization_id">
                    Organization
                </label>
                <select name="organization_id" id="organization_id" value="" class="form-control">
                    <option value="">All</option>
                    @foreach( $organizations as $organization)
                    <option value="{{ $organization->id }}"
                        {{ isset($filter['organization_id']) && $filter['organization_id'] == $organization->id ? 'selected' : '' }}>
                        {{ $organization->name }} ({{ $organization->code }})</option>
                    @endforeach 
                </select>
            </div>

            <div class="form-group col-md-2">
                <label for="emplid">
                    Empl ID
                </label>
                <input type="number" name="emplid" id="emplid"  class="form-control" 
                            value="{{ isset($filter['emplid']) ? $filter['emplid'] : '' }}"
                            onKeyPress="if(this.value.length==6) return false;"/>
            </div> 

            <div class="form-group col-md-2">
                <label for="pecsf_id">
                    PECSF ID
                </label>
                <input type="number" name="pecsf_id" id="pecsf_id"  class="form-control" 
                            value="{{ isset($filter['pecsf_id']) ? $filter['pecsf_id'] : '' }}"
                            onKeyPress="if(this.value.length==6) return false;"/>
            </div> 

            <div class="form-group col-md-2">
                <label for="name">
                    Name
                </label>
                <input name="name" id="name"  class="form-control" 
                    value="{{ isset($filter['name']) ? $filter['name'] : '' }}"/>
            </div> 

            <div class="form-group col-md-2">
                <label for="city">
                    City
                </label>
                <select class="form-control" name="city" id="city" {{ isset($pledge) ? 'disabled' : '' }}>
                    <option value="">All</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city->city }}" 
                            {{ isset($filter['city']) && $filter['city'] == $city->city ? 'selected' : '' }}>
                            {{ $city->city }}</option>
                    @endforeach
                </select>
            </div> 
        </div>

        <div class="form-row">
            <div class="form-group col-md-2">
                <label for="yearcd">
                    Calendar Year
                </label>
                <select id="yearcd" class="form-control" name="yearcd">
                    <option value="">All</option>
                    @foreach ($years as $year)
                        <option value="{{ $year }}" {{ ((isset($filter['yearcd']) ? $filter['yearcd'] : date('Y')) == $year) ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div> 

            <div class="form-group col-md-1">
                <label for="seqno">
                    Seq. No
                </label>
                <input type="number"  name="seqno" id="seqno"  class="form-control" 
                        value="{{ isset($filter['seqno']) ? $filter['seqno'] : '' }}">
            </div>

            <div class="form-group col-md-4">
                <label for="special_campaign_name">
                    Special Campaign Name 
                </label>
                <input type="text"  name="special_campaign_name" id="special_campaign_name"  class="form-control" 
                        value="{{ isset($filter['special_campaign_name']) ? $filter['special_campaign_name'] : '' }}">
            </div>

        </div>

        <div class="form-row">

            <div class="form-group col-md-2">
                <label for="deduct_pay_from">
                    Pay Date
                </label>
                <input type="date"  name="deduct_pay_from" id="deduct_pay_from"  class="form-control" 
                        value="{{ isset($filter['deduct_pay_from']) ? $filter['deduct_pay_from'] : '' }}">
            </div>

            <div class="form-group col-md-2">
                <label for="cancelled">
                    Cancel Status
                </label>
                <select class="form-control" name="cancelled" id="cancelled">
                    <option value="" {{ isset($filter['cancelled']) && $filter['cancelled'] == '' ? 'selected' : '' }}>All</option>
                    <option value="C" {{ isset($filter['cancelled']) && $filter['cancelled'] == 'C' ? 'selected' : '' }}>Cancelled</option>
                    <option value="N" {{ isset($filter['cancelled']) && $filter['cancelled'] == 'N' ? 'selected' : '' }}>Not Cancelled</option>
                </select>
            </div> 

            <div class="form-group col-md-2">
                <label for="one_time_amt_from">Amount (From)</label>
                <input class="form-control " type="number" id="one_time_amt_from" name="one_time_amt_from"
                        value="{{ isset($filter['one_time_amt_from']) ? $filter['one_time_amt_from'] : '' }}">
            </div>

            <div class="form-group col-md-2">
                <label for="one_time_amt_to">Amount (To)</label>
                <input class="form-control " type="number" id="one_time_amt_to" name="one_time_amt_to"
                        value="{{ isset($filter['one_time_amt_to']) ? $filter['one_time_amt_to'] : '' }}">
            </div>

            <div class="form-group col-md-1" style="min-width: 100px;">
                <label for="search">
                    &nbsp;
                </label>
                <button type="button" id="refresh-btn" value="Search" class="form-control btn-primary">Search</button>
            </div>
            <div class="form-group col-md-1" style="min-width: 100px;">
                <label for="search">
                    &nbsp;
                </label>
                <button type="button" id="reset-btn" value="Reset" class="form-control  btn-secondary " >Clear</button>
            </div>

        </div>

    </div> 
</form>  
</div>   

<div class="card">
	<div class="card-body">

        <table class="table table-bordered" id="special-campaign-table" style="width:100%">
			<thead>
				<tr>
                    <th>Tran ID</th>
                    <th>Org</th>
					<th>Empl ID</th>
                    <th>PECSF ID</th>
                    <th>Seqno</th>
                    <th>Name</th>
                    <th>City</th>
                    <th>Calendar Year</th>
                    <th>Special Campaign</th>
                    <th>Amount</th>
                    <th>Action </th>
                    <th>Cancelled</th>
                    <th>Deduct Pay</th> 
                    <th>Send to PSFT</th>
                    <th>Send At </th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    {{-- <th>Pool Name</th>
                    <th>Charites Name</th> --}}

				</tr>
			</thead>
		</table>

	</div>
</div>

@endsection


@push('css')

    <link href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">    
    <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/toastr/toastr.min.css') }}" rel="stylesheet">

	<style>
	#special-campaign-table_filter label {
		text-align: right !important;
        padding-right: 10px;
	}
    
    #special-campaign-table_filter {
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
    <script src="{{ asset('vendor/toastr/toastr.min.js') }}" ></script>

    <script>

    $(function() {

        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': '{{csrf_token()}}'
            }
        });

        var oTable = $('#special-campaign-table').DataTable({
            "scrollX": true,
            retrieve: true,
            "searching": true,
            processing: true,
            "language": {
               processing: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw text-info"></i><span class="sr-only">Loading...</span>'
            },
            stateSave: true,   
            serverSide: true,
            // select: true,
            'order': [[0, 'desc']],
            "initComplete": function(settings, json) {
                    oTable.columns.adjust().draw(false);

                @if (!(str_contains( url()->previous(), 'admin-pledge/special-campaign')))
                    oTable.page( 'first' ).draw( 'page' );
                @endif  
                
                min_height = $(".wrapper").outerHeight();
                $(".main-sidebar").css('min-height', min_height);
            },
            ajax: {
                url: '{!! route('admin-pledge.special-campaign.index') !!}',
                type: "GET",
                data: function (data) {
                    data.tran_id  = $('#tran_id').val();
                    data.organization_id = $('#organization_id').val();
                    data.emplid = $('#emplid').val();
                    data.pecsf_id = $('#pecsf_id').val();
                    data.seqno = $('#seqno').val();
                    data.name = $('#name').val();
                    data.city = $('#city').val();
                    data.yearcd = $('#yearcd').val();
                    data.one_time_amt_from = $('#one_time_amt_from').val();
                    data.one_time_amt_to = $('#one_time_amt_to').val();
                    data.cancelled = $('#cancelled').val();
                    data.special_campaign_name = $('#special_campaign_name').val();
                    data.deduct_pay_from = $('#deduct_pay_from').val();
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
                {data: 'organization.code',  defaultContent: '', className: "dt-nowrap"},
                {data: 'user.primary_job.emplid', defaultContent: '' },
                {data: 'pecsf_id', defaultContent: '' },
                {data: 'seqno', defaultContent: '' },
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
                // {data: 'description', orderable: false, searchable: false, className: "dt-nowrap",  },
                {data: 'special_campaign.name', defaultContent: '', orderable: false, searchable: false, className: "dt-nowrap",  },
                {data: 'one_time_amount', name: 'one_time_amount', 'className': 'dt-right', render: $.fn.dataTable.render.number(',', '.', 2, '')},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: "dt-nowrap"},
                {data: 'cancelled', name: 'cancelled',  orderable: false, searchable: false, className: "dt-nowrap",
                    render: function ( data, type, row, meta ) {
                            if( data == null) {
                                return '';
                            } else {
                                return 'Cancelled';
                            }
                        }
                },
                {data: 'deduct_pay_from', name: 'deduct_pay_from', className: "dt-nowrap" },
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
                // {data: 'fund_supported_pool.region.name', defaultContent: '', visible: false, searchable: true},
                // {data: 'charity.charity_name', defaultContent: '', visible: false, searchable: true},

            ],
            columnDefs: [
                {
                    
                },
            ]
        });

        $(window).keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                oTable.ajax.reload();
                return false;
            }
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
                        url:  '/admin-pledge/special-campaign/' + id,
                        success: function(data)
                        {
                            oTable.ajax.reload(null, false);	// reload datatables
                            // Toast('Success', 'Pledge ' + title +  ' was successfully deleted.', 'bg-success' );
                            toastr["success"]( 'The Pledge with Transaction ID ' + title +  ' has been successfully deleted.', '',
                                 {"closeButton": true, "newestOnTop": true, "timeOut": "5000" });
                        },
                        error: function(xhr, resp, text) {
                            if (xhr.status == 401 || xhr.status == 419) {
                                { // session expired 
                                    window.location.href = '/login'; 
                                }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: xhr.responseJSON.error,
                                })
                                console.log(xhr.responseJSON.error);
                            }
                        }
                    });
                } else if (result.isCancelledDenied) {
                    // Swal.fire('Changes are not saved', '', '')
                }
            })

        });


    });

@if ($message = Session::get('success'))
    $(function() {
        toastr["success"]( "{{ $message }}", '',
            {"closeButton": true, "newestOnTop": true, "timeOut": "5000" });
    });
@endif

    </script>
@endpush
