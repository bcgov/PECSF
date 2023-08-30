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
        <h1 class="text-center" style="color:#687278;">Welcome, {{ Auth::user()->name }}</h1>
        <p class="text-center h5"  style="color:#687278;"><b>Choose from the options below:</b></p>
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

    <div class="row p-5">
      <div class="col-12 col-md-5 offset-md-1 pt-5">
        <br><br>
        <br><br>
        <h1 class="mt-5 p1-5" style="font-size: 4em;">
          Generosity in Action
        </h1>
      </div>

        <div class="col-12 col-md-6">
            <section id="myCarousel" class=" carousel" aria-roledescription="carousel" aria-label="Highlighted television shows">
                <div class="carousel-inner">
                    <div class="controls">
                        <button type="button" class="rotation pause" aria-label="Stop automatic slide show">
                            <svg width="42" height="34" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                <rect class="background" x="2" y="2" rx="5" ry="5" width="38" height="24"></rect>
                                <rect class="border" x="4" y="4" rx="5" ry="5" width="34" height="20"></rect>

                                <polygon class="pause" points="17 8 17 20"></polygon>

                                <polygon class="pause" points="24 8 24 20"></polygon>

                                <polygon class="play" points="15 8 15 20 27 14"></polygon>
                            </svg>
                        </button>

                        <button type="button" class="previous" aria-controls="myCarousel-items" aria-label="Previous Slide">
                            <svg width="42" height="34" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                <rect class="background" x="2" y="2" rx="5" ry="5" width="38" height="24"></rect>
                                <rect class="border" x="4" y="4" rx="5" ry="5" width="34" height="20"></rect>
                                <polygon points="9 14 21 8 21 11 33 11 33 17 21 17 21 20"></polygon>
                            </svg>
                        </button>

                        <button type="button" class="next" aria-controls="myCarousel-items" aria-label="Next Slide">
                            <svg width="42" height="34" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                <rect class="background" x="2" y="2" rx="5" ry="5" width="38" height="24"></rect>
                                <rect class="border" x="4" y="4" rx="5" ry="5" width="34" height="20"></rect>
                                <polygon points="9 11 21 11 21 8 33 14 21 20 21 17 9 17"></polygon>
                            </svg>
                        </button>
                    </div>

                    <div id="myCarousel-items" class="carousel-items" aria-live="off">







                        <div class="carousel-item" role="group" aria-roledescription="slide" aria-label="4 of 6">

                                <img class="d-block w-100" src="{{asset('img/home/01.jpeg')}}" alt="Third slide">

                        </div>


                        <div class="carousel-item" role="group" aria-roledescription="slide" aria-label="5 of 6">

                                <img class="d-block w-100" src="{{asset('img/home/02.jpg')}}" alt="Third slide">




                        </div>


                        <div class="carousel-item" role="group" aria-roledescription="slide" aria-label="6 of 6">


                                <img class="d-block w-100" src="{{asset('img/home/04.jpg')}}" alt="Third slide">





                        </div>

                    </div>
                </div>

            </section>
        </div>




<!--
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
             <button style="border:none;background:none;">
                 <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                 <span class="sr-only">Back</span>
             </button>
            </a>
            <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                <button style="border:none;background:none;">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="sr-only">Next</span>
                </button>
            </a>
          </div>
        </div>
      </div>
    -->
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

    var CarouselPreviousNext = function (node, options) {
        // merge passed options with defaults
        options = Object.assign(
            { moreaccessible: false, paused: false, norotate: false },
            options || {}
        );

        // a prefers-reduced-motion user setting must always override autoplay
        var hasReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
        if (hasReducedMotion.matches) {
            options.paused = true;
        }

        /* DOM properties */
        this.domNode = node;

        this.carouselItemNodes = node.querySelectorAll('.carousel-item');

        this.containerNode = node.querySelector('.carousel-items');
        this.liveRegionNode = node.querySelector('.carousel-items');
        this.pausePlayButtonNode = null;
        this.previousButtonNode = null;
        this.nextButtonNode = null;

        this.playLabel = 'Start automatic slide show';
        this.pauseLabel = 'Stop automatic slide show';

        /* State properties */
        this.hasUserActivatedPlay = false; // set when the user activates the play/pause button
        this.isAutoRotationDisabled = options.norotate; // This property for disabling auto rotation
        this.isPlayingEnabled = !options.paused; // This property is also set in updatePlaying method
        this.timeInterval = 5000; // length of slide rotation in ms
        this.currentIndex = 0; // index of current slide
        this.slideTimeout = null; // save reference to setTimeout

        // Pause Button

        var elem = document.querySelector('.carousel .controls button.rotation');
        if (elem) {
            this.pausePlayButtonNode = elem;
            this.pausePlayButtonNode.addEventListener(
                'click',
                this.handlePausePlayButtonClick.bind(this)
            );
        }

        // Previous Button

        elem = document.querySelector('.carousel .controls button.previous');
        if (elem) {
            this.previousButtonNode = elem;
            this.previousButtonNode.addEventListener(
                'click',
                this.handlePreviousButtonClick.bind(this)
            );
            this.previousButtonNode.addEventListener(
                'focus',
                this.handleFocusIn.bind(this)
            );
            this.previousButtonNode.addEventListener(
                'blur',
                this.handleFocusOut.bind(this)
            );
        }

        // Next Button

        elem = document.querySelector('.carousel .controls button.next');
        if (elem) {
            this.nextButtonNode = elem;
            this.nextButtonNode.addEventListener(
                'click',
                this.handleNextButtonClick.bind(this)
            );
            this.nextButtonNode.addEventListener(
                'focus',
                this.handleFocusIn.bind(this)
            );
            this.nextButtonNode.addEventListener(
                'blur',
                this.handleFocusOut.bind(this)
            );
        }

        // Carousel item events

        for (var i = 0; i < this.carouselItemNodes.length; i++) {
            var carouselItemNode = this.carouselItemNodes[i];

            // support stopping rotation when any element receives focus in the tabpanel
            carouselItemNode.addEventListener('focusin', this.handleFocusIn.bind(this));
            carouselItemNode.addEventListener(
                'focusout',
                this.handleFocusOut.bind(this)
            );

            var imageLinkNode = carouselItemNode.querySelector('.carousel-image a');

            if (imageLinkNode) {
                imageLinkNode.addEventListener(
                    'focus',
                    this.handleImageLinkFocus.bind(this)
                );
                imageLinkNode.addEventListener(
                    'blur',
                    this.handleImageLinkBlur.bind(this)
                );
            }
        }

        // Handle hover events
        this.domNode.addEventListener('mouseover', this.handleMouseOver.bind(this));
        this.domNode.addEventListener('mouseout', this.handleMouseOut.bind(this));

        // initialize behavior based on options

        this.enableOrDisableAutoRotation(options.norotate);
        this.updatePlaying(!options.paused && !options.norotate);
        this.setAccessibleStyling(options.moreaccessible);
        this.rotateSlides();
    };

    /* Public function to disable/enable rotation and if false, hide pause/play button*/
    CarouselPreviousNext.prototype.enableOrDisableAutoRotation = function (
        disable
    ) {
        this.isAutoRotationDisabled = disable;
        this.pausePlayButtonNode.hidden = disable;
    };

    /* Public function to update controls/caption styling */
    CarouselPreviousNext.prototype.setAccessibleStyling = function (accessible) {
        if (accessible) {
            this.domNode.classList.add('carousel-moreaccessible');
        } else {
            this.domNode.classList.remove('carousel-moreaccessible');
        }
    };

    CarouselPreviousNext.prototype.showCarouselItem = function (index) {
        this.currentIndex = index;

        for (var i = 0; i < this.carouselItemNodes.length; i++) {
            var carouselItemNode = this.carouselItemNodes[i];
            if (index === i) {
                carouselItemNode.classList.add('active');
            } else {
                carouselItemNode.classList.remove('active');
            }
        }
    };

    CarouselPreviousNext.prototype.previousCarouselItem = function () {
        var nextIndex = this.currentIndex - 1;
        if (nextIndex < 0) {
            nextIndex = this.carouselItemNodes.length - 1;
        }
        this.showCarouselItem(nextIndex);
    };

    CarouselPreviousNext.prototype.nextCarouselItem = function () {
        var nextIndex = this.currentIndex + 1;
        if (nextIndex >= this.carouselItemNodes.length) {
            nextIndex = 0;
        }
        this.showCarouselItem(nextIndex);
    };

    CarouselPreviousNext.prototype.rotateSlides = function () {
        if (!this.isAutoRotationDisabled) {
            if (
                (!this.hasFocus && !this.hasHover && this.isPlayingEnabled) ||
                this.hasUserActivatedPlay
            ) {
                this.nextCarouselItem();
            }
        }

        this.slideTimeout = setTimeout(
            this.rotateSlides.bind(this),
            this.timeInterval
        );
    };

    CarouselPreviousNext.prototype.updatePlaying = function (play) {
        this.isPlayingEnabled = play;

        if (play) {
            this.pausePlayButtonNode.setAttribute('aria-label', this.pauseLabel);
            this.pausePlayButtonNode.classList.remove('play');
            this.pausePlayButtonNode.classList.add('pause');
            this.liveRegionNode.setAttribute('aria-live', 'off');
        } else {
            this.pausePlayButtonNode.setAttribute('aria-label', this.playLabel);
            this.pausePlayButtonNode.classList.remove('pause');
            this.pausePlayButtonNode.classList.add('play');
            this.liveRegionNode.setAttribute('aria-live', 'polite');
        }
    };

    /* Event Handlers */

    CarouselPreviousNext.prototype.handleImageLinkFocus = function () {
        this.liveRegionNode.classList.add('focus');
    };

    CarouselPreviousNext.prototype.handleImageLinkBlur = function () {
        this.liveRegionNode.classList.remove('focus');
    };

    CarouselPreviousNext.prototype.handleMouseOver = function (event) {
        if (!this.pausePlayButtonNode.contains(event.target)) {
            this.hasHover = true;
        }
    };

    CarouselPreviousNext.prototype.handleMouseOut = function () {
        this.hasHover = false;
    };

    /* EVENT HANDLERS */

    CarouselPreviousNext.prototype.handlePausePlayButtonClick = function () {
        this.hasUserActivatedPlay = !this.isPlayingEnabled;
        this.updatePlaying(!this.isPlayingEnabled);
    };

    CarouselPreviousNext.prototype.handlePreviousButtonClick = function () {
        this.previousCarouselItem();
    };

    CarouselPreviousNext.prototype.handleNextButtonClick = function () {
        this.nextCarouselItem();
    };

    /* Event Handlers for carousel items*/

    CarouselPreviousNext.prototype.handleFocusIn = function () {
        this.liveRegionNode.setAttribute('aria-live', 'polite');
        this.hasFocus = true;
    };

    CarouselPreviousNext.prototype.handleFocusOut = function () {
        if (this.isPlayingEnabled) {
            this.liveRegionNode.setAttribute('aria-live', 'off');
        }
        this.hasFocus = false;
    };

    /* Initialize Carousel and options */

    window.addEventListener(
        'load',
        function () {
            var carouselEls = document.querySelectorAll('.carousel');
            var carousels = [];

            // set example behavior based on
            // default setting of the checkboxes and the parameters in the URL
            // update checkboxes based on any corresponding URL parameters
            var checkboxes = document.querySelectorAll(
                '.carousel-options input[type=checkbox]'
            );
            var urlParams = new URLSearchParams(location.search);
            var carouselOptions = {};

            // initialize example features based on
            // default setting of the checkboxes and the parameters in the URL
            // update checkboxes based on any corresponding URL parameters
            checkboxes.forEach(function (checkbox) {
                var checked = checkbox.checked;

                if (urlParams.has(checkbox.value)) {
                    var urlParam = urlParams.get(checkbox.value);
                    if (typeof urlParam === 'string') {
                        checked = urlParam === 'true';
                        checkbox.checked = checked;
                    }
                }

                carouselOptions[checkbox.value] = checkbox.checked;
            });

            carouselEls.forEach(function (node) {
                carousels.push(new CarouselPreviousNext(node, carouselOptions));
            });

            // add change event to checkboxes
            checkboxes.forEach(function (checkbox) {
                var updateEvent;
                switch (checkbox.value) {
                    case 'moreaccessible':
                        updateEvent = 'setAccessibleStyling';
                        break;
                    case 'norotate':
                        updateEvent = 'enableOrDisableAutoRotation';
                        break;
                }

                // update the carousel behavior and URL when a checkbox state changes
                checkbox.addEventListener('change', function (event) {
                    urlParams.set(event.target.value, event.target.checked + '');
                    window.history.replaceState(
                        null,
                        '',
                        window.location.pathname + '?' + urlParams
                    );

                    if (updateEvent) {
                        carousels.forEach(function (carousel) {
                            carousel[updateEvent](event.target.checked);
                        });
                    }
                });
            });
        },
        false
    );

</script>
@endpush
