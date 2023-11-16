@extends('adminlte::page')

@section('content_header')

@include('admin-report.partials.tabs')

<h4 class="mx-1 mt-3">Challenge Page Data Report</h4>

<div class="d-flex mt-3">
    <div class="flex-fill">
        <p><button class="ml-2 btn btn-outline-primary" onclick="window.location.href='/administrators/dashboard'">
            Back    
        </button></p>    
    </div>

    <div class="d-flex">
        <div class="mr-2">
        </div>
    </div>
</div>

@endsection

@section('content')

<div class="card">
    <div class="card-body">
        <form id="search-form">
            <div class="form-row pb-2">
                <div class="form-group col-md-2">
                    <label>
                        Campaign Year
                    </label>
                    <select name="year" id="year" class="form-control ">
                        @foreach($year_options as $key => $year)
                            <option value="{{ $year }}" {{ $key == 0 ? 'selected' : '' }}>{{ $year}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="as_of_date">
                        As of Date
                    </label>
                    <select class="form-control" id="as_of_date" name="as_of_date">
                        @foreach ($date_options as $date) 
                            <option value="{{ $date }}">{{ $date }}</option>
                        @endforeach
                    </select>                
                    
                </div>
            </div>
        </form>
    </div>

        <table class="table table-bordered" id="dashboard-table" style="width:100%">
            <thead>
                <tr class="bg-light">
                    <th>Rank</th>
                    <th>Business Unit</th>
                    <th>Organization name</th>
                    <th>Participation rate (%)</th>
                    <th>Previous rate (%)</th>
                    <th>Change (%)</th>
                    <th>Donors</th>
                    <th>Dollars</th>
                    <th>Eligible EE Count</th>
                </tr>
            </thead>
        </table>

</div>

@endsection

@push('css')

    <link href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">    

	<style>

    .dataTables_scrollBody {
        margin-bottom: 10px;
    }

    div.dataTables_wrapper div.dataTables_processing {
      top: 5%;
    }

    table.dataTable  {
        border: none;
    }

    table.dataTable thead tr {
        border-left: none;
        border-right: none;
    }
    table.dataTable thead tr th {
        border-left: none;
        border-right: none;
    }
    table.dataTable tbody tr {
        border-left: none;
        border-right: none;
    }

    /* table.dataTable tbody tr.highlight {
        color: #2e8540;
        font-weight: bold;
    } */

    table.dataTable tbody tr td {
        border-left: none;
        border-right: none;
    }

    div.dataTables_filter {
        display: none;
    }

    .as-of-date {
        color: #313132 !important;
    }

    div.dataTables_wrapper div.dataTables_length label {
        padding-top: 6px;
    }

    div.dt-buttons {
       position: relative;
       float: left;
       margin-right: 2.3em;
    }

</style>
@endpush

@push('js')

<script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}" ></script>
<script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}" ></script>

<script src="{{ asset('vendor/datatables-plugins/buttons/js/dataTables.buttons.min.js') }}" ></script>
<script src="{{ asset('vendor/datatables-plugins/buttons/js/buttons.html5.min.js') }}" ></script>
<script src="{{ asset('vendor/datatables-plugins/jszip/jszip.min.js') }}" ></script>

<script>

$(function() {

    function ordinal_suffix_of(i) {
        var j = i % 10,
            k = i % 100;
        if (j == 1 && k != 11) {
            return i + "st";
        }
        if (j == 2 && k != 12) {
            return i + "nd";
        }
        if (j == 3 && k != 13) {
            return i + "rd";
        }
        return i + "th";
    }   

    function formatAsPercentage(num) {
        return new Intl.NumberFormat('default', {
            style: 'percent',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(num / 100);
    }

    function addCommas(nStr)
    {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }

     // Datatables
    var oTable = $('#dashboard-table').DataTable({
        dom: 'Blfrtip',
        buttons: [
            { extend: 'csv',  className: 'btn btn-primary', 
                text: 'Current page to CSV',
                filename: function() {
                        var date = $('#as_of_date').val();
                        return 'Challenge_Page_Data_on_' + date;
                },
            },
            { extend: 'excel', className: 'btn btn-primary',
                text: 'Current page to Excel',
                // messageTop: 'Year : ' + $('#year').val() + '  and As of date : ' + $('#as_of_date').val(),
                filename: function() {
                        var date = $('#as_of_date').val();
                        return 'Challenge_Page_Data_on_' + date;
                },
            },
        ], 
        retrieve: true,
        "searching": true,
        processing: true,
            "language": {
                processing: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw text-info"></i><span class="sr-only">Loading...</span>'
            },
        serverSide: true,
        // select: true,
        paging: true,
        pageLength: 100,
        ajax: {
            url: '{!! route('reporting.challenge-page-data') !!}',
            data: function (data) {
                data.campaign_year = $("select[name='year']").val();
                data.as_of_date = $("select[name='as_of_date']").val();
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
            {data: 'rank', name: 'rank', className: "dt-nowrap", searchable: false},
            {data: 'business_unit', name: 'business_unit', className: "", searchable: false },
            {data: 'organization_name', name: 'organization_name', className: "", searchable: false },
            {data: 'participation_rate', className: '', searchable: false },
            {data: 'previous_participation_rate', className: '', searchable: false},
            {data: 'change_rate', className: 'dt-nowrap', searchable: false},
            {data: 'donors', className: "dt-nowrap",  searchable: false},
            {data: 'dollars', className: "dt-nowrap", searchable: false},
            {data: 'ee_count', className: "dt-nowrap", searchable: false},
            
        ],
        columnDefs: [
            {
                render: function (data, type, row) {

                    // return "<div style='min-width: 20em; max-width: 40em;'><p class='text-success'>" + row.old_values + "</p><hr/>" +
                    //     "<p class='text-primary'>" + row.new_values + "</p></div>";
                    return ordinal_suffix_of( data );
                },
                targets: [0]

            },
            {
                // render: function (data, type, row) {

                //     // return "<div style='min-width: 20em; max-width: 40em;'><p class='text-success'>" + row.old_values + "</p><hr/>" +
                //     //     "<p class='text-primary'>" + row.new_values + "</p></div>";
                //     return formatAsPercentage( data );
                // },
                // targets: [3,4,5],
            },
            {
                render: DataTable.render.number(',', '.', 2, ''),
                targets: [7],

            },
        ],
        rowCallback: function (row, data) {
            if ( data.change_rate > 0  ) {
                $(row).addClass('highlight');
            }
        }
    });

    //  year
    $(document).on('change', '#year', function () {

        $('#as_of_date').find('option').remove().end();

        $.get({
            url: '{{ route('reporting.challenge-page-data.date-options') }}' +
                        '?campaign_year=' + $(this).val(),
            dataType: 'json',
            async: false,
            cache: false,
            timeout: 30000,
            success: function(data)
            {
                // console.log( data );
                if (data) {
                    for (let i = 0; i < data.length; i++) {
                        $('#as_of_date').append($('<option>', {
                                    value: data[i],
                                    text: data[i],
                        }));
                    }
                    $('#as_of_date').val(data[0]);
                    oTable.ajax.reload();
                }
            },
            error: function(response) {
                 console.log('Error');
            }
        });

    });

    $(document).on('change', '#as_of_date', function () {
        oTable.ajax.reload();
    });

});            

</script>

@endpush
