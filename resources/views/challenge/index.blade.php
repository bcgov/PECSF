@extends('adminlte::page')

@section('content_header')
<div class="mt-2">
    <h1>Statistics</h1>

    @include('challenge.partials.tabs')

    <h6 class="mt-3">Visit this page daily during the PECSF campaign to see updated statistics, including organization participation rates!<br>
        If you have questions about PECSF statistics, send us an e-mail at <a href="mailto:PECSF@gov.bc.ca?subject=Challenge%20page">PECSF@gov.bc.ca</a>.</h6>
</div>
@endsection

@section('content')

<div class="card">
    <div class="card-body">
        <input type="hidden" id="mode" value="list" name="mode" class="form-control " />
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
            <div class="organization_name form-group col-md-4">
                <label >
                Organization Name
                </label>
                <input type="text" id="organization_name" value="" name="organization_name" class="form-control " />
            </div>
        </div>
    </div>

        {{-- <div class="form-row p-1" id="last_update_section">
            <p><span class="text-secondary font-weight-bold pr-2">Data updated as of : </span>
                <span class="text-primary">{{ $last_update ? $last_update->format('l, M jS Y - g:ia') : '' }}</span></p> 
        </div> --}}

        {{-- <br> --}}
        <div class="text-center h2 font-weight-bold pb-1"><span class="as_of_date"></span></div> 

        <div class="row justify-content-md-center">
            <div class="col-sm-4">
                <div class="card p-0">
                    <div class="card-body p-0">
                        <table class="table table-donors">
                            <tbody>
                            <tr>
                                <td class="text-center align-middle border-0"><i class="far fa-user custom-icon-style"></i>
                                </td>
                                <td class="text-left align-middle border-0"><span class="total_donors h1">0.00</span>
                                        <h6>Donors<h6>
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
                                <td class="text-left align-middle border-0"><span class="total_dollars h1">0.00</span>
                                            <h6>Dollars</h6>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex mt-1 mb-3">
            <div class="flex-fill">
                <button id="download-pdf-btn" type="button" class="btn btn-primary">
                    <span class="mx-2 px-2">Download as PDF</span>
                </button>
            </div>
    
            <div class="d-flex">
                <div class="mr-2">
                    <div class="btn-group btn-group" role="group" aria-label="Basic example">
                        <button id="list-mode-btn" type="button" class="btn btn-primary">
                                <span class="mx-2 px-2">List</span>
                        </button>
                        <button type="button" class="btn btn-dark mx-0 px-0"></button>
                        <button id="chart-mode-btn" type="button" class="btn btn-outline-secondary">
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
<script src="{{ asset('vendor/echarts/5.4.3/echarts.min.js') }}" ></script>

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
        retrieve: false,
        "searching": true,
        processing: true,
            "language": {
                processing: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw text-info"></i><span class="sr-only">Loading...</span>'
            },
        serverSide: false,
        // select: true,
        // paging: false,
        ajax: {
            url: '{!! route('challenge.index') !!}',
            data: function (data) {
                data.year = $("select[name='year']").val();
                data.organization_name = $("input[name='organization_name']").val();
            },
            "complete": function(response) {
                // console.log(response.responseJSON);
                data = response.responseJSON;
                // Update the Total Donors and Total Dollars
                $('span.as_of_date').html( data.as_of_date);
                $('span.total_donors').html( data.total_donors);
                $('span.total_dollars').html( data.total_dollars);
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
            {data: 'rank', name: 'rank', "display": 'rank', "@data-order": 'rank',  className: "dt-nowrap", searchable: false},
            {data: 'organization_name', name: 'organization_name', className: "", searchable: true },
            {data: 'participation_rate', className: '', searchable: false },
            {data: 'previous_participation_rate', className: '', searchable: false},
            {data: 'change_rate', className: 'dt-nowrap', searchable: false},
            {data: 'donors', className: "dt-nowrap",  searchable: false},
            {data: 'dollars', className: "dt-nowrap", searchable: false},
        ],
        columnDefs: [
            {
                render: function (data, type, row) {

                    if (type == 'display') {
                        // return "<div style='min-width: 20em; max-width: 40em;'><p class='text-success'>" + row.old_values + "</p><hr/>" +
                        //     "<p class='text-primary'>" + row.new_values + "</p></div>";
                        return ordinal_suffix_of( data );
                    }
                    return data;
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
        
        // clear up chart data 
        $('#chart-section').data('load', 0);

        if ( ($('input[name="mode"]').val()) == 'chart') {

            $('#chart-mode-btn').trigger('click');
        }
        oTable.ajax.reload();

    });

    $(document).on('keyup', '#organization_name', function() {

        term = $('#organization_name').val();

        clearTimeout($.data(this, 'timer'));
        var wait = setTimeout(function() {
            // oTable.ajax.reload();
            $('#dashboard-table_filter input[type="search"]').val( term );
            oTable.search( term ).draw();
        }, 500);
        $(this).data('timer', wait);
    });

    $('#download-pdf-btn').on('click', function() {
        year = $('select[name="year"]').val();
        window.location.href = "{{ route('challenge.index') }}" + '?year=' + year +'&download';
    });

    // Charting Mode 
    $('#chart-section').hide();
    var myChart = echarts.init(document.getElementById('main'));

    $(document).on('click', '#list-mode-btn', function() {
        // console.log('list-mode clicked');


        $('input[name="mode"]').val('list');

        $('#list-section').show();
        $('#chart-section').hide();

        $('.organization_name').show();

        $('#list-mode-btn').removeClass('btn-outline-secondary').addClass('btn-primary');
        $('#chart-mode-btn').removeClass('btn-primary').addClass('btn-outline-secondary');

    });

    $(document).on('click', '#chart-mode-btn', function() {
        // console.log('chart-mode clicked');
        $('input[name="mode"]').val('chart');

        $('#list-section').hide();
        $('#chart-section').show();

        $('.organization_name').hide();

        $('#list-mode-btn').removeClass('btn-primary').addClass('btn-outline-secondary');
        $('#chart-mode-btn').removeClass('btn-outline-secondary').addClass('btn-primary');
        
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
                dataType: 'json',
                success: function (data) {

                    myChart.resize();

                    myChart.setOption({
                        title: {
                            text: 'Participation Rate Chart',
                            textStyle: { fontSize: 20, fontWeight: 'bold' }, 
                            // subtext: '纯属虚构',
                            // subtextStyle: { fontSize: 20, fontWeight: 'bold' }, 
                            left: 'center',
                        },
                        color: [
                            '#32C5E9',
                            '#67E0E3',
                            '#9FE6B8',
                            '#FFDB5C',
                            '#ff9f7f',
                            '#fb7293',
                            '#E062AE',
                            '#E690D1',
                            '#e7bcf3',
                            '#9d96f5',
                            '#8378EA',
                            '#96BFFF',

                            '#dd6b66',
                            '#759aa0',
                            '#e69d87',
                            '#8dc1a9',
                            '#ea7e53',
                            '#eedd78',
                            '#73a373',
                            '#73b9bc',
                            '#7289ab',
                            '#91ca8c',
                            '#f49f42'
                            ],
                        tooltip: {
                            trigger: 'item',
                            formatter: function (params) {
                                // console.log(params);
                                 return  `<b>${params.name}</b><br/>${params.seriesName} : ${params.data.value}% </br>Change: ${params.data.change}%`;
                            }
                        },
                        // legend: {
                        //     type: 'scroll',
                        //     orient: 'vertical',
                        //     right: 10,
                        //     top: 100,
                        //     bottom: 20,
                        //     selectedMode : false,
                        //     data: data.regions,
                        // },
                        series: [
                            {
                                name: 'Participation Rate',
                                type: 'pie',
                                radius: ['15%', '70%'],
                                center: ['50%', '45%'],
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

    $(window).on('resize', function(){
        if(myChart != null && myChart != undefined){
            myChart.resize();
        }
    });

});            

</script>

@endpush
