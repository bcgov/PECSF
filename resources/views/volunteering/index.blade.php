@extends('adminlte::page')
@section('content_header')
    <div class="d-flex mt-3">
        <h1>Dashboard</h1>
        <div class="flex-fill"></div>
    </div>
@endsection
@section('content')

    @if($is_registered)
<div class="row">
    <div class="col-md-12 justify-content-center pt-3 mb-5">
        <div class="card justify-content-center border-warning text-center" style="background:#D9EAF7;border-radius: 1em;">
            <div class=" justify-content-center card-body" style="color:#1a5a96;">
                <h5 class="card-title"></h5>
                <p class="card-text text-center">It's time for you to renew your volunteer registration</p>
                <p class="card-text text-center">
                    Click below to make any necessary updates to your information
                </p>
                <p>
                    <button class="btn btn-primary">Renew volunteer Registration</button>
                </p>
            </div>
        </div>
    </div>
</div>


    @endif

    @include('volunteering.partials.statistics')
@include('volunteering.partials.overall-graph')

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
      /*  if (requiredQuestion && requiredQuestion.length && requiredQuestion.val() === '') {
            alert("Please fill the mandatory fields to proceed");
            return false;
        }*/
var stop = false;
        requiredQuestion.each((index,e) => {
            if(this.val() == "")
            {
                $("."+this.name+"_error").val(this.error);
                stop = true;
            }
        });

        if(stop){
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

        const no_of_years = $("#volunteer-registration").find("[type=checkbox][name=no_of_years_opt_out]").prop('checked') ? 'Opt-out' : $("#volunteer-registration").find("[type=text][name=no_of_years]").val();
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
