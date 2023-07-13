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

<h1 class="mt-2">Daily Campaign</h1>

<div class="mt-3 btn-group btn-group-lg" role="group" aria-label="Basic example">
    <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('challenge.index') }}';">
            <span class="mx-2 px-5">Leaderboard</span>
    </button>
    <button type="button" class="btn btn-dark mx-0 px-0"></button>
    <button type="button" class="btn btn-success"  onclick="window.location.href='{{ route('challenge.daily_campaign') }}';">
            <span class="px-2">Daily Campaign Update<span>
    </button>
</div>
    
@endsection
@section('content')

    <form id="download_report" method="GET" enctype="multipart/form-data" action="{{ route("challenge.download") }}" >
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
                    <option value="region"> By Region</option>
                    <option value="organization"> By Organization</option>
                    <option value="department"> By Department</option>
                </select>
            </div>

            <div class="form-group col-md-3">
                <label for="sort">
                    Specify dates (Optional)
                </label>
                <select class="form-control" id="start_date" name="start_date">
                    <option value=""></option>
                    @foreach ($final_date_options as $date) 
                        <option final="1" value="{{ $date }}">{{ $date }}</option>
                    @endforeach
                    @foreach ($date_options as $date) 
                        <option value="{{ $date }}">{{ $date }}</option>
                    @endforeach
                </select>                
                
            </div>
            <div class="form-group col-md-1">
                <input type="hidden" name="excel" value = '1' />

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

$(function() {

    $(document).on('change', 'select[name="sort"]', function (e) {
        // oTable.ajax.reload();
        if ((this.value) == 'department') {
            $('select[name="start_date"] option[final="1"]').hide();
        } else {
            $('select[name="start_date"] option[final="1"]').show();
        }

    });

});            

</script>

@endpush