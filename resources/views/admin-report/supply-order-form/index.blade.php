@extends('adminlte::page')
@section('content_header')
    {{-- <h2>Reporting</h2> --}}
    @include('admin-report.partials.tabs')

    <h4 class="mx-1 mt-3">Supply Order Form Reporting</h4>
  
    <div class="d-flex mt-3">
        <div class="flex-fill">
            <p><button class="ml-2 btn btn-outline-primary" onclick="window.location.href='/administrators/dashboard'">
                Back    
            </button></p>    
        </div>

        <div class="d-flex">
            <div class="mr-2">
                <button type="button" class="active btn btn-primary">Find a Report</button>
                <button type="button" class="btn btn-outline-primary">Edit Supply Order Form</button>
            </div>
        </div>
    </div>

@endsection
@section('content')

    <div class="modal fade" style="display:none" id="edit-supply-modal" role="document">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-charity-name text-light" id="charity-modal-label">Supply Order Form Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="edit-supply-modal-body">


                        @include('volunteering.partials.supply')



                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="card">

        <div class="row">
            <div class="col-md-12 justify-content-start">
                <h3 class="main-blue">Filter Reports</h3>
            </div>
        </div>

        <div class=" row">

            <div class="col-md-4">

                <label>
Employee Name</label>
                    <input class="form-control" name="employee_name" id="employee_name" value="{{$request->employee_name}}"/>


            </div>

            <div class="col-md-4">
                <label>
              Organization Code </label>
                <input class="form-control" value="{{$request->organization_code}}" name="organization_code" id="organization_code"/>

            </div>
            <div class="col-md-2">
                <label>
                   Date Received </label>
                    <select id="month" class="form-control" name="month">
                        <option value="">Select a Month</option>
                    @foreach($months as $month)
                   <option {{$request->month == $month ? "selected" : "" }} value="{{$month}}">{{$month}}</option>
                  @endforeach
                    </select>

            </div>
            <div class="col-md-2">
                <label>
                   Select a Year </label>
                <select id="year" class="form-control" name="year">
                    <option value="">Select a Year</option>
                    @foreach($years as $year)
                        <option {{$request->year == $year ? "selected" : "" }} value="{{$year}}">{{$year}}</option>
                    @endforeach

                </select>
            </div>
        </div>

        <div class="row mt-4">

            <div class="col-md-2">
                <button class="form-control search btn btn-primary">Search</button>
            </div>
            <div class="col-md-2">
                <button class="form-control clear btn btn-outline-primary">Clear</button>
            </div>
        </div>


    </div>

    <div class="card">
        <div class="row">
            <div class="col-md-12 justify-content-start">
                <h3 class="main-blue">Results</h3>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2 justify-content-start">
                <button class="form-control delete btn btn-danger">Delete (<span id="delete_count">0</span>)</button>
            </div>
            <div class="col-md-2 justify-content-start">
                <button class="form-control export btn btn-primary">Export Data Set (<span id="export_count">0</span>)</button>
            </div>
        </div>

        <div class="mt-5 row">
            <div class="col-md-12 justify-content-center">
                <table class="table">

                    <thead>
                    <th>Select</th>
                    <th>Name</th>
                    <th>Date Received</th>
                    <th>Organization</th>
                    <th>Status</th>
                    <th>Options</th>
                    </thead>

                    @foreach($forms as $form)
                        <tbody>
                        <td><input class="form-control select" type="checkbox" value="{{$form->id}}" name="supply_order_form_selection[]" /></td>
                        <td>{{$form->first_name." ".$form->last_name}}</td>
                        <td>{{gmdate("Y-m-d H:i:s",strtotime($form->created_at))}}</td>
                        <td>{{$form->name}}</td>
                        <td><select>
                                <option value="1">Active</option>
                                <option value="0">Disabled</option>
                            </select></td>
                        <td><button class="btn btn-primary details" supply_order_form_id={{$form->id}} unit_suite_floor="{{$form->unit_suite_floor}}" date_required="{{gmdate("Y-m-d",strtotime($form->date_required))}}" comments="{{$form->comments}}" po_province="{{$form->po_province}}" po_postal_code="{{$form->po_postal_code}}" po="{{$form->po}}" po_city="{{$form->po_city}}" city="{{$form->city}}" province="{{$form->province}}" postal_code="{{$form->postal_code}}" physical_address="{{$form->physical_address}}"  address_type="{{$form->address_type}}" include_name="{{$form->include_name}}" business_unit_id="{{$form->business_unit_id}}" last_name="{{$form->last_name}}" first_name="{{$form->first_name}}" ten_rolls="{{$form->ten_rolls}}" five_rolls="{{$form->five_rolls}}" two_rolls="{{$form->two_rolls}}" calendars="{{$form->calendar}}" posters="{{$form->posters}}" stickers="{{$form->stickers}}">View / Edit</button></td>
                        </thead>

                        @endforeach
                </table>
            </div>

        </div>


    </div>


@endsection
@push('css')

    <link href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/datatables-plugins/fixedheader/css/fixedHeader.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">

    <style>
        #employee-table_filter label {
            display:none;
        }
        #employee-table_filter label {
            text-align: right !important;
            padding-right: 10px;
        }
        .dataTables_scrollBody {
            margin-bottom: 10px;
        }

        /* Blink */
        .blink {
            animation: blinker 0.6s linear infinite;
            /* color: #1c87c9;
            font-size: 30px;
            font-weight: bold;
            font-family: sans-serif; */
        }
        @keyframes blinker {
            50% {
                opacity: 0;
            }
        }
        .blink-one {
            animation: blinker-one 1s linear infinite;
        }
        @keyframes blinker-one {
            0% {
                opacity: 0;
            }
        }
        .blink-two {
            animation: blinker-two 1.4s linear infinite;
        }
        @keyframes blinker-two {
            100% {
                opacity: 0;
            }
        }

    </style>
@endpush


@push('js')

    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}" ></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}" ></script>
    <script src="{{ asset('vendor/datatables-plugins/fixedheader/js/dataTables.fixedHeader.min.js') }}" ></script>

    <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>

    <script>

        $(".select").click(function(){
          $("#delete_count").html($("[name='supply_order_form_selection[]']:checked").length);  ;
          $("#export_count").html($("[name='supply_order_form_selection[]']:checked").length);  ;
        });


        $(".export").click(function(){
            var formData = new FormData();
            formData.append("supply_order_form_selection[]", $.map($("[name='supply_order_form_selection[]']:checked"), function(e,i) {
            return +e.value;
            }));

            window.open("/reporting/supply-report/export?supply_order_form_selection="+$.map($("[name='supply_order_form_selection[]']:checked"), function(e,i) {
                return +e.value;
            }).join("-"));



        });

        $(".delete").click(function(){
            var formData = new FormData();
            formData.append("supply_order_form_selection[]", $.map($("[name='supply_order_form_selection[]']:checked"), function(e,i) {
                return +e.value;
            }));

            window.location.href = "/reporting/supply-report/delete?supply_order_form_selection="+$.map($("[name='supply_order_form_selection[]']:checked"), function(e,i) {
                return +e.value;
            }).join("-");

setTimeout(function(){window.location.reload();},3000);

        });

        $(".search").click(function(){

            window.location = "/reporting/supply-report/?year="+$("#year").val()+"&month="+$("#month").val()+"&employee_name="+$("#employee_name").val()+"&organization_code="+$("#organization_code").val();

        });

        $(".clear").click(function(){

            window.location = "/reporting/supply-report/";

        });

        $("#supply_order_form").attr("action","/reporting/supply-report");
        $("#supply_order_form").attr("method","UPDATE");
        $("#submit_hook").val("Update");
        var formData = new FormData();
        $("#supply_order_form").submit(function(e)
        {
            e.preventDefault();
            var form = document.getElementById("create_supply_order_form");



            $("select").each(function(){
                if($(this).val()){
                    if($(this).val().length > 0){
                        formData.append($(this).attr("name"), $(this).val());
                    }
                }

            });
            $("input").each(function(){
                if($(this).attr('type') != "submit"){
                    if($(this).attr('type') == "radio"){
                        if($(this).is(':checked')){
                            formData.append($(this).attr("name"), $(this).val());
                        }
                    }
                    else if($(this).attr('type') == "file"){
//formData.append('attachments[]',  $(this)[0].files[0]);
                    }
                    else{
                        formData.append($(this).attr("name"), $(this).val());
                    }
                }
            });
            $("textarea").each(function(){
                if($(this).val().length > 0) {
                    formData.append($(this).attr("name"), $(this).val());
                }
            });


            $(this).fadeTo("slow",0.2);
            $.ajax({
                url: $("#supply_order_form").attr("action"),
                type: "POST",
                data: formData,
                headers: {'X-CSRF-TOKEN': $("input[name='_token']").val()},
                processData: false,
                cache: false,
                contentType: false,
                dataType: 'json',
                success:function(response){
                    window.locationredirect = response[0];
                    console.log(response);
                    Swal.fire({
                        title: '<strong>Success!</strong>',
                        icon: 'info',
                        html:
                            'The form was submitted successfully. Your items will be sent in the mail within 3-5 business days. For assistance, please email pecsf@gov.bc.ca. For information and resources, please visit the PECSF website (gov.bc.ca). ',
                        showCloseButton: true,
                        showCancelButton: true,
                        focusConfirm: false,

                        confirmButtonAriaLabel: 'Volunteers!',
                        cancelButtonText:
                            'Close',
                        cancelButtonAriaLabel: 'Close'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "/reporting/supply-report";
                        }
                    });
                    $("#supply_order_form").fadeTo("slow",1);
                    $('.errors').html("");


                },
                error: function(response) {
                    $('.invalid-feedback').html("");
                    $("#supply_order_form").fadeTo("slow",1);

                    if(response.responseJSON.errors){
                        errors = response.responseJSON.errors;
                        for(const prop in response.responseJSON.errors){

                            tag = prop;
                            error = errors[prop][0].split(".");
                            error = error[0];
                            error = error.replace("_"," ");

                            $("[name="+tag+"]").parents("label").append('<span class="invalid-feedback">'+error.replace("field"," field ")+'</span>');
                        }
                    }
                    $(".invalid-feedback").css("display","block");
                    $("#bank_deposit_form").fadeTo("slow",1);
                },
            });

        });

        $(".details").click(function(){
            for(i=1;i<$(this)[0].attributes.length;i++){
                $("[name='"+$(this)[0].attributes[i].name+"'").val($(this)[0].attributes[i].value);
            }
            $('#edit-supply-modal').modal('show');
        });

        $(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                }
            });

        });
    </script>
@endpush
