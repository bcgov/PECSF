@extends('adminlte::page')

@section('content_header')

@include('admin-volunteering.partials.tabs')

    <h4 class="mx-1 mt-3">{{ $is_new_profile ? 'Create' : 'Edit' }} a Volunteer Profile</h4>

    <div class="mx-1 pt-2">
        <button class="btn btn-outline-primary" onclick="window.location.href='{{ route('admin-volunteering.profile.index') }}'">
            Back
        </button>
    </div>

@endsection

@section('content')

<div class="card pb-4">
  <div class="card-body py-0">


    <form action="{{ $is_new_profile ?  route("admin-volunteering.profile.store") : route("admin-volunteering.profile.update", $profile->id) }}"
            id="admin-volunteering-profile-form" method="POST">
        @csrf
        @if (!($is_new_profile))
            @method('PUT')
            <input type="hidden" id="profile_id" name="profile_id" value="{{ $profile->id }}">
        @endif

        @if (!($is_new_profile))
            <div class="d-flex  align-items-center my-2">
                <h4>Transaction ID: </b>{{ $profile->id }}</h4>
                <div class="flex-fill"></div>

                {{-- <div class="d-flex  align-items-center ">
                    <div class="mr-2">
                        <button  class="cancel-pledge btn btn-outline-danger mr-2" >Cancel this transaction</button>
                    </div>
                </div> --}}
            </div>
        @endif

        <div class="card m-0 mb-3">
            <div class="card-header bg-primary">
                <p class="h5">Campaign Year</p>
            </diV>
            <div class="card-body">
                    <div class="row">
                        <div class="form-group ">
                            <label for="campaign_year">Campaign Year</label>
                            @if ($is_new_profile)
                                <select class="form-control" style="width:100%;" name="campaign_year" id="campaign_year">
                                    @foreach ($campaignYears as $value)
                                        <option value="{{ $value }}" 
                                            {{ $value == today()->year ? 'selected' : '' }}>
                                            {{ $value }}</option>
                                    @endforeach
                                </select>
                            @else 
                                <input type="text" class="form-control" name="campaign_year" 
                                    value="{{ $is_new_profile ? $campaign_year : $profile->campaign_year }}" readonly>    
                            @endif
                        </div>
                    </div>
            </div>
        </div>

        <div class="card m-0 pb-3">
            <div class="card-header bg-primary">
                <p class="h5">Employee Information</p>
            </div>
            <div class="card-body ">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="organization_id">Organization</label>
                            {{-- @isset($profile) --}}
                            @if ($is_new_profile)
                                <select class="form-control" style="width:100%;" name="organization_id" id="organization_id">
                                    @foreach ($organizations as $organization)
                                        <option value="{{ $organization->id }}" code="{{ $organization->code }}"
                                            {{ $organization->code == 'GOV' ? 'selected' : '' }}>
                                            {{ $organization->name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <select class="form-control" style="width:100%;" name="organization_id" id="organization_id" readonly>
                                    <option value="{{ $profile->organization->id }}"  code="{{ $profile->organization->code }}" selected>{{ $profile->organization->name }}</option>
                                </select>
                            @endif
                    </div>
                    <div class="form-group col-md-7 emplid_section">
                        <label for="emplid">Employee</label>
                        @if ($is_new_profile)
                            <select class="form-control select2" style="width:100%;" name="emplid" id="emplid">
                                {{-- <option value="" selected>-- choose user --</option> --}}
                            </select>
                        @else
                            {{-- <select class="form-control" name="emplid" id="emplid" readonly>
                                <option value="{{ $profile->emplid }}" selected>{{ $profile->emplid  }}</option>
                            </select> --}}
                            <input type="text" class="form-control" name="emplid" value="{{ $profile->emplid }}" readonly>
                        @endif
                        @error('emplid')
                            <span class="text-strong text-danger">{{  $message  }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-md-3 pecsf_id_section">
                        <label for="user">PECSF ID</label>
                        {{-- @if (isset($profile))
                            <input type="text" class="form-control" name="pecsf_id" id="pecsf_id" value="{{ $profile->pecsf_id }}" readonly>
                        @else  --}}
                            <input type="text" class="form-control" name="pecsf_id" id="pecsf_id" value="{{ $profile->pecsf_id }}" {{ $is_new_profile ? '' : 'readonly' }}>
                        {{-- @endif --}}
                    </div>

                </div>

                <div class="form-row pecsf_id_section">
                    <div class="col-md-3 mb-3">
                        <label for="pecsf_first_name">First Name</label>
                        <input type="text" class="form-control" id="pecsf_first_name" name="pecsf_first_name"
                            value="{{ old('pecsf_first_name') ?? ( isset($profile) ? $profile->first_name : '') }}" {{ $is_new_profile ? '' : 'readonly' }}>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="pecsf_last_name">Last Name</label>
                        <input type="text" class="form-control" id="pecsf_last_name" name="pecsf_last_name"
                            value="{{ old('pecsf_last_name') ?? ( isset($profile) ? $profile->last_name : '') }}" {{ $is_new_profile ? '' : 'readonly' }}>
                    </div>
                    {{-- <div class="col-md-3 mb-3">
                        <label for="pecsf_city">City</label>
                        @if ($is_new_profile )
                            <select class="form-control" style="width:100%;" name="pecsf_city" id="pecsf_city" >
                                <option value="">Select a City</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->city }}" {{ $city->city == old('pecsf_city') || ( ($profile) && $city->city == $profile->city) ? 'selected' : '' }}>
                                        {{ $city->city }}</option>
                                @endforeach
                            </select>
                        @else
                           <input type="text" class="form-control" id="pecsf_city" name="pecsf_city"
                              value="{{ ( isset($profile) ? $profile->city : '') }}" readonly>
                        @endif
                    </div> --}}
                </div>

                <div class="form-row emplid_section">
                    <div class="col-md-2 mb-3">
                        <label for="user_emplid">Employee ID</label>
                        <input type="text" class="form-control border-0" id="user_emplid"
                                value="{{ (isset($profile) && $profile) ? $profile->emplid : '' }}"
                            disabled>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label for="user_region">Region</label>
                        <input type="text" class="form-control border-0" id="user_region"
                                value="{{ (isset($profile) && $profile->primary_job) ? $profile->primary_job->city_by_office_city->region->name . ' (' . $profile->primary_job->city_by_office_city->region->code . ')'  : '' }}"
                             disabled>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label for="user_dept">Department</label>
                        <input type="text" class="form-control border-0" id="user_dept"
                                value="{{ (isset($profile) && $profile->primary_job) ? $profile->primary_job->dept_name . ' (' . $profile->primary_job->deptid . ')' : '' }}"
                            disabled>
                    </div>
                </div>
                <div class="form-row emplid_section">
                    <div class="col-md-4 mb-3">
                        <label for="user_first_name">First name</label>
                        <input type="text" class="form-control border-0" id="user_first_name"
                            value="{{ (isset($profile) && $profile->primary_job) ? $profile->primary_job->first_name : '' }}"
                            disabled>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="user_last_name">Last name</label>
                        <input type="text" class="form-control border-0" id="user_last_name"
                            value="{{ (isset($profile) && $profile->primary_job) ? $profile->primary_job->last_name : '' }}"
                            disabled>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="user_email">Email</label>
                        <input type="text" class="form-control border-0" id="user_email"
                            value="{{ (isset($profile) && $profile->primary_job) ? $profile->primary_job->email : '' }}"
                             disabled>
                    </div>
                </div>
                <div class="form-row emplid_section">
                    <div class="col-md-4 mb-3">
                        <label for="user_bu">Business Unit</label>
                        <input type="text" class="form-control border-0" id="user_bu"
                            value="{{ (isset($profile) && $profile->primary_job) ? $profile->primary_job->bus_unit->name . ' (' . $profile->primary_job->bus_unit->code . ')' : '' }}"
                            disabled>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="user_org">Organization</label>
                        <input type="text" class="form-control border-0" id="user_org"
                            value="{{ (isset($profile) && $profile->primary_job) ? $profile->primary_job->organization_name : '' }}"
                            disabled>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="user_office_city">Office City</label>
                        <input type="text" class="form-control border-0" id="user_office_city" name="user_office_city"
                            value="{{ (isset($profile) && $profile->primary_job) ? $profile->primary_job->office_city : '' }}"
                            readonly>
                    </div>
                </div>

            </div>
        </div>


        {{-- Volunteer Details  --}}
        <div class="card m-0 mt-3 pb-0">
            <div class="card-header bg-primary">
                <div class="h5">Volunteer Details</div>
            </div>
            <div class="card-body ">

                <div class="form-row">
                    <div class="form-group col-12 pt-2">
                        <label for="business_unit_code">Business Unit</label>
                        <select type="text" class="form-control w-50" name="business_unit_code" id="business_unit_code"
                            placeholder="" role="listbox">
                            <option value="" selected="selected">Choose an Business Unit</option>
                            @foreach($business_units as $bu)
                                @if ($profile) 
                                    <option role="listitem" {{ ($profile && $profile->business_unit_code == $bu->code) ? "selected":""}} value="{{$bu->code}}">{{$bu->name}} ({{$bu->code}})</option>
                                @else
                                    <option role="listitem" {{ ($user->primary_job->business_unit == $bu->code) ? "selected":""}} value="{{$bu->code}}">{{$bu->name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row" id="no_of_years_area">
                    @if ($is_renew)
                        <div class="form-group col-12">
                            <label for="no_of_years">How many years have you been working with PECSF</label>
                            <div class="text-info">Note: this is a renew volunteer profile, no change required</div>
                            <select type="text" class="form-control w-25" name="no_of_years" id="no_of_years" role="listbox" disabled>
                                <option role="listitem" value="1" selected>1</option>
                            </select>
                        </div>
                    @else
                        <div class="form-group col-12">
                            <label for="no_of_years">How many years have you been working with PECSF</label>
                            <select type="text" class="form-control w-25" name="no_of_years" id="no_of_years" role="listbox">
                                <option role="listitem" value="0" selected>Please select</option>
                                @foreach ( range(1,50) as $value ) 
                                    <option role="listitem" value="{{ $value }}" 
                                    {{ ($profile && $profile->no_of_years && $profile->no_of_years == $i) ? "selected" : "" }}>
                                    {{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
                <div class="form-row">
            
                    <div class="form-group col-12">
                        <label for="preferred_role">Your Preferred Volunteer Role</label>
                        <select type="text" class="form-control w-25" name="preferred_role" id="preferred_role"
                             role="listbox">
                            <option role="listitem" value="">Please select</option>
                            @foreach( $role_list as $key => $value)
                                <option role="listitem" value="{{ $key }}" {{ ($profile && $profile->preferred_role == $key) ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach 
                        </select>
                    </div>
            
                </div>

                {{-- <div class="form-group row">
                    <label for="one_time_amount" class="col-sm-3 col-form-label">The One-time payroll deductions :</label>
                    <div class="col-sm-2">
                        <input type="text" name="one_time_amount" class="form-control" id="one_time_amount" placeholder="amount"
                            value="{{ $profile ? $profile->one_time_amount : '' }}">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="deduct_pay_from" class="col-sm-3 col-form-label">The One-time payroll deduct on :</label>
                    <div class="col-sm-2">
                        <input type="date" name="deduct_pay_from"  class="form-control" id="deduct_pay_from"
                                value="{{ $profile->deduct_pay_from->format('Y-m-d') }}" disabled>
                    </div>
                </div> --}}

            </div>
        </div>


        {{-- Pool or Non-Pool  --}}
        <div class="card m-0 mt-3  pb-0">
            <div class="card-header bg-primary">
                <div class="h5">Recognition Items</div>
            </div>

            <div id="address_type_error" class="pt-4 pl-3 text-danger font-weight-bold" style="display:none;">
            </div>  
            <div class="card-body ">
                <div class="accordion pt-3" id="method-selection">
                    <div class="card m-0">
                        <div class="card-header bg-light">
                            <div class="custom-control custom-radio">
                                <input data-toggle="collapse" data-target="#method-selection-1" type="radio"
                                    name="address_type" id="address_type-1" value="G" class="custom-control-input"
                                    {{ $profile->address_type == "G" ? 'checked' : '' }}/>
                                <label class="custom-control-label" for="address_type-1">Use the Global Address Listing</label>
                            </div>
                        </div>

                        <div id="method-selection-1" class="collapse {{ $profile->address_type == "G" ? 'show' : '' }}" data-parent="#method-selection">
                            <div class="card-body">

                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <div class="pl-3"><span id="user_full_address">{{ $profile && $profile->primary_job ? $profile->primary_job->full_address : '' }}</span></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                                         
                    <div class="card m-0">
                        <div class="card-header  bg-light">
                            <div class="custom-control custom-radio">
                                <input data-toggle="collapse" data-target="#method-selection-2" type="radio"
                                    name="address_type" id="address_type-2" value="S" class="custom-control-input"  {{ $profile->address_type != "G" ? 'checked' : '' }} />
                                <label class="custom-control-label" for="address_type-2">Use the following address:</label>
                            </div>
                        </div>
                        <div id="method-selection-2" class="collapse {{ $profile->address_type != "G" ? 'show' : '' }}" data-parent="#method-selection">
                            <div class="card-body">
                                
                                <div class="form-row">
                                    <div class="form-group col-12">
                                        <label for="address">Street address</label>
                                        <input id="address" name="address" type="text" value="{{ $profile ? $profile->address : '' }}" class="form-control">
                                    </div>
                                </div>
                        
                                <div class="form-row">
                                    <div class="form-group col-5">
                                        <label for="city">City</label>
                                        <select id="city" name="city" class="form-control" role="list" style="width: 100%" aria-hidden="true">
                                            <option role="listitem" value="">Select a City</option>
                                            @foreach ($cities as $city)
                                                <option role="listitem" value="{{ $city->id }}" {{ ($profile && $profile->city == $city->city) ? 'selected' : '' }}  >{{ $city->city }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                        
                                        <label for="province">Province</label>
                                        <select id="province" class="form-control" name="province" role="list">
                                            <option role="listitem" value="">Select a Province</option>
                                            @foreach( $province_list as $key => $value)
                                                <option role="listitem" value="{{ $key }}" {{ ($profile && $profile->province == $key) ? 'selected' : '' }}>{{ $value }}</option>
                                            @endforeach 
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="postal_code">Postal Code</label>
                                        <input type="text" class="form-control" name="postal_code" value="{{ $profile ? $profile->postal_code : '' }}">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <div class="form-group col-12 pt-3">
                    <hr>
                </div>
                
                <div class="form-group col-12">
                    <label>
                        <input id="opt_out_recongnition" name="opt_out_recongnition" 
                            type="checkbox" {{ ($profile && $profile->opt_out_recongnition == "Y") ? "checked":""}}  
                            name="address_type" value="Y" {{ ($profile && $profile->opt_out_recongnition == 'Y') ? 'checked' : '' }}>
                        <span class="pl-2">I wish to opt-out from receiving recognition items.</span>
                    </label>
                </div>

            </div>
        </div>

        <div class="mt-3">
            <button type="button" class="btn btn-outline-primary" onclick="location.href='{{ route('admin-volunteering.profile.index') }}'">Cancel</button>
            <input class="btn btn-primary ml-2" type="submit" value="Save">
        </div>

    </form>

  </div>
</div>
@endsection


@push('css')

<link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">

<style>
    .select2-selection--multiple{
        overflow: hidden !important;
        height: auto !important;
        min-height: 38px !important;
    }

    .select2-container .select2-selection--single {
        height: 38px !important;
        }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
    }

    .select2-selection--single.is-invalid {
        border-color: #e3342f ;
        padding-right: 2.19rem !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23e3342f' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23e3342f' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.4em + 0.6875rem) center;
        background-size: calc(0.8em + 0.375rem) calc(0.8em + 0.375rem);
    }

    /* #nav-tab li:not(.active)  a{
        pointer-events: none;
        color: #555;
    }

    #nav-tab li a.active {
        pointer-events: none;
        color: #000;
    }


    nav.nav a.nav-link.active {
        text-decoration: underline !important;
        font-weight: bold;
    }


    .summary-card .form-control[disabled] {
        border: 0;
        background-color: rgb(252, 252, 252) ;
    }

    .amount-selection input[type=radio] {
        width: 18px;
        height: 18px;
    }

    .amount-selection .form-check {
        padding-top: 4px ;
    }

    .amount-selection .form-check-label {
        padding-left: 8px;
    } */

    .form-control:disabled, .form-control[readonly] {
        border: none;
        /* text-align: right; */
        background-color: #f7f7f7;
    }

    .form-control.amount:disabled, .form-control.amount[readonly] {
        text-align: right;
    }


</style>

@endpush

@push('js')

<script src="{{ asset('vendor/select2/js/select2.min.js') }}" ></script>
<script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>

<script>

$(function () {

    $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': '{{csrf_token()}}'
            }
    });

    // Part 1 - Donar / Employee Profile
    function reset_user_profile_info() {
        $('#user_first_name').val('');
        $('#user_last_name').val('');
        $('#user_email').val('');
        $('#user_emplid').val('');
        $('#user_dept').val('');
        $('#user_bu').val('');
        $('#user_org').val('');
        $('#user_office_city').val('');
        $('#user_region').val('');

        $('#user_full_address').html('');

        // reset when this is renew
        // $('#no_of_years').prop('disabled',false);
        $('#no_of_years_area').show();
    }

    $('#organization_id').change( function() {
        profile_id = $('#profile_id').val();
        if (!profile_id) {
            reset_user_profile_info();
        }
        $('#emplid').val(null).trigger('change');

        code = $("select[name='organization_id']").find(":selected").attr('code');
        if (code == 'GOV') {
            $('.emplid_section').show();
            $('.pecsf_id_section').hide();
        } else {
            $('.emplid_section').hide();
            $('.pecsf_id_section').show();
        }

    });

    // Trigger change for the first load
    $( "#organization_id").trigger( "change" );

    $('#emplid').select2({
        allowClear: true,
        placeholder: "Type employee ID",
        ajax: {
            url: '{{ route("admin-volunteering.profile.users") }}'
            , dataType: 'json'
            , delay: 250
            , data: function(params) {
                var query = {
                    'org_id' : $('#organization_id').val(),
                    'q': params.term
                , }
                return query;
            }
            , processResults: function(data) {
                return {
                    results: data
                    };
            }
            , cache: false
        }
    });

    $('#emplid').on('select2:select', function (e) {
        var data = e.params.data;

        reset_user_profile_info();
        if (data.emplid) {
            $('#user_first_name').val( data.first_name );
            $('#user_last_name').val( data.last_name );
            $('#user_email').val( data.email);
            $('#user_emplid').val( data.emplid );
            $('#user_dept').val( data.department );
            $('#user_bu').val( data.business_unit );
            $('#user_org').val( data.organization);
            $('#user_office_city').val( data.office_city);
            $('#user_region').val(data.region);

            $('#user_full_address').html( data.full_address );

            // set business unit 
            $('#business_unit_code').val(data.business_unit_code);

            if (data.profile_count > 0) {
                $('#no_of_years').val(1);
                // $('#no_of_years').prop('disabled',true);
                $('#no_of_years_area').hide();
            } else {
                // $('#no_of_years').val('');
                // $('#no_of_years').prop('disabled',false);
                $('#no_of_years_area').show();
            }
        }
    });

    $('#emplid').on('select2:unselect', function (e) {
        var data = e.params.data;
            reset_user_profile_info();
    });

    // function get_campaign_profile_id()
    // {
    //     profile_id = 0;
    //     $.get({
    //         url: '{{ route('admin-pledge.administrators.pledgeid') }}' +
    //                     '?org_id=' + $('#organization_id').val() +
    //                     '&campaign_year_id=' + $('#campaign_year_id').val() +
    //                     '&user_id=' + $('#user_id').val() +
    //                     '&pecsf_id=' + $('#pecsf_id').val(),
    //         dataType: 'json',
    //         async: false,
    //         cache: false,
    //         timeout: 30000,
    //         success: function(data)
    //         {
    //             // console.log( data.id );
    //             profile_id =  data.id;
    //         },
    //         error: function(response) {
    //             console.log('Error');
    //         }
    //     });

    //     return profile_id;

    // }

    function get_nongov_user_detail() {

        // clean up the old values
        $('#pecsf_first_name').val('');
        $('#pecsf_last_name').val('');
        $('#pecsf_city').val('');

        $.get({
            url: '{{ route('admin-pledge.administrators.nongovuser') }}' +
                        '?org_id=' + $('#organization_id').val() +
                        '&pecsf_id=' + $('#pecsf_id').val(),
            dataType: 'json',
            async: false,
            cache: false,
            timeout: 30000,
            success: function(data)
            {
                console.log( data );
                if(data) {
                    $('#pecsf_first_name').val( data.first_name );
                    $('#pecsf_last_name').val( data.last_name );
                    $('#pecsf_city').val( data.city );
                }
            },
            error: function(response) {
                console.log('Error');
            }
        });
    }

    $('#pecsf_id').on('blur', function (e) {
        e.stopPropagation();
        get_nongov_user_detail();
    })

    $('#pecsf_id').on('keypress', function (e) {
        e.stopPropagation();

        var keycode = (e.keyCode ? e.keyCode : e.which);
        if(keycode == '13') {
            // console.log('enter pressed - ' +  this.value);
            get_nongov_user_detail();
        }
    })


    // Form Submission -- handle single submission only
    // $(document).on("click", "button[type='submit']", function(e) {

    //     $("#admin-volunteering-profile-form").submit(function(e){
    //         if(submit_count > 0) {
    //             e.preventDefault();
    //         }
    //         submit_count++;
    //     });
    // });


    $("#admin-volunteering-profile-form").submit(function(e) {
        e.preventDefault();

        // reset submission count
        submit_count = 0;

        var valid = true;
            // array for the fields in the form (for clean up previous errors)
            var fields = [];
            fields = ['campaign_year', 'organization_id', 'emplid', 'pecsf_id', 'pecsf_first_name', 'pecsf_last_name', 
                        'business_unit_code', 'no_of_years', 'preferred_role', 'address_type', 
                        'address', 'city', 'province', 'postal_code', 'opt_out_recongnition',
                    ];

            $.each( fields, function( index, field_name ) {
                $('#admin-volunteering-profile-form [name='+ field_name +']').nextAll('span.text-danger').remove();
                $('#admin-volunteering-profile-form [name='+ field_name +']').removeClass('is-invalid');
            });
            // $('#admin-pldege-campaign-form [name="charities[]"]').nextAll('span.text-danger').remove();
            // $('#admin-pldege-campaign-form [name="percentages[]"]').nextAll('span.text-danger').remove();
            $('#emplid').parent().find('.select2-selection--single').removeClass('is-invalid');
            $('#emplid').parent().find('span.text-danger').remove();
            $('#address_type_error').html('');
            $('#address_type_error').hide();

            var form = $('#admin-volunteering-profile-form');
            // $('#admin-pldege-campaign-form input[name=step]').val( step );

            $("#admin-volunteering-profile-form").fadeTo("slow",0.2);
            $.ajax({
                // method: "PUT",
                //     url:  '/settings/business-units/' + id,
                method: "POST",
                url: "/admin-volunteering/profile{{ $profile->id ? '/'.$profile->id : '' }}",
                //data: form.serialize(),
                @if ($is_new_profile)
                    data: form.find(':not(input[name=_method])').serialize(),  // serializes the form's elements exclude _method.
                @else
                    data: form.serialize(),  // serializes the form's elements exclude _method.
                @endif
                async: false,
                cache: false,
                timeout: 30000,
                success: function(data)
                {
                    console.log(data );
                    window.location = '{{ route("admin-volunteering.profile.index") }}';

                },
                error: function(response) {
                    $("#admin-volunteering-profile-form").fadeTo("slow",1);
                    valid = false;
                    if (response.status == 422) {
                        $.each(response.responseJSON.errors, function(field_name,error){
                            if ( field_name.includes('.') ) {
                                items = field_name.split(".");
                                pos = Number(items[ items.length -1 ]);
                                $(document).find('[name="' + items[0] + '[]"]:eq(' + pos + ')').parent().append('<span class="text-strong text-danger">' +error+ '</span>');
                                $(document).find('[name="' + items[0] + '[]"]:eq(' + pos + ')').addClass('is-invalid');
                            } else if (field_name == 'address_type') {
                                $('#address_type_error').html(error);
                                $('#address_type_error').show();
                            } else {

                                if (field_name == 'emplid') {
                                   $('#emplid').parent().find('.select2-selection--single').addClass('is-invalid');
                                }

                                $(document).find('[name=' + field_name + ']').parent().append('<span class="text-strong text-danger">' +error+ '</span>');
                                $(document).find('[name=' + field_name + ']').addClass('is-invalid');
                            }

                            // additional checking for pledge existence
                            code = $("select[name='organization_id'] option:selected").attr('code');

                            // if (step == 1 && field_name == 'campaign_year_id' && code != 'GOV') {

                            //     profile_id = get_campaign_profile_id();
                            //     if (profile_id > 0) {
                            //         $(document).find('[name=' + field_name + ']').parent().append('<span class="d-block text-strong text-danger">' +
                            //             'There is an existing pledge for this donor. Would you like to change it? Click <a ' +
                            //             'href="/admin-pledge/campaign/'+profile_id+'/edit">here</a> to proceed.' + '</span>');
                            //     }
                            // }

                        })
                    }
                    console.log('Error');
                },
                complete: function(response) {
                    console.log('test') ;
                },
            });

            return valid;

    });


});

</script>

@endpush




