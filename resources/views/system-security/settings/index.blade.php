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

{{-- @if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ $message }} 
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif --}}

<form id="setting-edit-form" action="{{ route('system.settings.store') }}"  method="post">
    @csrf
    <input type="hidden" name="task" value="save" />
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
                    @if ($allow_signout_all)
                        <button type="button" name="signout_all" value="1" class="signout-all btn form-control btn-danger">
                            Signout all current logged in users 
                        </button>
                    @endif
                </div>
        
            </div>
        </div>
    </div>

    <div class="row pt-4 pl-2">
        <div>
            <button type="button" name="save" value="1" class="save btn form-control btn-primary">Save</button>
        </div>
    </div>

</form>


@endsection


@push('css')
    <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/toastr/toastr.min.css') }}" rel="stylesheet">

@endpush

@push('js')
<script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>
<script src="{{ asset('vendor/toastr/toastr.min.js') }}" ></script>

<script>

    $(function() {

        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove(); 
            });
        }, 5000);

        $(document).on("click", ".save, .signout-all" , function(e) {
            e.preventDefault();

            title_text = 'Are you sure to update this setting ?';
            if (this.name == 'signout_all') {
                title_text = 'Are you sure to sign out all current logged in users?';
            }

            Swal.fire( {
                    title: title_text,
                    text: 'This action cannot be undone.',
                    icon: 'info',   
                    showDenyButton: true,
                    // showCancelButton: true,
                    confirmButtonText: 'Yes',
                    denyButtonText: 'No',
                    buttonsStyling: false,
                    //confirmButtonClass: 'btn btn-danger',
                    customClass: {
                        confirmButton: 'btn btn-primary px-4', //insert class here
                        cancelButton: 'btn btn-danger ml-2', //insert class here
                        denyButton: 'btn btn-outline-secondary px-4 ml-2',
                    }
                    //denyButtonText: `Don't save`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
            
                        $("input[name='task']").val(this.name)
                        $('#setting-edit-form').submit();
                    } 
                });
        });

    });


@if ($message = Session::get('success'))
    $(function() {
        toastr["success"]( "{{ $message }}", '',
            {"closeButton": true, "newestOnTop": true, "timeOut": "5000" });
    });
@endif

</script>
@endpush
