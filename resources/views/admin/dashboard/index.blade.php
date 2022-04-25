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
                    <div class="card-body mt-4 text-center">
                        <div>
                            <img src="{{asset('img/admin/2.png')}}" alt="" style="height:100px">
                        </div>
                        Pledge Administration <br>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card">
                    <div class="card-body mt-4  text-center">
                        <div>
                            <img src="{{asset('img/admin/1.png')}}" alt="" style="height:100px">
                        </div>
                        Campaign Set-up<br>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body mt-4  text-center">
                        <div>
                            <img src="{{asset('img/admin/4.png')}}" alt="" style="height:100px">
                        </div>
                        Training, Communications and Engagement<br>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card">
                    <div class="card-body mt-4  text-center">
                        <div>
                            <img src="{{asset('img/admin/3.png')}}" alt="" style="height:100px">
                        </div>
                        Reporting<br>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection