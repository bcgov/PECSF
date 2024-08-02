@extends('adminlte::page')

@section('content_header')

@include('admin-campaign.partials.tabs')

    <h4 class="mx-1 mt-3">Statistics Updates</h4>

    <div class="d-flex mt-3">
        <div class="flex-fill">
            <p><button class="ml-2 btn btn-outline-primary" onclick="window.location.href='/administrators/dashboard'">
                Back    
            </button></p>
        </div>

        <div class="d-flex">
            <div class="mr-2">
            </div>
        </div>
    </div>

@endsection
@section('content')


<form id="setting-edit-form">


    <div class="card">
        <div class="card-body">
            <p class="font-italic  text-danger"><u>Note:</u> For final date before March 1, it will be considered a previous campaign year (e.g 2024-02-20, the campiagn year is still 2023)</p>

            <div class="row pb-2">
                <div class="col-md-12"><h4 class="text-primary">Statistics Page Updates</h4></div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="challenge_start_date">Start Date</label>
                    <input type="date" class="form-control input-control" name="challenge_start_date" 
                                value="{{ $setting->challenge_start_date->toDateString() }}" />
                </div>
                <div class="form-group col-md-3">
                    <label for="challenge_end_date">End Date</label>
                    <input type="date" class="form-control input-control" name="challenge_end_date" 
                                value="{{ $setting->challenge_end_date->toDateString() }}" />
                </div>
                <div class="form-group col-md-3">
                    <label for="challenge_final_date">Final Date</label>
                    <input type="date" class="form-control input-control" name="challenge_final_date" 
                                value="{{ $setting->challenge_final_date->toDateString() }}" />
                </div>

                <div class="form-group col-md-3">
                    <label for="finalize_challenge_data">&nbsp;</label>
                        <button type="button" class="finalize_challenge_data btn form-control btn-danger">
                            Finalize Statistics Page
                        </button>
                </div>
            </div>
  
            <div class="row pt-4">
                <div class="col-md-12"><h4 class="text-primary">Daily Campaign Updates</h4></div>
            </div>
            <div class="form-row pt-2">
                <div class="form-group col-md-3">
                    <label for="campaign_start_date">Start Date</label>
                    <input type="date" class="form-control input-control" name="campaign_start_date" 
                                value="{{ $setting->campaign_start_date->toDateString() }}" />
                </div>
                <div class="form-group col-md-3">
                    <label for="campaign_end_date">End Date</label>
                    <input type="date" class="form-control input-control" name="campaign_end_date" 
                                value="{{ $setting->campaign_end_date->toDateString() }}" />
                </div>
                <div class="form-group col-md-3">
                    <label for="campaign_final_date">Final Date</label>
                    <input type="date" class="form-control input-control" name="campaign_final_date" 
                                value="{{ $setting->campaign_final_date->toDateString() }}" />
                </div>

            </div>

            <div class="row pt-4 pl-2">
                <div>
                    <a class="save btn form-control btn-primary">Save</a>
                </div>
                <div class="pl-2">
                    <a href="/administrators/dashboard" class="btn form-control btn-secondary">Cancel</a>
                </div>
            </div>
        
        </div>
    </div>

</form>


@endsection


@push('css')
    <link href="{{ asset('vendor/toastr/toastr.min.css') }}" rel="stylesheet">

@endpush

@push('js')
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>
    <script src="{{ asset('vendor/toastr/toastr.min.js') }}" ></script>

    <script>

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        });


    $(document).on("click", ".save" , function(e) {

        var form = $('#setting-edit-form');

        info = 'Confirm to update this record?';
        if (confirm(info))
        {
            var fields = ['challenge_start_date', 'challenge_end_date','challenge_final_date',
                          'campaign_start_date', 'campaign_end_date','campaign_final_date'];
            $.each( fields, function( index, field_name ) {
                 $('#setting-edit-form [name='+field_name+']').nextAll('span.text-danger').remove();
            });

            $.ajax({
                method: "POST",
                url:  '/settings/challenge',
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    // Toast('Success', 'The setting was successfully updated.', 'bg-success' );
                    toastr["success"]( 'The setting has been successfully updated.', '',
                                {"closeButton": true, "newestOnTop": true, "timeOut": "5000" });
                },
                complete: function(xhr, resp) {
                    min_height = $(".wrapper").outerHeight();
                    $(".main-sidebar").css('min-height', min_height);
                },
                error: function(response) {
                    if (response.status == 422) {

                        $.each(response.responseJSON.errors, function(field_name,error){
                            $(document).find('[name='+field_name+']').after('<span class="text-strong text-danger">' +error+ '</span>')
                        })
                    }
                    console.log('Error');
                }
            });

        };
    });

    $(document).on("click", ".finalize_challenge_data" , function(e) {

        var form = $('#setting-edit-form');

        info = 'Confirm to finalize Statistics Page Data ?';
        if (confirm(info))
        {        

            $.ajax({
                    method: "POST",
                    url:  '/settings/challenge/finalize_challenge_data',
                    data: form.serialize(), // serializes the form's elements.
                    success: function(data)
                    {
                        // Toast('Success', 'The Statistics Page Data were successfully finalized.', 'bg-success' );
                        toastr["success"]( 'The Statistics Page data has been successfully finalized.', '',
                                    {"closeButton": true, "newestOnTop": true, "timeOut": "5000" });
                    },
                    error: function(response) {
                        if (response.status == 422) {

                            text = "Failed: Unable to finalize the challenge page data with following error(s): \n\n";
                            $.each(response.responseJSON.errors, function(field_name,errors){
                                $.each(errors, function(idx, error_message){
                                    text += error_message + "\n";
                                })
                            })
                            alert( text );
                        }
                    }
                });
        }

    }); 

    </script>
@endpush
