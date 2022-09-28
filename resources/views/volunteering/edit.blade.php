@extends('adminlte::page')
@section('content_header')
    <div class="d-flex mt-3">
        <h1>Volunteer Profile</h1>
        <div class="flex-fill"></div>
    </div>
@endsection
@section('content')

    @if($is_registered)
        <form action="{{route('volunteering.update')}}" method="POST" id="volunteer_registration_form">
            @csrf

        <div class="card p-4 mt-4">
            <h1 class="text-primary">Volunteer Details</h1>
            <div class="row">
                <div class="col-12 col-md-6 ">

                    <div class="step-1 ">
                        <p class="">
                            <strong>Your Organization</strong>
                        </p>
                        <div class="organization_id_error">

                        </div>
                        <select name="organization_id" id="" class="form-control" required>
                            <option value="">Please select</option>
                            @foreach($organizations as $org)
                                <option {{$is_registered->organization_id == $org->id? "selected":""}} value="{{$org->id}}">{{$org->name}}</option>
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

            <h1 class="text-primary mt-4">Recognition Items</h1>
            <div class="row text-left mt-4">
                <div class="col">
                    <label>
                        <input type="radio"
                               @php
                               if(empty($global_address))
                                   {
                                       echo "disabled";
                                   }
                               @endphp

                               {{$is_registered->address_type == "Global" ? "checked":""}} id="globalOption" name="address_type" value="Global">
                        <input type="hidden"  name="global_address" value="{{$global_address}}" />
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
                    <input name="new_address" type="text" value="{{explode(",",$is_registered->new_address)[0]}}" class="form-control" placeholder="">
                    <span class="new_address_error" class="text-danger"></span>

                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-4">
                    <label>City</label>
                    <select name="city" class="form-control">
                        <option value="">Select a City</option>
                        @foreach($cities as $city)
                            <option value="{{$city->city}}" {{ ((str_replace(" ","",strtolower(explode(",",$is_registered->new_address)[1])) == strtolower($city->city)) ? "selected" : "") }}>{{$city->city}}</option>
                        @endforeach
                    </select>
                    <span class="city_error" class="text-danger"></span>
                </div>


                <div class="col-md-4">
                    <label>Province</label>
                    <select class="form-control" name="province">
                        <option  value="">Select a Province</option>
                        <option value="Alberta">Alberta</option>
                        <option value="British Columbia">British columbia</option>
                        <option value="Manitoba">Manitoba</option>
                        <option value="New Brunswick">New brunswick</option>
                        <option value="Newfoundland and Labrador">Newfoundland and labrador</option>
                        <option value="Nova Scotia">Nova scotia</option>
                        <option value="Nunavut">Nunavut</option>
                        <option value="Prince Edward Island">Prince edward island</option>
                        <option value="Quebec">Quebec</option>
                        <option value="Saskatchewan">Saskatchewan</option>
                        <option value="Yukon">Yukon</option>
                        <option
                        value="Ontario">Ontario</option>
                    </select>
                    <span class="province_error" class="text-danger"></span>
                </div>


                <div class="col-md-4">
                    <label>Postal Code</label>
                    <input name="postal_code" value="{{$is_registered->address_type == "New" ? explode(",",$is_registered->new_address)[3] : ""}}" type="text" class="form-control" placeholder="">
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
                <a href="/profile"><button  style="color:#1a5a96;border:#1a5a96 1px solid;background:white;border-radius:3px;" class="btn cancel-btn">Cancel</button></a>
                &nbsp;
                <x-button class="save-btn">Save</x-button>
                &nbsp;
            </div>
        </div>
        </form>
        @push('js')
            <script>

                if($("#globalOption").is(":checked") || $("#optOut").is(":checked"))
                {
                    $("[name=province]").val('');
                    $("[name=city]").val('');
                    $("[name=postal_code]").val('');
                    $("[name=new_address]").val('');
                }
                else{
                    $("[name=province]").val('{{substr(ucfirst($province),0,strlen($province))}}');
                    $("[name=city]").val('{{substr(ucfirst($setcity),0,strlen($setcity))}}');
                }



               $('.btn').on('click', function (e) {
                if($(this).hasClass("cancel-btn")){
                    e.preventDefault();
                    window.location = $(this).parents("a").attr("href");
                }
                });

               $('.save-btn').on('click', function (e) {
                    e.preventDefault();
                   const form = $('#volunteer_registration_form').get(0);
                   $(".invalid-feedback").remove();
                   $.ajax({
                       type: "POST",
                       url: form.action,
                       data: $(form).serialize(),
                       success: function (response) {
                           // Silent
                          alert("You have successfully updated your PECSF volunteer registration ");
                          window.location = "/profile";
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

            </script>
        @endpush
    @endif
@endsection
