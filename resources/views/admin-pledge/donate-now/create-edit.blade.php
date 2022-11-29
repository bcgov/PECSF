@extends('adminlte::page')

@section('content_header')

@include('admin-pledge.partials.tabs')

    <div class="d-flex mt-3">
        <h3>{{ $is_new_pledge ? 'Create' : 'Edit' }} a Donate Now Pledge</h3>
        <div class="flex-fill"></div>
    
        <div class="d-flex">
            <div class="mr-2">
                <x-button class="btn-primary mr-2" :href="route('admin-pledge.donate-now.index')"> Back </x-button>        
            </div>
        </div>


    </div>
@endsection

@section('content')

<div class="card pb-4">
  <div class="card-body py-0">
    

    <form action="{{ $is_new_pledge ?  route("admin-pledge.donate-now.store") : route("admin-pledge.donate-now.update", $pledge->id) }}" 
            id="admin-pldege-donate-now-form" method="POST">
        @csrf
        @if (!($is_new_pledge))
            @method('PUT')
            <input type="hidden" id="pledge_id" name="pledge_id" value="{{ $pledge->id }}">
        @endif

        @if (!($is_new_pledge))
            <div class="d-flex  align-items-center my-2">
                <h4>Transaction ID: </b>{{ $pledge->id }}</h4> 
                <div class="flex-fill"></div>

                <div class="d-flex  align-items-center ">
                    <div class="mr-2">
                        <button  class="cancel-pledge btn btn-outline-danger mr-2" >Cancel this transaction</button>        
                    </div>
                </div>
            </div>
        @endif

        <div class="card m-0 mb-3">
            <div class="card-header bg-primary">
                <p class="h5">Calendar Year</p>
            </diV>
            <div class="card-body">
                    <div class="row">
                        <div class="form-group ">
                            <label for="yearcd">Calendar Year</label>
                            @if ($is_new_pledge)
                                <select id="yearcd" class="form-control" name="yearcd" style="max-width:200px;">
                                    @foreach ($campaignYears as $cy)
                                        <option value="{{ $cy->calendar_year }}"
                                            {{ ($cy->calendar_year == date('Y')) ? 'selected' : '' }}>
                                            {{ $cy->calendar_year }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <select id="yearcd" class="form-control" name="yearcd" style="max-width:200px;" {{ $is_new_pledge ? '' : 'readonly' }}>
                                <option value="{{ $pledge->calendar_year }}" selected>{{ $pledge->yearcd }}</option>
                                </select>
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
                        <label for="user_id">Organization</label>
                            {{-- @isset($pledge) --}}
                            @if ($is_new_pledge)
                                <select class="form-control" style="width:100%;" name="organization_id" id="organization_id">
                                    @foreach ($organizations as $organization)
                                        <option value="{{ $organization->id }}" code="{{ $organization->code }}" 
                                            {{ $organization->code == 'GOV' ? 'selected' : '' }}>
                                            {{ $organization->name }}</option>
                                    @endforeach
                                </select>
                            @else 
                                <select class="form-control" style="width:100%;" name="organization_id" id="organization_id" readonly>
                                    <option value="{{ $pledge->organization_id }}"  code="{{ $organization->code }}" selected>{{ $pledge->organization->name }}</option>
                                </select>
                            @endif
                    </div>
                    <div class="form-group col-md-7 emplid_section">
                        <label for="user">Employee</label>
                        @if ($is_new_pledge)
                            <select class="form-control select2" style="width:100%;" name="user_id" id="user_id">
                                {{-- <option value="" selected>-- choose user --</option> --}}
                            </select>
                        @else 
                            <select class="form-control" name="user_id" id="user" readonly>
                                <option value="{{ $pledge->user_id }}" selected>{{ $pledge->user ? $pledge->user->name : '' }}</option>
                            </select>
                        @endif
                        @error('user_id')
                            <span class="text-strong text-danger">{{  $message  }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-md-3 pecsf_id_section">
                        <label for="user">PECSF ID</label>
                        {{-- @if (isset($pledge))
                            <input type="text" class="form-control" name="pecsf_id" id="pecsf_id" value="{{ $pledge->pecsf_id }}" readonly>
                        @else  --}}
                            <input type="text" class="form-control" name="pecsf_id" id="pecsf_id" value="{{ $pledge->pecsf_id }}" {{ $is_new_pledge ? '' : 'readonly' }}>
                        {{-- @endif --}}
                    </div>
        
                </div>
        
                <div class="form-row pecsf_id_section">
                    <div class="col-md-3 mb-3">
                        <label for="pecsf_first_name">First Name</label>
                        <input type="text" class="form-control" id="pecsf_first_name" name="pecsf_first_name" 
                            value="{{ old('pecsf_first_name') ?? ( isset($pledge) ? $pledge->first_name : '') }}" {{ $is_new_pledge ? '' : 'readonly' }}>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="pecsf_last_name">Last Name</label>
                        <input type="text" class="form-control" id="pecsf_last_name" name="pecsf_last_name" 
                            value="{{ old('pecsf_last_name') ?? ( isset($pledge) ? $pledge->last_name : '') }}" {{ $is_new_pledge ? '' : 'readonly' }}>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="pecsf_city">City</label>
                        @if ($is_new_pledge )
                            <select class="form-control" style="width:100%;" name="pecsf_city" id="pecsf_city" >
                                <option value="">Select a City</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->city }}" {{ $city->city == old('pecsf_city') || ( ($pledge) && $city->city == $pledge->city) ? 'selected' : '' }}>
                                        {{ $city->city }}</option>
                                @endforeach
                            </select>
                        @else
                           <input type="text" class="form-control" id="pecsf_city" name="pecsf_city" 
                              value="{{ ( isset($pledge) ? $pledge->city : '') }}" readonly>
                        @endif
                    </div>
                </div>
        
                <div class="form-row emplid_section">
                    <div class="col-md-2 mb-3">
                        <label for="user_emplid">Employee ID</label>
                        <input type="text" class="form-control border-0" id="user_emplid" 
                                value="{{ (isset($pledge) && $pledge->user) ? $pledge->user->primary_job->emplid : '' }}" 
                            disabled>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label for="user_region">Region</label>
                        <input type="text" class="form-control border-0" id="user_region" 
                                value="{{ (isset($pledge) && $pledge->user) ? $pledge->user->primary_job->region->name . ' (' . $pledge->user->primary_job->region->code . ')'  : '' }}" 
                             disabled>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label for="user_dept">Department</label>
                        <input type="text" class="form-control border-0" id="user_dept" 
                                value="{{ (isset($pledge) && $pledge->user) ? $pledge->user->primary_job->dept_name . ' (' . $pledge->user->primary_job->deptid . ')' : '' }}" 
                            disabled>
                    </div>
                </div>
                <div class="form-row emplid_section">
                    <div class="col-md-4 mb-3">
                        <label for="user_first_name">First name</label>
                        <input type="text" class="form-control border-0" id="user_first_name" 
                            value="{{ (isset($pledge) && $pledge->user) ? $pledge->user->primary_job->first_name : '' }}"
                            disabled>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="user_last_name">Last name</label>
                        <input type="text" class="form-control border-0" id="user_last_name" 
                            value="{{ (isset($pledge) && $pledge->user) ? $pledge->user->primary_job->last_name : '' }}"
                            disabled>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="user_email">Email</label>
                        <input type="text" class="form-control border-0" id="user_email" 
                            value="{{ (isset($pledge) && $pledge->user) ? $pledge->user->primary_job->email : '' }}"
                             disabled>
                    </div>
                </div>
                <div class="form-row emplid_section">
                    <div class="col-md-4 mb-3">
                        <label for="user_bu">Business Unit</label>
                        <input type="text" class="form-control border-0" id="user_bu" 
                            value="{{ (isset($pledge) && $pledge->user) ? $pledge->user->primary_job->bus_unit->name . ' (' . $pledge->user->primary_job->bus_unit->code . ')' : '' }}" 
                            disabled>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="user_org">Organization</label>
                        <input type="text" class="form-control border-0" id="user_org" 
                            value="{{ (isset($pledge) && $pledge->user) ? $pledge->user->primary_job->organization_name : '' }}" 
                            disabled>
                    </div>
                </div>
        
            </div>
        </div>

        {{-- Pool or Non-Pool  --}}
        <div class="card m-0 mt-3  pb-0">
            <div class="card-header bg-primary">
                <div class="h5">Donate To Charity</div>
            </div>

            <div class="card-body ">        
                <div class="accordion pt-3" id="method-selection">
                    <div class="card m-0">
                        <div class="card-header bg-light">
                            <div class="custom-control custom-radio">
                                <input data-toggle="collapse" data-target="#method-selection-1" type="radio" 
                                name="pool_option" id="pool-option-1" value="P" class="custom-control-input" 
                                    {{ $pool_option == "P" ? 'checked' : '' }}/>
                                <label class="custom-control-label" for="pool-option-1">Select a regional Fund Supported Pool</label>
                            </div>
                        </div>
                
                        <div id="method-selection-1" class="collapse {{ $pool_option == "P" ? 'show' : '' }}" data-parent="#method-selection">
                            <div class="card-body">
                
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="pool_id">Fund Supported Pool</label>
                                        <select class="form-control" name="pool_id" id="pool_id">
                                            <option value="" selected>Choose a pool</option>
                                            @foreach ($fspools as $fspool) 
                                                <option value="{{ $fspool->id }}"
                                                    @if ( $pledge->id && $pool_option == "P")
                                                        {{  $pledge->fund_supported_pool->id == $fspool->id ? 'selected' : ''  }}
                                                    @endif
                                                    >{{ $fspool->region->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                        
                            </div>
                        </div>
                    </div>
                
                    <div class="card m-0">
                        <div class="card-header  bg-light">
                            <div class="custom-control custom-radio">
                                <input data-toggle="collapse" data-target="#method-selection-2" type="radio" 
                                    name="pool_option" id="pool-option-2" value="C" class="custom-control-input"  {{ $pool_option == "C" ? 'checked' : '' }} />
                                <label class="custom-control-label" for="pool-option-2">Select one charities from the CRA list</label>
                            </div>
                        </div>
                        <div id="method-selection-2" class="collapse {{ $pool_option == "C" ? 'show' : '' }}" data-parent="#method-selection">
                            <div class="card-body">
                                {{-- <div class="row justify-content-end">
                                    <div class="col-md-5">
                                        <h6 class='font-weight-bold'>Bi-weekly (pay period) Deduction Amount : </h6>
                                    </div>
                                    <div class="col-md-1">
                                        <span class='font-weight-bold' id="pay_period_figure"></span>
                                    </div>
                                </div>
                                <div class="row justify-content-end">
                                    <div class="col-md-5">
                                        <h6 class='font-weight-bold'>One-time Deduction Amount</h6> 
                                    </div>
                                    <div class="col-md-1">
                                        <span class='font-weight-bold' id="one_time_figure"></span>
                                    </div>
                                </div> --}}
                                <table class="table" id="charity-table">
                                    <tbody>
                                    
                                        {{-- @php $charities = ($pool_option == "C" and isset($pledge)) ? $pledge->distinct_charities->pluck('id') : [''] @endphp
                                        @foreach ( $charities as $index => $oldCharity)
                                        <tr id="charity{{ $index }}">
                                            @php  $pledge_charity = ($pool_option == "C" && isset($pledge)) ? $pledge->distinct_charities[$index] : new \App\Models\PledgeCharity  @endphp 
                                            @include('admin-pledge.campaign.partials.charities', ['index' => $index, 'pledge_charity' => $pledge_charity]) 
                                        </tr>
                                        @endforeach
                                        <tr id="charity{{ isset($pledge) ? $pledge->distinct_charities->count() + 1 : 1 }}"></tr> --}}

                                        <div class="form-row">
                                            <div class="form-group col-md-11">
                                                <label for="charity_id">Charity CRA Organization Name and Business Number</label>
                                                <select name="charity_id"  class="form-control select2 @error('charity_id') is-invalid @enderror" 
                                                            style="width: 100%">
                                                    
                                                    {{-- @isset($pledge_charity) --}}
                                                    @if ($pledge)
                                                        <option value="{{ $pledge->charity_id }}" selected>{{ $pledge->charity->charity_name }}</option>
                                                    @endisset
                                    
                                                </select>
                                                @error( 'charities.')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                    
                                        </div>
                                    
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="special_program">Program or Branch Name</label>
                                                <input type="text" name="special_program" class="form-control" 
                                                    value="{{  $pledge ? $pledge->special_program : '' }}" />
                                            </div>
                                    
                                    
                                        </div>   

                                    </tbody>
                                </table>
                
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Deduction  --}}
        <div class="card m-0 mt-3 pb-0">
            <div class="card-header bg-primary">
                <div class="h5">Deductions</div>
            </div>
            <div class="card-body ">

                <div class="form-group row">
                    <label for="one_time_amount" class="col-sm-3 col-form-label">The One-time payroll deductions :</label>
                    <div class="col-sm-2">
                        <input type="text" name="one_time_amount" class="form-control" id="one_time_amount" placeholder="amount"
                            value="{{ $pledge ? $pledge->one_time_amount : '' }}">
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="deduct_pay_from" class="col-sm-3 col-form-label">The One-time payroll deduct on :</label>
                    <div class="col-sm-2">
                        <input type="date" name="deduct_pay_from"  class="form-control" id="deduct_pay_from" 
                                value="{{ $pledge->deduct_pay_from }}" disabled>
                    </div>
                </div>

            </div>
        </div>

        
        <div class="mt-3">
            <input class="btn btn-primary" type="submit" value="Save">
            <button class="btn btn-outline-primary" onclick="location.href='{{ route('admin-pledge.donate-now.index') }}'">Cancel</button>
        </div>

    </form>
    
  </div>
</div>
@endsection


@push('css')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
        $('#user_region').val('');  
    }

    $('#organization_id').change( function() {
        pledge_id = $('#pledge_id').val();
        if (!pledge_id) {
            reset_user_profile_info();
        }
        $('#user_id').val(null).trigger('change');
        
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

    $('#user_id').select2({
        allowClear: true,
        placeholder: "Type employee ID",
        ajax: {
            url: '{{ route('admin-pledge.administrators.users') }}'
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

    $('#user_id').on('select2:select', function (e) {
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
            $('#user_region').val(data.region);
        }
    });

    $('#user_id').on('select2:unselect', function (e) {
        var data = e.params.data;
            reset_user_profile_info();            
    });

    function get_campaign_pledge_id()
    {
        pledge_id = 0;
        $.get({
            url: '{{ route('admin-pledge.administrators.pledgeid') }}' + 
                        '?org_id=' + $('#organization_id').val() +
                        '&campaign_year_id=' + $('#campaign_year_id').val() +
                        '&user_id=' + $('#user_id').val() +
                        '&pecsf_id=' + $('#pecsf_id').val(),
            dataType: 'json',
            async: false,
            cache: false,
            timeout: 30000,
            success: function(data)
            {
                // console.log( data.id );
                pledge_id =  data.id;
            },
            error: function(response) {
                console.log('Error');
            }
        });

        return pledge_id;        

    }

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


   // Part 2 function to initialize select2 dynamic
    $("select[name='charity_id']").select2({
        allowClear: true,
        placeholder: 'select charity',
        ajax: {
            url: '/settings/fund-supported-pools/charities'
            , dataType: 'json'
            , delay: 250
            , data: function(params) {
                var query = {
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


    // Form Submission -- handle single submission only
    // $(document).on("click", "button[type='submit']", function(e) {

    //     $("#admin-pldege-donate-now-form").submit(function(e){
    //         if(submit_count > 0) {
    //             e.preventDefault();
    //         }
    //         submit_count++;
    //     });
    // });        


    $("#admin-pldege-donate-now-form").submit(function(e) {
        e.preventDefault();

        // reset submission count 
        submit_count = 0;

        var valid = true;
            // array for the fields in the form (for clean up previous errors)
            var fields = [];
            fields = ['organization_id', 'user_id', 'pecsf_id', 'pecsf_first_name', 'pecsf_last_name', 'pecsf_city',
                        'pool_option', 'pool_id', 'charity_id', 'special_program', 'one_time_amount' ];

            $.each( fields, function( index, field_name ) {
                $('#admin-pldege-donate-now-form [name='+ field_name +']').nextAll('span.text-danger').remove();
                $('#admin-pldege-donate-now-form [name='+ field_name +']').removeClass('is-invalid');
            });
            // $('#admin-pldege-campaign-form [name="charities[]"]').nextAll('span.text-danger').remove();
            // $('#admin-pldege-campaign-form [name="percentages[]"]').nextAll('span.text-danger').remove();

            var form = $('#admin-pldege-donate-now-form');
            // $('#admin-pldege-campaign-form input[name=step]').val( step );

            $("#admin-pldege-donate-now-form").fadeTo("slow",0.2);
            $.ajax({
                // method: "PUT",
                //     url:  '/settings/business-units/' + id,
                method: "POST",
                url: "/admin-pledge/donate-now/{{ $pledge->id ? $pledge->id : '' }}", 
                //data: form.serialize(), 
                @if ($is_new_pledge) 
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
                    window.location = '{{ route("admin-pledge.donate-now.index") }}';

                },
                error: function(response) {
                    $("#admin-pldege-donate-now-form").fadeTo("slow",1);
                    valid = false;
                    if (response.status == 422) {   
                        $.each(response.responseJSON.errors, function(field_name,error){
                            if ( field_name.includes('.') ) {   
                                items = field_name.split(".");
                                pos = Number(items[ items.length -1 ]);
                                $(document).find('[name="' + items[0] + '[]"]:eq(' + pos + ')').parent().append('<span class="text-strong text-danger">' +error+ '</span>');
                                $(document).find('[name="' + items[0] + '[]"]:eq(' + pos + ')').addClass('is-invalid');
                            } else {
                                $(document).find('[name=' + field_name + ']').parent().append('<span class="text-strong text-danger">' +error+ '</span>');
                                $(document).find('[name=' + field_name + ']').addClass('is-invalid');
                            }

                            // additional checking for pledge existence
                            code = $("select[name='organization_id'] option:selected").attr('code');

                            // if (step == 1 && field_name == 'campaign_year_id' && code != 'GOV') {

                            //     pledge_id = get_campaign_pledge_id();
                            //     if (pledge_id > 0) {
                            //         $(document).find('[name=' + field_name + ']').parent().append('<span class="d-block text-strong text-danger">' + 
                            //             'There is an existing pledge for this donor. Would you like to change it? Click <a ' + 
                            //             'href="/admin-pledge/campaign/'+pledge_id+'/edit">here</a> to proceed.' + '</span>');
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
        
    // Cancel
    @if ($pledge->id)        

        $(document).on("click", ".cancel-pledge" , function(e) {
            e.preventDefault();

            id = {{ $pledge->id }};
            // title = $pledge->id;

            Swal.fire( {
                title: 'Are you sure you want to cancel the pledge "' + id + '" ?',
                text: 'This action cannot be undone.',
                // icon: 'question',
                showDenyButton: true,
                // showCancelButton: true,
                confirmButtonText: 'Yes',
                denyButtonText: 'No',
                buttonsStyling: false,
                //confirmButtonClass: 'btn btn-danger',
                customClass: {
                	confirmButton: 'btn btn-primary', //insert class here
                    cancelButton: 'btn btn-danger ml-2', //insert class here
                    denyButton: 'btn btn-outline-secondary ml-2',
                }
                //denyButtonText: `Don't save`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    // Swal.fire('Saved!', '', '')
                    $.ajax({
                        method: "POST",
                        url:  '/admin-pledge/donate-now/' + id + '/cancel',
                        success: function(data)
                        {
                            // oTable.ajax.reload(null, false);	// reload datatables
                            // Toast('Success', 'Pledge ' + id +  ' was successfully cancel.', 'bg-success' );
                            console.log(data ); 
                            window.location = '{{ route("admin-pledge.donate-now.index") }}';

                        },
                        error: function(response) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.responseJSON.error,
                            })
                            console.log(response.responseJSON.error);
                        }
                    });
                } else if (result.isCancelledDenied) {
                    // Swal.fire('Changes are not saved', '', '')
                }
            })

        });

    @endif


});

</script>

@endpush




