
<script>
$(function () {

    var business_unit_options = {!!json_encode($business_units)!!};

    $("#sub_type").select2( {minimumResultsForSearch: -1}  );
    $("#event_type").select2( {minimumResultsForSearch: -1} );
    $(".org_hook,#pecsfid,#bcgovid,#employeename,.address_hook,.sub_type").hide();


    $("#pecsf_id").change(function(){
        nongovuserinfo();
    });

    $("input[name='charity_selection']").click(function(){
        if($(this).val() == "dc"){
            $("#organizations").show();
            $(".org_hook").show();
            $("#add_row").show();
            $(".form-pool").hide();
            $("#pool_filter").parents(".form-group").show();
        } else {
            $(".form-pool").show();
            $("#organizations").hide();
            $("#add_row").hide();
            $(".org_hook").hide();
            $("#pool_filter").parents(".form-group").hide();
        }
    });

    // $("#event_type").change(function(){
    //     $(".sub_type").hide();
    //     $("#bcgovid").hide();
    //     $("#pecsfid").hide();
    //         if($("#event_type").val().toLowerCase() == "cheque one-time donation" || $("#event_type").val().toLowerCase() == "cash one-time donation"){
    //             $("#employeename").show();
    //             $(".sub_type").show();
    //             if($("[name='organization_code']").val() == "GOV"){
    //                 $("#pecsfid").find("input").hide();
    //                 $("#bcgovid").find("input").show();
    //                 $("#pecsfid").find("label").hide();
    //                 $("#bcgovid").find("label").show();
    //             }
    //             else{
    //                 $("#pecsfid").find("input").show();
    //                 $("#bcgovid").find("input").hide();
    //                 $("#pecsfid").find("label").show();
    //                 $("#bcgovid").find("label").hide();
    //             }
    //         }
    // });

    $("[name='event_type'],[name='organization_code']").change(function(){
        $("#employeename").hide();
        $(".sub_type").hide();
        $("#bcgovid").hide();
        $("#pecsfid").hide();

        if($(this).val()=="Fundraiser"){
            $("#sub_type").html('<option value="none">None</option><option value="Auction">Auction</option><option value="Entertainment">Entertainment</option><option value="Food">Food</option><option value="Other">Other</option><option value="Sports">Sports</option>');
            $(".address_hook").hide();
            $("#sub_type").select2( {minimumResultsForSearch: -1} );
            $(".sub_type").show();
            // if($("#event_type").val().toLowerCase() == "cheque one-time donation" || $("#event_type").val().toLowerCase() == "cash one-time donation"){
            //     $("#employeename").show();


        // if($("[name='organization_code']").val() == "GOV"){
            // $("#pecsfid").find("input").show();
            $("#bcgovid").find("input").hide();
            // $("#pecsfid").find("label").show();
            $("#bcgovid").find("label").hide();
            // $("#pecsfid").show();
            $("#bcgovid").hide();

            $("#bcgovid").find("input").val("");

        // }
        // else{
        //     $("#pecsfid").find("input").show();
        //     $("#bcgovid").find("input").hide();
        //     $("#pecsfid").find("label").show();
        //     $("#pecsfid").show();

            // $("#bcgovid").find("label").hide();
        // }
            // }
        } else if($(this).val()=="Gaming") {

            $("#sub_type").html('<option value="50/50 Draw">50/50 Draw</option><option value="none">None</option>');
            $(".address_hook").hide();
            $("#sub_type").select2( {minimumResultsForSearch: -1} );
            $(".sub_type").show();

    // if($("#event_type").val().toLowerCase() == "cheque one-time donation" || $("#event_type").val().toLowerCase() == "cash one-time donation"){
    //     $("#employeename").show();


        // if($("[name='organization_code']").val() == "GOV"){
        //     $("#pecsfid").find("input").hide();
        //     $("#bcgovid").find("input").show();
        //     $("#pecsfid").find("label").hide();
        //     $("#bcgovid").find("label").show();
        //     $("#bcgovid").show()
        // }
        // else{
            // $("#pecsfid").find("input").show();
            $("#bcgovid").find("input").hide();
            // $("#pecsfid").find("label").show();
            $("#bcgovid").find("label").hide();
            $("#bcgovid").hide();
            // $("#pecsfid").show();

        // }
            $("#bcgovid").find("input").val("");
    // }
        } else {
              // do nothing 
        }
    
        if($("[name='organization_code']").val() == "GOV"){

            $('#event_type>option:not([value=""]').prop('disabled',false);
            // $("#event_type>option[value='Fundraiser']").prop('disabled',false);
            // $("#event_type>option[value='Gaming']").prop('disabled',false);
            
            if($("#event_type").val().toLowerCase() == "cheque one-time donation" || $("#event_type").val().toLowerCase() == "cash one-time donation"){
            
            // if($("[name='organization_code']").val() == "GOV"){
                // $("#pecsfid").find("input").hide();
                $("#bcgovid").find("input").show();
                // $("#pecsfid").find("label").hide();
                $("#bcgovid").find("label").show();
                $("#bcgovid").show();
                $("#employeename").show();


            } else {
                // $("#pecsfid").find("input").show();
                $("#bcgovid").find("input").hide();
                // $("#pecsfid").find("label").show();
                $("#bcgovid").find("label").hide();
                $("#bcgovid").hide();
                $("#employeename").show();

            }
        // }
        } else if ($("[name='organization_code']").val() == "RET"){
                // $("#pecsfid").find("label").hide();
                // $("#pecsfid").find("input").hide();
                $("#bcgovid").find("label").hide();
                $("#bcgovid").find("input").hide();

            if($("#event_type").val() == "Gaming" || $("#event_type").val() == "Fundraiser")
            {
                alert("Invalid Event Type for Retiree. We selected a default option on your behalf.");
                $("#event_type").val("Cash One-Time Donation").trigger("change");
            }
            $('#event_type>option:not([value=""]').prop('disabled',false);
            $("#event_type>option[value='Fundraiser']").prop('disabled',true);
            $("#event_type>option[value='Gaming']").prop('disabled',true);
            if($("#event_type").val().toLowerCase() == "cheque one-time donation" || $("#event_type").val().toLowerCase() == "cash one-time donation"){
                $("#employeename").show();
                // $("#pecsfid").find("input").show();
                // $("#pecsfid").find("label").show();
                // $("#pecsfid").show();
            }
        } else{
            $('#event_type>option:not([value=""]').prop('disabled',false);
            // $("#event_type>option[value='Fundraiser']").prop('disabled',false);
            // $("#event_type>option[value='Gaming']").prop('disabled',false);
            // if($("#event_type").val().toLowerCase() == "cheque one-time donation" || $("#event_type").val().toLowerCase() == "cash one-time donation"){
            //     $("#employeename").show();

                // if($("[name='organization_code']").val() == "GOV"){
                //     $("#pecsfid").find("input").hide();
                //     $("#bcgovid").find("input").show();
                //     $("#pecsfid").find("label").hide();
                //     $("#bcgovid").find("label").show();
                //     $("#bcgovid").show();

                // }
                // else{
                    // $("#pecsfid").find("input").show();
                    $("#bcgovid").find("input").hide();
                    // $("#pecsfid").find("label").show();
                    $("#bcgovid").find("label").hide();
                    // $("#pecsfid").show();
                    $("#bcgovid").hide();
                    $("#employeename").show();

            //     }
            // }
        }

        $(".address_hook").show();

        $("#city").select2();
        $("#province").select2();
    // }

});

// $("body").on("change","[name='attachments[]']",function(){
// $(this).parents("tr").find(".filename").html( $(this)[0].files[0].name);
// });

// let attachment_number = 1;
// $("body").on("click",".add_attachment_row",function(e){
// e.preventDefault();

//     text = $("#attachment-tmpl").html();
//     text = text.replace(/XXX/g, attachment_number + 1);
//     $('.attachment').last().after( text );
//     attachment_number++;

// });

function nongovuserinfo(){
    $.get({
        url: '/admin-pledge/campaign-nongov-user' +
            '?org_id=' + $('#organization_code').val() +
            '&pecsf_id=' + $('#pecsf_id').val(),
        dataType: 'json',
        async: false,
        cache: false,
        timeout: 30000,
        success: function(data)
        {
            if(data && data.first_name != undefined && data.last_name != undefined) {
                $('#employee_name').val( data.last_name +","+ data.first_name );
                $("#employment_city").val(data.city).select2();
                $("#region").val($("#region [code="+$('#employment_city option[value="'+data.city+'"]').attr("region")+"]").val());
                $("#region").select2();
            }
        },
        error: function(response) {
        }
    });
}


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
            type:"POST",
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
                        window.location.href = "/volunteering";
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


    $("#bank_deposit_form").submit(function(e)
    {
        e.preventDefault();
        var form = document.getElementById("create_pool");

        total_count = $('#bank_deposit_form .dropzone .dz-complete').length;
        error_count = $('#bank_deposit_form .dropzone .dz-error').length;
        if (total_count > 3 ) {
            Swal.fire({
                title: "Problem on upload files",
                text: "You have reached the maximum number of allowed file uploads. To continue, please remove some files from your current selection and try again.",
                icon: "warning"
            });
            return; 
        }
        if (error_count > 0 ) {
            Swal.fire({
                title: "Problem on upload files",
                text: "You have uploaded some unsupported format or file size exceeds limit file(s). Please remove and upload a valid file within the specified size constraints",
                icon: "warning"
            });
            return;
        }

        $(".max-charities-error").hide();
        $(".charity-error-hook").css("border","none")

        formData = new FormData();

        $("#bank_deposit_form select").each(function(){
            // if($(this).val()){
            //     if($(this).val().length > 0){
                    formData.append($(this).attr("name"), $(this).val());
            //     }
            // }
        });

        $("#bank_deposit_form input").each(function(){
            if($(this).attr('type') != "submit"){
                if($(this).attr('type') == "radio"){
                    if($(this).is(':checked')){
                        formData.append($(this).attr("name"), $(this).val());
                    }
                } else if($(this).attr('type') == "file"){
                    if (this.value) {
                        formData.append('attachments[]',  $(this)[0].files[0]);
                    }
                } else{
                    // if($(this).val().length > 0){
                        formData.append($(this).attr("name"), $(this).val());
                    // }
                }
            }
        });

        $("textarea").each(function(){
            if($(this).val().length > 0) {
                formData.append($(this).attr("name"), $(this).val());
            }
        });

        formData.append('province', $('select[name="province"]').val() );

        formData.append("org_count", $(".organization").length);
        // formData.append("ignoreFiles", ignoreFiles);

        $(this).fadeTo("slow",0.2);
        $.ajax({
            url: $("#bank_deposit_form").attr("action"),
            type:"POST",
            data: formData,
            headers: {'X-CSRF-TOKEN': $("input[name='_token']").val()},
            processData: false,
            cache: false,
            contentType: false,
            dataType: 'json',
            success:function(response){
                Swal.fire({
                    title: '<strong>Success!</strong>',
                    icon: 'success',
                    html:
                        'Form Submitted!',
                    showCloseButton: false,
                    showCancelButton: true,
                    focusConfirm: false,
                }).then((result) => {
                    $("#bank_deposit_form").fadeTo("slow",1);
                    $('.errors').html("");

                    window.location = response[0];
                    console.log(response);
                });
                $('[submission_id='+$('#form_id').val()+']').val(1).trigger('change');
            },
            error: function(response) {
                $('.errors').html("");
                $(".donation_percent_errors").html("");
                if(response.responseJSON.errors){
                    errors = response.responseJSON.errors;
                    for(const prop in response.responseJSON.errors){
                        count = prop.substring(prop.indexOf(".")+1);
                        tag = prop.substring(0,prop.indexOf("."));
                        error = errors[prop][0].split(".");
                        error = error[0] + error[1].substring(1,error[1].length);
                        error = error.replace("_"," ");
                        $("."+prop+"_errors").html('<span class="invalid-feedback">'+error+'</span>');
                        $(".donation_percent_errors").eq((parseInt(prop.replace("donation_percent.",""))) - 1).html('<span class="invalid-feedback">'+error+'</span>');
                        $("."+prop.substring(0,(prop.indexOf(".") - 1 ))+"_errors").html('<span class="invalid-feedback">'+error+'</span>');
                    }
                }
                $(".invalid-feedback").css("display","block");
                $("#bank_deposit_form").fadeTo("slow",1);
            },
        });

    });

$('#organization_code').select2({
    minimumResultsForSearch: -1,
    ajax: {
        url: '/bank_deposit_form/organization_code',
        dataType: 'json'
    }
});


$('.organization_name').select2({
ajax: {
url: '/bank_deposit_form/organization_name',
dataType: 'json'
}
});

$("#employment_city,#region,#business_unit").select2();


$('.more-info').click( function(event) {
event.stopPropagation();
// var current_id = event.target.id;
id = $(this).attr('data-id');
name = $(this).attr('data-name');

console.log( 'more info - ' + id );
if ( id  ) {
// Lanuch Modal page for listing the Pool detail
$.ajax({
url: '/donate-now/regional-pool-detail/' + id,
type: 'GET',
// data: $("#notify-form").serialize(),
dataType: 'html',
success: function (result) {
$('#regionalPoolModal  .modal-title span').html(name);
target = '#regionalPoolModal .pledgeDetail';
$(target).html('');
$(target).html(result);
},
complete: function() {
},
error: function (result) {
    target = '.pledgeDetail';
    $(target).html('');
$(target).html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
}
})

$('#regionalPoolModal').modal('show')
}
});

$("body").on("click",".view_attachment",function(){
window.open(URL.createObjectURL($(this).parents("tr").find("input")[0].files[0]));
});

function readFile(file) {
const reader = new FileReader();
reader.addEventListener('load', (event) => {
const result = event.target.result;
// Do something with result
});
reader.readAsDataURL(file);
}


// $("html").on("drop", function(e) { e.preventDefault(); e.stopPropagation(); });
/*
// Drag enter
$('.upload-area').on('dragenter', function (e) {
e.stopPropagation();
e.preventDefault();
$("#upload-area-text").text("Drop");
});

// Drag over
$('.upload-area').on('dragover', function (e) {
e.stopPropagation();
e.preventDefault();
$("#upload-area-text").html("<span style='margin-left:75px'>Drop</span>");
});

// Drag over
$('.upload-area').on('dragleave', function (e) {
e.stopPropagation();
e.preventDefault();
$("#upload-area-text").html("Drag and Drop Or <u>Browse</u> Files");
});


// Drop
$('.upload-area').on('drop', function (e) {
e.stopPropagation();
e.preventDefault();
$(".attachment_errors").html("");
$("#upload-area-text").html("Drag and Drop Or <u>Browse</u> Files");
var file = e.originalEvent.dataTransfer.files;
var allowed = ["pdf","xls","xlsx","csv","png","jpeg","jpg"];
    $(".attachment_errors").html("");
    if(allowed.indexOf(file[0].name.substring(file[0].name.lastIndexOf(".")+1).toLowerCase()) < 0){
        $(".attachment_errors").html('<span class="invalid-feedback">File must be "pdf","xls","xlsx","csv","png","jpeg","jpg"</span>');
        $(".invalid-feedback").show();
        return;
    }
    if(file[0].size < 2097152) {
        formData.append('attachments[]', file[0]);
        $("#attachments").append("<div style='min-width:100px;'>"+file[0].name+"<i attachment='"+file[0].name+"' class='remove_attachment fas fa-window-close'></i></div>");
        const index = ignoreFiles.indexOf(file[0].name);
        if (index > -1) { // only splice array when item is found
            ignoreFiles.splice(index, 1); // 2nd parameter means remove one item only
        }
    }
    else{
        $(".attachment_errors").html("<span class='invalid-feedback'>Please upload a smaller file < 2MB</span>");
        $(".invalid-feedback").show();
    }
});
*/
// $("#attachment_input_1").change(function(e){
// e.stopPropagation();
// e.preventDefault();
// $("#upload-area-text").html("<u>Browse</u> Files");

// var allowed = ["pdf","xls","xlsx","csv","png","jpeg","jpg"];
// var file = e.target.files;
//     $(".attachment_errors").html("");
//     if(allowed.indexOf(file[0].name.substring(file[0].name.lastIndexOf(".")+1).toLowerCase()) < 0){
//         $(".attachment_errors").html('<span class="invalid-feedback">File must be "pdf","xls","xlsx","csv","png","jpeg","jpg"</span>');
//         $(".invalid-feedback").show();
//         return;
//     }
// if(file[0].size < 2097152)
// {
//     formData.append('attachments[]', file[0]);
//     $("#attachments").append("<div style='min-width:100px;'>"+file[0].name+"<i attachment='"+file[0].name+"' class='remove_attachment fas fa-window-close'></i></div>");
//     $(".invalid-feedback").show();
//     const index = ignoreFiles.indexOf(file[0].name);
//     if (index > -1) { // only splice array when item is found
//         ignoreFiles.splice(index, 1); // 2nd parameter means remove one item only
//     }
// }
// else{
//     $(".attachment_errors").html("<span class='invalid-feedback'>Please upload a smaller file < 2MB</span>");
//     $(".invalid-feedback").show();
// }
// });

// var ignoreFiles = [];
// $("body").on("click",".remove_attachment",function(){
// $(this).parent().remove();
// ignoreFiles.push($(this).attr("attachment"));
// $("#attachment_input_1").val("");
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
    function formatState (state) {
        if (!state.id) {
            return state.text;
        }

        var $state = $(
            '<span><i class="'+state.element.text+'color nav-icon fa fa-circle "></i> ' + state.text + '</span>'
        );
        return $state;
    };
    $(".status").select2(
        {
            templateResult:formatState,
            templateSelection:formatState
        }
    );


    $("body").on("change","#bc_gov_id",function(){

        if ($(this).val() == '000000') {
            // no vaidation onb this special employee iD
        } else {
            $.ajax({
                url: "/bank_deposit_form/bc_gov_id?id="+$(this).val(),
                type: "GET",
                headers: {'X-CSRF-TOKEN': $("input[name='_token']").val()},
                processData: false,
                cache: false,
                contentType: false,
                dataType: 'json',
                success:function(response){
                    $("#employment_city").parents(".form-body").fadeTo("fast",0.25);
                    $("#employment_city").val(response.office_city).select2();
                    $("#region").val($("#region option[code='"+response.tgb_reg_district+"']").val()).select2();
                    $("#business_unit").val(response.business_unit_id).select2();
                    $("#employee_name").val(response.last_name+","+response.first_name);
                    setTimeout(function(){
                        $("#employment_city").parents(".form-body").fadeTo("slow",1);
                    },500);
                },
                error: function(response) {
                    Swal.fire({
                        title:'Employee Id '+ $("#bc_gov_id").val() +' not Found!' ,
                        icon: 'error',
                        html:
                            '<strong>'+ $("#bc_gov_id").val() +' Not Found!</strong>',
                        showCloseButton: true,
                        showCancelButton: true,
                        focusConfirm: false,
                    });
                },
            });
        }
    });

    $("body").on("change","#organization_code",function(){

        // re-populate business unit options based on organization selection 
        selected_org_code = $("#organization_code").val();

        $('#business_unit option').slice(1).remove();
        business_unit_options.forEach(function (item, index) {
            console.log(item, index);

            new_option = $('<option>', { 
                        value: item.id,
                        text : item.name,
                        'data-org' : item.org_code,
            });

            if (selected_org_code == "GOV") {
                if (!(item.org_code)) {
                    $('#business_unit').append(new_option);
                }
            } else {
                if (selected_org_code == item.org_code) {
                    $('#business_unit').append(new_option);
                }
            }

        });

        $("#business_unit option[data-org='" + selected_org_code + "']").attr('selected','selected').change();

        // if($(this).val() != "GOV" && $(this).val() != "false"){
        //     $.ajax({
        //         url: "/bank_deposit_form/business_unit?id="+$(this).val(),
        //         type: "GET",
        //         headers: {'X-CSRF-TOKEN': $("input[name='_token']").val()},
        //         processData: false,
        //         cache: false,
        //         contentType: false,
        //         dataType: 'json',
        //         success:function(response){
        //             $("#employment_city").parents(".form-body").fadeTo("fast",0.25);
        //             // $("#business_unit").val(response.business_unit_id).select2();
        //             setTimeout(function(){
        //                 $("#employment_city").parents(".form-body").fadeTo("slow",1);
        //             },500);
        //         },
        //         error: function(response) {
        //             Swal.fire({
        //                 title: '<strong>Not Found!</strong>',
        //                 icon: 'error',
        //                 html:
        //                     'Business Unit not found!',
        //                 showCloseButton: true,
        //                 showCancelButton: true,
        //                 focusConfirm: false,
        //             });
        //         },
        //     });
        // }
        // else if($(this).val() == "GOV"){
        //     // $("#business_unit").val("").select2();
        // }
    });
    $("#keyword").keypress(function(e){
        if(e.which == 13) {
           e.preventDefault();
        }
    });

    $("#city").change(function(){
        if($("#city option[value='"+$(this).val()+"']").attr("province") == "BC")
        {
            $("#province").val("British Columbia").select2();
        }
        else{
            $("#province").val("Ontario").select2();
        }
    });

});    
</script>
