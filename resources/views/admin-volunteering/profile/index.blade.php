@extends('adminlte::page')

@section('content_header')


    <div class="d-flex mt-3">
        <h2>Maintain Volunteer Profile</h2>
        <div class="flex-fill"></div>
    </div>

    @include('admin-volunteering.partials.tabs')

    <div class="d-flex mt-3">
        <div class="flex-fill">
            <p><button class="ml-2 btn btn-outline-primary" onclick="window.location.href='/administrators/dashboard'">
                Back    
            </button></p>
        </div>

        <div class="d-flex">
            <div class="mr-2">
                <x-button class="btn-primary" :href="route('admin-volunteering.profile.create')">Create Volunteer Profile</x-button>
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

        <div class="form-row">
            <div class="form-group col-md-1">
                <label for="tran_id">
                    Tran ID
                </label>
                <input type="number" name="tran_id" id="tran_id"  class="form-control" 
                    value="{{ isset($filter['tran_id']) ? $filter['tran_id'] : '' }}" />
            </div>

            <div class="form-group col-md-2">
                <label for="campaign_year">
                    Campaign Year
                </label>
                <select id="campaign_year" class="form-control" name="campaign_year">
                    <option value="all">All</option>
                    @foreach ($year_list as $year)
                        <option value="{{ $year }}" {{ 
                            isset($filter['campaign_year']) ? ($filter['campaign_year'] == $year ? 'selected' : '') :
                            ($year == today()->year ? 'selected' : '') }}>
                            {{ $year }} 
                        </option>
                    @endforeach
                </select>
            </div> 

            <div class="form-group col-md-3">
                <label for="organization_id">
                    Organization
                </label>
                <select name="organization_code" id="organization_code" value="" class="form-control">
                    <option value="">All</option>
                    @foreach( $organizations as $organization)
                    <option value="{{ $organization->code }}"
                        {{ isset($filter['organization_code']) && $filter['organization_code'] == $organization->code ? 'selected' : '' }}>
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

        </div>

        <div class="form-row">

            <div class="form-group col-md-4">
                <label for="business_unit_code">
                    Business Unit
                </label>
                <select type="text" class="form-control" name="business_unit_code" id="business_unit_code"
                        placeholder="" role="listbox">
                    <option value="" selected="selected">All</option>
                    @foreach($business_units as $bu)
                        <option role="listitem" value="{{$bu->code}}"
                            {{ isset($filter['business_unit_code']) && $filter['business_unit_code'] == $key ? 'selected' : '' }}>
                            {{$bu->name}}</option>
                    @endforeach
                </select>
            </div> 

            <div class="form-group col-md-2">
                <label for="preferred_role">
                    Preferred Role
                </label>
                <select class="form-control" name="preferred_role" id="preferred_role" role="listbox">
                    <option value="">All</option>
                    @foreach ($role_list as $key => $value)
                        <option role="listitem" value="{{ $key }}" 
                            {{ isset($filter['preferred_role']) && $filter['preferred_role'] == $key ? 'selected' : '' }}>
                            {{ $value }}</option>
                    @endforeach
                </select>
            </div> 

            <div class="form-group col-md-2">
                <label for="no_of_years">
                    No of years
                </label>
                <select class="form-control" name="no_of_years" id="no_of_years" role="listbox">
                    <option role="listitem" value="">All</option>
                    @foreach ( range(1,50) as $value ) 
                        <option role="listitem" value="{{ $value }}" 
                        {{ isset($filter['no_of_years']) && $filter['no_of_years'] == $key ? 'selected' : '' }}>
                        {{ $value }}</option>
                    @endforeach
                </select>
            </div> 

            <div class="form-group col-md-2">
                <label for="city">
                    Office City
                </label>
                <select class="form-control" name="city" id="city" role="listbox">
                    <option role="listitem" value="">All</option>
                    @foreach ($cities as $city ) 
                        <option role="listitem" value="{{ $city->city }}" 
                        {{ isset($filter['city']) && $filter['city'] == $city->city ? 'selected' : '' }}>
                        {{ $city->city }}</option>
                    @endforeach
                </select>
            </div> 

            <div class="form-group col-md-1">
                <label for="search">
                    &nbsp;
                </label>
                <button type="button" id="refresh-btn" value="Search" class="form-control btn-primary">Search</button>
            </div>
            <div class="form-group col-md-1">
                <label for="search">
                    &nbsp;
                </label>
                <button type="button" id="reset-btn" value="Reset" class="form-control  btn-secondary" >Clear</button>
            </div>

        </div>

    </div> 
</form>  
</div>   

<div class="card">
	<div class="card-body">

        <table class="table table-bordered" id="donate-now-table" style="width:100%">
			<thead>
				<tr>
                    <th>Tran ID</th>
                    <th>Campaign Year</th>
                    <th>Org</th>
					<th>Empl ID</th>
                    <th>PECSF ID</th>
                    <th>Name</th>
                    {{-- <th>Region</th> --}}
                    <th>Business Unit</th>
                    <th>No Of Years</th>
                    <th>Preferred Role</th>
                    <th>Region</th>
                    <th>Office City</th>
                    <th>Action </th>
                    <th>Created At</th>
                    <th>Updated At</th>
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
	#donate-now-table_filter label {
		text-align: right !important;
        padding-right: 10px;
	}
    
    #donate-now-table_filter {
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

        var oTable = $('#donate-now-table').DataTable({
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

                @if (!(str_contains( url()->previous(), 'admin-volunteering/profile')))
                    oTable.page( 'first' ).draw( 'page' );
                @endif

            },
            ajax: {
                url: '{!! route('admin-volunteering.profile.index') !!}',
                type: "GET",
                data: function (data) {
                    data.tran_id  = $('#tran_id').val();
                    data.organization_code = $('#organization_code').val();
                    data.emplid = $('#emplid').val();
                    data.pecsf_id = $('#pecsf_id').val();
                    data.name = $('#name').val();
                    data.city = $('#city').val();
                    data.campaign_year = $('#campaign_year').val();
                    data.business_unit_code = $('#business_unit_code').val();
                    data.no_of_years = $('#no_of_years').val();
                    data.preferred_role = $('#preferred_role').val();

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
                {data: 'campaign_year', className: "dt-nowrap", className: "dt-center"},
                {data: 'organization_code',  defaultContent: '', className: "dt-nowrap"},
                {data: 'emplid', defaultContent: '' },
                {data: 'pecsf_id', defaultContent: '' },

                {data: 'primary_job.name', defaultContent: '', className: "dt-nowrap",
                    render: function ( data, type, row, meta ) {
                        if(row.pecsf_id) {
                            return row.last_name + ',' + row.first_name;
                        } else {
                            return data;
                        }
                    }
                },
                // {data: 'primary_job.tgb_reg_district', defaultContent: '', className: "dt-nowrap",
                //     render: function ( data, type, row, meta ) {
                //             if(row.pecsf_id) {
                //                 return row.related_city.region.name;
                //                 return '';
                //             } else {
                //                 return row.primary_job.city_by_office_city.region.name;
                //             }
                //         }
                // },
                {data: 'business_unit.name',  defaultContent: '', className: "dt-nowrap"},
                {data: 'no_of_years',  defaultContent: '', className: "dt-nowrap"},
                {data: 'preferred_role_name',  defaultContent: '', className: "dt-nowrap"},
                {data: 'employee_region.name',  defaultContent: '', orderable: false, searchable: false,  className: "dt-nowrap"},
                {data: 'employee_city.city', defaultContent: '', orderable: false, searchable: false, className: "dt-nowrap" },
                {data: 'action', name: 'action', orderable: false, searchable: false, className: "dt-nowrap"},
                {data: 'created_at', name: 'created_at', className: "dt-nowrap" },
                {data: 'updated_at', name: 'updated_at', className: "dt-nowrap" },

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
        $(document).on("click", ".delete-profile" , function(e) {
            e.preventDefault();

            id = $(this).attr('data-id');
            title = $(this).attr('data-code');

            Swal.fire( {
                title: 'Are you sure you want to delete the profile "' + title + '" ?',
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
                        url:  '/admin-volunteering/profile/' + id,
                        success: function(data)
                        {
                            oTable.ajax.reload(null, false);	// reload datatables
                            // Toast('Success', 'The volunteer profile ' + title +  ' was successfully deleted.', 'bg-success' );
                            toastr["success"]( 'The volunteer profile "' + title + '" has been successfully deleted.', '',
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
