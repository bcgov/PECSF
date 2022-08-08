@extends('adminlte::page')

@section('content')
<div class="container mt-1">
    <div class="row">
        <div class="col-9 col-sm-9">
            <h1>Make a Donation</h1>
            <p class="text-muted">When you give through PECSF 100% of your donated dollars goes to the organizations you choose to support.</p>
            @yield("step-content")
        </div>
        <div class="col-3 col-sm-3">
            <img src="{{ asset('img/donor.png') }}" alt="Group of volunteers making wreaths at a table" class="py-5 img-fluid">
        </div>

        <div class="col-9 col-sm-9">

        </div>


    </div>
</div>

@endsection
