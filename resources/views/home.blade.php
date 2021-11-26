@extends('adminlte::page')

@section('content')
<div id="carouselExampleControls" class="carousel slide m-n3 mt-n5" data-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img class="d-block w-100" src="{{asset('img/home/1.jpeg')}}" alt="First slide">
    </div>
    <div class="carousel-item">
      <img class="d-block w-100" src="{{asset('img/home/2.jpeg')}}" alt="Second slide">
    </div>
    <div class="carousel-item">
      <img class="d-block w-100" src="{{asset('img/home/3.jpeg')}}" alt="Third slide">
    </div>
  </div>
  <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h2 class="text-center">Welcome, {{ Auth::user()->name }}</h2>
            <p class="text-center"><b>Choose from the options below:</b></p>

            <div class="row p-0">
                <div class=" col-md-4 p-2">
                    <div class="card" style="height:220px" >
                        <div class="card-body text-center">
                            <img src="/svgs/volunteer.svg" alt="Connect">

                            <p class="text-primary "> Volunteer</p>
                            <p class="mt-1"> Make a local impact by helping those in need.</p>
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </div>
                <div class=" col-md-4 p-2">
                  
                    <div class="card" style="height:220px" >
                        <div class="card-body text-center">
                            <img src="/svgs/give.svg" alt="Connect" height="32">

                            <p class="text-primary "> <a style="font-size:22px;" href="/donate">Give </a></p>


                            <p>Support the charities of your choice with payroll deduction in any amount.</p>
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>

                   

                </div>
                <div class=" col-md-4 p-2">
                    <div class="card" style="height:220px" >
                        <div class="card-body text-center">
                            <img src="/svgs/connect.svg" alt="Connect">
                            <p class="text-primary "> Connect</p>
                            <p class="mt-1"> Get involved with fun and engaging community events.</p>
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
    
</div>
@endsection
