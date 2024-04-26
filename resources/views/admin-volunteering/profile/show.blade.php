@extends('adminlte::page')

@section('content_header')

@include('admin-volunteering.partials.tabs')

    <h4 class="mx-1 mt-3">Review a Volunteer Profile</h4>

    <div class="mx-1 pt-2">
        <button class="btn btn-outline-primary" onclick="window.location.href='{{ route('admin-volunteering.profile.index') }}'">
            Back    
        </button> 
    </div>

@endsection

@section('content')

<div class="card pb-4">
  <div class="card-body py-0">

        <div class="d-flex  align-items-center my-2">
            <h4><b>Transaction ID: </b><span>{{ $profile->id }}</span></h4>
            {{-- @if ($profile->cancelled)
                <h4 class="ml-3 border border-danger rounded p-2 text-danger font-weight-bold">Cancelled</h4>
            @endif
            <div class="flex-fill"></div>

            <div class="d-flex  align-items-center ">
                <div class="mr-2">
                </div>
            </div> --}}
        </div>

        <div class="card m-0 mt-3">
            <div class="card-header bg-primary">
                <p class="h5">Campaign Year</p>
            </diV>
            <div class="card-body">
                <div class="row">
                    <div class="form-group ">
                        <label for="campaign_year">Campaign Year</label>
                        <select id="campaign_year" class="form-control" name="campaign_year" style="max-width:200px;" readonly>
                            <option value="{{ $profile->calendar_year }}" selected>{{ $profile->campaign_year }}</option>
                        </select>
                    </div>
                </div>
            </div>
          </div>

        <div class="card m-0 pb-3">
            <div class="card-header bg-primary">
                <div class="h5">Employee Information</div>
            </div>
            <div class="card-body ">
            @if( $profile->organization_code == 'GOV' )
                <div class="form-row">
                    <div class="col-md-3 mb-3">
                        <label for="">Organization</label>
                        <input type="text" class="form-control" value="{{ $profile->organization->name }}" disabled>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="emplid">Employee ID</label>
                        <input id="emplid" type="text" class="form-control" value="{{ $profile->emplid }}" disabled>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="employee_region">Region</label>
                        <input id="employee_region" type="text" class="form-control" 
                            value="{{ $profile->primary_job->city_by_office_city->region->name . ' (' . $profile->primary_job->city_by_office_city->region->code . ')'  }}" disabled>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="employee_dept">Department</label>
                        <input id="employee_dept" type="text" class="form-control" 
                            value="{{ $profile->primary_job->deptid }}" disabled>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="user_first_name">First name</label>
                        <input type="text" class="form-control" value="{{ $profile->primary_job->first_name }}" disabled>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="user_last_name">Last name</label>
                        <input type="text" class="form-control" value="{{ $profile->primary_job->last_name }}" disabled>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="user_email">Email</label>
                        <input type="text" class="form-control"  value="{{ $profile->primary_job->email }}" disabled>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="user_bu">Business Unit</label>
                        <input type="text" class="form-control" value="{{ $profile->primary_job->bus_unit->name }}" disabled>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="user_org">Organization</label>
                        <input type="text" class="form-control" value="{{ $profile->primary_job->organization }}" disabled>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="user_office_city">Office City</label>
                        <input type="text" class="form-control" value="{{ $profile->primary_job->office_city ?? ''  }}" disabled>
                    </div>
                </div>
            @else
                <div class="form-row">
                    <div class="col-md-3 mb-3">
                        <label for="">Organization</label>
                        <input type="text" class="form-control" value="{{ $profile->organization->name }}" disabled>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="">PECSF ID</label>
                        <input type="text" class="form-control" value="{{ $profile->pecsf_id }}" disabled>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-3 mb-3">
                        <label for="">First name</label>
                        <input type="text" class="form-control" value="{{ $profile->first_name }}" disabled>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="">Last name</label>
                        <input type="text" class="form-control" value="{{ $profile->last_name }}" disabled>
                    </div>
                    {{-- <div class="col-md-3 mb-3">
                        <label for="">City</label>
                        <input type="text" class="form-control"  value="{{ $profile->city }}" disabled>
                    </div> --}}
                </div>
            @endif
            </div>
        </div>

        {{-- Volunteer Details --}}
        <div class="card m-0 mt-3 pb-0">
            <div class="card-header bg-primary">
                <div class="h5">Volunteer Details</div>
            </div>
            <div class="card-body ">

                <div class="form-row">
                    <div class="col-md-6">
                        <label for="busienss_unit">Business Unit</label>
                        <input id="busienss_unit" type="text" class="form-control" value="{{ $profile->business_unit->name }}" disabled>
                    </div>
                </div>
                <div class="form-row pt-3">
                    <div class="col-md-6">
                        <label for="">How many years have you been working with PECSF</label>
                        <input id="no_of_years" type="text" class="form-control" value="{{ $profile->no_of_years }}" disabled>
                    </div>
                </div>
                <div class="form-row pt-3">
                    <div class="col-md-6">
                        <label for="preferred_role">Your Preferred Volunteer Role</label>
                        <input id="preferred_role" type="text" class="form-control"  value="{{ $profile->preferred_role_name }}" disabled>
                    </div>
                </div>

            </div>
        </div>

        {{-- Recognition Items  --}}
        <div class="card m-0 mt-3 pb-3">
            <div class="card-header bg-primary">
                <div class="h5">Recognition Items</div>
            </div>

            <div class="card-body ">
               
                    {{-- Global Address --}}
                    <div class="form-row">
                        <div class="form-group col-12 pt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="address_type" id="address_type_1" value="G" 
                                    {{ ($profile->address_type == 'G')  ? 'checked' : '' }} disabled>
                                <label class="form-check-label font-weight-bold" for="address_type_1">
                                    Use the Global Address Listing
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="pl-4" id="other_address_area" style="{{ ($profile->address_type == 'G') ? '' : 'display:none;' }}"> 
                        <div class="form-row">
                            <div class="form-group col-12">
                                <input id="address" name="address" type="text" value="{{ $profile->full_address }}" class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    {{-- Other Address --}}
                    <div class="form-row">
                        <div class="form-group col-12 ">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="address_type" id="address_type_2" value="S"
                                    {{ ($profile && $profile->address_type != 'G') ? 'checked' : '' }} disabled>
                                <label class="form-check-label font-weight-bold" for="address_type_2">
                                    Use the following address:
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="pl-4 pt-1" id="other_address_area" style="{{ ($profile->address_type == 'G') ? 'display:none;' : '' }}"> 
                        <div class="form-row">
                                <div class="form-group col-12">
                                    <label for="address">Street address</label>
                                    <input id="address" name="address" type="text" value="{{ $profile->address }}" class="form-control" disabled>
                                </div>
                        </div>
                
                        <div class="form-row">
                            <div class="form-group col-5">
                                <label for="city">City</label>
                                <input id="city" name="city" type="text" value="{{ $profile->city }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-4">
                
                                <label for="province">Province</label>
                                <input id="province" name="province" type="text" value="{{ $profile->province }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="postal_code">Postal Code</label>
                                <input type="text" class="form-control" name="postal_code" value="{{ $profile->postal_code }}" disabled>
                            </div>
                        </div>
                
                    </div>   

                    <div class="form-group col-12 pt-3">
                        <hr>
                    </div>
                    
                    <div class="form-group col-12">
                        <label>
                            <input id="opt_out_recongnition" name="opt_out_recongnition" 
                                type="checkbox" {{ ($profile->opt_out_recongnition == "Y") ? "checked":""}}  
                                    name="opt_out_recongnition" value="Y" {{ ($profile->opt_out_recongnition == 'Y') ? 'checked' : '' }} disabled>
                            <span class="pl-2">I wish to opt-out from receiving recognition items.</span>
                        </label>
                    </div>
                    
            </div>
        </div>

        <div class="card m-0 mt-3 pb-3">
            <div class="card-header bg-primary">
                <div class="h5">Audit Information</div>
            </div>
            <div class="card-body ">
                <div class="row no-gutters">
                    <div class="col-3">
                        <p><b>Created by:</b>
                            {{ $profile->created_by->name }} </p>
                    </div>
                    <div class="col-3">
                        <p><b>Created at:</b>
                            {{ date_timezone_set($profile->created_at, timezone_open('America/Vancouver')) }}
                               </p>
                    </div>
                  </div>

                <div class="row">
                  <div class="col-3">
                    <p><b>Modified by:</b>
                        {{ isset($profile->updated_by) ? $profile->updated_by->name : ''}} </p>
                  </div>
                  <div class="col-3">
                    <p><b>Modified at:</b>
                        {{ date_timezone_set($profile->updated_at, timezone_open('America/Vancouver')) }} </p>
                  </div>
                </div>

            </div>
        </div>

  </div>
</div>
@endsection




