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

    <div class="modal fade" id="edit-event-modal" >
        <div class="modal-dialog custom-modal">
            <div class="modal-content">
                <div class="modal-header bg-primary">
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
    <link href="{{ asset('vendor/toastr/toastr.min.css') }}" rel="stylesheet">

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

        input[type='radio'] {
            accent-color: #1A5A96;
            height:20px; 
            width:20px; 
            vertical-align: middle;
        }
        
        input[name="keyword"] {
            border:#000 1px solid;
        }

    </style>

@endpush
@push('js')

    <script src="{{ asset('vendor/select2/js/select2.min.js') }}" ></script>
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}" ></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}" ></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>
    <script src="{{ asset('vendor/toastr/toastr.min.js') }}" ></script>

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
            $("#edit-event-modal").find("input:not(#form_submitter_name)").attr("disabled",false);
            $("#edit-event-modal").find("button").attr("disabled",false);

            $("#edit-event-modal").find(".specific_community_or_initiative").attr("disabled",false);
            $("#edit-event-modal").find(".organization_name").attr("disabled",true);
            
            $(".upload-area").show();
            $("#edit-event-modal").find(".specific_community_or_initiative").attr("disabled",false);
            $("#edit-event-modal").find(".organization_name").attr("disabled",true);
            $("#edit-event-modal").find("input.calendar_year").attr("disabled",true);

            $(".upload-area").show();

            // $('#organization_code').trigger('change');
        });

        $(document).on("click", ".edit-event-modal" , function(e) {
            e.preventDefault();
            var row_number = 0;
            var form_id = $(this).attr("form-id");
            $("#form_id").val($(this).attr("form-id"));
            $("#bank_deposit_form").attr("action","/bank_deposit_form/update")
            $("#sub_type").attr("disabled",false);
            $.get("/admin-pledge/details",
                {
                    form_id: $(this).attr("form-id")
                },
                function (data, status) {
                    $('#organizations').html("");
                    $('#form_submitter_name').val(data[0]['form_submitted_by']['name']);
                    $('#form_submitter').val(data[0]['form_submitter_id']);
                    $("#event_type").val(data[0].event_type).select2( {minimumResultsForSearch: -1} );
                    $("#business_unit").val(data[0].business_unit).select2();

                    $("#organization_code").html("<option value='"+data[0].organization_code+"'>"+data[0].organization.name+"</option>");
                    $("#organization_code").select2({
                        minimumResultsForSearch: -1,
                        ajax: {
                            url: '/bank_deposit_form/organization_code',
                            dataType: 'json'
                        }
                    });
                    $("[name='event_type']").trigger("change");                    
                    $("#deposit_amount").val(data[0].deposit_amount);
                    $("#deposit_date").val(data[0].deposit_date);
                    // $("#campaign_year").html( (data[0].calendar_year - 1));
                    $("input.calendar_year").val( (data[0].calendar_year - 1));
                    $("input[name='campaign_year']").val( data[0].calendar_year_id );
                    $("#employee_name").val(data[0].employee_name);

                    if(data[0].event_type == "Fundraiser" || data[0].event_type == "Gaming"){                        
                        $("#sub_type").val(data[0].sub_type).select2( {minimumResultsForSearch: -1} );
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
                        $("#sub_type").val(data[0].sub_type).select2( {minimumResultsForSearch: -1} );
                        $("#pecsf_id").val(data[0].pecsf_id);
                        $("#bc_gov_id").val(data[0].bc_gov_id);

                        if(data[0].organization_code == "GOV"){
                            //enable all event type by default
                            var eventTypeDropdown = $('#event_type');
                            eventTypeDropdown.find('option').prop('disabled', false);  

                            $("#pecsfid").find("label").hide();
                            $("#pecsfid").find("input").hide();
                            $("#bcgovid").find("label").show();
                            $("#bcgovid").find("input").show();
                            $("#pecsfid").hide();
                            $("#bcgovid").show();
                        }
                        else{                            

                            // If "non-GOV" is selected (except RET), disable specific options
                            // if(data[0].organization_code != "RET") {
                            //     disableOneTime(); 
                            // }
                            
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




                    if(data[0].charities.length > 0){
                        $("#organizations").show();
                        $(".org_hook").show();
                        $("#add_row").show();
                        $(".form-pool").hide();
                        $("input[value='dc']").attr("checked","checked");

                        $('#noselectedresults').hide();

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
                        $("#pool"+data[0].fund_supported_pool.region_id).trigger('click');

                    }
                    // $("#edit-event-modal-body").find(".col-md-2").removeClass("col-md-2").addClass("col-md-4");

                    let attachment_number = 1;
                    $(".upload-area").hide();
                    $('#attachments').html("");
                    for(i=0;i<data[0].attachments.length;i++) {
                        text = $("#attachment-tmpl").html();
                        text = text.replace(/XXX/g, attachment_number + 1);
                        $('#attachments').append( text );
                        attachment_number++;

                        $('.attachment').last().find(".filename").html("Attachment #"+(i + 1)+" - "+data[0].attachments[i].original_filename +" ");
                    //  $('.attachment').last().find(".view_attachment").attr("href","/bank_deposit_form/download"+data[0].attachments[i].local_path.substring(data[0].attachments[i].local_path.lastIndexOf("/")));
                        $('.attachment').last().find(".view_attachment").attr("href","/bank_deposit_form/download/"+ data[0].attachments[i].id);                     
                     //$('.attachment').last().find(".delete_attachment").attr("href","/bank_deposit_form/"+form_id+"/delete"+data[0].attachments[i].local_path.substring(data[0].attachments[i].local_path.lastIndexOf("/")));
                     //$('.attachment').last().find(".delete_attachment").attr("href","javascript:deleteAttachment("+form_id+", '"+data[0].attachments[i].local_path.substring(data[0].attachments[i].local_path.lastIndexOf("/"))+"');");

                    }
                    $("#edit-event-modal").find("select").attr("disabled",true);
                    $("#edit-event-modal").find("input").attr("disabled",true);
                    $("#edit-event-modal").nextAll("button").attr("disabled",true);

                    if(data[0].existing == true){
                        if(data[0].event_type == "Fundraiser" || data[0].event_type == "Gaming"){
                            // $("#pecsfid").html($("#pecsfid").html());
                            $("#pecsfid *,#pecsfid").show();
                            $("#bcgovid").hide();
                            // $(".pecsf_id_errors").html("There is a previous Cash or Cheque One-time donation submission from this user. A PECSF ID pre-pended with an S is required for this field.");
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

        // $("select").select2();

        function deleteAttachment(formid, filename){

            // Construct the URL with parameters
            var deleteUrl = '/bank_deposit_form/' + formid + '/delete' + filename;

            // Send an AJAX request to delete the attachment
            $.ajax({
                type: 'GET', // GET method
                url: deleteUrl, // The URL with parameters
                success: function(data) {
                    // Handle the response, e.g., display a success message
                    $('a[attr-id="' + attrIdValue + '"]').hide();
                },
                error: function(xhr, status, error) {
                    alert("An error occurred: " + error);
                }
            });
        
        }


        // function disableOneTime() {
        //     var eventTypeDropdown = $('#event_type');

        //     // Enable all options
        //     eventTypeDropdown.find('option').prop('disabled', false);

        //     // eventTypeDropdown.find('option[value="Cash One-Time Donation"]').prop('disabled', true);
        //     // eventTypeDropdown.find('option[value="Cheque One-Time Donation"]').prop('disabled', true);

        //     // Set the selected index and update the displayed option text
        //     var selectedIndex = 0; // Index of the default option
        //     eventTypeDropdown.find('option').eq(selectedIndex).prop('selected', true);
        //     eventTypeDropdown.trigger('change');
        // }


        // $('#organization_code').change(function(e){
        //     var selectedOrganization = $(this).val();
        //     if (selectedOrganization !== 'GOV' && selectedOrganization !== 'RET') {
        //         disableOneTime();    
        //     } else {
        //         var eventTypeDropdown = $('#event_type');
        //         eventTypeDropdown.find('option').prop('disabled', false);
        //     }    
        // });    



        $(".status").change(function(e){
            e.preventDefault();
            $("#submission_id").val($(this).attr("submission_id"))
            // if($(this).val()==2)
            // {
            //     $(this).val(0).select2(
            //         {
            //             templateResult:formatState,
            //             templateSelection:formatState
            //         }
            //     );
            //     $('#lock-event-modal').modal("show");
            // }
            // else 
            if($(this).val() == 1){
             Swal.fire({
                   title: 'Approved?',
                    text: 'Should we approve this event ?',
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

                                // Swal.fire({
                                //     title: '<strong>Success!</strong>',
                                //     icon: 'info',
                                //     html:
                                //         'The Event was successfully approved and can be viewed in the main list',
                                //     showCloseButton: true,
                                //     showCancelButton: true,
                                //     focusConfirm: false,
                                //     confirmButtonText:
                                //         '<i class="fa fa-thumbs-up"></i> Go To list',
                                //     confirmButtonAriaLabel: 'Go To list',
                                //     cancelButtonText:
                                //         'Close',
                                //     cancelButtonAriaLabel: 'Close'
                                // }).then((result) => {
                                //     if (result.isConfirmed) {
                                //         window.location.href = "/admin-pledge/maintain-event";
                                //     }
                                // });
                                window.location.href = "/admin-pledge/submission-queue";                    
                            });                            
                    } else {
                        $(".status").val(0).trigger("change");
                    }
                    $('.modal').modal('hide');
                    // window.location.href = "/admin-pledge/submission-queue";                    
                });

            } else if ($(this).val() == 2) {

                    Swal.fire( {
                        title: 'Are you sure you want to lock the event pledge "' + $("#submission_id").val() + '" ?',
                        text: 'This action cannot be undone.',
                        // icon: 'question',
                        //showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonText: 'Yes, lock it!',
                        buttonsStyling: false,
                        //confirmButtonClass: 'btn btn-danger',
                        customClass: {
                            confirmButton: 'btn btn-danger', //insert class here
                            cancelButton: 'btn btn-secondary ml-2', //insert class here
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {

                            $.post("/admin-pledge/status",
                            {
                                submission_id: $("#submission_id").val(),
                                status: $(this).val(),
                            },
                            function (data, status) {
                                // Swal.fire({
                                //     icon: "success",
                                //     title: "The pledge " + $("#submission_id").val() + " has been locked.",
                                //     showConfirmButton: true,
                                // }).then((result) => {
                                //     window.location.href = "/admin-pledge/submission-queue";     
                                // });
                                // Swal.fire('Status updated!', '', 'success');
                                window.location.href = "/admin-pledge/submission-queue";  
                            }).fail(function() {
                                Swal.fire('Status failed to update!', '', 'fail');
                                window.location.href = "/admin-pledge/submission-queue";     
                            });
                               
                        } else {
                            window.location.href = "/admin-pledge/submission-queue";     
                        }

                    });

            } else {

                $.post("/admin-pledge/status",
                    {
                        submission_id: $("#submission_id").val(),
                        status: $(this).val(),
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


            $(document).ready(function() {
                $('#edit-event-modal').on('hidden.bs.modal', function() {
                    location.reload();
                });
            });  


        $(function() {
            min_height = $(".content > .container-fluid").outerHeight();
            $(".wrapper").css('min-height', min_height );
        })

@if ($message = Session::get('success'))
    $(function() {
        toastr["success"]( "{{ $message }}", '',
            {"closeButton": true, "newestOnTop": true, "timeOut": "5000" });
    });
@endif

    </script>
    @include('admin-pledge.submission-queue.partials.add-event-js')
    @include('donate.partials.choose-charity-js')

    @include('volunteering.partials.dropzone-js')    
@endpush
