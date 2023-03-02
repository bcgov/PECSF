@extends('adminlte::page')
@section('content_header')
    <div class="d-flex mt-3">
        <h1>Dashboard</h1>
        <div class="flex-fill"></div>
    </div>
@endsection
@section('content')
    @if($is_registered && $show)
        <div class="modal fade" id="edit-event-modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-charity-name text-light" id="charity-modal-label">Renew Volunteer Registration</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="edit-event-modal-body">
                           <section id="1" >

                               <br>
                               <div class="row">
                                   <div style="background:#d9e9f6;" class="col-md-12 alert alert-info text-grey-dark text-bold" role="alert">
                                       <i  class="text-gray-dark fas fa-info-circle fa-1x bottom-right" ></i> Please take a moment to review the information below. To update your details, Click 'Edit'
                                   </div>
                               </div>
                               <div class="row justify-content-start">
                                   <div class="">
                                       <button onclick="$('#2').show();$('#volunteer_registration_form').show();$('#1').hide();" class="btn btn-primary edit justify-content-end">Edit</button>
                                   </div>
                                   &nbsp;
                                   <div class="">
                                       <button onclick="$('#address_type').html($('[data-value-for=address_type]').first().text());$('#preferred_role').html($('[data-value-for=preferred_role]').first().text());$('#no_of_years').html($('[data-value-for=no_of_years]').first().text());$('#organization_final').html($('[data-value-for=organization]').first().text());$('#3').hide();$('#2').hide();$('#1').hide();$('#4').show();$('#volunteer_registration_form').show();" class="btn btn-primary justify-content-end">If the information is correct, click “Renew Registration"</button>
                                   </div>
                               </div>
                               <br>
                               <div class="row">
                                   <div class="card p-3 mt-1 col-md-12 justify-content-center" style="background:#f2f2f2;">
                                       <h1 class="text-primary">Your Details</h1>
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

                                       <h1 class="text-primary mt-4">Mailing Address</h1>

                                       <div class="d-flex">
                                           <div data-value-for="address_type">
                                               {{$is_registered->address_type == "Opt-out" ? "Opt-out" : $is_registered->new_address}}
                                           </div>
                                       </div>
                                   </div>
                               </div>

                               <div class="row">
                                   <p>Personal information collected through this registration process is collected by the BC Public Service Agency for the purpose of facilitating PECSF volunteering recognition activities and program improvements under section 26 (c) and (e) of the Freedom of Information and Protection of Privacy Act</p>
                                   <p>Questions about the collection of your personal information can be directed to the Campaign Manager, Provincial Employees Community Services fund at 250 356-1736 or <a href="mailto:PECSF@gov.bc.ca">PECSF@gov.bc.ca</a></p>
                               </div>
                           </section>




                            <form style="display:none;" action="{{route('volunteering.update')}}" method="POST" id="volunteer_registration_form">

                                @csrf

                                <div class="card p-4 mt-4">
                                    <section style="display:none;" id="2">
                                    <h1 class="text-primary">Volunteer Details</h1>
                                    <div class="row">
                                        <div class="col-12 col-md-6 ">

                                            <div class="step-1 ">
                                                <p class="">
                                                    <strong>Your Organization</strong>
                                                </p>
                                                <div class="organization_id_error">

                                                </div>
                                                <select name="business_unit_id" id="organization" class="form-control" required>
                                                    <option value="">Please select</option>
                                                    @foreach($organizations as $org)
                                                        <option {{$is_registered->business_unit_id == $org->id? "selected":""}} value="{{$org->id}}">{{$org->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-12 col-md-6">
                                            <div class="step-1">
                                                <div class="step-1">
                                                    <strong>
                                                        How many years have you been working with PECSF
                                                    </strong>
                                                    <div class="no_of_years_error">

                                                    </div>
                                                    <select   name="no_of_years" id="" class="form-control" required>
                                                        <option value="">Please select</option>
                                                        @php
                                                            for($i=0;$i<51;$i++){
                                                                echo '<option '.(($is_registered->no_of_years == $i) ? "selected" : " ").' value="'.$i.'">'.$i.'</option>';
                                                            }
                                                        @endphp
                                                    </select>

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-12 col-md-6">
                                            <div class="step-1">
                                                <strong>                            Your Preferred Volunteer Role
                                                </strong>
                                                <div class="preferred_role_error">

                                                </div>
                                                <select name="preferred_role" id="" class="form-control" required>
                                                    <option value="">Please Select</option>
                                                    <option value="Canvasser" {{$is_registered->preferred_role == "Canvasser" ? "selected":""}}>Canvasser</option>
                                                    <option value="Lead Coordinator" {{$is_registered->preferred_role == "Lead Coordinator" ? "selected" :""}}>Lead Coordinator</option>
                                                    <option value="Office Contact" {{$is_registered->preferred_role == "Office Contact" ? "selected":""}}>Office Contact</option>
                                                    <option value="Event Planner" {{$is_registered->preferred_role == "Event Planner" ? "selected":""}}>Event Planner</option>
                                                </select>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <a target="_blank" href="https://www2.gov.bc.ca/gov/content/careers-myhr/about-the-bc-public-service/corporate-social-responsibility/pecsf/volunteer" class="text-primary text-bold mt-4" style="text-decoration:underline;">Learn more about available volunteer roles with PECSF</a>
                                        </div>
                                    </div>

                                        <div class="row justify-content-start mt-2">
                                           <a onclick="$('section#2').hide();$('section#3').show();" class="btn btn-primary">Next</a>
                                        </div>
                            </section>
                                    <section style="display:none;" id="3">
                                        <h1 class="text-primary mt-4">Recognition Items</h1>
                                        <div class="row text-left mt-4">
                                            <div class="col">
                                          <div class="address_type_error"></div>
                                                <label>
                                                    <input type="radio"
                                                           @php
                                                               if(empty($global_address))
                                                                   {
                                                                       echo "disabled";
                                                                   }
                                                           @endphp

                                                           {{$is_registered->address_type == "Global" ? "checked":""}} id="globalOption" name="address_type" value="Global">
                                                    <input type="hidden"  name="global_address" value="{{$global_address->address1.", ".$global_address->city.", ".$global_address->stateprovince.", ".$global_address->country.", ".$global_address->postal}}" />
                                                    Use my Global Address Listing
                                                </label>
                                            </div>
                                        </div>
                                        <div class="row text-left mt-4">
                                            <div class="col">
                                                <label>
                                                    <input {{$is_registered->address_type == "New" ? "checked":""}} type="radio" name="address_type" value="New">
                                                    Use the following address:
                                                </label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label>Street address</label>
                                                <input name="new_address" type="text" value="
                                                 @if(is_array(explode(",",$is_registered->new_address)))
                                                    @php
                                                        echo explode(",",$is_registered->new_address)[0];
                                                    @endphp
                                                   @else
                                                   @php
                                                        echo $is_registered->new_address;
                                                @endphp
                                                @endif
                                                " class="form-control" placeholder="">
                                                <span class="new_address_error" class="text-danger"></span>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-4">
                                                <label>City</label>
                                                <select name="city" class="form-control">
                                                    <option value="">Select a City</option>
                                                    @foreach($cities as $city)
                                                        @if(!empty($is_registered))
                                                            @if(is_array($is_registered->new_address))
                                                                <option value="{{$city->city}}" {{ ((str_replace(" ","",strtolower(explode(",",(array_key_exists(1,$is_registered->new_address) ? $is_registered->new_address[1] : "")))) == strtolower($city->city)) ? "selected" : "") }}>{{$city->city}}</option>
                                                            @else
                                                                <option value="{{$city->city}}" >{{$city->city}}</option>
                                                            @endif
                                                        @else
                                                            <option value="{{$city->city}}">{{$city->city}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <span class="city_error" class="text-danger"></span>
                                            </div>


                                            <div class="col-md-4">
                                                <label>Province</label>
                                                <select class="form-control" name="province">
                                                    <option  value="">Select a Province</option>

                                                    <option  {{$is_registered->province == "Alberta" ? "selected":""}} value="Alberta">Alberta</option>
                                                    <option  {{$is_registered->province == "British Columbia" ? "selected":""}} value="British Columbia">British columbia</option>
                                                    <option  {{$is_registered->province == "Manitoba" ? "selected":""}} value="Manitoba">Manitoba</option>
                                                    <option  {{$is_registered->province == "New Brunswick" ? "selected":""}} value="New Brunswick">New brunswick</option>
                                                    <option  {{$is_registered->province == "Newfoundland and Labrador" ? "selected":""}} value="Newfoundland and Labrador">Newfoundland and labrador</option>
                                                    <option  {{$is_registered->province == "Nova Scotia" ? "selected":""}} value="Nova Scotia">Nova scotia</option>
                                                    <option  {{$is_registered->province == "Nunavut" ? "selected":""}} value="Nunavut">Nunavut</option>
                                                    <option  {{$is_registered->province == "Prince Edward Island" ? "selected":""}} value="Prince Edward Island">Prince edward island</option>
                                                    <option  {{$is_registered->province == "Quebec" ? "selected":""}} value="Quebec">Quebec</option>
                                                    <option  {{$is_registered->province == "Saskatchewan" ? "selected":""}} value="Saskatchewan">Saskatchewan</option>
                                                    <option  {{$is_registered->province == "Yukon" ? "selected":""}} value="Yukon">Yukon</option>
                                                    <option  {{$is_registered->province == "Ontario" ? "selected":""}} value="Ontario">Ontario</option>
                                                </select>
                                                <span class="province_error" class="text-danger"></span>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Postal Code</label>
                                                <input name="postal_code" value="{{$is_registered->city}}" type="text" class="form-control" placeholder="">
                                                <span class="postal_code_error" class="text-danger"></span>
                                            </div>
                                        </div>
                                        <div class="row text-left mt-4">
                                            <div class="col">
                                                <label>
                                                    <input id="optOut" type="radio" {{$is_registered->address_type == "Opt-out" ? "checked":""}}  name="address_type" value="Opt-out">
                                                    I wish to opt-out from receiving recognition items.
                                                </label>
                                            </div>
                                        </div>
                                        <div class="row mt-5">
                                            <a  onclick="$('#2').show();$('#3').hide()" style="color:#1a5a96;border:#1a5a96 1px solid;background:white;border-radius:3px;" class="btn">Back</a>
                                              &nbsp;  <a onclick="$('[data-value-for=organization]').html($('#organization option:selected').text());$('#no_of_years').html($('[name=no_of_years] option:selected').first().text());$('#preferred_role').html($('[name=preferred_role] option:selected').first().text()); (($('[name=address_type]:checked').val() =='New') ? $('[data-value-for=address_type]').html($('[name=new_address]').val() + ',' + $('[name=city]').val() + ','+ $('[name=province]').val() + ',' + $('[name=postal_code]').val()) : $('[name=address_type]:checked').val() =='Global' ? $('[data-value-for=address_type]').html('Global') : $('[data-value-for=address_type]').html('Opt-Out'));if($('[name=address_type]:checked').val() == 'New'){($('[name=new_address]').val().length < 1 ? $('.new_address_error').html('The street address field is required').css('color','red') :$('.new_address_error').html(''));($('[name=postal_code]').val().length < 1 ? $('.postal_code_error').html('The postal code field is required').css('color','red') :($('.postal_code_error').html('')));} if($('[name=postal_code]').val().length > 0 && $('[name=new_address]').val().length > 1 || $('[name=address_type]:checked').val() != 'New' ){$('#4').show();$('#3').hide();}" class="btn btn-primary">Save All Changes</a>
                                        </div>
                                    </section>
                                    <section id="4" style="display:none;">


                                  <h1>Confirmation</h1>
                                        <div class="row">
                                            <div style="background:#d9e9f6;" class="col-md-12 alert alert-info text-grey-dark text-bold" role="alert">
                                                <i  class="text-gray-dark fas fa-info-circle fa-1x bottom-right" ></i> Confirm your information is correct below and click  “Renew Registration” to complete.  If it is not correct, use the “back” button to make corrections.
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="card p-3 mt-1 col-md-12 justify-content-center" style="background:#f2f2f2;">
                                                <h1 class="text-primary">Your Details</h1>
                                                <div class="d-flex ">
                                                    <strong>
                                                        Your Organization
                                                    </strong>
                                                </div>
                                                <div class="d-flex">
                                                    <div id="organization_final" data-value-for="organization">

                                                    </div>
                                                </div>

                                                <div class="d-flex mt-2">
                                                    <strong>
                                                        Number of years you have been volunteering with PECSF
                                                    </strong>
                                                </div>
                                                <div class="d-flex">
                                                    <div id="no_of_years">

                                                    </div>
                                                </div>

                                                <div class="d-flex mt-2">
                                                    <strong>
                                                        Your preferred Volunteer Role
                                                    </strong>
                                                </div>
                                                <div class="d-flex">
                                                    <div id="preferred_role">

                                                    </div>
                                                </div>
                                                <a target="_blank" href="https://www2.gov.bc.ca/gov/content/careers-myhr/about-the-bc-public-service/corporate-social-responsibility/pecsf/volunteer" class="text-primary text-bold mt-4" style="text-decoration:underline;">Learn more about available volunteer roles with PECSF</a>

                                                <h1 class="text-primary mt-4">Mailing Address</h1>

                                                <div class="d-flex">
                                                    <div id="address_type" data-value-for="address_type">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <p>Personal information collected through this registration process is collected by the BC Public Service Agency for the purpose of facilitating PECSF volunteering recognition activities and program improvements under section 26 (c) and (e) of the Freedom of Information and Protection of Privacy Act</p>
                                            <p>Questions about the collection of your personal information can be directed to the Campaign Manager, Provincial Employees Community Services fund at 250 356-1736 or <a href="mailto:PECSF@gov.bc.ca">PECSF@gov.bc.ca</a></p>
                                        </div>
                                       <a  onclick="$('#3').show();$('#4').hide()" style="color:#1a5a96;border:#1a5a96 1px solid;background:white;border-radius:3px;" class="btn ">Back</a>
                                        <a class="save-btn btn btn-primary">Renew Registration</a>
                                    </section>

                                    <section style="display:none;" id="5" >
                                        <div class="row justify-content-center">
                                            <h3 class="text-primary">Volunteer Registration Renewed Successfully</h3>
<br>
                                            <h6>You have successfully renewed your registration as a volunteer with PECSF</h6>
                                        </div>
                                        <div class="row justify-content-center mt-5">
<i class="fa fas fa-check-circle fa-10x" style="color:green;"></i>
                                        </div>
                                        <div class="row justify-content-center mt-5">
                                            <a type="button" class="btn btn-primary" onclick="window.location.reload();" data-dismiss="modal" >Return Home</a>
                                        </div>
                                    </section>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
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
                    <button class="btn btn-primary" onclick="$('#edit-event-modal').modal('show');">Renew volunteer Registration</button>
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

    $('.save-btn').on('click', function (e) {
        e.preventDefault();
        const form = $('#volunteer_registration_form').get(0);
        $(".invalid-feedback").remove();
        $.ajax({
            type: "POST",
            url: form.action,
            data: $(form).serialize(),
            success: function (response) {
               $("#4").hide();
               $("#5").show();
               $('.modal-footer').hide();
               setTimeout(function()
               {
                   window.location.reload();
                   }
                   ,5000);
            },
            error: function (response) {
                if(response.responseJSON.errors) {
                    errors = response.responseJSON.errors;
                    for (const prop in response.responseJSON.errors) {
                        count = prop.substring(prop.indexOf(".") + 1);
                        tag = prop.substring(0, prop.indexOf("."));
                        error = errors[prop][0];
                        error = error.replace("_", " ");
                        $("." + prop + "_error").html('<span class="invalid-feedback">'+error+'</span>');
                    }
                }
                $(".invalid-feedback").show();
            },
            complete: function () {
                registrationUnderProcess = false;
            }
        });
    });


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
