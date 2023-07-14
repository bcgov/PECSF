@extends('adminlte::page')

@section('content_header')
<div class="mt-3">
<h1>Challenge</h1>

<ul class="mt-3 menu nav nav-pills" id="pills-tab">
    <li class="nav-item nav-center-4">
        <a  class="nav-link active disabled"
           href="{{ route('challenge.index') }}" role="tab" aria-controls="pills-home" aria-selected="true">
            Leaderboard</a>
    </li>
    <li class="nav-item nav-center-4">
        <a class="nav-link" href="{{  route('challenge.daily_campaign') }}" role="tab" aria-controls="pills-profile" aria-selected="false">
            Daily Campaign Update</a>
    </li>
</ul>

<h6 class="mt-3">Visit this page daily during the PECSF campaign to see updated statistics, including organization participation rates!<br>
    If you have questions about PECSF statistics, send us an e-mail at <a href="mailto:PECSF@gov.bc.ca?subject=Challenge%20page">PECSF@gov.bc.ca</a>.</h6>
</div>
@endsection

@section('content')

<div class="card">
    <div class="card-body">
        <form id="search-form" action="{{ route('challenge.index') }}" method="post">
            @csrf
            <div class="form-row pb-2">
                <div class="form-group col-md-2">
                    <label>
                        Campaign Year
                    </label>
                    <select name="year" id="year" class="form-control ">
                        @foreach($year_options as $annum)
                            <option {{ old('year')==$annum?"selected":""}} value="{{$annum}}">{{$annum}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                <label >
                Organization Name
                </label>
                <input type="text" id="organization_name" value="" name="organization_name" class="form-control " />
            </div>
        </form>
    </div>

        {{-- <div class="form-row p-1" id="last_update_section">
            <p><span class="text-secondary font-weight-bold pr-2">Data updated as of : </span>
                <span class="text-primary">{{ $last_update ? $last_update->format('l, M jS Y - g:ia') : '' }}</span></p> 
        </div> --}}

        {{-- <br> --}}
        @if ($summary)
            <div class="text-center h2 text-secondary font-weight-bold pb-1">{{ $summary->as_of_date->format('l, M jS Y ') }}</div> 

            <div class="row justify-content-md-center">
                <div class="col-sm-4">
                <div class="card p-0">
                    <div class="card-body p-0">
                    <table class="table table-donors">
                        <tbody>
                        <tr>
                            <td class="text-center align-middle border-0"><i class="far fa-user custom-icon-style"></i>
                            </td>
                            <td class="text-left align-middle border-0"><span class="h1">{{ number_format($summary->donors) }}</span>
                                    <h6>Total Donors<h6>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
                </div>
                <div class="col-sm-4">
                <div class="card p-0">
                    <div class="card-body p-0">
                    <table class="table table-amount">
                        <tbody>
                        <tr>
                            <td class="text-center align-middle border-0"><i class="fa fa-donate custom-icon-style" ></i>
                            </td>
                            <td class="text-left align-middle border-0"><span class="h1">{{ number_format($summary->dollars) }}</span>
                                        <h6>Total Dollars</h6>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
                </div>
            </div>
        @endif

    
        <table class="table table-bordered" id="dashboard-table" style="width:100%">
            <thead>
                <tr class="bg-light">
                    <th>Rank</th>
                    <th>Organization name</th>
                    <th>Participation rate</th>
                    <th>Previous rate</th>
                    <th>Change</th>
                    <th>Donors</th>
                    <th>Dollars</th>
                </tr>
            </thead>
        </table>

    </div>
</div>

@endsection

@push('css')

    
    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">

	<style>
	/* #dashboard-table_filter label {
		text-align: right !important;
        padding-right: 10px;
	}  */
    .dataTables_scrollBody {
        margin-bottom: 10px;
    }

    /* #dashboard-table_filter {
        display: none;
    }

    table.dataTable.no-footer {
        border-bottom: none;
    } */

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

    table.dataTable tbody tr.highlight {
        color: #2e8540;
        font-weight: bold;
    }
    table.dataTable tbody tr td {
        border-left: none;
        border-right: none;
    }

    div.dataTables_filter {
        display: none;
    }

    .custom-icon-style {
        font-size: 3.0em; 
         color: #616161;
    }
   
    .table-donors {
        border-left: 5px solid #3272d9;
       
    }   
    .table-donors .h1 {
        color: #3272d9;
        font-size: 2.3em;
    }

    .table-amount {
        border-left: 5px solid #2a854e;
    }     
    .table-amount .h1 {
        color: #2a854e ;
        font-size: 2.3em;
    }

</style>
@endpush

@push('js')

<script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>
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
        // dom: 'lrt',
        // "scrollX": true,
        retrieve: true,
        "searching": true,
        processing: true,
            "language": {
                processing: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw text-info"></i><span class="sr-only">Loading...</span>'
            },
        serverSide: true,
        select: true,
        // paging: false,
        "initComplete": function(settings, json) {
            min_height = $(".wrapper").outerHeight();
            $(".main-sidebar").css('min-height', min_height);
        },
        ajax: {
            url: '{!! route('challenge.index') !!}',
            data: function (data) {
                data.year = $("select[name='year']").val();
                data.organization_name = $("input[name='organization_name']").val();
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
            {data: 'organization_name', name: 'organization_name', className: "", searchable: false },
            {data: 'participation_rate', className: '', searchable: false },
            {data: 'previous_participation_rate', className: '', searchable: false},
            {data: 'change_rate', className: 'dt-nowrap', searchable: false},
            {data: 'donors', className: "dt-nowrap",  searchable: false},
            {data: 'dollars', className: "dt-nowrap", searchable: false},
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
                render: function (data, type, row) {

                    // return "<div style='min-width: 20em; max-width: 40em;'><p class='text-success'>" + row.old_values + "</p><hr/>" +
                    //     "<p class='text-primary'>" + row.new_values + "</p></div>";
                    return formatAsPercentage( data );
                },
                targets: [2,3,4],
            },
            {
                render: DataTable.render.number(',', '', 0, '$'),
                targets: [6],
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
        // oTable.ajax.reload();
        event.preventDefault();

        $("#search-form").submit();

        // Update the last update datetime
        current_year =  new Date().getFullYear();
        if ($("select[name='year']").val() == current_year ) {
            $('#last_update_section').show();
        } else {
            $('#last_update_section').hide();
        }

    });

    $(document).on('keyup', '#organization_name', function() {

        var $this = $(this);

        clearTimeout($.data(this, 'timer'));

        var wait = setTimeout(function() {
            oTable.ajax.reload();
        }, 500);
        $(this).data('timer', wait);
    });


});            

</script>

@endpush
