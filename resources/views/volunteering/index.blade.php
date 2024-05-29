@extends('adminlte::page')

@section('content')

<div class="container mb-4">
  <div class="row">
    <div class="col-12 col-xl-12 ">
        <h1 class="text-center text-primary" >Volunteering in the BC Public Service</h1>
        <p class="text-center h5 text-secondary"><strong>Choose from the options below:</strong></p>
    </div>
  </div>
</div>


<div class="container home-tiles px-0">
    <div class="row justify-content-md-center" style="min-height:640px" >

        <div class="col col-md-8">
            <div class="container h-100 px-0" >
                <div class="row h-50 pb-2">
                    <div class="col col-md-6">
                    <a href="#" data-toggle="modal" data-target="#learn-more-modal">
                        <div class="card px-2 d-table" style="height: 100%">
                            <div class="card-body d-table-cell align-middle text-center">
                            {{-- <span  class="card-body text-center" data-toggle="modal" data-target="#learn-more-modal">                   --}}
                                <i class="nav-icon fas fa-chalkboard-teacher fa-2x"></i><br>
                                <p class="font-weight-bold"><span>Learn</span></p>
                                <p class="">Need more information about the ways to volunteer with PECSF.</p>
                                <p></p>
                            {{-- </span> --}}
                            </div>

                        </div>
                    </a>
                    </div>
                    <div class="col col-md-6">
                        <a href="#" data-toggle="modal" data-target="#training-guide-modal">
                    {{-- <a href="{{route('volunteering.training')}}"> --}}
                        <div class="card px-2 d-table" style="height: 100%">
                            <div class="card-body d-table-cell align-middle text-center">
                                <i class="x nav-icon fas fa-graduation-cap fa-2x"></i>
                                <p class="font-weight-bold"><span >Training</span></p>
                                <p class="" >Be prepared for your volunteer role by completing PECSF training.  </p>
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
                                    <i class="nav-icon fas fa-money-check-alt fa-2x "></i><br>
                                    <p class="font-weight-bold "><span>eForm</span></p>
                                    <p >Submit for your cash, cheque, fundraising or gaming bank deposit form.</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col col-md-6">
                        <a href="#" data-toggle="modal" data-target="#communication-guide-modal">
                        {{-- <a href="{{route('volunteering.communication')}}"> --}}
                            <div class="card px-2 d-table" style="height: 100%">
                                <div class="card-body d-table-cell align-middle text-center">
                                    <i class="nav-icon fas fa-comments fa-3x "></i><br>
                                    <p class="font-weight-bold"><span>Communications / Resources</span></p>
                                    <p class="">Need additional information or help? We are here to help! </p>
                                    <p class="p-1"></P>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>


        <div class="col col-md-4" style=" {{ (!$campaignYear->isVolunteerRegistrationOpen() && (!$last_year_profile)) ? 'display:none;' : '' }} ">
            <div class="container h-100">

                @if ($campaignYear->isVolunteerRegistrationOpen() )
                    @if ($profile)
                        <a href="{{route('volunteering.profile.show', $profile->id)}}">
                    @elseif ($last_year_profile) 
                        <a href="{{route('volunteering.profile.create')}}">
                    @else
                        <a href="{{route('volunteering.profile.create')}}">
                    @endif
                @else
                    @if ($profile)
                        <a href="{{route('volunteering.profile.show', $profile->id)}}">
                    @elseif ($last_year_profile)
                        <a href="{{route('volunteering.profile.show', $last_year_profile->id)}}">
                    @else 
                        
                    @endif
                @endif
                <div class="card card_hook d-table" style="height: 100%; width: 100%;">
                    <div class="card-body d-table-cell align-middle text-center">
                        {{-- <img src="/svgs/give.svg" style="color:white;" alt="Connect" height="55"> --}}
                        <i class="nav-icon fa fa-user-edit fa-9x pb-3"></i><br>
                        @if ($campaignYear->isVolunteerRegistrationOpen() )
                            @if ($profile)
                                <p class="font-weight-bold"> <span>Profile</span></p>
                                <p>View or edit your PECSF volunteer profile.</p>
                            @elseif ($last_year_profile) 
                                <p class="font-weight-bold"> <span>Renew</span></p>
                                <p>Renew your volunteer commitment for the fall campaign.</p>
                            @else
                                <p class="font-weight-bold"> <span>Register</span></p>
                                <p>Become a PECSF volunteer for your organization today.</p>
                            @endif
                        @else
                            @if ($profile)
                                <p class="font-weight-bold"> <span>Profile</span></p>
                                <p>View PECSF volunteer profile.</p>
                            @elseif ($last_year_profile) 
                                <p class="font-weight-bold"> <span>Last Year Profile</span></p>
                                <p>View your Last Year PECSF volunteer profile.</p>
                            @else 
                                <p class="font-weight-bold"> <span>TBD</span></p>
                                <p>Registration is not open yet</p>
                            @endif
                        @endif
                    </div>
                </div>
                @if ($campaignYear->isVolunteerRegistrationOpen() || ($last_year_profile)) 
                    </a>
                @endif
            </div>
        </div>

    </div>
</div>

@include('volunteering.partials.learn-more-modal')
@include('volunteering.partials.training-guide-modal')
@include('volunteering.partials.communication-guide-modal')

@endsection

@push('css')
<style>
    .home-tiles .card:not(.is-closed) {
        border-radius: 12px;
        border: 2px solid #b2b3c533;
        background-color: #ffffff;
    }

    .home-tiles .card:not(.is-closed):hover {
        background-color: #1a5a96;
        color: white;
    }

    .home-tiles .card:not(.is-closed):hover i {
      text-decoration-color: white;
      color:white;
    }

    .home-tiles .card span {
      font-size: 22px;
    }

    .home-tiles a:hover {
        color: white;
    }

</style>
@endpush


@push('js')
<script>

</script>
@endpush
