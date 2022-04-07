@extends('adminlte::page')
@section('content_header')
    <div class="d-flex mt-3">
        <h1>PECSF Volunteering</h1>
        <div class="flex-fill"></div>
    </div>
@endsection
@section('content')
@if($user->isVolunteer())
    @include('volunteering.partials.statistics')
@else
    @include('volunteering.partials.no-statistics')
@endif
@include('volunteering.partials.overall-graph')
<div class="card">
    <div class="card-body">
        @include('volunteering.partials.tabs')
        @if($user->isVolunteer())
            <div class="card">
                <div class="card-body">
                    <strong>
                        Event Coordinator Training Session 1
                    </strong>
                    <div>
                        📅 Monday, May 2nd 2022 | 
                        ⏰ 10.00 am 12.30 pm PST | 
                        📍 <a href="#">https://teams.microsoft.com/dl/lancher/launcherread</a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <strong>
                        Event Coordinator Training Session 2
                    </strong>
                    <div>
                        📅 Monday, June 2nd 2022 | 
                        ⏰ 10.00 am 12.30 pm PST | 
                        📍 <a href="#">https://teams.microsoft.com/dl/lancher/launcherread</a>
                    </div>
                </div>
            </div>
        @else
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
        @endif
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
    $("#volunteer-registration").on('click', '[name=no_of_years_opt_out]', function (e) {
        $("#volunteer-registration").find("[name=no_of_years]").prop('required', !this.checked);
    });
    $("#volunteer-registration").on('click', '[name=address_type]', function (e) {
        const isRequired = $("#volunteer-registration").find("[type=radio][name=address_type]:checked").val() === 'new';
            $("#volunteer-registration").find("[name=new_address]").prop('required', isRequired);
        
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
        } else if (e.to == 5) {
            $(this).find(".prev-btn").removeClass("d-none");
            $(this).find(".finish-btn").removeClass("d-none");
            $(this).find(".signup-btn").addClass("d-none");
            $(this).find(".next-btn").addClass("d-none");
        }  else if (e.to == 6) {
            $(this).find(".finish-btn").addClass("d-none");
            $(this).find(".signup-btn").removeClass("d-none");
            $(this).find(".prev-btn").addClass("d-none");
        } else {
            $(this).find(".prev-btn").removeClass("d-none");
            $(this).find(".next-btn").removeClass("d-none");
            $(this).find(".signup-btn").addClass("d-none");
            $(this).find(".finish-btn").addClass("d-none");
        }
        
        const no_of_years = $("#volunteer-registration").find("[type=checkbox][name=no_of_years_opt_out]").prop('checked') ? 'opt-out' : $("#volunteer-registration").find("[type=text][name=no_of_years]").val();
        const address_type = $("#volunteer-registration").find("[type=radio][name=address_type]:checked").val();
        


        $("#summary-table").find('[data-value-for="organization"]').html($("#volunteer-registration").find("[name=organization_id] option:selected").text());
        $("#summary-table").find('[data-value-for="no_of_years"]').html(no_of_years);
        $("#summary-table").find('[data-value-for="address_type"]').html(address_type);
        $("#summary-table").find('[data-value-for="preferred_role"]').html($("#volunteer-registration").find("[name=preferred_role] option:selected").text());
    });

    $('.signup-btn').on('click', function () {
        window.location.reload();
    });
    let registrationUnderProcess = false;
    $('.finish-btn').on('click', function () {
        if (registrationUnderProcess) {
            return;
        }
        const form = $('#volunteer_registration_form').get(0);
        registrationUnderProcess = true;
        $.ajax({
            type: "POST",
            url: form.action,
            data: $(form).serialize(),
            success: function (response) {
                // Silent
            },
            error: function (res) {
                alert('something wrong!');
                console.error(res);
            },
            complete: function () {
                registrationUnderProcess = false;
            }
        });
    });

</script>
@endpush
@endsection
