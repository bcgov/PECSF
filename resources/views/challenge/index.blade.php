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
    <ul class="nav nav-pills mb-3" id="pills-tab" >
        <li class="nav-item col-md-3">
            <a style="text-align:center;" class="nav-link <?php echo (Route::current()->getName() == 'challege.index') ? 'active' : ''; ?>"
               href="<?php echo e(route('challege.index')); ?>" role="tab" aria-controls="pills-home" aria-selected="true">
                Leaderboard</a>
        </li>

        <li class="nav-item col-md-3">
            <a style="text-align:center;" class="nav-link <?php echo e(str_contains( Route::current()->getName(), 'challege.daily_campaign') ? 'active' : ''); ?>"
               href="<?php echo e(route('challege.daily_campaign')); ?>"  aria-controls="pills-profile" aria-selected="false">Daily Campaign Update</a>
        </li>


    </ul>
<h5 class="mt-3">Visit this page daily during the PECSF campaign to see updated statistics, including organization participation rates!<br>
    View and download daily statistics updates below.<br>
    If you have questions about PECSF statistics, send us an e-mail at <a href="mailto:PECSF@gov.bc.ca?subject=Challenge%20page">PECSF@gov.bc.ca</a>.</h5>
</div>
@endsection
@section('content')

<div class="card">
<div class="card-body">
    <div class="form-row">
        <div class="col-md-4">
            <label>
                Campaign Year
                <select name="year" id="year" style="min-width: 380px" class="form-control ">
                    <option {{$year==2021?"selected":""}} value="2021">2021</option>
                    <option {{$year==2020?"selected":""}} value="2020">2020</option>
                </select>
            </label>
        </div>
        <div class="col-md-4">
            <label style="min-width: 380px">
                Organization Name
                <input type="text" id="organization_name" value="{{$request->organization_name}}" name="organization_name" class="form-control " />
            </label>
        </div>
        <div class="d-flex col-md-4">

        </div>
    </div>
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
                    <td>{{$charity['name']}}</td>
                    <td>{{is_numeric($charity['participation_rate'])? round($charity['participation_rate'] * 100) : "No Data"}}%</td>
                    <td>{{is_numeric($charity['previous_participation_rate']) ? round($charity['previous_participation_rate']): "No Data"}}%</td>
                    <td>{{is_numeric($charity['change']) ? round($charity['change']) : "No Data"}}%</td>
                    <td>{{$charity['donors']}}</td>
                    <td>${{number_format($charity['dollars'])}}</td>
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
        window.location = "/challenge?year="+$('#year').val()+"&organization_name="+$("#organization_name").val();
    });

    var new_sort = '{{ $request->sort == "ASC" ? "DESC" : "ASC" }}';

    function sortTable(field='participation_rate')
    {
        window.location = "/challenge?field="+field+"&sort="+new_sort+"&year="+year+"&organization_name="+$("#organization_name").val();
        $(".sort-hook").attr("src",);
    }
</script>
@endpush
