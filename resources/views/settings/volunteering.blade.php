@extends('adminlte::page')

@section('content_header')




    <div class="d-flex mt-3">
        <h1>Volunteer Settings</h1>
        <div class="flex-fill"></div>
    </div>
    <br>
    <br>
    @include('settings.partials.tabs')

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
                <div class="col-md-12"> <label>Start Date</label><br>
                    <input type="date" class="form-control" name="volunteer_start_date" value="" /><br></div>
            </div>
            <div class="row">
                <div class="col-md-12">
                <label>End Date</label><br>
                <input type="date" class="form-control" name="volunteer_end_date" value="" /><br>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label>Language</label><br>
                    <textarea  class="form-control" name="volunteer_language" value="{{$settings->volunteer_language}}"></textarea><br>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="pledgeModal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="">More Info</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Deposit Date</th>
                            <th>Deposit Amount</th>
                            <th>Description</th>
                            <th>Employment City</th>
                        </tr>
                        </thead>
                        <tbody id="more_info_pledge">

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('css')
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">    

    <link href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">    
    <style>
        #campaign-table_filter label {
            text-align: right !important;
            padding-right: 10px;
        }
        .dataTables_scrollBody {
            margin-bottom: 10px;
        }

        .select2 {
            width:100% !important;
        }
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

        table tr{
            background:#fff;
        }
    </style>
    <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">

@endpush
@push('js')
    <script src="{{ asset('vendor/select2/js/select2.min.js') }}" ></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        });
        var now = new Date('{{substr($settings->volunteer_end_date,0,strpos($settings->volunteer_end_date," "))}}');

        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);

        var today = now.getFullYear()+"-"+(month)+"-"+(day) ;

        $('[name=volunteer_end_date]').val(today);

        var now = new Date('{{substr($settings->volunteer_start_date,0,strpos($settings->volunteer_start_date," "))}}');

        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);

        var today = now.getFullYear()+"-"+(month)+"-"+(day) ;

        $('[name=volunteer_start_date]').val(today);

        $("input,textarea").change(function(){

            $.post("{{ route("settings.change") }}",
                {
                    name: $(this).attr("name"),
                    value: $(this).val()
                },
                function (data) {

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
                },'json');

        });
</script>


@endpush

