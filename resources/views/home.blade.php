@extends('adminlte::page')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12 col-xl-8 offset-xl-2">
            <h1 class="text-center">Welcome, {{ Auth::user()->name }}</h1>
            <p class="text-center h4"><b>Choose from the options below:</b></p>

            <div class="row p-3">
                <div class=" col-md-4 p-2">
                    <div class="card" style="height:220px" >
                        <a class="card-body text-center"  href="{{route('volunteering.index')}}">
                          <img src="/svgs/volunteer.svg" alt="Volunteer">
                          <p class="text-primary "> <span style="font-size:22px;">Volunteer</span></p>
                          <p class="mt-1"> Make a local impact by helping those in need.</p>
                          <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class=" col-md-4 p-2">
                    <div class="card" style="height:220px" >
                        <a href="{{route('donate')}}" class="card-body text-center">
                            <img src="/svgs/give.svg" alt="Connect" height="32">
                            <p class="text-primary "> <span style="font-size:22px;" >Give </span></p>
                            <p>Support the charities of your choice with payroll deduction in any amount.</p>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class=" col-md-4 p-2">
                    <div class="card" style="height:220px" >
                        <a href="{{route('contact')}}" class="card-body text-center">
                            <img src="/svgs/connect.svg" alt="Connect">
                            <p class="text-primary ">  <span style="font-size:22px;">Contact </span></p>
                            <p class="mt-1">Got questions? We are here to help you!</p>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
<div class="mx-n3 mt-5 bg-primary">
  <div class="container">
    <div class="row">
      <div class="col-12 col-md-5 offset-md-1 pt-5">
        <br><br>
        <br><br>
        <h1 class="mt-5 p1-5" style="font-size: 4em;">
          Generosity in Action
        </h1>
      </div>
      <div class="col-12 col-md-6">
        <div class="py-5">
          <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
              <div class="carousel-item active">
                <img class="d-block w-100" src="{{asset('img/home/01.jpeg')}}" alt="First slide">
              </div>
              <div class="carousel-item">
                <img class="d-block w-100" src="{{asset('img/home/02.jpg')}}" alt="Second slide">
              </div>
              <div class="carousel-item">
                <img class="d-block w-100" src="{{asset('img/home/04.jpg')}}" alt="Third slide">
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
        </div>
      </div>
    </div>
  </div>
</div>
  
  
@endsection
