@extends('adminlte::page')

@section('content')

<div class="container mb-4">
  <div class="row">
    <div class="col-12 col-xl-12 ">
        <h1 class="text-center  text-primary" >Welcome to PECSF</h1>
        <p class="text-center h5 text-secondary"><strong>Choose from the options below:</strong></p>
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

<div class="mx-n3 mt-5 bg-primary">
    <div class="container">

        <div class="row p-5">
            <div class="col-12 col-md-5 offset-md-1 pt-5">
                <br><br>
                <br><br>
                <p class="mt-5" style="font-size: 4em; line-height: 120%;">Generosity in Action</p>
            </div>

            <div class="col-12 col-md-6">
                <div class="py-5">
                    <div id="myCarousel" class="carousel slide" data-ride="carousel">

                        <div class="controls">
                            <button class="rotation pause" aria-label="Stop automatic slide show">
                                <svg width="42" height="34" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                    <rect class="background" x="2" y="2" rx="5" ry="5" width="38" height="24"></rect>
                                    <rect class="border" x="4" y="4" rx="5" ry="5" width="34" height="20"></rect>
                                    <polygon class="pause" points="17 8 17 20"></polygon>
                                    <polygon class="pause" points="24 8 24 20"></polygon>
                                    <polygon class="play" points="15 8 15 20 27 14"></polygon>
                                </svg>
                            </button>

                            <button class="previous" aria-controls="myCarousel-items" aria-label="Previous Slide">
                                <svg width="42" height="34" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                    <rect class="background" x="2" y="2" rx="5" ry="5" width="38" height="24"></rect>
                                    <rect class="border" x="4" y="4" rx="5" ry="5" width="34" height="20"></rect>
                                    <polygon points="9 14 21 8 21 11 33 11 33 17 21 17 21 20"></polygon>
                                </svg>
                            </button>
                    
                            <button class="next" aria-controls="myCarousel-items" aria-label="Next Slide">
                                <svg width="42" height="34" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                    <rect class="background" x="2" y="2" rx="5" ry="5" width="38" height="24"></rect>
                                    <rect class="border" x="4" y="4" rx="5" ry="5" width="34" height="20"></rect>
                                    <polygon points="9 11 21 11 21 8 33 14 21 20 21 17 9 17"></polygon>
                                </svg>
                            </button>
                        </div>

                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img class="d-block w-100" src="{{asset('img/home/01.jpeg')}}" alt="A person is holding a poster that says #PECSF Proud! $2 Million">
                            </div>
                            <div class="carousel-item">
                               <img class="d-block w-100" src="{{asset('img/home/02.jpg')}}" alt="A picture of the PECSF team holding posters that says #PECSF Proud! $2 Million and #PECSF proud">
                            </div>
                            <div class="carousel-item">
                                <img class="d-block w-100" src="{{asset('img/home/04.jpg')}}" alt="A picture of the PECSF team holding posters that says #PECSF Proud! $2 Million and #PECSF proud">
                            </div>
                        </div>
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
                    <a class="btn btn-primary" href="/challenge" role="button">Go to statistics page</a>
                </div>
            </div>

        </div>
    </div>
</div>
-->

@include('donations.partials.learn-more-modal')

@if( session()->has('has-announcement') )
  @include('system-security.announcements.partials.modal')
@endif

@endsection

@push('css')


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

<style>
        
    /* Shared CSS for Pause, Previous and Next Buttons */

    .carousel .controls {
        box-sizing: border-box;
        position: absolute;
        top: 1em;
        z-index: 10;
        display: flex;
        width: 100%;
        padding: 0.25em 1.25em 0;
    }

    .carousel .controls button {
        position: absolute;
        z-index: 10;
        flex: 0 0 auto;
        margin: 0;
        padding: 0;
        border: none;
        background: transparent;
        outline: none;
    }

    .carousel .controls button.previous {
       right: 70px;
    }

    .carousel .controls button.next {
      right: 18px;
    }

    /* SVG Controls */

    .carousel .controls svg .background {
        stroke: black;
        fill: black;
        stroke-width: 1px;
        opacity: 0.6;
    }

    .carousel .controls svg .border {
        fill: transparent;
        stroke: transparent;
        stroke-width: 2px;
    }

    .carousel .controls svg .pause {
        stroke-width: 4;
        fill: transparent;
        stroke: transparent;
    }

    .carousel .controls svg .play {
        stroke-width: 1;
        fill: transparent;
        stroke: transparent;
    }

    .carousel .controls .pause svg .pause {
        fill: white;
        stroke: white;
    }

    .carousel .controls .play svg .play {
        fill: white;
        stroke: white;
    }

    .carousel .controls svg polygon {
        fill: white;
        stroke: white;
    }

    .carousel .controls button:focus svg .background,
    .carousel .controls button:hover svg .background,
    .carousel .controls button:hover svg .border {
        fill: #005a9c;
        stroke: #005a9c;
        opacity: 1;
    }

    .carousel .controls button:focus svg .border {
        stroke: white;
    }

    /* More accessible carousel styles, with caption and controls above/below image */

    .carousel-moreaccessible {
        padding: 0;
        margin: 0;
        position: relative;
        border: #eee solid 4px;
        border-radius: 5px;
    }

    /* Shared CSS for Pause and Tab Controls */

    .carousel-moreaccessible .controls {
        position: relative;
        top: 0;
        left: 0;
        padding: 0.25em 0.25em 0;
    }

    .carousel.carousel-moreaccessible .controls {
        position: static;
        height: 36px;
    }

    .carousel.carousel-moreaccessible .controls button.previous {
        right: 60px;
    }

    .carousel.carousel-moreaccessible .controls button.next {
        right: 6px;
    }

    .carousel-moreaccessible .carousel-items,
    .carousel-moreaccessible .carousel-items.focus {
        padding: 0;
        border: none;
    }

    .carousel-moreaccessible .carousel-items.focus .carousel-image a {
        padding: 2px;
        border: 3px solid #005a9c;
    }

    /* More accessible caption styling */

    .carousel-moreaccessible .carousel-item {
        padding: 0;
        margin: 0;
        max-height: none;
    }

    .carousel-moreaccessible .carousel-item .carousel-caption {
        position: static;
        padding: 0;
        margin: 0;
        height: 60px;
        color: black;
    }

    .carousel-moreaccessible .carousel-item .carousel-caption p {
        padding: 0;
        margin: 0;
    }

    .carousel-moreaccessible .carousel-item .carousel-caption h3 {
        font-size: 1.1em;
        padding: 0;
        margin: 0;
    }

    .carousel-moreaccessible .carousel-item .carousel-caption a:hover {
     background-color: rgb(0 0 0 / 20%);
    }

    .carousel-moreaccessible .carousel-item .carousel-caption a:focus {
        padding: 4px;
        border: 2px solid #005a9c;
        background-color: transparent;
        color: black;
        outline: none;
    }

    /* Additional customization on this page, override the standard button behaviour */
    #myCarousel .btn:focus, #myCarousel button:focus {
        border: none !important;
    }

</style>

@endpush


@push('js')

@if( session()->has('has-announcement') )
  <script src="{{ asset('vendor/ckeditor5/build/ckeditor.js') }}" ></script>
  <script>
      $(function() {    
          ClassicEditor
              .create( document.querySelector( '.announcement_preview' ), {
                  licenseKey: ''
              })
              .then( editor => {
                  window.editor = editor;
                  const toolbarElement = editor.ui.view.toolbar.element;
                  
                  editor.enableReadOnlyMode( 'my-feature-id' );
                  toolbarElement.style.display = 'none';
              })
              .catch( error => {
                  console.error( 'Oops, something went wrong!' );
                  console.error( 'Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:' );
                  console.warn( 'Build id: lu84jocs3y82-nohdljl880ze' );
                  console.error( error );
              });    

          // alert('testing');
          $('#announcementModal').modal('show');
          
      });
  </script>
@endif


<script>

    $(function () {
        var isPlaying = true;

        $('#myCarousel').carousel({
            interval:2000,
            pause: "false",
        });

        $('button.rotation').click(function () {

            playLabel = 'Start automatic slide show';
            pauseLabel = 'Stop automatic slide show';

            if (isPlaying) {
                $('#myCarousel').carousel('pause');
                $(this).removeClass('pause');
                $(this).addClass('play');
                $(this).css('aria-label', playLabel);
            }
            else {
                $('#myCarousel').carousel('cycle');
                $(this).removeClass('play');
                $(this).addClass('pause');
                $(this).css('aria-label', pauseLabel);
            }
            isPlaying = !isPlaying;
            $(this).focus();
        });


        $('button.previous').click(function () {
            $('#myCarousel').carousel('prev');
            $(this).focus();
        });

        $('button.next').click(function () {
            $('#myCarousel').carousel('next');
            $(this).focus();
        });

    });

</script>

@endpush
