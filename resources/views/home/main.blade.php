@extends('layouts.web')

@section('content')
<div class="container">
    <div class="row mt-5">
        <div class="col-md-6">
            <div class="py-3">
                <img src="{{asset('img/brand/PECSF_Logo_Vert_RGB.jpg')}}" alt="">
                <div class="mt-4">
                    <h2>Engaging Header here</h2>
                    <p class="mt-4">
                        PECSF, also called the Community Fund, is the Province of British Columbia’s unique, employee-driven workplace giving program. Started by caring, community-minded public servants in 1965, it has raised over $50 million for charities throughout the province. BC servants are supported to grow and develop in meaningful ways like volunteering through PECSF!
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            @include('home.slider')
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-md-4">
            <a href="{{route('donate')}}">
                <div class="card bg-light p-3 m-3 h-100">
                    <p>GIVE</p>
                    <p>
                        Explore your charity option
                    </p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <div class="card bg-light p-3 m-3 h-100">
                <p>VOLUNTEER</p>
                <p>
                    Take charge of your career
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light p-3 m-3 h-100">
                <p>CONNECT</p>
                <p>
                    Find out what's happening in the BC Public Service and the Impact of PECSF on local communities
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
