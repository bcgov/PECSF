@extends('adminlte::page')

@section('content_header')
    {{-- <h2>Reporting</h2> --}}
    @include('admin-report.partials.tabs')
    <div class="d-flex mt-3">
        <h4>Eligible Employee Report</h4>
        <div class="flex-fill"></div>
    </div>
@endsection
@section('content')

<p><a href="/administrators/dashboard" class="BC-Gov-SecondaryButton">Back</a></p>

<form id="eligible-employee-form" method="post">
<div class="card search-filter">

    <div class="card-body pb-0 ">
        <h2>Search Criteria</h2>

        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="year">
                    Year
                </label>
                <select name="year" value="" class="form-control">
                    {{-- <option value="">All</option> --}}
                    @foreach( $years as $year => $as_of_date)
                        <option value="{{ $year }}" {{ today()->year == $year ? 'selected' : '' }}>{{ $year }} [Snapshot on : {{ $as_of_date }}]</option>
                    @endforeach 
                </select>
            </div>

            <div class="form-group col-md-1">
                <label for="emplid">
                    Emplid
                </label>
                <input name="emplid"  class="form-control" />
            </div>

            <div class="form-group col-md-2">
                <label for="name">
                    Employee Name
                </label>
                <input name="name"   class="form-control" />
            </div>

            {{-- <div class="form-group col-md-2">
                <label for="empl_status">
                    Status
                </label>
                <select name="empl_status" value="" class="form-control">
                    <option value="">All</option>
                    @foreach( $empl_status_List as $key => $value)
                        <option value="{{ $key }}">{{ $value }} ({{ $key }})</option>
                    @endforeach 
                </select>
            </div> --}}

            <div class="form-group col-md-2">
                <label for="office_city">
                    Office City 
                </label>
                <select name="office_city" value="" class="form-control">
                    <option value="">All</option>
                    @foreach( $office_cities as $value)
                        <option value="{{ $value  }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>    

        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="organization">
                    Organization
                </label>
                <select name="organization"  value="" class="form-control">
                    <option value="">All</option>
                    @foreach( $organizations as $value)
                    <option value="{{ $value }}">{{ $value }} </option>
                    @endforeach 
                </select>
            </div>

            <div class="form-group col-md-2">
                <label for="business_unit">
                    Business Unit
                </label>
                <input name="business_unit"  class="form-control" />
            </div>
            <div class="form-group col-md-2">
                <label for="department">
                    Department
                </label>
                <input name="department"  class="form-control" />
            </div>
            <div class="form-group col-md-2">
                <label for="tgb_reg_district">
                    Regional District
                </label>
                <input name="tgb_reg_district"  class="form-control" />
            </div>

            <div class="form-group col-md-1">
                <label for="search">
                    &nbsp;
                </label>
                <button type="button" id="refresh-btn" value="Refresh" class="form-control btn btn-primary" />Search</button>
            </div>
            <div class="form-group col-md-1">
                <label for="search">
                    &nbsp;
                </label>
                <button type="button" id="reset-btn" value="Reset" class="form-control btn btn-secondary">Reset</button>
            </div>
        </div>

    </div>    
</div>
</form>

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

        <div id="export-section" class="px-3 float-right">
            <button type="button" id="export-btn" value="export" class="btn btn-primary">Export</button>
            <span id="export-section-result"></span>
        </div>

        <table class="table table-bordered" id="employee-table" style="width:100%">
            <thead>
                <tr>
                    <th>Emplid</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Address 1</th>
                    <th>Address 2</th>
                    <th>City</th>
                    <th>Province</th>
                    <th>Postal code</th>
                    <th>Organization</th>
                    <th>BU code</th>
                    <th>BU name</th>
                    <th>Dept ID </th>
                    <th>Dept Name</th>
                    <th>Region</th>
                </tr>
            </thead>
        </table>

    </div>
</div>

@endsection


@push('css')


    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/fixedheader/3.2.4/css/fixedHeader.dataTables.min.css" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">

	<style>
    #employee-table_filter label {
        display:none;
    }
	#employee-table_filter label {
		text-align: right !important;
        padding-right: 10px;
	}
    .dataTables_scrollBody {
        margin-bottom: 10px;
    }
    
    div.dataTables_wrapper div.dataTables_processing {
      top: 5%;
    }
    
    /* Blink */
    .blink {
        animation: blinker 0.6s linear infinite;
        /* color: #1c87c9;
        font-size: 30px;
        font-weight: bold;
        font-family: sans-serif; */
    }
    @keyframes blinker {
        50% {
            opacity: 0;
        }
    }
    .blink-one {
      animation: blinker-one 1s linear infinite;
    }
    @keyframes blinker-one {
        0% {
            opacity: 0;
        }
    }
    .blink-two {
      animation: blinker-two 1.4s linear infinite;
    }
    @keyframes blinker-two {
        100% {
            opacity: 0;
        }
    }

</style>
@endpush


@push('js')

    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.2.4/js/dataTables.fixedHeader.min.js"></script>

    <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>

    <script>

    $(function() {

        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': '{{csrf_token()}}'
            }
        });

        // Datatables
        var oTable = $('#employee-table').DataTable({
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
            fixedHeader: true,   
            "initComplete": function(settings, json) {
                    oTable.columns.adjust().draw();
            },
            ajax: {
                url: '{!! route('reporting.eligible-employees.index') !!}',
                data: function (data) {
                    // data.term = $('#user').val();
                    data.year = $("select[name='year']").val();
                    data.emplid = $("input[name='emplid']").val();
                    data.name = $("input[name='name']").val();
                    // data.empl_status = $("select[name='empl_status']").val();
                    data.office_city = $("select[name='office_city']").val();
                    data.organization = $("select[name='organization']").val();
                    data.business_unit  = $("input[name='business_unit']").val();
                    data.department = $("input[name='department']").val();
                    data.tgb_reg_district = $("input[name='tgb_reg_district']").val();
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
                {data: 'emplid', name: 'emplid', className: "dt-nowrap" },
                {data: 'name', name: 'name', className: "dt-nowrap" },
                {data: 'empl_status' },
                {data: 'office_address1', name: 'office_address1', className: "dt-nowrap" },
                {data: 'office_address2', name: 'office_address2', className: "dt-nowrap"},
                {data: 'office_city',  className: "dt-nowrap" },
                {data: 'office_stateprovince' },
                {data: 'office_postal' },
                {data: 'organization_name', defaultContent: '', className: "dt-nowrap"  },
                // {data: 'organization.name' },
                {data: 'business_unit', defaultContent: '', className: "dt-nowrap"  },
                {data: 'business_unit_name', defaultContent: '', className: "dt-nowrap"  },
                {data: 'deptid', defaultContent: '', className: "dt-nowrap"  },
                {data: 'dept_name', defaultContent: '', className: "dt-nowrap"  },
                {data: 'tgb_reg_district', defaultContent: '', 'type':'string' },

            ],
            columnDefs: [
                    {
                        width: '5em',
                        targets: [0]
                    },
            ]
        });
        
        // Move the export button to the filter area
        $('#employee-table_filter').parent().append( $('#export-section') );

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
           
            $('.search-filter input').map( function() {$(this).val(''); });
            $('.search-filter select').map( function() { return $(this).val(''); })

            oTable.search( '' ).columns().search( '' ).draw();
        });

        // For auto-refresh 
        var intervalID = null;
        var batch_id = null;

        $('#export-btn').on('click', function() {
            
            // if (confirm("Are you sure to export the selected data ?")) {
                
            //     var export_url = '{{ route('reporting.eligible-employees.export2csv') }}';
            //     filter = $('#eligible-employee-form').serialize();
            //     let _url = export_url + '?export=1&' + filter;
            //     window.location.href = _url;
            // }

            Swal.fire({
                text: 'Are you sure to export the selected data ?'  ,
                // icon: 'question',
                //showDenyButton: true,
                confirmButtonText: 'Export',
                showCancelButton: true,
            }).then((result) => {

                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {

                    // refresh data tables first
                    oTable.draw();
                    $('#export-btn').prop('disabled', true);
                    $('#export-section-result').html('Queued. Please wait.');

                    var form = $('#eligible-employee-form');

                    // Use ajax call to submit
                    $.ajax({
                        method: "GET",
                        dataType: 'json',
                        url: '{!! route('reporting.eligible-employees.export2csv') !!}',
                        data: form.serialize(), // serializes the form's elements.
                        success: function(data) {
                            batch_id = data.batch_id;
                            console.log('export job submit');
                            intervalID = setInterval(exportProgress, 3000);
                        },
                        error: function(response) {
                            $('#export-btn').prop('disabled', false);
                            console.log('Error');
                        }
                    });
                }

            })

        })

        
        function exportProgress() {

            $.ajax({
                method: "GET",
                dataType: 'json',
                url:  '/reporting/eligible-employees/export-progress/' + batch_id,
                success: function(data)
                {
                    if (data.finished) {
                        clearInterval(intervalID);
                        $('#export-btn').prop('disabled', false);
                    }
                    $('#export-section-result').html(data.message);
                },
                error: function(response) {
                    if (response.status == 422) {
                        $('#export-btn').prop('disabled', false);
                        $('#export-section-result').html('');

                        Swal.fire({
                            title: 'Export failed!',
                            text: response.responseJSON.message,
                            icon: 'error',
                        })
                        clearInterval(intervalID);
                    }
                    console.log('export job error');
                }
                
            });

        }

    });
    </script>
@endpush
