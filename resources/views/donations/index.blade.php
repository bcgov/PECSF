@extends('adminlte::page')
@section('content_header')

    {{-- Show when Campaign Year Open --}}
    <div class="d-flex justify-content-center pt-3">
        <div class="card border-warning bg-success text-center" style="max-width: 50em; border-radius: 1em;">
            <div class="card-body">
                <h5 class="card-title"></h5>
                @if ( $campaignYear->isOpen() ) 
                    <p class="card-text text-left text-white">
                        From {{ $campaignYear->start_date->format('F jS') }} - {{ $campaignYear->end_date->format('F jS') }} we are in a period of open enrolment for the PECSF Campaign.
                        The choices you make and save by end of day {{ $campaignYear->end_date->format('F jS')}} will begin with your first pay period in January. 
                    </p>
                    @if ( count($cyPledges) > 0)
                        <p class="card-text text-left text-white">
                            To make changes to your proposed pledge, click into the box below where your 2023 choices are shown. 
                        </p>
                        <a href="{{ route('donate.edit') }}" class="btn btn-primary">Make change to your proposed pledge</a>
                    @else 
                        <a href="{{ route('donate') }}" class="btn btn-primary">Donate to PECSF Now</a>
                    @endif
                @else
                        <p class="card-text text-left text-white">
                            The Fall campaign has closed, to make changes to your PECSF pledge please email PECSF@gov.bc.ca
                        </p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="d-flex mt-3">
        <h1>My Donations</h1>
        @if($pledges->count() > 0)
            <div class="flex-fill"></div>
            @if (!$campaignYear->isOpen() ) 
                <x-button :href="route('donate')">Donate to PECSF Now</x-button>
            @endif
            <x-button style="outline-primary" class="ml-2" data-toggle="modal" data-target="#learn-more-modal" >Why donate to PECSF?</x-button>
        @endif
    </div>
    <div class="d-flex flex-column">
        <p class="m-0">
            Since you started giving* through PECSF, you've donated {{$totalPledgedDataTillNow}}, as BC Public Servent.
        </p>
        <small>reflects pledge totals from 2005 onwards</small>
    </div>
@endsection
@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-center justify-content-lg-start mb-2" role="tablist">
            <div class="px-4 py-1 mr-2 border-bottom border-primary">
                <x-button role="tab" href="#" style="">
                    Donation History
                </x-button>
            </div>
        </div>
        @if($pledges->count() > 0)
            @include('donations.partials.history')
        @else
        <div class="text-center text-primary">
            <p>
                <strong>No Campaign has been started yet.</strong>
            </p>
            <p>
                You do not have any active campaigns right now. <br>
                Click on one of the options below to get started!
            </p>
            <x-button :href="route('donate')">Donate to PECSF Now</x-button>
            <p class="pt-3">
                OR
            </p>
            <x-button style="link" data-toggle="modal" data-target="#learn-more-modal">Learn more about donating to PECSF.</x-button>
        </div>
        @endif
    </div>
</div>

@include('donations.partials.learn-more-modal')

@push('js')
<script>
    $('#learn-more-modal').on('slide.bs.carousel', function (e) {
        if(e.to == 0) {
            $(this).find(".prev-btn").addClass("d-none");
        }
        else if (e.to === 5) {
            $(this).find(".next-btn").addClass("d-none");
            $(this).find(".ready-btn").removeClass("d-none");
        } else {
            $(this).find(".prev-btn").removeClass("d-none");
            $(this).find(".next-btn").removeClass("d-none")
            $(this).find(".ready-btn").addClass("d-none");
        }
    })
</script>
@endpush
@endsection