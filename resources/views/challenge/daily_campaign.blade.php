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
    <h1>Daily Campaign</h1>
    <ul class="nav nav-pills mb-3" id="pills-tab" >
        <li class="nav-item col-md-3">
            <a style="text-align:center;" class="nav-link <?php echo e(str_contains( Route::current()->getName(), 'challege.index') ? 'active' : ''); ?>"
               href="<?php echo e(route('challege.index')); ?>" role="tab" aria-controls="pills-home" aria-selected="true">Leaderboard</a>
        </li>
        <li class="nav-item col-md-3">
            <a style="text-align:center;" class="nav-link <?php echo e(str_contains( Route::current()->getName(), 'challege.daily_campaign') ? 'active' : ''); ?>"
               href="<?php echo e(route('challege.daily_campaign')); ?>"  aria-controls="pills-profile" aria-selected="false">Daily Campaign Update</a>
        </li>
    </ul>
@endsection
@section('content')

    <form id="download_report" method="GET" enctype="multipart/form-data" action="{{ route("challege.download") }}" >
        @csrf
        <div class="form-row">
            <div class="form-group col-md-12">
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

