@extends('adminlte::page')
@section('content_header')
    <div class="d-flex mt-3">
        <h1>PECSF Volunteering</h1>
        <div class="flex-fill"></div>
        <x-button data-toggle="modal" data-target="#volunteer-registration">Register as a Volunteer</x-button>
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
            <x-button data-toggle="modal" data-target="#volunteer-registration">Register as a Volunteer</x-button>
            <p class="pt-3">
                OR
            </p>
            <x-button style="link" data-toggle="modal" data-target="#learn-more-modal">Learn more about volunteering with PECSF.</x-button>
        </div>
    </div>
</div>

@include('volunteering.partials.learn-more-modal')
@include('volunteering.partials.registration-modal')
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
    });
    $('#volunteer-registration').on('slide.bs.carousel', function (e) {
        const activeStep = Number.parseInt($(e.relatedTarget).data('step')) - 1;
        const requiredQuestion = $("#volunteer-registration-carousel").find('.carousel-item.active').find("[required]");
        if (requiredQuestion && requiredQuestion.length && requiredQuestion.val() === '') {
            alert("Please fill the mandatory fields to proceed");
            return false;
        }
        $(".formsteps .step").each((index, e) => {
            $(e).removeClass("active").removeClass("done");
            if (activeStep === index) {
                $(e).addClass("active");
            } else if (index < activeStep) {
                $(e).addClass("done");
            }
        });
        $(".formsteps .divider").each((index, e) => {
            $(e).removeClass("done");
            if (index < activeStep) {
                $(e).addClass("done");
            }
        });
        if(e.to == 0) {
            $(this).find(".prev-btn").addClass("d-none");
        } else if (e.to == 4) {
            $(this).find(".prev-btn").removeClass("d-none");
            $(this).find(".finish-btn").removeClass("d-none");
            $(this).find(".signup-btn").addClass("d-none");
            $(this).find(".next-btn").addClass("d-none");
        }  else if (e.to == 5) {
            $(this).find(".finish-btn").addClass("d-none");
            $(this).find(".signup-btn").removeClass("d-none");
        } else {
            $(this).find(".prev-btn").removeClass("d-none");
            $(this).find(".next-btn").removeClass("d-none");
            $(this).find(".signup-btn").addClass("d-none");
            $(this).find(".finish-btn").addClass("d-none");
        }
    });

</script>
@endpush
@endsection
