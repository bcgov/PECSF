<?php

namespace App\Http\Controllers\System;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Models\visitsMonitoring;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\TransactionTimesMonitoring;

class UserActivityController extends Controller
{
     /**
     * create a new instance of the class
     *
     * @return void
     */
    function __construct()
    {
         $this->middleware('permission:setting');
    }

    /**
     * Display a listing of the resource.
    */
    public function page_visits_overview(Request $request)
    {

        $validator = Validator::make(request()->all(), [
            'day_start'    => 'date|before_or_equal:day_end',
        ],[
            'day_start.before_or_equal' => 'The start date cannot be later than the end date. Please check your date range.',
        ]);
        //run validation which will redirect on failure
        $validator->validate();

        // Statistic Calculation Process
        $time_range = $request->has('time_range') ? $request->time_range : 'year';

        $start_date = today()->subDays(6);
        $end_date = today();

        $row_limit = 30;

        // For charting
        $x_axis_data = [];
        $y_axis_data = [];
        $series_data = [];
        $data_zoom = [];
        $visit_total_count = 0;
        $visit_average = 0;
        $visit_no_of_category = 0;
        $visits = collect([]);

        // Base SQL
        $base_sql = VisitsMonitoring::whereNot(function ($query) {
                    $query->whereRaw('LOWER(page) like ?', ['%administration%/'])
                        ->orWhereRaw('LOWER(page) like ?', ['%administrators/%'])
                        ->orWhereRaw('LOWER(page) like ?', ['%reporting/%'])
                        ->orWhereRaw('LOWER(page) like ?', ['%thank-you%'])
                        ->orWhereRaw('LOWER(page) like ?', ['%admin-%'])
                        ->orWhereRaw('LOWER(page) like ?', ['%login%'])
                        ->orWhereRaw('LOWER(page) like ?', ['%logout%'])
                        ->orWhereRaw('LOWER(page) like ?', ['%system/%'])
                        ->orWhereRaw('LOWER(page) like ?', ['%settings/%'])
                        ->orWhereRaw('LOWER(page) like ?', ['%user-monitoring/%'])
                        ->orWhereRaw('LOWER(page) like ?', ['%system/%']);
                    });

        switch ($time_range) {
            case 'year':   // By Annual 

                $sql = clone $base_sql;
                $visits = $sql->selectRaw('year(created_at) as category,
                                            min(DATE(created_at)) as begin_date,
                                            max(DATE(created_at)) as end_date,
                                            page, count(*) as count')
                                ->when( $request->filter, function ($q) use($request) {
                                    $q->where('page', 'like', '%'.$request->filter.'%');
                                })
                                ->groupBy('category', 'page')
                                ->orderBy('category', 'asc')
                                ->get();

                $y_axis_data = $visits->sortBy('page')->pluck('page')->unique()->values();      

                $sql2 = clone $base_sql;
                $min_max_dates = $sql2->selectRaw("min(DATE(created_at)) as begin_date,  max(date(created_at)) as end_date")->first();
                $begin = $min_max_dates->begin_date; 
                $end = $min_max_dates->end_date;

                $years = $this->getYearsInRange($begin, $end);

                $categories = $years;
                $x_axis_data = $years;

                break;
            case 'month':     // By months

                $sql = clone $base_sql;
                $visits = $sql->selectRaw('CONCAT(year(created_at),\'-\',month(DATE(created_at))) as category,
                                min(DATE(created_at)) as begin_date,
                                max(DATE(created_at)) as end_date,
                                page, count(*) as count')
                            ->when( $request->year, function ($q) use($request) {
                                    return $q->whereBetween('created_at', [ $request->year . '-01-01', $request->year +1 . '-01-01'] );
                                })
                            ->when( $request->filter, function ($q) use($request) {
                                $q->where('page', 'like', '%'.$request->filter.'%');
                            })
                            ->groupBy('category', 'page')
                            ->orderBy('category', 'asc')
                            ->get();

                $y_axis_data = $visits->sortBy('page')->pluck('page')->unique()->values();      

                $sql2 = clone $base_sql;
                $min_max_dates = $sql2->selectRaw("min(DATE(created_at)) as begin_date,  max(date(created_at)) as end_date")->first();
                $begin = $min_max_dates->begin_date; 
                $end = $min_max_dates->end_date;
                // $begin = $visits->min('begin_date'); 
                // $end = $visits->max('end_date');
                
                $months = $this->getMonthsInRange($begin, $end);
                $categories = array_column($months, 'month');
                $x_axis_data = array_column($months, 'title');
 
                break;
            case 'week':

                $sql = clone $base_sql;
                $visits = $sql->selectRaw('CONCAT(year(created_at),\'-\',week(DATE(created_at))) as category,
                                    min(DATE(created_at)) as begin_date,
                                    max(DATE(created_at)) as end_date,
                                    page, count(*) as count')
                                ->when( $request->year, function ($q) use($request) {
                                    return $q->whereBetween('created_at', [ $request->year . '-01-01', $request->year +1 . '-01-01'] );
                                })
                                ->when( $request->filter, function ($q) use($request) {
                                    $q->where('page', 'like', '%'.$request->filter.'%');
                                })
                                ->groupBy('category', 'page')
                                ->orderBy('category', 'asc')
                                ->get();

                $y_axis_data = $visits->sortBy('page')->pluck('page')->unique()->values();      
                    
                // $begin = $visits->min('week_number'); 
                // $end = $visits->max('week_number');

                $sql2 = clone $base_sql;
                $min_max_dates = $sql2->selectRaw("min(DATE(created_at)) as begin_date,  max(date(created_at)) as end_date")->first();
                $begin = $min_max_dates->begin_date; 
                $end = $min_max_dates->end_date;                
                // $begin = $visits->min('begin_date'); 
                // $end = $visits->max('end_date');

                $weeks = $this->getWeeksInRange($begin, $end);
                $categories = array_column($weeks, 'week');
                $x_axis_data = array_column($weeks, 'title');

                break;

            default:
            // day
                $row_limit = 50;

                $start_date = $request->day_start ? Carbon::create($request->day_start) : $start_date;
                $end_date   = $request->day_end ? Carbon::create($request->day_end) : $end_date;

                $sql = clone $base_sql;
                $visits = $sql->selectRaw('DATE(created_at) as category, page, COUNT(*) as count')
                    ->whereRaw("created_at between '" . $start_date . "' and ADDDATE('" . $end_date . "', INTERVAL 1 DAY)")
                    ->when( $request->filter, function ($q) use($request) {
                        $q->where('page', 'like', '%'.$request->filter.'%');
                    })
                    ->groupBy('category', 'page')
                    ->orderBy('category', 'asc')
                    ->get();

                $y_axis_data = $visits->sortBy('page')->pluck('page')->unique()->values();      

                $interval = '1 day';
                $period   = new CarbonPeriod($start_date, $interval, $end_date);
                $period_in_date = [];
                foreach ($period as $date) {
                    $period_in_date[] = $date->format('Y-m-d');
                }

                $categories = $period_in_date;
                $x_axis_data = $period_in_date; // $visits->pluck('date')->unique()->values();          // x-Axis

        }

        $visit_total_count = $visits->sum('count');
        $visit_average = round($visit_total_count / count($categories),2);
        $visit_no_of_category = count($categories);   // count($y_axis_data);

        // Update Key Metrics Only
        if ($request->has('key_metrics_only') && $request->key_metrics_only) {

            $data = [
                'visit_total_count' => $visit_total_count,
                'visit_average' => $visit_average,
                'visit_no_of_category' => $visit_no_of_category,
            ]; 

            return json_encode($data);
        }



        // Prepare data structure for ECharts
        $series_data = [];

        foreach ($y_axis_data as $page) {
            $dataPoints = [];
            foreach ($categories as $category ) {
                // Find the matching record for this date and page
                $visit = $visits->firstWhere(fn ($v) => $v->category == $category  && $v->page == $page);
                $dataPoints[] = $visit ? $visit->count : 0;
            }

            $parsedUrl = parse_url($page); 

            $series_data[] = [
                'name' => isset($parsedUrl['path']) ? $parsedUrl['path'] : '/',
                'type' => 'bar',  // 'line',
                // 'barWidth' => 'null',
                'stack' => 'Total',
                'smooth' => true,
                'data' => $dataPoints,
            ];


            $data_zoom = [];

            if (count($x_axis_data) > $row_limit) {
                $data_zoom = [
                    [
                        'show' => true,
                        'realtime' => true,
                        'start' => 70,
                        'end' => 100,
                        'xAxisIndex' => [0, 1],
                    ],
                    [
                        'type' => 'inside',
                        'realtime' => true,
                        'start'=> 70,
                        'end' => 100,
                        'xAxisIndex' => [0, 1],
                    ],
                ];
            }

        }

        if($request->ajax()) {
            
            $data = [
                'x_axis_data' => $x_axis_data,
                'y_axis_data' => $y_axis_data,
                'series_data' => $series_data,
                'data_zoom' => $data_zoom,

                'visit_total_count' => $visit_total_count,
                'visit_average' => $visit_average,
                'visit_no_of_category' => $visit_no_of_category,
            ]; 

            return json_encode($data);
        }

        // default and options
        $cloned_sql = clone $base_sql;
        $category_options = $cloned_sql->selectRaw('distinct page')
                                ->orderBy('page')
                                ->pluck('page');

        $cloned_sql = clone $base_sql;
        $years = $cloned_sql->selectRaw("distinct YEAR(created_at) as year")
                                ->orderBy('created_at', 'desc')
                                ->pluck('year');
        $years = range(today()->year, 2024);


        $week_date = today()->format('Y-m-d');
        $day_start = $start_date->format('Y-m-d');
        $day_end = $end_date->format('Y-m-d');

        return view('system-security.user-activity.page_visits_overview', compact(
            'time_range',  'years', 'week_date', 'day_start', 'day_end', 'category_options',
            'x_axis_data', 'y_axis_data', 'series_data', 'data_zoom',      
            'visit_total_count', 'visit_average', 'visit_no_of_category',
        ));
    }

    /**
     * Display a listing of the resource.
     */
    public function transaction_timings(Request $request)
    {

        $validator = Validator::make(request()->all(), [
            'day_start'    => 'date|before_or_equal:day_end',
        ],[
            'day_start.before_or_equal' => 'The start date cannot be later than the end date. Please check your date range.',
        ]);
        //run validation which will redirect on failure
        $validator->validate();

        // Statistic Calculation Process
        $time_range = $request->has('time_range') ? $request->time_range : 'day';
        $filter = $request->has('filter') ? $request->filter : 'pledges';

        $start_date = today()->subDays(6);
        $end_date = today();

        $row_limit = 30;

        // For charting
        $x_axis_data = [];      // by year, month, week, custom 
        $y_axis_data = [];
        $series_data = [];
        $data_zoom = [];
        $visit_min = 0;
        $visit_avg = 0;
        $visit_max = 0;
        $visits = collect([]);

        // Base SQL
        $category_options = ['pledges', 'donate_now_pledges','special_campaign_pledges', 'bank_deposit_forms', 'volunteer_profiles'];

        $base_sql = TransactionTimesMonitoring::where(function ($query) use($category_options ) {
                        $query->whereIn('table_name', $category_options);
                    });

        switch ($time_range) {
            case 'year':   // By Annual 

                $sql = clone $base_sql;
                $visits = $sql->selectRaw('year(created_at) as category,
                                            min(DATE(created_at)) as begin_date,
                                            max(DATE(created_at)) as end_date,
                                            table_name,
                                            action_type,
                                            min(duration) as min_duration,
                                            max(duration) as max_duration,
                                            sum(duration) /count(*) as avg_duration')
                                ->when( $filter, function ($q) use($filter) {
                                    $q->where('table_name', $filter);
                                })
                                ->groupBy('category', 'table_name', 'action_type')
                                ->orderBy('category', 'asc')
                                ->get();
 
                $y_axis_data = $visits->sortBy('action_type')->pluck('action_type')->unique()->values();      

                $sql2 = clone $base_sql;
                $min_max_dates = $sql2->selectRaw("min(DATE(created_at)) as begin_date,  max(date(created_at)) as end_date")->first();
                $begin = $min_max_dates->begin_date; 
                $end = $min_max_dates->end_date;

                $years = $this->getYearsInRange($begin, $end);

                $categories = $years;
                $x_axis_data = $years;

                break;
            case 'month':     // By months

                $sql = clone $base_sql;
                $visits = $sql->selectRaw('CONCAT(year(created_at),\'-\',month(DATE(created_at))) as category,
                                            min(DATE(created_at)) as begin_date,
                                            max(DATE(created_at)) as end_date,
                                            table_name,
                                            action_type,
                                            min(duration) as min_duration,
                                            max(duration) as max_duration,
                                            sum(duration) /count(*) as avg_duration')
                            ->when( $request->year, function ($q) use($request) {
                                    return $q->whereBetween('created_at', [ $request->year . '-01-01', $request->year +1 . '-01-01'] );
                                })
                            ->when( $filter, function ($q) use($filter) {
                                $q->where('table_name', $filter);
                            })
                            ->groupBy('category', 'table_name', 'action_type')
                            ->orderBy('category', 'asc')
                            ->get();

                $y_axis_data = $visits->sortBy('action_type')->pluck('action_type')->unique()->values();      

                $sql2 = clone $base_sql;
                $min_max_dates = $sql2->selectRaw("min(DATE(created_at)) as begin_date,  max(date(created_at)) as end_date")->first();
                $begin = $min_max_dates->begin_date; 
                $end = $min_max_dates->end_date;
                // $begin = $visits->min('begin_date'); 
                // $end = $visits->max('end_date');
                
                $months = $this->getMonthsInRange($begin, $end);
                $categories = array_column($months, 'month');
                $x_axis_data = array_column($months, 'title');
                // dd( [$begin, $end, $months , $x_axis_data, $visits ]);    
                break;
            case 'week':

                $sql = clone $base_sql;
                $visits = $sql->selectRaw('CONCAT(year(created_at),\'-\',week(DATE(created_at))) as category,
                                    min(DATE(created_at)) as begin_date,
                                    max(DATE(created_at)) as end_date,
                                    table_name,
                                    action_type,
                                    min(duration) as min_duration,
                                    max(duration) as max_duration,
                                    sum(duration) /count(*) as avg_duration')
                                ->when( $request->year, function ($q) use($request) {
                                    return $q->whereBetween('created_at', [ $request->year . '-01-01', $request->year +1 . '-01-01'] );
                                })
                                ->when( $filter, function ($q) use($filter) {
                                    $q->where('table_name', $filter);
                                })
                                ->groupBy('category', 'table_name', 'action_type')
                                ->orderBy('category', 'asc')
                                ->get();

                $y_axis_data = $visits->sortBy('action_type')->pluck('action_type')->unique()->values();      
                    
                // $begin = $visits->min('week_number'); 
                // $end = $visits->max('week_number');

                $sql2 = clone $base_sql;
                $min_max_dates = $sql2->selectRaw("min(DATE(created_at)) as begin_date,  max(date(created_at)) as end_date")->first();
                $begin = $min_max_dates->begin_date; 
                $end = $min_max_dates->end_date;                
                // $begin = $visits->min('begin_date'); 
                // $end = $visits->max('end_date');

                $weeks = $this->getWeeksInRange($begin, $end);
                $categories = array_column($weeks, 'week');
                $x_axis_data = array_column($weeks, 'title');
                // dd( [$begin, $end, $weeks , $x_axis_data, $visits ]);

                break;

            default:
                // day
                $row_limit = 50;

                $start_date = $request->day_start ? Carbon::create($request->day_start) : $start_date;
                $end_date   = $request->day_end ? Carbon::create($request->day_end) : $end_date;

                $sql = clone $base_sql;
                $visits = $sql->selectRaw('DATE(created_at) as category,
                                            table_name,
                                            action_type,
                                            min(duration) as min_duration,
                                            max(duration) as max_duration,
                                            sum(duration) /count(*) as avg_duration')
                    ->whereRaw("created_at between '" . $start_date . "' and ADDDATE('" . $end_date . "', INTERVAL 1 DAY)")
                    ->when( $filter, function ($q) use($filter) {
                        $q->where('table_name', $filter);
                    })
                    ->groupBy('category', 'table_name', 'action_type')
                    ->orderBy('category', 'asc')
                    ->get();

                $y_axis_data = $visits->sortBy('action_type')->pluck('action_type')->unique()->values();      

                $interval = '1 day';
                $period   = new CarbonPeriod($start_date, $interval, $end_date);
                $period_in_date = [];
                foreach ($period as $date) {
                    $period_in_date[] = $date->format('Y-m-d');
                }

                $categories = $period_in_date;
                $x_axis_data = $period_in_date; // $visits->pluck('date')->unique()->values();          // x-Axis

        }

        // For key matrix
        $action_type = $request->has('action_type') ? $request->action_type  : ($y_axis_data ? $y_axis_data[0] : 'create') ;

        $visit_min = round($visits->where('action_type', $action_type)->min('min_duration'),2);
        $visit_avg = $visits->where('action_type', $action_type)->count() > 0 ? 
                                round( $visits->where('action_type', $action_type)->sum('avg_duration') / $visits->where('action_type', $action_type)->count(),2) 
                                : 0;
        $visit_max = round($visits->where('action_type', $action_type)->max('max_duration'),2);  

        // Update Key Metrics Only
        if ($request->has('key_metrics_only') && $request->key_metrics_only) {

            $data = [
                'visit_min' => $visit_min,
                'visit_avg' => $visit_avg,
                'visit_max' => $visit_max,
            ]; 

            return json_encode($data);
        }

        // Prepare data structure for ECharts
        $series_data = [];

        foreach ($y_axis_data as $action_type ) {
            $dataPoints_min = [];
            $dataPoints_avg = [];
            $dataPoints_max = [];
            foreach ($categories as $category ) {
                // Find the matching record for this date and action_type
                $visit = $visits->firstWhere(fn ($v) => $v->category == $category  && $v->action_type == $action_type);
                $dataPoints_min[] = $visit ? round($visit->min_duration,2) : 0;
                $dataPoints_avg[] = $visit ? round($visit->avg_duration,2) : 0;
                $dataPoints_max[] = $visit ? round($visit->max_duration,2) : 0;
            }


            $labelOption = [
                'show' => true,
                'position' => 'insideBottom',
                'distance' => 50,
                'align' => 'left',
                'verticalAlign' => 'middle',
                'formatter' => 'min {c}' ,
                'rotate' => 90,
                'fontSize' => 16,
            ];


            // $parsedUrl = parse_url($page); 
            $labelOption['formatter'] = 'min {c}';
            $series_data[] = [
                'name' => $action_type,
                'type' => 'bar',
                'barGap' => 0,
                'label' => $labelOption,
                'emphasis' => [
                    'focus' => 'series'
                ],
                'data' => $dataPoints_min,
            ];

            $labelOption['formatter'] = 'avg {c}';
            $series_data[] = [
                'name' => $action_type,
                'type' => 'bar',
                'barGap' => 0,
                'label' => $labelOption,
                'emphasis' => [
                    'focus' => 'series'
                ],
                'data' => $dataPoints_avg,
            ];

            $labelOption['formatter'] = 'max {c}';
            $series_data[] = [
                'name' => $action_type,
                'type' => 'bar',
                'barGap' => 0,
                'label' => $labelOption,
                'emphasis' => [
                    'focus' => 'series'
                ],
                'data' => $dataPoints_max,
            ];

            // $series_data[] = [
            //     'name' => isset($parsedUrl['path']) ? $parsedUrl['path'] : '/',
            //     'type' => 'bar',  // 'line',
            //     // 'barWidth' => 'null',
            //     'stack' => 'Total',
            //     'smooth' => true,
            //     'data' => $dataPoints,
            // ];


            $data_zoom = [];

            if (count($x_axis_data) > $row_limit) {
                $data_zoom = [
                    [
                        'show' => true,
                        'realtime' => true,
                        'start' => 70,
                        'end' => 100,
                        'xAxisIndex' => [0, 1],
                    ],
                    [
                        'type' => 'inside',
                        'realtime' => true,
                        'start'=> 70,
                        'end' => 100,
                        'xAxisIndex' => [0, 1],
                    ],
                ];
            }

        }

        if($request->ajax()) {
            
            $data = [
                'x_axis_data' => $x_axis_data,
                'y_axis_data' => $y_axis_data,
                'series_data' => $series_data,
                'data_zoom' => $data_zoom,

                'visit_min' => $visit_min,
                'visit_avg' => $visit_avg,
                'visit_max' => $visit_max,

            ]; 

            return json_encode($data);
        }

        // default and options
        // $cloned_sql = clone $base_sql;
        // $category_options = $cloned_sql->selectRaw('distinct page')
        //                         ->orderBy('page')
        //                         ->pluck('page');

        $cloned_sql = clone $base_sql;
        $years = $cloned_sql->selectRaw("distinct YEAR(created_at) as year")
                                ->orderBy('created_at', 'desc')
                                ->pluck('year');
        $years = range(today()->year, 2024);


        $week_date = today()->format('Y-m-d');
        $day_start = $start_date->format('Y-m-d');
        $day_end = $end_date->format('Y-m-d');

        return view('system-security.user-activity.transaction_timings', compact(
            'time_range',  'years', 'week_date', 'day_start', 'day_end', 'category_options', 
            'x_axis_data', 'y_axis_data', 'series_data', 'data_zoom',      
            'visit_min', 'visit_avg', 'visit_max',
        ));

        


    }


    public function transaction_counts_overview(Request $request)
    {

        $validator = Validator::make(request()->all(), [
            'day_start'    => 'date|before_or_equal:day_end',
        ],[
            'day_start.before_or_equal' => 'The start date cannot be later than the end date. Please check your date range.',
        ]);
        //run validation which will redirect on failure
        $validator->validate();

        // Statistic Calculation Process
        $time_range = $request->has('time_range') ? $request->time_range : 'day';
        $table_name = $request->has('table_name') ? $request->table_name : 'pledges';
        $filter = $request->has('filter') ? $request->filter : null;

        $start_date = today()->subDays(6);
        $end_date = today();

        $row_limit = 30;

        // For charting
        $x_axis_data = [];
        $y_axis_data = [];
        $series_data = [];
        $data_zoom = [];
        $visit_total_count = 0;
        $visit_average = 0;
        $visit_no_of_category = 0;
        $visits = collect([]);

        // Base SQL
        $table_names = ['pledges', 'donate_now_pledges','special_campaign_pledges', 'bank_deposit_forms', 'volunteer_profiles'];

        // Base SQL
        $base_sql = TransactionTimesMonitoring::where(function ($query) use($table_names) {
            $query->whereIn('table_name', $table_names);
        });

        switch ($time_range) {
            case 'year':   // By Annual 

                $sql = clone $base_sql;
                $visits = $sql->selectRaw('year(created_at) as category,
                                            min(DATE(created_at)) as begin_date,
                                            max(DATE(created_at)) as end_date,
                                            action_type, count(*) as count')
                                ->when( $table_name, function ($q) use($table_name) {
                                    $q->where('table_name', $table_name);
                                })
                                ->when( $filter, function ($q) use($filter) {
                                    $q->where('action_type', $filter);
                                })
                                ->groupBy('category', 'action_type')
                                ->orderBy('category', 'asc')
                                ->get();

                $y_axis_data = $visits->sortBy('action_type')->pluck('action_type')->unique()->values();      

                $sql2 = clone $base_sql;
                $min_max_dates = $sql2->selectRaw("min(DATE(created_at)) as begin_date,  max(date(created_at)) as end_date")
                                    ->when( $table_name, function ($q) use($table_name) {
                                        $q->where('table_name', $table_name);
                                    })
                                    ->when( $filter, function ($q) use($filter) {
                                        $q->where('action_type', $filter);
                                    })
                                    ->first();
                $begin = $min_max_dates->begin_date; 
                $end = $min_max_dates->end_date;

                $years = $this->getYearsInRange($begin, $end);

                $categories = $years;
                $x_axis_data = $years;

                break;
            case 'month':     // By months

                $sql = clone $base_sql;
                $visits = $sql->selectRaw('CONCAT(year(created_at),\'-\',month(DATE(created_at))) as category,
                                min(DATE(created_at)) as begin_date,
                                max(DATE(created_at)) as end_date,
                                action_type, count(*) as count')
                            ->when( $request->year, function ($q) use($request) {
                                    return $q->whereBetween('created_at', [ $request->year . '-01-01', $request->year +1 . '-01-01'] );
                                })
                            ->when( $table_name, function ($q) use($table_name) {
                                $q->where('table_name', $table_name);
                            })
                            ->when( $filter, function ($q) use($filter) {
                                    $q->where('action_type', $filter);
                                })
                            ->groupBy('category', 'action_type')
                            ->orderBy('category', 'asc')
                            ->get();

                $y_axis_data = $visits->sortBy('action_type')->pluck('action_type')->unique()->values();      

                $sql2 = clone $base_sql;
                $min_max_dates = $sql2->selectRaw("min(DATE(created_at)) as begin_date,  max(date(created_at)) as end_date")
                                            ->when( $table_name, function ($q) use($table_name) {
                                                $q->where('table_name', $table_name);
                                            })
                                            ->when( $filter, function ($q) use($filter) {
                                                $q->where('action_type', $filter);
                                            })
                                            ->first();
                $begin = $min_max_dates->begin_date; 
                $end = $min_max_dates->end_date;
                // $begin = $visits->min('begin_date'); 
                // $end = $visits->max('end_date');
                
                $months = $this->getMonthsInRange($begin, $end);
                $categories = array_column($months, 'month');
                $x_axis_data = array_column($months, 'title');
 
                break;
            case 'week':

                $sql = clone $base_sql;
                $visits = $sql->selectRaw('CONCAT(year(created_at),\'-\',week(DATE(created_at))) as category,
                                    min(DATE(created_at)) as begin_date,
                                    max(DATE(created_at)) as end_date,
                                    action_type, count(*) as count')
                                ->when( $request->year, function ($q) use($request) {
                                    return $q->whereBetween('created_at', [ $request->year . '-01-01', $request->year +1 . '-01-01'] );
                                })
                                ->when( $table_name, function ($q) use($table_name) {
                                    $q->where('table_name', $table_name);
                                })
                                ->when( $filter, function ($q) use($filter) {
                                    $q->where('taction_type', $filter);
                                })
                                ->groupBy('category', 'action_type')
                                ->orderBy('category', 'asc')
                                ->get();

                $y_axis_data = $visits->sortBy('action_type')->pluck('action_type')->unique()->values();                          

                // $begin = $visits->min('week_number'); 
                // $end = $visits->max('week_number');

                $sql2 = clone $base_sql;
                $min_max_dates = $sql2->selectRaw("min(DATE(created_at)) as begin_date,  max(date(created_at)) as end_date")
                                            ->when( $table_name, function ($q) use($table_name) {
                                                $q->where('table_name', $table_name);
                                            })
                                            ->when( $filter, function ($q) use($filter) {
                                                $q->where('action_type', $filter);
                                            })
                                            ->first();
                $begin = $min_max_dates->begin_date; 
                $end = $min_max_dates->end_date;                
                // $begin = $visits->min('begin_date'); 
                // $end = $visits->max('end_date');

                $weeks = $this->getWeeksInRange($begin, $end);
                $categories = array_column($weeks, 'week');
                $x_axis_data = array_column($weeks, 'title');

                break;

            default:
                // day
                $row_limit = 50;

                $start_date = $request->day_start ? Carbon::create($request->day_start) : $start_date;
                $end_date   = $request->day_end ? Carbon::create($request->day_end) : $end_date;

                $sql = clone $base_sql;
                $visits = $sql->selectRaw('DATE(created_at) as category, action_type, COUNT(*) as count')
                                    ->whereRaw("created_at between '" . $start_date . "' and ADDDATE('" . $end_date . "', INTERVAL 1 DAY)")
                                    ->when( $table_name, function ($q) use($table_name) {
                                        $q->where('table_name', $table_name);
                                    })
                                    ->when( $filter, function ($q) use($filter) {
                                        $q->where('action_type', $filter);
                                    })
                                    ->groupBy('category', 'action_type')
                                    ->orderBy('category', 'asc')
                                    ->get();

                $y_axis_data = $visits->sortBy('action_type')->pluck('action_type')->unique()->values();                      

                $interval = '1 day';
                $period   = new CarbonPeriod($start_date, $interval, $end_date);
                $period_in_date = [];
                foreach ($period as $date) {
                    $period_in_date[] = $date->format('Y-m-d');
                }

                $categories = $period_in_date;
                $x_axis_data = $period_in_date; // $visits->pluck('date')->unique()->values();          // x-Axis

        }

        $visit_total_count = $visits->sum('count');
        $visit_average = round($visit_total_count / count($categories),2);
        $visit_no_of_category = count($categories);   // count($y_axis_data);

        // Update Key Metrics Only
        if ($request->has('key_metrics_only') && $request->key_metrics_only) {

            $data = [
                'visit_total_count' => $visit_total_count,
                'visit_average' => $visit_average,
                'visit_no_of_category' => $visit_no_of_category,
            ]; 

            return json_encode($data);
        }



        // Prepare data structure for ECharts
        $series_data = [];

        foreach ($y_axis_data as $action_type) {
            $dataPoints = [];
            foreach ($categories as $category ) {
                // Find the matching record for this date and page
                $visit = $visits->firstWhere(fn ($v) => $v->category == $category  && $v->action_type == $action_type);
                $dataPoints[] = $visit ? $visit->count : 0;
            }

            // $parsedUrl = parse_url($page); 

            $series_data[] = [
                'name' => $action_type ? $action_type : 'create',
                'type' => 'bar',  // 'line',
                // 'barWidth' => 'null',
                'stack' => 'Total',
                'smooth' => true,
                'data' => $dataPoints,
            ];


            $data_zoom = [];

            if (count($x_axis_data) > $row_limit) {
                $data_zoom = [
                    [
                        'show' => true,
                        'realtime' => true,
                        'start' => 70,
                        'end' => 100,
                        'xAxisIndex' => [0, 1],
                    ],
                    [
                        'type' => 'inside',
                        'realtime' => true,
                        'start'=> 70,
                        'end' => 100,
                        'xAxisIndex' => [0, 1],
                    ],
                ];
            }

        }

        if($request->ajax()) {
            
            $data = [
                'x_axis_data' => $x_axis_data,
                'y_axis_data' => $y_axis_data,
                'series_data' => $series_data,
                'data_zoom' => $data_zoom,

                'visit_total_count' => $visit_total_count,
                'visit_average' => $visit_average,
                'visit_no_of_category' => $visit_no_of_category,
            ]; 

            return json_encode($data);
        }

        // default and options
        $cloned_sql = clone $base_sql;
        $category_options = $cloned_sql->selectRaw('distinct action_type')
                                ->orderBy('action_type')
                                ->pluck('action_type');

        $cloned_sql = clone $base_sql;
        $years = $cloned_sql->selectRaw("distinct YEAR(created_at) as year")
                                ->orderBy('created_at', 'desc')
                                ->pluck('year');
        $years = range(today()->year, 2024);


        $week_date = today()->format('Y-m-d');
        $day_start = $start_date->format('Y-m-d');
        $day_end = $end_date->format('Y-m-d');

        return view('system-security.user-activity.transaction_counts_overview', compact(
            'time_range',  'years', 'week_date', 'day_start', 'day_end', 'category_options', 'table_names',
            'x_axis_data', 'y_axis_data', 'series_data', 'data_zoom',      
            'visit_total_count', 'visit_average', 'visit_no_of_category',
        ));
    }

    

    protected function getWeeksInRange($startDate, $endDate)
    {
        // Parse the start and end dates using Carbon
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
    
        // Adjust the start date to the previous Sunday (if not already Sunday)
        if ($start->dayOfWeek != Carbon::SUNDAY) {
            $start->startOfWeek(Carbon::SUNDAY);
        }

        // Initialize an empty array to hold the year and week numbers
        $weeks = [];
    
        // Loop through the range, incrementing by weeks
        while ($start <= $end) {
            // Get the year and week number
            // $weeks[] = [
            //     'year' => $start->year,
            //     'week' => $start->week,
            // ];
            $weeks[] = [
                'week'  => $start->year . '-' . $start->week,
                'title' => $start->format('Y M d') . PHP_EOL . $start->copy()->addDays(6)->format('M d'),
            ];
    
            // Move to the next week
            $start->addWeek();
        }
    
        return $weeks;
    }

    protected function getMonthsInRange($startDate, $endDate)
    {
        // Parse the start and end dates using Carbon
        $start = Carbon::parse($startDate)->startOfMonth();
        $end = Carbon::parse($endDate)->startOfMonth();

        // Initialize an empty array to hold the year and month numbers
        $months = [];

        // Loop through the range, incrementing by one month
        while ($start <= $end) {
            // Get the year and month
            $months[] = [
                'month' => $start->year . '-' . $start->month, 
                'title' => $start->format('M') . PHP_EOL . $start->year, // Full month name (e.g., January)
            ];

            // Move to the next month
            $start->addMonth();
        }

        return $months;
    }

    protected function getYearsInRange($startDate, $endDate)
    {
        // Parse the start and end dates using Carbon
        $start = Carbon::parse($startDate)->startOfMonth();
        $end = Carbon::parse($endDate)->startOfMonth();

        $years = [];

        // Loop through the range, incrementing by one year
        while ($start->year <= $end->year) {
            $years[] = $start->year;
    
            // Move to the next year
            $start->addYear();
        }
    
        return $years;
    }

}

