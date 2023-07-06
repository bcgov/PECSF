@extends('adminlte::page')

@section('content_header')
    {{-- <h2>Reporting</h2> --}}
    @include('system-security.partials.tabs')
    <div class="d-flex mt-3">
        <h4>System Settings</h4>
        <div class="flex-fill"></div>
    </div>
@endsection
@section('content')

<p><a href="/administrators/dashboard" class="BC-Gov-SecondaryButton">Back</a></p>



<form id="setting-edit-form">

    <div class="card">
        <div class="card-body">
            <div class="row pb-2">
                <div class="col-md-12"><h4 class="text-primary">System Lock Down (Scheduled Maintenance Time Range)</h4></div>
            </div>
            <div class="form-row px-4">
                <div class="form-group col-md-3">
                    <label for="system_lockdown_start">Lock Down Start Time</label>
                    <input type="datetime-local" class="form-control input-control" name="system_lockdown_start" 
                                value="{{ $setting->system_lockdown_start->format('Y-m-d H:i') }}" />
                </div>
                <div class="form-group col-md-3">
                    <label for="system_lockdown_end">Lock Down End Time</label>
                    <input type="datetime-local" class="form-control input-control" name="system_lockdown_end" 
                                value="{{ $setting->system_lockdown_end->format('Y-m-d H:i') }}" />
                </div>
            </div>
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
            var fields = ['system_lockdown_start', 'system_lockdown_end'];
            $.each( fields, function( index, field_name ) {
                 $('#setting-edit-form [name='+field_name+']').nextAll('span.text-danger').remove();
            });

            $.ajax({
                method: "POST",
                url:  '/system/settings',
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
