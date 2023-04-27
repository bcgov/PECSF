@php
    function ordinal($number) {
        $ends = array('th','st','nd','rd','th','th','th','th','th','th');
        if ((($number % 100) >= 11) && (($number%100) <= 13))
            return $number. 'th';
        else
            return $number. $ends[$number % 10];
    }
@endphp
@extends('adminlte::page')
@section('content_header')
<div class="mt-3">
<h1>Challenge</h1>
    <ul class="menu nav nav-pills" id="pills-tab">
        <li class="nav-item nav-center-4">
            <a style="text-align:center;" class="nav-link <?php echo (Route::current()->getName() == 'challege.index') ? 'active' : ''; ?>"
               href="<?php echo e(route('challege.index')); ?>" role="tab" aria-controls="pills-home" aria-selected="true">
                Leaderboard</a>
        </li>
        <li class="nav-item nav-center-4">
            <a style="text-align:center;" class="nav-link <?php echo e(str_contains( Route::current()->getName(), 'challege.daily_campaign') ? 'active' : ''); ?>"
               href="<?php echo e(route('challege.daily_campaign')); ?>"  aria-controls="pills-profile" aria-selected="false">Daily Campaign Update</a>
        </li>
    </ul>
<h6 class="mt-3">Visit this page daily during the PECSF campaign to see updated statistics, including organization participation rates!<br>
    If you have questions about PECSF statistics, send us an e-mail at <a href="mailto:PECSF@gov.bc.ca?subject=Challenge%20page">PECSF@gov.bc.ca</a>.</h6>
</div>
@endsection
@section('content')

<div class="card">
<div class="card-body">
    <div class="form-row">
        <div class="col-md-6">
            <label>
                Campaign Year
                <select name="year" id="year" style="min-width:250px;" class="form-control ">
                    @foreach($years as $annum)
                        <option {{$year==$annum?"selected":""}} value="{{$annum}}">{{$annum}}</option>
                    @endforeach
                </select>
            </label>
            <label class="pl-2" style="min-width:250px;">
                Organization Name
                <input type="text" id="organization_name" value="{{$request->organization_name}}" name="organization_name" class="form-control " />
            </label>
        </div>
        <div class="d-flex col-md-6">

        </div>
    </div>
    <br>

    @if(!empty($totals[0]))
    <table class="table table-bordered rounded" id="myTable3">
        <thead>
        <tr class="bg-light">
            <th>Date</th>
            <th>Donors</th>
            <th>Dollars</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{{$date}}</td>
            <td>{{$totals[0]->donors}}</td>
            <td>${{number_format($totals[0]->dollars,2)}}</td>
        </tr>
        </tbody>
    </table>
    @else
        <p>We could not calculate totals for the currently selected active year ({{$year}})</p>
    @endif

    <br>
<table class="table table-bordered rounded" id="myTable2">
    <thead>
    <tr class="bg-light">
        <th onclick="sortTable('participation_rate')" style="cursor: pointer;">Rank <img style="width:16px;height:16px;" class="sort-hook float-right" src="@php echo  ($request->sort == "ASC") ? asset("img/icons/FilterDescending.png"):asset("img/icons/FilterAscending.png") @endphp" /></th>
        <th onclick="sortTable('name')" style="cursor: pointer;">Organization name <img style="width:16px;height:16px;" class="sort-hook float-right" src="@php echo  ($request->sort == "ASC") ? asset("img/icons/FilterDescending.png"):asset("img/icons/FilterAscending.png") @endphp" /></th>
        <th onclick="sortTable('participation_rate')" style="cursor: pointer;">Participation rate <img style="width:16px;height:16px;" class="sort-hook float-right" src="@php echo  ($request->sort == "ASC") ? asset("img/icons/FilterDescending.png"):asset("img/icons/FilterAscending.png") @endphp" /></th>
        <th onclick="sortTable('previous_participation_rate')" style="cursor: pointer;">Previous rate <img style="width:16px;height:16px;" class="sort-hook float-right" src="@php echo  ($request->sort == "ASC") ? asset("img/icons/FilterDescending.png"):asset("img/icons/FilterAscending.png") @endphp" /></th>
        <th onclick="sortTable('change')" style="cursor: pointer;">Change <img style="width:16px;height:16px;" class="sort-hook float-right" src="@php echo  ($request->sort == "ASC") ? asset("img/icons/FilterDescending.png"): asset("img/icons/FilterAscending.png") @endphp" /></th>
        <th onclick="sortTable('donors')" style="cursor: pointer;">Donors <img style="width:16px;height:16px;" class="sort-hook float-right" src="@php echo  ($request->sort == "ASC") ? asset("img/icons/FilterDescending.png"): asset("img/icons/FilterAscending.png") @endphp" /></th>
        <th onclick="sortTable('dollars')" style="cursor: pointer;">Dollars <img style="width:16px;height:16px;" class="sort-hook float-right" src="@php echo  ($request->sort == "ASC") ? asset("img/icons/FilterDescending.png"): asset("img/icons/FilterAscending.png") @endphp" /></th>
    </tr>
    </thead>


<tbody id="charities">
@foreach($charities as $index => $charity)
<tr>
<td>{{ordinal($count)}}</td>
@php
                        if($request->sort == "ASC"){
                            $count--;
                        }
                        else{
                            $count++;
                        }
                    @endphp
                    <td>{{$charity['organization_name']}}</td>
                    <td>{{ number_format($charity['participation_rate'] ? $charity['participation_rate'] : ($charity->participation_rate ? $charity->participation_rate : 0 ),2)}}%</td>
                    <td>{{number_format($charity['previous_participation_rate'] ? $charity['previous_participation_rate'] : 0,2)}}%</td>
                    <td>{{number_format($charity['change'] ? $charity['change'] : 0,2)}}%</td>
                    <td>{{$charity['donors']}}</td>
                    <td>${{number_format(floatval(str_replace("$","",$charity['dollars'])),2)}}</td>
                </tr>
            @endforeach
</tbody>
        </table>
<br>
    </div>
</div>
<br>
<br>
@endsection
@push('js')
<script>
    var year = '{{ $request->year ? $request->year : "2021" }}';
    var orgNameTimer;

    $("#organization_name").keyup(function(){
        clearTimeout(orgNameTimer);
        orgNameTimer = setTimeout(orgFilter,800);
    });

    function orgFilter(){
        window.location = "/challenge?year="+$('#year').val()+"&organization_name="+$("#organization_name").val();
    }

    $("#sort,#start_date").change(function(){
        $.ajax({
            method: "GET",
            url:  '/challenge/preview?sort=' + $("#sort").val() + '&start_date=' + new Date($("#start_date").val()).getFullYear(),
            success: function(data)
            {
                $("#preview").html(data);
            },
            error: function(data) {
                $("#preview").html(data);
            }
        });
    });

    $("select[name='year']").change(function(){
        if($('#year').val() > 2021)
        {
            window.location = "/challenge/currentyear?year="+$('#year').val()+"&organization_name="+$("#organization_name").val();
        }
        else{
            window.location = "/challenge?year="+$('#year').val()+"&organization_name="+$("#organization_name").val();
        }
    });

    var new_sort = '{{ $request->sort == "ASC" ? "DESC" : "ASC" }}';

    function sortTable(field='participation_rate')
    {
        @if($year > 2021)
            window.location = "/challenge/currentyear?field="+field+"&sort="+new_sort+"&year="+year+"&organization_name="+$("#organization_name").val();
        $(".sort-hook").attr("src",);
            @else
                window.location = "/challenge?field="+field+"&sort="+new_sort+"&year="+year+"&organization_name="+$("#organization_name").val();
        $(".sort-hook").attr("src",);
            @endif

    }
</script>
@endpush
