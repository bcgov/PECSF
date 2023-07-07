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

@if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ $message }} 
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<form id="setting-edit-form" action="{{ route('system.settings.store') }}"  method="post">
    @csrf
    <div class="card">
        <div class="card-body">
            <div class="row pb-2">
                <div class="col-md-12"><h4 class="text-primary">System Lock Down (Scheduled Maintenance Time Frame)</h4></div>
            </div>
            <div class="form-row px-4">
                <div class="form-group col-md-3">
                    <label for="system_lockdown_start">Lock Down Start Time</label>
                    <input type="datetime-local" class="form-control input-control" name="system_lockdown_start" 
                                value="{{ old('system_lockdown_start') ? old('system_lockdown_start') : $setting->system_lockdown_start->format('Y-m-d H:i') }}" />
                    @error('system_lockdown_start')
                        <span class="invalid-feedback d-block">
                            {{  $message  }}
                        </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    <label for="system_lockdown_end">Lock Down End Time</label>
                    <input type="datetime-local" class="form-control input-control" name="system_lockdown_end" 
                                value="{{ old('system_lockdown_end') ? old('system_lockdown_end') : $setting->system_lockdown_end->format('Y-m-d H:i') }}" />
                    @error('system_lockdown_end')
                        <span class="invalid-feedback d-block">
                            {{  $message  }}
                        </span>
                    @enderror
                </div>

                <div class="form-group col-md-1">
                </div>    

                <div class="form-group col-md-3">
                    <label for="signout_all">&nbsp;</label>

                    <button name="signout_all" value="1" class="signout-all btn form-control btn-danger" {{ $allow_signout_all ? '' : 'disabled'}}>
                        Signout all current logged in users 
                    </button>
                </div>
        
            </div>
        </div>
    </div>

    <div class="row pt-4 pl-2">
        <div>
            <button name="save" value="1" class="save btn form-control btn-primary">Save</button>
        </div>
    </div>

</form>


@endsection


@push('css')

@endpush

@push('js')
    {{-- <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script> --}}

    <script>

    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove(); 
        });
    }, 6000);

    $(document).on("click", ".save, .signout-all" , function(e) {
        // e.preventDefault();

        var form = $('#setting-edit-form');

        info = 'Confirm to update this record?';
        if (this.name == 'signout_all') {
            info = 'Confirm to sign out all current logged in users?';
        }

        if (confirm(info))
        {
            // var fields = ['system_lockdown_start', 'system_lockdown_end'];
            // $.each( fields, function( index, field_name ) {
            //      $('#setting-edit-form [name='+field_name+']').nextAll('span.text-danger').remove();
            // });

            $('#setting-edit-form').submit();

        };
    });

    </script>
@endpush
