@extends('adminlte::page')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12 col-sm-7">
            <h1>Make a Donation</h1>
            <p class="text-muted">When you give through PECSF 100% of your donated dollars goes to the organizations you choose to support.</p>
            @yield("step-content")
        </div>
        <div class="col-12 col-sm-5">
            <img src="{{ asset('img/donor.png') }}" alt="Donor" class="py-5 img-fluid">
        </div>
    </div>
</div>

@endsection