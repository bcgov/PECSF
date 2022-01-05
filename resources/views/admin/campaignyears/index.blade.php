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
        <!--
        <div class="d-flex justify-content-center justify-content-lg-start mb-2" role="tablist">
            <div class="px-4 py-1 mr-2 border-bottom border-primary">
                <x-button role="tab" href="#" style="">
                    Donation History
                </x-button>
            </div>
        </div>
    -->
        @if(count($campaign_years) > 0)
            @include('admin.campaignyears.partials.list')
        @else
        <div class="text-center text-primary">
            <p>
                <strong>No Campaign year has been setup yet.</strong>
            </p>
           
        </div>
        @endif
    </div>
</div>

@endsection