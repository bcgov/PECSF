@extends('adminlte::page')

@section('content_header')
    {{-- <h2>Reporting</h2> --}}
    @include('system-security.partials.tabs')

    <div class="d-flex mt-3">
        <h4>Page Visits Overview</h4>
        <div class="flex-fill"></div>
    </div>

@endsection
@section('content')

<div class="card">
    <div class="card-body">

        <!-- Header -->       


        <div class="row text-center px-5">
            <div class="col-8 ">
                
                    <div class="form-inline">
                        <label class="my-1 mr-2" for="time-range">Option:</label>
                        <select id="time-range" name="time-range" class="form-control mr-2">
                            <option value="year" {{ $time_range == 'year' ? 'selected' : '' }}>By years</option>
                            <option value="month" {{ $time_range == 'month' ? 'selected' : '' }}>By months</option>
                            <option value="week" {{ $time_range == 'week' ? 'selected' : '' }}>By weeks</option>
                            <option value="day" {{ $time_range == 'day' ? 'selected' : '' }}>Custom</option>
                        </select>
                        
                        <select id="year-picker" name='year' class="form-control d-none">
                            <option value="">All</option>
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ $year == today()->year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                        
                        <div id="day-range-picker" class="{{ $time_range == 'day' ? '' : 'd-none' }} form-inline">
                            <input type="date" id="day-start" name="day-start" value="{{ $day_start }}"class="form-control mr-2">
                            <input type="date" id="day-end" name="day-end"  value="{{ $day_end }}" class="form-control">
                        </div>
                    </div>
                
                
            </div>
            <div class="col-4 ">

                    <div class="form-inline">
                        <label class="my-1 mr-2" for="filter">Filter:</label>
                    
                        <select id="filter" name='filter' class="form-control">
                            <option value="">All</option>
                            @foreach($category_options as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>                
                    </div>

            </div>
        </div>

        <div class="pt-4"></div> 

        <!-- Key Metrics -->
        <div class="row text-center px-5">
            <div class="col-md-4">
                <div class="bg-primary text-white p-3 rounded">
                    <h2 class="h5">Total Visits</h2>
                    <p class="h3" id="visit_total_count">{{ $visit_total_count }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-success text-white p-3 rounded">
                    <h2 class="h5">Number of period</h2>
                    <p class="h3" id="visit_no_of_category">{{ $visit_no_of_category }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-warning text-white p-3 rounded">
                    <h2 class="h5">Average Visit</h2>
                    <p class="h3" id="visit_average">{{ $visit_average }}</p>
                </div>
            </div>
        </div>

        <!-- Placeholder for Charts -->
        <div id="transactionsChart" style="width: 100%; height: 700px; margin-top:10px;"></div>

    </div>
</div>

@endsection


@push('css')
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">    
    <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">

@endpush

@push('js')
<script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>
<script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}" ></script>
<script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}" ></script>
<script src="{{ asset('vendor/echarts/5.6.0/echarts.min.js') }}" ></script>

<script>

    $(function() {

        var chartDom = document.getElementById('transactionsChart');
        var myChart = echarts.init(chartDom);
        var option = {
            title: [
                {
                    left: 'center',
                    text: 'Visits Per Period',
                },
            ],
            tooltip: { trigger: 'axis' },
            legend: {
                top: '3%', 
                left: '5%',
                right: '5%',
                padding: 10,
                data:  @json($y_axis_data),
            },
            dataZoom: @json($data_zoom),
            grid: {
                top: '25%',
                left: '3%',
                right: '4%',
                bottom: '10%',
                containLabel: true
            },
            toolbox: {
                feature: {
                    saveAsImage: {}
                }
            },
            xAxis: {
                type: 'category',
                axisLabel: { interval: 0, rotate: 30 },
                data: @json($x_axis_data),
            },
            yAxis: { type: 'value' },
            series: @json( $series_data ),
        };

        myChart.setOption(option);

        // Update the picker based on selection
        $('#time-range').on('change', function() {

            $('#year-picker').addClass('d-none');
            $('#day-range-picker').addClass('d-none');

            if (this.value === 'month') {
                $('#year-picker').removeClass('d-none');
            } else if (this.value === 'week') {
                $('#year-picker').removeClass('d-none');
            } else if (this.value === 'day') {
                $('#day-range-picker').removeClass('d-none');
            }

            update_chart();

        });

        let typingTimer; // Timer identifier
        let doneTypingInterval = 1000; // Time in milliseconds (1 second)

        $('select[name="year"], #day-start, #day-end').on('change', function() {
            clearTimeout(typingTimer); // Clear any existing timer
            typingTimer = setTimeout(function() {

                var startDate = $('#day-start').val();
                var endDate = $('#day-end').val();
                
                // Ensure both dates are selected
                if ($('#time-range').val() == 'day') {
                    if (startDate && endDate) {
                        // Convert the input date strings to Date objects
                        var start = new Date(startDate);
                        var end = new Date(endDate);
                        
                        // Calculate the difference in time (milliseconds)
                        var timeDiff = end - start;
                        
                        // Calculate the difference in days
                        var diffDays = timeDiff / (1000 * 3600 * 24);
                        
                        // Check if the difference exceeds 40 days
                        if (diffDays > 365) {
                            // Show error message if the difference is more than 40 days
                            // $('#errorMessage').show();
                            Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "Your selected date range is too large. Please choose a range with no more than 31 dates.",
                            // footer: '<a href="#">Why do I have this issue?</a>'
                        });

                        } else {
                            // Hide the error message if the difference is within 40 days
                            // $('#errorMessage').hide();

                            update_chart();
                        }
                    }
                } else {
                    update_chart();
                }
    
            }, doneTypingInterval); // Set the timer to wait for the specified interval
        });
        

        $('#filter').on('change', function() {
            console.log( this.value);
            var name = this.value;
            var currentOption = myChart.getOption();

            $.each(currentOption.legend[0].data, function(index, item) {
                //  params.selected[index] = { path: item, selected: false };
                state = false; 
                if (name == '' || name == item) { state = true; }
                currentOption.legend[0].selected[item] = state;
            });

            myChart.setOption(currentOption);

            update_key_metrics();

        });


        // Listen for the legend selection change
        myChart.on('legendselectchanged', function (params) {
            console.log('Legend selection changed:', params);

            // Example action: log the selected legend items
            console.log('Selected Legend:', params.name);
            console.log('Selected Legend:', params.selected);

            var currentOption = myChart.getOption();

            $.each(params.selected, function(index, item) {
                //  params.selected[index] = { path: item, selected: false };
                state = false; 
                if (index == params.name) {state = true; }
                currentOption.legend[0].selected[index] = state;
            });

            $('#filter').val(params.name);

            // Apply the updated option
            myChart.setOption(currentOption);

            update_key_metrics();

        });

        function update_key_metrics() {

            var formData = {
                key_metrics_only : true,
                time_range : $("select[name='time-range']").val(),
                year :  $("select[name='year']").val(),
                filter : $("select[name='filter']").val(),
                day_start : $("input[name='day-start']").val(),
                day_end : $("input[name='day-end']").val(),
            };

            $.ajax({
                url: '{!! route("system.page-visits-overview") !!}',
                method: 'GET',
                data: formData,
                success: function(response) {
               
                    data = JSON.parse( response );

                    $('#visit_total_count').html(data.visit_total_count);
                    $('#visit_no_of_category').html(data.visit_no_of_category);
                    $('#visit_average').html(data.visit_average);

                },
                error: function(xhr) {
                    console.log(xhr.responseJSON.message);
                }
            });
        }


        function update_chart() {

            var formData = {
                time_range : $("select[name='time-range']").val(),
                year :  $("select[name='year']").val(),
                day_start : $("input[name='day-start']").val(),
                day_end : $("input[name='day-end']").val(),
            };

            // Show loading....
            myChart.showLoading({ 
                text: 'Loading data...', 
                maskColor: 'rgba(255, 255, 255, 0.5)',
                fontSize: 20,
                fontWeight: 'normal',
                spinnerRadius: 15,
            }); 

            $.ajax({
                url: '{!! route("system.page-visits-overview") !!}',
                method: 'GET',
                data: formData,
                success: function(response) {
               
                    data = JSON.parse( response );

                    // Assuming response is a JSON object with xData and yData arrays
                    var x_data = data.x_axis_data; // count per date 
                    var y_data = data.y_axis_data; // page (Legend)
                    var series_data = data.series_data; // The series data
                    var data_zoom = data.data_zoom; // The series data

                    $('#visit_total_count').html(data.visit_total_count);
                    $('#visit_no_of_category').html(data.visit_no_of_category);
                    $('#visit_average').html(data.visit_average);

                    // Update the chart with new data
                    var currentOption = myChart.getOption();
                    currentOption.legend[0].data = y_data;
                    currentOption.dataZoom = data_zoom;
                    currentOption.xAxis[0].data = x_data;
                    currentOption.series = series_data;

                    // filter = $("select[name='filter']").val();
                    $.each(currentOption.legend[0].data, function(index, item) {
                        state = true; 
                        currentOption.legend[0].selected[item] = state;
                    });
                    myChart.setOption(currentOption);
                    
                    // Reset filter
                    $('#filter').val('');

                    myChart.hideLoading(); // Hide loading indicator after success

                },
                error: function(xhr) {
                    // console.error("Error fetching data: ", error);
                    // alert("There was an error fetching the new data.");

                    let errorMessage = "An unknown error occurred.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: errorMessage,
                        // footer: '<a href="#">Why do I have this issue?</a>'
                    });
                    myChart.hideLoading(); // Hide loading indicator 

                }
            });

        }
        

    });

</script>
@endpush
