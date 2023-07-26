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
            <div class="text-center h2 font-weight-bold pb-1 as-of-date">{{ $summary->as_of_date->format('l, F jS Y ') }}</div> 

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

        <div class="d-flex mt-3">
            <div class="flex-fill">
            </div>
    
            <div class="d-flex">
                <div class="mr-2">
                    <div class="mt-3 btn-group btn-group" role="group" aria-label="Basic example">
                        <button id="list-mode-btn" type="button" class="btn btn-success">
                                <span class="mx-2 px-2">List</span>
                        </button>
                        <button type="button" class="btn btn-dark mx-0 px-0"></button>
                        <button id="chart-mode-btn" type="button" class="btn btn-secondary">
                                <span class="px-2">Chart<span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="list-section"> 
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
        <div id="chart-section" data-load="0"> 
            <div id="main" class="pt-2" style="width: auto;height:700px;"></div>
        </div>

    </div>
</div>

@endsection

@push('css')

    <link href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">    

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
        border-left: 5px solid #1A5A96;
       
    }   
    .table-donors .h1 {
        color: #1A5A96;
        font-size: 2.3em;
    }

    .as-of-date {
        color: #313132 !important;
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

<script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}" ></script>
<script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}" ></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js" ></script>

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

    // Charting Mode 
    var myChart = echarts.init(document.getElementById('main'));

    $(document).on('click', '#list-mode-btn', function() {
        console.log('list-mode clicked');

        $('#list-section').show();
        $('#chart-section').hide();

        $('#list-mode-btn').removeClass('btn-secondary').addClass('btn-success');
        $('#chart-mode-btn').removeClass('btn-success').addClass('btn-secondary');

    });

    $(document).on('click', '#chart-mode-btn', function() {
        console.log('chart-mode clicked');
        $('#list-section').hide();
        $('#chart-section').show();

        $('#list-mode-btn').removeClass('btn-success').addClass('btn-secondary');
        $('#chart-mode-btn').removeClass('btn-secondary').addClass('btn-success');
        
        year = $('select[name="year"]').val();

        if ($('#chart-section').data('load') == 0) {
            // Asynchronous Data Loading
            $.ajax({
                url: '{{  route('challenge.index') }}',
                type: 'GET',
                data : {
                    'year': year,
                    'chart': 1,
                },
                // data: $("#notify-form").serialize(),
                dataType: 'json',
                success: function (data) {

                    myChart.setOption({
                        title: {
                            text: 'Challenge Page Charting Example',
                            subtext: '纯属虚构',
                            left: 'center',
                        },
                        tooltip: {
                            trigger: 'item',
                            formatter: function (params) {
                                console.log(params);
                                 return  `${params.seriesName}<br/>${params.name}: ${params.data.value} (${params.data.change}%)`;
                            }
                            // formatter: '{a} <br/>{b} : {c}%'
                        },
                        legend: {
                            type: 'scroll',
                            orient: 'vertical',
                            right: 10,
                            top: 120,
                            bottom: 20,
                            data: data.regions,
                        },
                        series: [
                            {
                                name: 'Participation Rate',
                                type: 'pie',
                                radius: ['30%', '70%'],
                                center: ['40%', '50%'],
                                data:  data.values,
                                emphasis: {
                                    itemStyle: {
                                    shadowBlur: 10,
                                    shadowOffsetX: 0,
                                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                                    }
                                }
                            }
                        ]
                    });

                    $('#chart-section').data('load', 1);

                },
                complete: function() {
                },
                error: function (result) {
                    console.log('error to get the chart data');
                    console.log( result );
                }
            });

        }

    });

});            

</script>

@endpush
