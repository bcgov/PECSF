@extends('adminlte::page')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12 col-sm-7">
            <h1>Make a Donation</h1>
            <p class="text-muted">A blurb about how 100% of charity goes to that charity to enforce PECSF benefits</p>
            @yield("step-content")
        </div>
        <div class="col-12 col-sm-5">
            <img src="{{ asset('img/donor.png') }}" alt="Donor" class="py-5 img-fluid">
        </div>
    </div>
</div>

@endsection