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
            <div class="row">
                <div class="col-md-12"> <label class="text-primary"><h1>Challenge Page Updates</h1></label></div>
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
                <div class="col-md-3">
                    <label>&nbsp;</label><br>
                    <a class="save btn form-control btn-primary">Save</a>
                </div>
            </div>
  
        
        </div>
    </div>


    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12"> <label class="text-primary"><h1>Campaign Page Updates</h1></label></div>
            </div>
            <div class="form-row">
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
                <div class="col-md-3">
                    <label>&nbsp;</label><br>
                    <a class="save btn form-control btn-primary">Save</a>
                </div>
            </div>
        
        </div>
    </div>

</form>


@endsection


@push('css')

    {{-- <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css" rel="stylesheet"> --}}
	{{-- <style>
	#campaignyear-table_filter label {
		text-align: right !important;
        padding-right: 10px;
	}
    .dataTables_scrollBody {
        margin-bottom: 10px;
    }
</style> --}}
    {{-- <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet"> --}}

@endpush

@push('js')
    {{-- <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script> --}}
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>

    <script>

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        });


    // $("input").change(function(){
    //     $.post("/settings/challenge",
    //         {
    //             'name': $(this).attr("name"),
    //             'value': $(this).val()
    //         },
    //         function (data, status) {
    //             Swal.fire({
    //                 title: '<strong>Success!</strong>',
    //                 icon: 'success',
    //                 html:
    //                     'Setting was changed',
    //                 showCloseButton: false,
    //                 showCancelButton: true,
    //                 focusConfirm: false,
    //             }).then((result) => {

    //             });
    //         },"json");
    // });

    // $(".save").click(function(){
    //    $("input").trigger("change");
    // });

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
                    
                    // $('#setting-edit-form').modal('hide');

                    // var code = $("#bu-edit-model-form [name='code']").val();
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
