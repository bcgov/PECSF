@extends('adminlte::page')
@section('content_header')
    <div class="d-flex mt-3">
        <h1>PECSF Volunteering</h1>
        <div class="flex-fill"></div>
        <x-button href="#">Register as a Volunteer</x-button>
    </div>
@endsection
@section('content')
<div class="card">
    <div class="card-body">
        @include('volunteering.partials.tabs')
        <div class="text-center text-primary">
            <p class="mt-5">
                <strong>No Events to Display</strong>
            </p>
            <p>
                You do not have any active campaigns right now. <br>
                Click on one of the options below to get started!
            </p>
            <x-button href="#">Register as a Volunteer</x-button>
            <p class="pt-3">
                OR
            </p>
            <x-button style="link" data-toggle="modal" data-target="#learn-more-modal">Learn more about volunteering with PECSF.</x-button>
        </div>
    </div>
</div>

@include('volunteering.partials.learn-more-modal')

@push('js')
<script>
    $('#learn-more-modal').on('slide.bs.carousel', function (e) {
        if(e.to == 0) {
            $(this).find(".prev-btn").addClass("d-none");
        }
        else if (e.to === 6) {
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
