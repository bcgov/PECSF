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
<p class="h5 mt-3">Visit this page daily during the PECSF campaign to see updated statistics, including organization participation rates!<br>

    View and download daily statistics updates below.<br>

    If you have questions about PECSF statistics, send us an e-mail at <a href="mailto:PECSF@gov.bc.ca?subject=Challenge%20page">PECSF@gov.bc.ca</a>.</p>
</div>
@endsection
@section('content')
<div class="d-flex justify-content-end">
<label style="min-width: 130px">
Campaign Year
<select name="year" class="form-control form-control-sm">
<option {{$year==2021?"selected":""}} value="2021">2021</option>
<option {{$year==2020?"selected":""}} value="2020">2020</option>
</select>
</label>
</div>
<div class="card">
<div class="card-body">
<table class="table table-bordered rounded" id="myTable2">
<tr class="bg-light">
<th onclick="sortTable('rank')" style="cursor: pointer;">Rank</th>
<th onclick="sortTable('name')" style="cursor: pointer;">Organization Name <img style="width:16px;height:16px;" class="sort-hook float-right" src="@php echo  ($request->sort == "ASC") ? asset("img/icons/FilterDescending.png"):asset("img/icons/FilterAscending.png") @endphp" /></th>
<th onclick="sortTable('participation_rate')" style="cursor: pointer;">Participation rate <img style="width:16px;height:16px;" class="sort-hook float-right" src="@php echo  ($request->sort == "ASC") ? asset("img/icons/FilterDescending.png"):asset("img/icons/FilterAscending.png") @endphp" /></th>
<th onclick="sortTable('participation_rate')" style="cursor: pointer;">Previous participation rate </th>
<th onclick="sortTable('donors')" style="cursor: pointer;">Donors <img style="width:16px;height:16px;" class="sort-hook float-right" src="@php echo  ($request->sort == "ASC") ? asset("img/icons/FilterDescending.png"): asset("img/icons/FilterAscending.png") @endphp" /></th>
<th onclick="sortTable('dollars')" style="cursor: pointer;">Dollar Donated <img style="width:16px;height:16px;" class="sort-hook float-right" src="@php echo  ($request->sort == "ASC") ? asset("img/icons/FilterDescending.png"): asset("img/icons/FilterAscending.png") @endphp" /></th>
</tr>


@foreach($charities as $index => $charity)
<!--  <tr>
<td>{{$count == 0 ? 'st' : ($count == 1 ? 'nd' : ($count == 2 ? 'rd' : 'th')) }}</td>
<td>{{$charity['name']}}</td>
<td>{{$charity['participation_rate']}}%</td>
<td>{{$charity['final_participation_rate']}}%</td>
<td>
@if($charity['change'] < 0)
    <span style="color:red">
@else
    <span style="color:green">
@endif
{{$charity['change']}}%
</td>
<td>{{$charity['total_donors']}}</td>
<td>${{number_format($charity['total_donation'])}}</td>
</tr> -->
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
                    <td>{{round(($charity['participation_rate'] * 100),2)}}%</td>
                    <td>{{$charity['previous_participation_rate']}}%</td>
                    <td>{{$charity['donors']}}</td>
                    <td>${{$charity['dollars']}}</td>
                </tr>
            @endforeach
        </table>
<br>
        <div>
            {{$charities->links()}}
        </div>


    </div>
</div>

<br>
<br>
<form id="download_report" method="GET" enctype="multipart/form-data" action="{{ route("challege.download") }}" >
    @csrf
<div class="form-row">
    <div class="form-group col-md-12">
        <h1>Daily Campaign Update</h1>
        <h6>View and download daily statistics updates during the annual PECSF campaign.</h6>
    </div>

</div>

<div class="form-row">
    <div class="form-group col-md-2">

        <label for="sort">
            View by
        </label>
            <select id="sort" class="select form-control" name="sort">
                <option type="radio" style="position: relative;top: 2px;" name="sort" value="region"> By Region</option>
                <option type="radio" style="position: relative;top: 2px;" checked name="sort" value="organization"> By Organization</option>
                <option type="radio" style="position: relative;top: 2px;" name="sort" value="department"> By Department</option>
            </select>


    </div>
    <div class="form-group col-md-3">
        <label for="sort">
            Specify dates (Optional)
        </label>
        <input class="form-control" type="date" id="start_date" name="start_date" />
    </div>
    <div class="form-group col-md-1">
        <input type="submit" style="margin-top:30px;" class="btn btn-primary" value="Download Report" />
    </div>
</div>
</form>

<div class="form-row">
    <div id="preview" class="form-group col-md-12">

    </div>
</div>


@endsection
@push('js')
<script>
    var year = '{{ $request->year ? $request->year : "2021" }}';

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
        window.location = "/challenge?year="+$(this).val();
    });

    var new_sort = '{{ $request->sort == "ASC" ? "DESC" : "ASC" }}';
function sortTable(field='participation_rate') {
    window.location = "/challenge?field="+field+"&sort="+new_sort+"&year="+year;
    $(".sort-hook").attr("src",);
}
</script>
@endpush
