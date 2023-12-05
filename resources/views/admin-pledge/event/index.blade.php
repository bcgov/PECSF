@extends('adminlte::page')

@section('content_header')

    <div class="d-flex mt-3 pb-1">
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
                <div class="button-group">
                    <a href="/admin-pledge/maintain-event/create" class="btn btn-primary">Add a New Event Pledge</a>
                    <a id="pills-home-tab" style="color:#1a5a96;background:transparent;font-weight:bold;text-decoration: none;" 
                        class="{{ str_contains(Route::current()->getName(), 'admin-pledge.submission-queue') ? 'active' : '' }} btn btn-secondary activewhite"  
                        href="{{ route('admin-pledge.submission-queue.index') }}">PECSF Event Submission Queue</a>
                </div>
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
                    <input type="number" name="tran_id" id="tran_id"  class="form-control" 
                        value="{{ isset($filter['tran_id']) ? $filter['tran_id'] : '' }}" />
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
                        Empl ID / Name
                    </label>
                    <input name="emplid" id="emplid"  class="form-control" 
                                value="{{ isset($filter['emplid']) ? $filter['emplid'] : '' }}"/>
                </div> 
    
                <div class="form-group col-md-2">
                    <label for="pecsf_id">
                        PECSF ID
                    </label>
                    <input type="number" name="pecsf_id" id="pecsf_id"  class="form-control" 
                            value="{{ isset($filter['pecsf_id']) ? $filter['pecsf_id'] : '' }}"
                            onKeyPress="if(this.value.length==6) return false;" />
                </div> 
    
                <div class="form-group col-md-2">
                    <label for="description">
                        Event Name
                    </label>
                    <input name="description" id="description"  class="form-control" 
                            value="{{ isset($filter['description']) ? $filter['description'] : '' }}"/>
                </div> 
    
                <div class="form-group col-md-2">
                    <label for="employment_city">
                        Employment City
                    </label>
                    <select class="form-control" name="employment_city" id="employment_city">
                        <option value="">All</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->city }}" 
                                {{ isset($filter['employment_city']) && $filter['employment_city'] == $city->city ? 'selected' : '' }}>
                                {{ $city->city }}</option>
                        @endforeach
                    </select>
                </div> 
            </div>
    
            <div class="form-row">
                <div class="form-group col-md-2">
                    <label for="campaign_year">
                        Calendar Year
                    </label>
                    <select id="campaign_year_id" class="form-control" name="campaign_year_id">
                        <option value="">All</option>
                        @foreach ($campaign_years as $cy)
                            {{-- <option value="{{ $cy->id }}" {{ ($cy->calendar_year == date('Y')) ? 'selected' : '' }}>{{ $cy->calendar_year }} --}}
                                <option value="{{ $cy->id }}" {{ 
                                    isset($filter['campaign_year_id']) ? ($filter['campaign_year_id'] == $cy->id ? 'selected' : '') :
                                    ($cy->calendar_year == (date('Y') + 1) ? 'selected' : '') }}>
                                    {{ $cy->calendar_year }} 
                            </option>
                        @endforeach
                    </select>
                </div> 
    
                <div class="form-group col-md-2">
                    <label for="event_type">Event Type</label>
                    <select name="event_type" id="event_type" value="" class="form-control">
                        <option value="">Select a Event Type</option>
                        @foreach ($event_types as $event_type) 
                            <option value="{{ $event_type }}" {{ isset($filter['event_type']) ? ($filter['event_type'] == $event_type ? 'selected' : '') : '' }}>
                                {{ $event_type }}</option>
                        @endforeach
                    </select>
                </div>
    
                <div class="form-group col-md-2">
                    <label for="sub_type">Sub Type</label>
                    <select name="sub_type" id="sub_type" value="" class="form-control">
                        <option value="">Select a Sub Type</option>
                        @foreach ($sub_types as $sub_type) 
                            <option value="{{ $sub_type }}" {{ isset($filter['sub_type']) ? ($filter['sub_type'] == $sub_type ? 'selected' : '') : '' }}>
                                {{ $sub_type }}</option>
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
    
            <table class="table table-bordered" id="event-pledge-table" style="width:100%">
                <thead>
                    <tr>
                        <th>Tran ID</th>
                        <th>Calendar Year</th>
                        <th>Organization Code</th>
                        <th>Form Submitter</th>
                        <th>Employee ID</th>
                        <th>PECSF Identifier</th>
                        <th>Employee Name</th>
                        <th>Business Unit</th>
                        <th>Dept ID</th>
                        <th>Dept Name</th>
                        <th>Region</th>
                        <th>Office City</th>
                        <th>Event Type</th>
                        <th>Donation Amount</th>
                        <th>Sub Type</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
    
        </div>
    </div>

<!-- Modal -->
<div class="modal fade" id="pledgeDetailModal" tabindex="-1" role="dialog" aria-labelledby="pledgeDetailModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
        <div class="modal-header bg-primary">
            <h5 class="modal-title" id="pledgeDetailModalTitle">Event Pledge Detail -- Tran ID <span class="tran-id"></span>
                    <span class="text-dark font-weight-bold"></span></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
</div>

@endsection

@push('css')

        <link href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
        <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">        

	<style>
	#event-pledge-table_filter label {
		text-align: right !important;
        padding-right: 10px;
	}

    #event-pledge-table_filter {
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

        var oTable = $('#event-pledge-table').DataTable({
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

                    @if (!(str_contains( url()->previous(), 'admin-pledge/maintain-event')))
                        oTable.page( 'first' ).draw( 'page' );
                    @endif

                    min_height = $(".wrapper").outerHeight();
                    $(".main-sidebar").css('min-height', min_height);
            },
            ajax: {
                url: '{!! route('admin-pledge.maintain-event.index') !!}',
                type: "GET",
                data: function (data) {
                    data.tran_id  = $("form.filter input[name='tran_id']").val(); 
                    data.organization_code = $("form.filter select[name='organization_code']").val();
                    data.emplid = $("form.filter input[name='emplid']").val(); 
                    data.pecsf_id = $("form.filter input[name='pecsf_id']").val();
                    data.description = $("form.filter input[name='description']").val();
                    data.employment_city = $("form.filter select[name='employment_city']").val();
                    data.campaign_year_id = $("form.filter select[name='campaign_year_id']").val(); 
                    data.event_type = $("form.filter select[name='event_type']").val();
                    data.sub_type   = $("form.filter select[name='sub_type']").val();
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
                {data: 'campaign_year.calendar_year', defaultContent: '', className: "dt-nowrap", className: "dt-center"},
                {data: 'organization_code'  },
                {data: 'form_submitted_by.name', defaultContent: '', className: "dt-nowrap"},
                {data: 'bc_gov_id', defaultContent: '' },
                {data: 'pecsf_id', defaultContent: '' },
                {data: 'employee_name', defaultContent: '', className: "dt-nowrap" },
                {data: 'bu.code', defaultContent: '' },
                {data: 'deptid', defaultContent: '', className: "dt-nowrap" },
                {data: 'dept_name', defaultContent: '',className: "dt-nowrap" },
                {data: 'region.name', defaultContent: '' },
                {data: 'employment_city', defaultContent: '' },
                {data: 'event_type', defaultContent: '',className: "dt-nowrap" },
                {data: 'deposit_amount', name: 'deposit_amount', className: 'dt-right', render: $.fn.dataTable.render.number(',', '.', 2, '') },
                {data: 'sub_type', defaultContent: '', className: "dt-nowrap", 
                        render: function (data, type, row) {
                            return data == 'false' ? '' : data;
                        }
                },
                {data: 'action', name: 'action', orderable: false, searchable: false, className: "dt-nowrap"},

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
            $('.search-filter select').map( function() { return $(this).val(''); });

            oTable.search( '' ).columns().search( '' ).draw();
        });


        $(document).on("click",".more-info-pledge", function(event){
            event.preventDefault();
            event.stopPropagation();

            // var current_id = event.target.id;
            id  = $(this).data('id');

            target = '#pledgeDetailModal .modal-body';
            $("#pledgeDetailModal span.tran-id").html(id);
            $(target).html('');

            console.log( 'more info - ' );

            // Lanuch Modal page for listing the Pool detail
            $.ajax({
                url: '/admin-pledge/maintain-event/' + id,
                type: 'GET',
                // data: 'id='+ id,
                dataType: 'html',
                success: function (result, text, xhr) {
                    // $('.modal-title span').html(name);
                    if(result.indexOf('body class="login-page"') != -1){
                        window.location.href = '/login';
                    }
                    $(target).html(result);
                    $('#pledgeDetailModal').modal('show');
                },
                error: function(xhr, resp, text) {
                    alert("Something went wrong, Please try again...");
                },
            })

        });

    });            
    </script>

@endpush
