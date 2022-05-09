@extends('adminlte::page')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col text-center">
                <h1 class="text-primary">Welcome, PECSF Administrator</h1>
                <p>Choose from the options below:</p>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card">
                <a href="{{ route('settings.pledge') }}">
                    <div class="card-body mt-4 text-center">
                        <div>
                            <img src="{{asset('img/admin/2.png')}}" alt="Pledge Administration" style="height:100px">
                        </div>
                        Pledge Administration <br>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>   
                </div>
                 
            </div>

            <div class="col">                
                <div class="card">    
                    <a href="{{ route('settings.campaignyears.index') }}">
                    <div class="card-body mt-4  text-center">
                        <div>
                            <img src="{{asset('img/admin/1.png')}}" alt="Campaign Set-up" style="height:100px">
                        </div>
                        Campaign Set-up<br>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                    </a>    
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">                
                <div class="card">
                    <a href="{{ route('settings.others') }}">
                    <div class="card-body mt-4  text-center">
                        <div>
                            <img src="{{asset('img/admin/4.png')}}" alt="Training, Communications and Engagement" style="height:100px">
                        </div>
                        Training, Communications and Engagement<br>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                    </a>
                </div>
            </div>

            <div class="col">
                <div class="card">
                    <a href="{{ route('settings.reporting') }}">
                    <div class="card-body mt-4  text-center">
                        <div>
                            <img src="{{asset('img/admin/3.png')}}" alt="Reporting" style="height:100px">
                        </div>
                        Reporting<br>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                    </a>  
                </div>  
            </div>
        </div>
    </div>
@endsection