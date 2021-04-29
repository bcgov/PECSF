@extends('adminlte::page')

@section('content')
<div class="container pl-2 pr-3">
    <div class="row">
        <div class="col-md-8">
            <h2>Welcome, {{ Auth::user()->name }}</h2>
            <p><b>Quick Actions</b></p>

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
        <div class="col-md-4 card p-0">

            <div id="carouselExampleIndicators" class="carousel slide h-100" data-ride="carousel">
                <ol class="carousel-indicators">
                    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                </ol>
                <div class="carousel-inner h-100">
                    <div class="carousel-item active h-100">
                        <img src="img/slider1.png" class="img-fluid w-100 h-100" style="object-fit:cover">

                    </div>
                    <div class="carousel-item h-100">
                        <img src="img/slider2.png" class="img-fluid w-100 h-100" style="object-fit:cover">

                    </div>
                    <div class="carousel-item h-100">
                        <img src="img/slider3.png" class="img-fluid w-100 h-100" style="object-fit:cover">
                    </div>
                </div>

            </div>

        </div>
    </div>
    <div class="row">
        <p class="w-50"><b>Things to Note</b></p>
        <p class="w-50 text-right">View all</p>
        <div class="col-12 col-md-12">
            <div class="row mb-3">
                <div class="col-3 col-md-3">
                    <img src="/svgs/post1.svg" class="img-fluid">
                </div>
                <div class="col-9 col-md-9 p-3">
                    <div class="clearfix">

                        <div class="text-wrap float-left h4 font-weight-bold" style="margin-right:75px">Campaign 2020 Volunteer Training Happening Now</div>

                        <span class=" position-absolute float-right ml-2" style="right:0;top:20px;">
                            <button class="btn btn-primary btn-sm">Register</button>
                            <i class="far fa-bookmark ml-4"></i>
                        </span>
                    </div>

                    <p class="mt-2">Volunteer for your ministry’s Fall campaign now!</p>
                    <p>Registration Deadline: <b>June 12, 2021</b></p>

                </div>
            </div>
            <hr>
            <div class="row mb-3">
                <div class="col-3 col-md-3">
                    <img src="/svgs/post2.png" class="img-fluid">
                </div>
                <div class="col-9 col-md-9 p-3">
                    <div class="clearfix">

                        <div class="text-wrap float-left h4 font-weight-bold" style="margin-right:75px">Have some fun and support our local furry friends at the BCSPCA</div>
                        <span class=" position-absolute float-right ml-2" style="right:0;top:20px;">
                            <button class="btn btn-primary btn-sm">Register</button>
                            <i class="far fa-bookmark ml-4"></i>
                        </span>
                    </div>
                    <p class="mt-2">Join a virtual meet & greet with some furry friends</p>
                    <p>Registration Deadline: <b>May 9, 2021</b></p>
                </div>
            </div>
            <hr>
            <div class="row mb-3">
                <div class="col-3 col-md-3">
                    <img src="/svgs/post3.svg" class="img-fluid">
                </div>
                <div class="col-9 col-md-9 p-3">
                    <div class="clearfix">

                        <div class="text-wrap float-left h4 font-weight-bold" style="margin-right:75px">Volunteers needed for community tree planting initiative!</div>

                        <span class=" position-absolute float-right ml-2" style="right:0;top:20px;">
                            <button class="btn btn-primary btn-sm">Register</button>
                            <i class="far fa-bookmark ml-4"></i>
                        </span>
                    </div>

                    <p class="mt-2">Friends Uniting for Nature (FUN) Society is seeking volunteers for our annual tree planting project. Please get in touch for more details</p>

                    <p>Response Deadline: <b>May 9, 2021</b></p>

                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3 p-0">
        <p class="w-50"><b>Upcoming Events</b></p>
        <p class="w-50 text-right">View all</p>
        <div class="col-4 col-md-4">
            <div class="card" style="width: 18rem;">
                <img src="/svgs/event1.svg" class="card-img-top" alt="Virtual Lunch and Learn">
                <div class="card-body">
                    <div class="row no-gutters">
                        <div class="col-md-2">
                            <div>Apr<br>15</div>
                        </div>
                        <div class="col-md-10">
                            <h5 class="card-title">Virtual Lunch and Learn</h5>
                            <p class="card-text text-muted">Take a break from work and enjoy a guided workshop by Jane from...</p>

                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-4 col-md-4">
            <div class="card" style="width: 18rem;">
                <img src="/svgs/event2.svg" class="card-img-top" alt="Virtual Lunch and Learn">
                <div class="card-body">
                    <div class="row no-gutters">
                        <div class="col-md-2">
                            <div>Apr<br>19</div>
                        </div>
                        <div class="col-md-10">
                            <h5 class="card-title">2021 Virtual Run for PECSF</h5>
                            <p class="card-text text-muted">NEW EVENT...come out for an afternoon of exercise and help us..</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-4 col-md-4">
            <div class="card" style="width: 18rem;">
                <img src="/svgs/event3.svg" class="card-img-top" alt="Virtual Lunch and Learn">
                <div class="card-body">
                    <div class="row no-gutters">
                        <div class="col-md-2">
                            <div>May<br>3</div>
                        </div>
                        <div class="col-md-10">
                            <h5 class="card-title">Virtual Fundraiser Cooking Class</h5>

                            <p class="card-text text-muted">Get your apron ready and join PECSF at our first Virtual fundraise..</p>


                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <p class="w-50"><b>Recent Stories</b></p>
        <p class="w-50 text-right">View all</p>
        <div class="col-4 col-md-4">
            <div class="card" style="width: 18rem;">
                <img src="/svgs/recent1.svg" class="card-img-top" alt="Virtual Lunch and Learn">
                <div class="card-body">
                    <h5 class="card-title  text-muted">January 22, 2019</h5>
                    <p class="card-text">PECSF Member Receives an Award at the Annual Caring for BC Gala!</p>
                </div>
            </div>
        </div>
        <div class="col-4 col-md-4">
            <div class="card" style="width: 18rem;">
                <img src="/svgs/recent2.svg" class="card-img-top" alt="Virtual Lunch and Learn">
                <div class="card-body">
                    <h5 class="card-title  text-muted">January 1, 2019</h5>
                    <p class="card-text">PECSF Team Achieves a Fundraising Goal!</p>

                </div>
            </div>
        </div>
        <div class="col-4 col-md-4">
            <div class="card" style="width: 18rem;">
                <img src="/svgs/recent3.svg" class="card-img-top" alt="Virtual Lunch and Learn">
                <div class="card-body">
                    <h5 class="card-title  text-muted">January 22, 2019</h5>
                    <p class="card-text">BC employees fundraise for local animal shelter through PECSF</p> 
                </div>
            </div>
        </div>
       

    </div>
    <div class="row">
        <p class="w-50"><b>Helpful Resources</b></p>
        <p class="w-50 text-right">View all</p>
           <div class="col-4 col-md-4">
               <div class="card" style="width: 18rem;">
                   <img src="/svgs/help1.svg" class="card-img-top" alt="Virtual Lunch and Learn">
                   <div class="card-body">
                       <h5 class="card-title  text-muted">January 22, 2019</h5>
                       <p class="card-text">PECSF Member Receives an Award at the Annual Caring for BC Gala!
</p> <a href="#" class="card-link text-right float-right">Read More</a>
                   </div>
               </div>
           </div>
           <div class="col-4 col-md-4">
               <div class="card" style="width: 18rem;">
                   <img src="/svgs/help2.svg" class="card-img-top" alt="Virtual Lunch and Learn">
                   <div class="card-body">
                       <h5 class="card-title ">PECSF 101 Training Video</h5>

                       <p class="card-text">This video and its contents are meant for BC Public Service employees. If you have any qu..</p>
 <p class="card-text"><small class="text-muted">youtube</small></p>


                   </div>
               </div>
           </div>
           <div class="col-4 col-md-4">
               <div class="card" style="width: 18rem;">
                   <img src="/svgs/help3.svg" class="card-img-top" alt="Virtual Lunch and Learn">
                   <div class="card-body">
                       <h5 class="card-title  text-muted">January 22, 2019</h5>
                       <p class="card-text">PECSF Member Receives an Award at the Annual Caring for BC Gala!</p>
 <a href="#" class="card-link text-right float-right">Read More</a>
                   </div>
               </div>
           </div>

    </div>
</div>
@endsection
