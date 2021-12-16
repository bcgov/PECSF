@extends('adminlte::page')
@section('content_header')
    <div class="d-flex mt-3">
        <h1>My Donations</h1>
        <div class="flex-fill"></div>
        <x-button :href="route('donate')">Donate to PECSF Now</x-button>
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
        @if(count($pledges) > 0)
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