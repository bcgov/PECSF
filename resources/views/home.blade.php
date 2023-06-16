@extends('adminlte::page')

@section('content')

<style>
    .home-tiles .card {
        border-radius: 12px;
        border: 2px solid #b2b3c533;
        background-color: #ffffff;
    }

    .home-tiles .card:hover {
        background-color: #1a5a96;
        color: white;
    }

    .home-tiles .card:hover i {
      text-decoration-color: white;
      color:white;
    }

    .home-tiles .card span {
      font-size: 22px;
    }

    .home-tiles .card:hover span{
        text-decoration-color: white;
        color:white;
        text-decoration: underline;
    }

    .home-tiles .card:hover p {
        text-decoration-color: white;
        color:white;
        text-decoration: underline;
    }

</style>

<div class="container mb-4">
  <div class="row">
    <div class="col-12 col-xl-12 ">
        <h1 class="text-center text-secondary">Welcome, {{ Auth::user()->name }}</h1>
        <p class="text-center h5 text-secondary"><b>Choose from the options below:</b></p>
    </div>
  </div>
</div>  


<div class="container home-tiles px-0">
  <div class="row" style="min-height:580px" >

    <div class="col col-md-6">
      <div class="container h-100">

        <a  href="{{route('donations.list')}}">
          <div class="card card_hook d-table" style="height: 100%">
              <div class="card-body d-table-cell align-middle text-center">
                {{-- <img src="/svgs/give.svg" style="color:white;" alt="Connect" height="55"> --}}
                <i class="nav-icon fa fa-hand-holding-heart fa-2x"></i><br>
                <p class="font-weight-bold"> <span>Donations</span></p>
                <p >Support the charities of your choice with payroll deductions in any amount.</p>
              </div>  
          </div>
        </a>
      </div>
    </div>

    <div class="col col-md-6">
      <div class="container h-100 px-0" >
          <div class="row h-50 pb-2">
            <div class="col col-md-6">
              <a href="#" data-toggle="modal" data-target="#learn-more-modal">                  
                <div class="card px-2 d-table" style="height: 100%">
                    <div class="card-body d-table-cell align-middle text-center">
                      {{-- <span  class="card-body text-center" data-toggle="modal" data-target="#learn-more-modal">                   --}}
                        <i class="nav-icon fas fa-info-circle fa-2x"></i><br>
                        <p class="font-weight-bold"><span>Learn</span></p>
                        <p class="">Need more information about PECSF or how to donate ?</p>
                        <p></p>
                      {{-- </span> --}}
                    </div>

                </div>
              </a>                    
            </div>
            <div class="col col-md-6">
              <a href="{{route('volunteering.index')}}">
                <div class="card px-2 d-table" style="height: 100%">
                  <div class="card-body d-table-cell align-middle text-center">
                    {{-- <a class="card-body text-center"  href="{{route('volunteering.index')}}"> --}}
                      <i class="x nav-icon fas fa-hands-helping fa-2x"></i>
                      <p class="font-weight-bold"><span >Volunteering</span></p>
                      <p class="" >Looking to do more than just donate? Volunteer to help run a campaign or host an event.</p>
                    {{-- </a> --}}
                  </div>
                </div>
              </a>
            </div>

          </div>

          <div class="row h-50 pt-3">
            <div class="col col-md-6">
              <a href="{{route('bank_deposit_form')}}">
                <div class="card px-2 d-table" style="height: 100%">
                  <div class="card-body d-table-cell align-middle text-center">
                    {{-- <a href="{{route('bank_deposit_form')}}" class="card-body"> --}}
                        <i class="nav-icon fas fa-money-check-alt fa-2x "></i><br>
                        <p class="font-weight-bold "><span>eForm</span></p>
                        <p >Submit for your cash, cheque, fundraising or gaming bank deposit form.</p>
                    {{-- </a> --}}
                  </div>
                </div>
              </a>
            </div>

            <div class="col col-md-6">
              <a href="{{route('contact')}}">              
                <div class="card px-2 d-table" style="height: 100%">
                  <div class="card-body d-table-cell align-middle text-center">
                    {{-- <a href="{{route('contact')}}" class="card-body"> --}}
                        <i class="nav-icon fas fa-question-circle fa-2x "></i><br>
                        <p class="text-primary font-weight-bold"><span style="font-size:22px;">Contact</span></p>
                        <p class="">Got questions? &nbsp;We are here to help !</p>
                        <p class="p-1"></P>
                    {{-- </a> --}}
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
    </div>
  </div>
  
</div>

{{-- <div class="container mt-5">
      <div class="row">
        <div class="col-12 col-xl-12 ">
            <h1 class="text-center">Welcome, {{ Auth::user()->name }}</h1>
            <p class="text-center h4"><b>Choose from the options below:</b></p>

            <div class="row p-3">
            <div class=" col-md-12 p-2">
                <div class="card card_hook ">
                    <a href="{{route('contact')}}" class="card-body text-center">
                        <i class="x nav-icon fas fa-info-circle  fa-2x bottom-right"></i><br>
                        <p style="color:black;">Learn more about PECSF and how to donate</p>
                    </a>
                </div>
            </div>
            </div>



            <div class="row p-3">

                <div class=" col-md-9 p-2">
                    <div class="card card_hook " style="height:505px" >
                        <a href="{{route('donations.list')}}" style="margin-top:15%;" class="card-body text-center">
                            <img src="/svgs/give.svg" style="color:white;" alt="Connect" height="55">
                            <p class="text-primary "> <span style="font-size:22px;" >Donations </span></p>
                            <p style="color:black;">Support the charities of your choice with payroll deduction in any amount.</p>
                        </a>
                    </div>
                </div>


                <div class=" col-md-3 p-2">
                    <div class="card_hook card pt-4" style="height:auto" >
                        <a class="card-body text-center"  href="{{route('volunteering.index')}}">
                            <i class="x nav-icon fas fa-hands-helping fa-2x"></i>
                            <p class="text-primary "> <span style="font-size:20px;">Volunteering</span></p>
                            <p class="mt-1" style="color:black;"> Looking to do more than just donate? Volunteer to help run a campaign or host an event.</p>
                        </a>
                    </div>
                    <div class="card_hook card" style="height:auto;" >
                        <a href="{{route('contact')}}" class="card-body text-center">
                            <i class="x nav-icon fas fa-question-circle fa-2x "></i>
                            <p class="text-primary ">  <span style="font-size:22px;">Contact </span></p>
                            <p class="mt-1">Got questions? We are here to help!</p>
                        </a>
                    </div>
                </div>


            </div>


            <div class="row p-3">
                <div class=" col-md-12 p-2">
                    <div class="card card_hook ">
                        <a href="{{route('bank_deposit_form')}}" class="card-body text-center">
                            <i class="x nav-icon fa-solid fas fa-money-check-alt fa-2x bottom-right"></i><br>
                            <p class="text-primary "> <span style="font-size:22px;" >eForm </span></p>
                            <p style="color:black;">Submit for your cash, cheque, fundraising or gaming bank deposit form.</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}
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
              <span class="sr-only">Back</span>
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
<!--
<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-12 col-xl-8 offset-xl-2">
            <h1 class="text-center">Check out our leaderboard</h1>

            <div class="row justify-content-center">
                <div class="text-center">
                    <a class="btn btn-primary" href="/challenge" role="button">Go to challenge page</a>
                </div>
            </div>

        </div>
    </div>
</div>
-->

@include('donations.partials.learn-more-modal')

@endsection

@push('js')
<script>
  
</script>
@endpush
