@extends('adminlte::page')
@section('content_header')
    <div class="d-flex mt-3">
        <h1>Security - PECSF Administrators</h1>
        <div class="flex-fill"></div>
    </div>
@endsection
@section('content')
<div class="card">
    <div class="card-body">
        
        @if(count($administrators) > 0)
            @include('admin.administrators.partials.list')
        @else
        <div class="text-center text-primary">
            <p>
                <strong>No PECSF Administrator found.</strong>
            </p>
           
        </div>
        @endif
    </div>
</div>

@endsection