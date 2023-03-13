@extends('adminlte::page')
@section('content_header')
@include('admin-campaign.partials.tabs')
@endsection
@section('content')



    <p>

    </p>



    <div style="clear:both;float:none;"></div>
    <br>
    <br>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12"> <label class="text-primary"><h1>Challenge page Updates</h1></label></div>
                </div>
            <div class="row">
                <div class="col-md-3"> <label>Start Date</label><br>
                    <input type="date" class="form-control input-control" name="challenge_start_date" value="" /><br>
                </div>
                <div class="col-md-3">
                    <label>End Date</label><br>
                    <input type="date" class="form-control input-control" name="challenge_end_date" value="" /><br>
                </div>
                <div class="col-md-3">
                    <label>Final Date</label><br>
                    <input type="date" class="form-control input-control" name="challenge_final_date" value="" /><br>
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label><br>
                    <a class=" save btn form-control btn-primary">Save</a>
                </div>
            </div>


        </div>
    </div>


    <p>

    </p>



    <div style="clear:both;float:none;"></div>
    <br>
    <br>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12"> <label class="text-primary"><h1>Daily Campaign Updates</h1></label></div>
            </div>
            <div class="row">
                <div class="col-md-3"> <label>Start Date</label><br>
                    <input type="date" class="form-control input-control" name="campaign_start_date" value="" /><br></div>
                <div class="col-md-3">
                    <label>End Date</label><br>
                    <input type="date" class="form-control input-control" name="campaign_end_date" value="" /><br>
                </div>
                <div class="col-md-3">
                    <label>Final Date</label><br>
                    <input type="date" class="form-control input-control" name="campaign_final_date" value="" /><br>
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label><br>

                    <a class="save form-control btn btn-primary">Save</a>
                </div>
            </div>
        </div>
    </div>

@endsection


@push('css')

    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
	<style>
	#campaignyear-table_filter label {
		text-align: right !important;
        padding-right: 10px;
	}
    .dataTables_scrollBody {
        margin-bottom: 10px;
    }
</style>
    <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">

@endpush

@push('js')
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>

    <script>

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        });

    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove();
        });
    }, 3000);

    var now = new Date('{{substr($settings->challenge_end_date,0,strpos($settings->challenge_end_date," "))}}');

    var day = ("0" + now.getDate()).slice(-2);
    var month = ("0" + (now.getMonth() + 1)).slice(-2);

    var today = now.getFullYear()+"-"+(month)+"-"+(day) ;

    $('[name=challenge_end_date]').val(today);

    var now = new Date('{{substr($settings->challenge_start_date,0,strpos($settings->challenge_start_date," "))}}');

    var day = ("0" + now.getDate()).slice(-2);
    var month = ("0" + (now.getMonth() + 1)).slice(-2);

    var today = now.getFullYear()+"-"+(month)+"-"+(day) ;

    $('[name=challenge_start_date]').val(today);

        var now = new Date('{{substr($settings->challenge_final_date,0,strpos($settings->challenge_final_date," "))}}');

        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);

        var today = now.getFullYear()+"-"+(month)+"-"+(day) ;

        $('[name=challenge_final_date]').val(today);

        var now = new Date('{{substr($settings->campaign_final_date,0,strpos($settings->campaign_final_date," "))}}');

        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);

        var today = now.getFullYear()+"-"+(month)+"-"+(day) ;

        $('[name=campaign_final_date]').val(today);

        var now = new Date('{{substr($settings->campaign_start_date,0,strpos($settings->campaign_start_date," "))}}');

        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);

        var today = now.getFullYear()+"-"+(month)+"-"+(day) ;

        $('[name=campaign_start_date]').val(today);

        var now = new Date('{{substr($settings->campaign_end_date,0,strpos($settings->campaign_end_date," "))}}');

        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);

        var today = now.getFullYear()+"-"+(month)+"-"+(day) ;

        $('[name=campaign_end_date]').val(today);


    $("input").change(function(){
        $.post("/settings/challenge",
            {
                'name': $(this).attr("name"),
                'value': $(this).val()
            },
            function (data, status) {
                Swal.fire({
                    title: '<strong>Success!</strong>',
                    icon: 'success',
                    html:
                        'Setting was changed',
                    showCloseButton: false,
                    showCancelButton: true,
                    focusConfirm: false,
                }).then((result) => {

                });
            },"json");
    });

    $(".save").click(function(){
       $("input").trigger("change");
    });

    </script>
@endpush
