<style>
.modal-body {
    overflow-y: auto; /* Add a vertical scrollbar when content overflows */
}


.modal-body{
    max-height: 80vh; /* Set a maximum height for the modal content */
}
</style>    

@extends('adminlte::page')
@section('content_header')
    <div class="d-flex mt-3">
        <h1>Pledge Administration</h1>
        <div class="flex-fill"></div>
    </div>
    <br>
    <br>
    @include('admin-pledge.partials.tabs')
@endsection
@section('content')
    <a href="/admin-pledge/maintain-event">Event Pledge List ></a> <span><strong>Event Submission Queue</strong></span>
    <br>
    <br>
    <p><a href="/admin-pledge/maintain-event"><button class="btn btn-outline-primary" role="button"  >Back To List</button></a></p>
    <div style="clear:both;float:none;"></div>
    <br>
    <br>
    <div class="card">
        <div class="card-body">
            <h3 class="blue">PECSF Event Submissions Queue</h3>

            <table class="table">
                <thead>
                <tr>
                    <th>Tran ID</th>
                    <th>Donation Type</th>
                    {{-- <th>Submitter ID</th> --}}
                    <th>Form Submitter Name</th>
                    <th>Deposit Amount</th>
                    <th>Org</th>
                    <th>PECSF ID</th>
                    <th>EMPLID</th>
                    <th>Business Unit</th>
                    <th>Dept ID</th>
                    <th>Dept Name</th>
                    <th>Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($submissions as $key => $submission)
                    @include('admin-pledge.submission-queue.partials.result_row')
                @endforeach
                </tbody>
            </table>


        </div>
    </div>

    <div class="modal fade" id="edit-event-modal" tabindex="-1" >
        <div class="modal-dialog custom-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-charity-name" id="charity-modal-label">Submission Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- content will be load here -->
                    <button class="btn btn-primary edit">Edit</button>
                    <div id="edit-event-modal-body">
                        @include('admin-pledge.submission-queue.partials.form')
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @include('admin-pledge.submission-queue.partials.reginal-pool')
    <div class="modal fade" id="lock-event-modal">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-charity-name" id="charity-modal-label">Lock this submission?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- content will be load here -->
                    <p>If a new submission is required current submission should be locked to prevent accidental use.<br>This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="submission_id" />
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger lock-submission">Lock Submission <i class="fa fa-lock" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('css')

    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">

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
        .custom-modal {
            max-width: 80%; /* Set the desired width */
        }
    </style>

@endpush
@push('js')

    <script src="{{ asset('vendor/select2/js/select2.min.js') }}" ></script>
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}" ></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}" ></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>

    <script type="x-tmpl" id="organization-tmpl">
        @include('volunteering.partials.add-organization', ['index' => 'XXX', 'charity' => "YYY"] )
    </script>

    <script type="x-tmpl" id="attachment-tmpl">
        @include('volunteering.partials.add-attachment', ['index' => 'XXX'] )
    </script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            }
        });

        $(".edit").click(function(){
            $("#edit-event-modal").find("select").attr("disabled",false);
            $("#edit-event-modal").find("input").attr("disabled",false);
            $("#edit-event-modal").find("button").attr("disabled",false);
        });

        $(document).on("click", ".edit-event-modal" , function(e) {
            e.preventDefault();
            var row_number = 0;
            $("#form_id").val($(this).attr("form-id"));
            $("#bank_deposit_form").attr("action","/bank_deposit_form/update")
            $("#sub_type").attr("disabled",false);
            $.get("/admin-pledge/details",
                {
                    form_id: $(this).attr("form-id")
                },
                function (data, status) {
                    $('#organizations').html("");
                    $("#event_type").val(data[0].event_type).select2();
                    $("#business_unit").val(data[0].business_unit).select2();
                    $("[name='event_type']").trigger("change");
                    $("#deposit_amount").val(data[0].deposit_amount);
                    $("#deposit_date").val(data[0].deposit_date);
                    $("#campaign_year").html( (data[0].calendar_year - 1));
                    $("#employee_name").val(data[0].employee_name);

                    if(data[0].event_type == "Fundraiser" || data[0].event_type == "Gaming"){                        
                        $("#sub_type").val(data[0].sub_type).select2();
                        $("#pecsf_id").val(data[0].pecsf_id);
                        $("#bc_gov_id").val(data[0].bc_gov_id);

                        //fundraiser and gaming are group donation types. no need idividual id
                        $("#bcgovid").find("label").hide();
                        $("#bcgovid").find("input").hide();
                        $("#bcgovid").hide();

                        $("#pecsfid").find("label").show();
                        $("#pecsfid").find("input").show();                            
                        $("#pecsfid").show();
                    }
                    else{
                        $("#address_1").val(data[0].address_line_1);
                        $("#city").val(data[0].address_city).select2();
                        $("#province").val(data[0].address_province).select2();
                        $("#postal_code").val(data[0].address_postal_code);
                        $("#sub_type").val(data[0].sub_type).select2();
                        $("#pecsf_id").val(data[0].pecsf_id);
                        $("#bc_gov_id").val(data[0].bc_gov_id);

                        if(data[0].organization_code == "GOV"){
                            $("#pecsfid").find("label").hide();
                            $("#pecsfid").find("input").hide();
                            $("#bcgovid").find("label").show();
                            $("#bcgovid").find("input").show();
                            $("#pecsfid").hide();
                            $("#bcgovid").show();
                        }
                        else{
                            $("#pecsfid").find("label").show();
                            $("#pecsfid").find("input").show();
                            $("#bcgovid").find("label").hide();
                            $("#bcgovid").find("input").hide();
                            $("#pecsfid").show();
                            $("#bcgovid").hide();
                        }

                    }

                    $("#employment_city").val(data[0].employment_city).select2();
                    $("#region").val(data[0].region_id).select2();
                    $("#description").val(data[0].description);

                    $("#organization_code").html("<option value='"+data[0].organization_code+"'>"+data[0].organization_code+"</option>");
                    $("#organization_code").select2({
                        ajax: {
                            url: '/bank_deposit_form/organization_code',
                            dataType: 'json'
                        }
                    });



                    if(data[0].charities.length > 0){
                        $("#organizations").show();
                        $(".org_hook").show();
                        $("#add_row").show();
                        $(".form-pool").hide();
                        $("input[value='dc']").attr("checked","checked");


                        for(i=0;i<data[0].charities.length;i++){
                            text = $("#organization-tmpl").html();
                            text = text.replace(/XXX/g, row_number + 1);
                            $('#organizations').append( text );
                            $("#organizations").css("display","block");
                            row_number++;
                            $('.organization').last().find(".organization_name").val(data[0].charities[i].organization_name);
                            $('.organization').last().find("[name='id[]']").val(data[0].charities[i].vendor_id);
                            $('.organization').last().find("[name='vendor_id[]']").val(data[0].charities[i].vendor_id);
                            $('.organization').last().find("[name='donation_percent[]']").val(data[0].charities[i].donation_percent);
                            $('.organization').last().find("[name='additional[]']").val(data[0].charities[i].specific_community_or_initiative);
                            $(".next_button").attr("disabled",false);
                        }
                    }
                    else{
                        $("#pool"+data[0].regional_pool_id).attr("checked",true);
                    }
                    $("#edit-event-modal-body").find(".col-md-2").removeClass("col-md-2").addClass("col-md-4");

                    let attachment_number = 1;
                    $(".upload-area").hide();
                    $('#attachments').html("");
                    for(i=0;i<data[0].attachments.length;i++) {
                        text = $("#attachment-tmpl").html();
                        text = text.replace(/XXX/g, attachment_number + 1);
                        $('#attachments').append( text );
                        attachment_number++;
                     $('.attachment').last().find(".filename").html("Attachment #"+(i + 1)+" "+data[0].attachments[i].local_path.substring(data[0].attachments[i].local_path.lastIndexOf("/"))+" ");
                        $('.attachment').last().find(".view_attachment").attr("href","/bank_deposit_form/download"+data[0].attachments[i].local_path.substring(data[0].attachments[i].local_path.lastIndexOf("/")));
                    }
                    $("#edit-event-modal").find("select").attr("disabled",true);
                    $("#edit-event-modal").find("input").attr("disabled",true);
                    $("#edit-event-modal").nextAll("button").attr("disabled",true);

                    if(data[0].existing == true){
                        if(data[0].event_type == "Fundraiser" || data[0].event_type == "Gaming"){
                            $("#pecsfid").html($("#pecsfid").html());
                            $("#pecsfid *,#pecsfid").show();
                            $("#bcgovid").hide();
                            $(".pecsf_id_errors").html("There is a previous Cash or Cheque One-time donation submission from this user. A PECSF ID pre-pended with an S is required for this field.");
                        } else {
                            if(data[0].organization_code == "GOV"){
                                $("#bcgovid *,#bcgovid").show();
                                $("#bcgovid").show();
                                if($("#event_type").val().toLowerCase() == "cheque one-time donation" || $("#event_type").val().toLowerCase() == "cash one-time donation"){
                                    $("#pecsfid *,#pecsfid").show();
                                }
                            }else{
                                $("#pecsfid").html($("#pecsfid").html());
                                $("#pecsfid *,#pecsfid").show();
                                $("#bcgovid").hide();
                                $(".pecsf_id_errors").html("There is a previous Cash or Cheque One-time donation submission from this user. A PECSF ID pre-pended with an S is required for this field.");
                            }
                        }
                    }

                    $('#edit-event-modal').modal('show');
                },"json");
        });

        $("select").select2();

        $(".status").change(function(e){
            e.preventDefault();
            $("#submission_id").val($(this).attr("submission_id"))
            if($(this).val()==2)
            {
                $(this).val(0).select2(
                    {
                        templateResult:formatState,
                        templateSelection:formatState
                    }
                );
                $('#lock-event-modal').modal("show");
            }
            else if($(this).val() == 1){
             Swal.fire({
                   title: 'Approved?',
                    text: 'Should We Approved This Event?',
                    showCloseButton: true,
                    showCancelButton: true,
                    focusConfirm: false,
                    confirmButtonText:
                        '<i class="fa fa-thumbs-up"></i> Approve!',
                    confirmButtonAriaLabel: 'Approved!',
                    cancelButtonText:
                        '<i class="fa fa-thumbs-down"></i> Maybe later',
                    cancelButtonAriaLabel: 'Maybe later.'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(this).parents("tr").remove();
                        $.post("/admin-pledge/status",
                            {
                                submission_id: $("#submission_id").val(),
                                status: 1
                            },
                            function (data, status) {

                                Swal.fire({
                                    title: '<strong>Success!</strong>',
                                    icon: 'info',
                                    html:
                                        'The Event was successfully approved and can be viewed in the main list',
                                    showCloseButton: true,
                                    showCancelButton: true,
                                    focusConfirm: false,
                                    confirmButtonText:
                                        '<i class="fa fa-thumbs-up"></i> Go To list',
                                    confirmButtonAriaLabel: 'Go To list',
                                    cancelButtonText:
                                        'Close',
                                    cancelButtonAriaLabel: 'Close'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "/admin-pledge/maintain-event";
                                    }
                                });
                            });                            
                    } else {
                        $(".status").val(0).trigger("change");
                    }
                    $('.modal').modal('hide');
                    window.location.href = "/admin-pledge/submission-queue";                    
                });

               }

               else{

                $.post("/admin-pledge/status",
                    {
                        submission_id: $("#submission_id").val(),
                        status: 0
                    },
                    function (data, status) {
                    });
        }
        });

        $(".lock-submission").click(function(){
            $(".status"+$("#submission_id").val()).val(2).select2(
                {
                    templateResult:formatState,
                    templateSelection:formatState
                });
            $.post("/admin-pledge/status",
                {
                    submission_id: $("#submission_id").val(),
                    status: 2
                },
                function (data, status) {
                });

            $('#lock-event-modal').modal("hide");
        });

        $(".closeModalBtn").click(function() {
                $("#regionalPoolModal").modal("hide"); 
            });

    </script>
    @include('admin-pledge.submission-queue.partials.add-event-js')
    @include('donate.partials.choose-charity-js')
@endpush
