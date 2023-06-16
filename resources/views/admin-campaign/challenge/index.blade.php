@extends('adminlte::page')

@section('content_header')
    {{-- <h2>Reporting</h2> --}}
    @include('admin-campaign.partials.tabs')
    <div class="d-flex mt-3">
        <h4>Challenge Updates</h4>
        <div class="flex-fill"></div>
    </div>
@endsection
@section('content')

<p><a href="/administrators/dashboard" class="BC-Gov-SecondaryButton">Back</a></p>



<form id="setting-edit-form">

    <div class="card">
        <div class="card-body">
            <div class="row pb-2">
                <div class="col-md-12"><h4 class="text-primary">Challenge Page Updates</h4></div>
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

@endpush

@push('js')
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>

    <script>

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        });

    function Toast( toast_title, toast_body, toast_class) {
            $(document).Toasts('create', {
                            class: toast_class,
                            title: toast_title,
                            autohide: true,
                            delay: 3000,
                            body: toast_body
            });
    }



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
                    Toast('Success', 'The setting was successfully updated.', 'bg-success' );
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

    </script>
@endpush
