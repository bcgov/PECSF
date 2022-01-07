@extends('adminlte::page')
@section('content_header')
    <div class="d-flex mt-3">
        <h1>PECSF Campaign Year</h1>
        <div class="flex-fill"></div>
    </div>
@endsection
@section('content')
<div class="card">
    <div class="card-body">
        
        @if(count($campaign_years) > 0)
            @include('admin.campaignyears.partials.list')
        @else
        <div class="text-center text-primary">
            <p>
                <strong>No campaign year has been setup yet.</strong>
            </p>
           
        </div>
        @endif
    </div>
</div>

@endsection