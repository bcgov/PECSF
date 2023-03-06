@extends('adminlte::page')
@section('content_header')
    <div class="d-flex mt-3">
        <h1>Volunteer Profile</h1>
        <div class="flex-fill"></div>
    </div>
@endsection
@section('content')

    @if($is_registered)
        <a href="/volunteering/edit"><button class="btn btn-primary">Edit Your Information</button></a>
        <div class="card p-4 mt-4">
            <h1 class="text-primary">Volunteer Details</h1>
            <div class="d-flex ">
                <strong>
                    Your Organization
                </strong>
            </div>
            <div class="d-flex">
                <div data-value-for="organization">
                    {{$is_registered->name}}
                </div>
            </div>

            <div class="d-flex mt-2">
                <strong>
                    Number of years you have been volunteering with PECSF
                </strong>
            </div>
            <div class="d-flex">
                <div data-value-for="no_of_years">
                    {{$is_registered->no_of_years}}
                </div>
            </div>

            <div class="d-flex mt-2">
                <strong>
                    Your preferred Volunteer Role
                </strong>
            </div>
            <div class="d-flex">
                <div data-value-for="preferred_role">
                    {{$is_registered->preferred_role}}
                </div>
            </div>
            <a target="_blank" href="https://www2.gov.bc.ca/gov/content/careers-myhr/about-the-bc-public-service/corporate-social-responsibility/pecsf/volunteer" class="text-primary text-bold mt-4" style="text-decoration:underline;">Learn more about available volunteer roles with PECSF</a>

            <h1 class="text-primary mt-4">Recognition Items</h1>
            <div class="d-flex mt-1">
                <div>
                    <strong>Address for receiving recognition items</strong>
                </div>
            </div>
            <div class="d-flex">
                <div data-value-for="address_type">
                  {{$is_registered->address_type == "Opt-out" ? $is_registered->address_type : $is_registered->new_address}}
                </div>
            </div>
        </div>
        @else
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
                $(".error").hide();
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
              /*      if (requiredQuestion && requiredQuestion.length && requiredQuestion.val() === '') {
                        alert("Please fill the mandatory fields to proceed");
                        return false;
                    }
                    */
                    var stop = false;
                    $(".error").hide();
                    requiredQuestion.each((index,e) => {
                        if(e.value == "")
                        {
                            $("."+e.name+"_error").html(e.attributes[0].value);
                            $("."+e.name+"_error").show();
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
                    } else if (e.to == 2) {
                        fail = false;
                        if($("[name=address_type]:checked").val() == "New")
                        {
                            $("[name=city],[name=province],[name=postal_code],[name=street_address]").each(function(){
                                if($(this).val().length < 3){
                                    fail = true;
                                    $("#"+$(this).attr("name")+"_error").html("The "+$(this).attr("name").replace("_"," ")+" field is required");
                                }
                            });
                        }
                        if(fail){
                            e.preventDefault();
                        }
                        else{
                            $(this).find(".prev-btn").removeClass("d-none");
                            $(this).find(".finish-btn").removeClass("d-none");
                            $(this).find(".signup-btn").addClass("d-none");
                            $(this).find(".next-btn").addClass("d-none");

                        }
                    }  else if (e.to == 3) {
                        if($(".signup-btn").hasClass("d-none")){
                            e.preventDefault();
                            return;
                        }
                    }  else {
                        $(this).find(".prev-btn").removeClass("d-none");
                        $(this).find(".next-btn").removeClass("d-none");
                        $(this).find(".signup-btn").addClass("d-none");
                        $(this).find(".finish-btn").addClass("d-none");
                    }

                    const no_of_years =  $("#volunteer-registration").find("[name=no_of_years]").val();
                    const address_type = ($("#volunteer-registration").find("[type=radio][name=address_type]:checked").val() == "Opt-out")? "Opt-out" : $("[type=radio][name=address_type]:checked").val() == "Global" ? $("#global_address").val() : $("[name=street_address]").val() + ", " + $("[name=city]").val() + ", " + $("[name=province]").val()+", "+$("[name=postal_code]").val();
                    $("#summary-table").find('[data-value-for="business_unit_id"]').html($("#volunteer-registration").find("[name=business_unit_id] option:selected").text());
                    $("#summary-table").find('[data-value-for="no_of_years"]').html(no_of_years);
                    $("#summary-table").find('[data-value-for="address_type"]').html(address_type);
                    $("#summary-table").find('[data-value-for="preferred_role"]').html($("#volunteer-registration").find("[name=preferred_role] option:selected").text());
                });

                $('.signup-btn').on('click', function () {
                    window.location.reload();
                });

               $('.finish-btn').on('click', function () {

                    const form = $('#volunteer_registration_form').get(0);
                    registrationUnderProcess = true;
                    $.ajax({
                        type: "POST",
                        url: form.action,
                        data: $(form).serialize(),
                        success: function (response) {
                            // Silent
                            $(".finish-btn").addClass("d-none");
                            $(".signup-btn").removeClass("d-none");
                            $(".prev-btn").addClass("d-none");
                            $('#volunteer-registration').carousel("next");


                        },
                        error: function (res) {
                            alert(Object.values(res.responseJSON.errors)[0]);
                        },
                        complete: function () {
                            registrationUnderProcess = false;
                        }
                    });
                });

            </script>
        @endpush
        @endif


@endsection
