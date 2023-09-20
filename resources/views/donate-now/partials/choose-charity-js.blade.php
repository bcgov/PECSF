<script>
    $(document).ready(function(){
        var keywordTypingTimer;

        var row_number = 0;
        $(document).on('click', '.pagination a', function(event){
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            fetch_data(page);
        });

        $("#charity_keyword").keyup(function(){
            clearTimeout(keywordTypingTimer);
            keywordTypingTimer = setTimeout(fetch_data,800)
        });

        $(document).on("change","#charity_keyword",function() {
            fetch_data(1);
        });

        $(document).on("change","#charity_category",function() {
            fetch_data(1);
        });

        $("#charity_province").change(function(){
            fetch_data(1);
        });

        $("#pool_filter").change(function(){
            fetch_data(1);
        });

        function fetch_data(page=1)
        {
            $("#charities").fadeTo("slow",0.2);
            $(".noresults").html("");

            var selected_vendors = "";
            $("input[name='vendor_id[]']").each(function(){
                selected_vendors += $(this).val() != "undefined" ? $(this).val()+",":"";
            });

            $.ajax({
                url:"/bank_deposit_form/organizations?page="+page+"&category="+$("#charity_category").val()
                    +"&province="+$("#charity_province").val()+"&keyword="+$("#charity_keyword").val()
                    +"&pool_filter="+$("#pool_filter").val()
                    +"&selected_vendors="+selected_vendors,
                success:function(data)
                {
                    // $("#charities").fadeTo("slow",1);
                    // $(".charity-container").html(data);
                    $("#charities_select_area").fadeTo("slow",1);
                    $('#charities_select_area').html(data);
                }
            });
        }

        //

        function toggleSelectedCountArea() {
            if($(".organization").length < 1) {
                $("#noselectedresults").show();
                $('#selectedcountresults').hide();
            } else {
                $("#noselectedresults").hide();
                $('#selectedcountresults').show();
            }
        }

        function Toast( toast_title, toast_body, toast_class) {
            $(document).Toasts('create', {
                class: toast_class,
                title: toast_title,
                autohide: true,
                delay: 3000,
                body: toast_body
            });
        }

        $("body").on("click",".select",function(e){
            e.preventDefault();
            // $("#noselectedresults").html("");

            // reset -- charities error message
            $('.min-charities-error').html('');
            $('.min-charities-error').removeClass('error');
            $(".charity-error-hook").css("border","none");

            // if($(".organization").length < 11){
                text = $("#organization-tmpl").html();
                text = text.replace(/XXX/g, row_number + 1);
                $('#organizations').append( text );
                // $("#organizations").css("display","block");
                row_number++;
                $('.organization').last().find(".organization_name").val($(this).attr("name"));
                // $('.organization').last().find("[name='id[]']").val($(this).attr('org_id'));
                $('.organization').last().find("[name='vendor_id[]']").val($(this).attr('org_id'));
                $('.organization').last().find("[name='charities[]']").val($(this).attr('org_id'));
                $('.organization').last().find(".specific_community_or_initiative").val($(this).attr("program_name"));
                $('.organization').last().find(".specific_community_or_initiative").prop("readonly", true);
                $(this).addClass("active");
                $(this).html("Selected");
                $(this).removeClass("select")
                $(this).addClass("selected")
                $(".next_button").attr("disabled",false);

            // }
            // else{
            //     $(".max-charities-error").show();
            //     $(".charity-error-hook").css("border","red 2px solid")
            // }

            toggleSelectedCountArea();
            // if ($("input[name='charities[]']").length < 1) {
            //     $('#selectedcountresults').hide();
            // } else {
            //     $('#selectedcountresults').show();
            // }
            $('#selectedcountresults').html(  $("input[name='charities[]']").length + ' item(s) selected');

            if ($(".organization").length > 2) {
                    Toast('More than one charity were specified','Please be aware, only one charity is required for Donate Now pledge.',"bg-danger");
                    return false;
            }

        });

        $("body").on("click",".selected",function(e) {
            e.preventDefault();
            $(":input[value='"+$(this).attr("org_id")+"']").parents("tr").remove();
            $(this).html("Select").removeClass("active").addClass("select").removeClass("selected");
            if($(".organization").length < 11){
                $(".next_button").attr("disabled",false);

                $(".charity-error-hook").css("border","none")
                // $(".max-charities-error").hide();
                $("div[org_id='"+$(this).parents(".organization").find("input[name='vendor_id[]']").val() +"'").html("Select").removeClass("active").addClass("select");
            }

            toggleSelectedCountArea();
            // if($(".organization").length < 1)
            // {
            //     $("#noselectedresults").html("You have not chosen any charities");
            //     $(".next_button").attr("disabled",true);

            //     $('#selectedcountresults').hide();
            // } else {
            //     $('#selectedcountresults').show();
            // }

            $('#selectedcountresults').html(  $("input[name='charities[]']").length + ' item(s) selected');

        });

        $("body").on("click",".remove",function(e){
            // if($(".organization").length < 11){
                $(".next_button").attr("disabled",false);

                $('.min-charities-error').html('');
                $('.min-charities-error').removeClass('error');
                $(".charity-error-hook").css("border","none")
                // $(".max-charities-error").hide();
                $("[org_id='"+$(this).parents(".organization").find("input[name='vendor_id[]']").val() +"'").html("Select").removeClass("active selected").addClass("select");
            // }

            // if($(this).parents("tr").siblings().length < 1)
            // {
            //     $("#noselectedresults").html("You have not chosen any charities");
            //     $("#noselectedresults").show();

            //     // $(".next_button").attr("disabled",true);

            // }
            $(this).parents("tr").remove();

            // if ($("input[name='charities[]']").length < 1) {
            //     $('#selectedcountresults').hide();
            // } else {
            //     $('#selectedcountresults').show();
            // }

            toggleSelectedCountArea();

            $('#selectedcountresults').html(  $("input[name='charities[]']").length + ' item(s) selected');

        });

        $("body").on("click",".view_details",function(e){
            if($(this).attr("pool_image").length > 21){
                $("table.charity").hide();
                $("table.fsp").show();
                // $(".modal-header").hide();
                // $(".modal-footer").hide();
                $("#pool_registration_number").html($(this).attr("registration_number"));
                $("#pool_name").html($(this).attr("charity_name"));
                $("#pool_image").attr("src",$(this).attr("pool_image"));
                $("#pool_description").html($(this).attr("pool_description"));
                $("#program_name").html("<b>Program:</b> " + $(this).attr("charitable_programs"));
                $("#charityDetails").modal("show");
            }
            else{
                e.preventDefault();
                $("table.charity").show();
                $("table.fsp").hide();
                $(".modal-header").show();
                $(".modal-footer").show();
                $("#registration_number").html($(this).attr("registration_number"));
                $("#charity_status").html($(this).attr("charity_status"));
                $("#effective_date_of_status").html($(this).attr("effective_date_of_status"));
                $("#sanction").html($(this).attr("sanction"));
                $("#designation").html($(this).attr("designation"));
                $("#address").html($(this).attr("address"));
                $("#city").html($(this).attr("city"));
                $("#province").html($(this).attr("province"));
                $("#country").html($(this).attr("country"));
                $("#postal_code").html($(this).attr("postal_code"));
                $("#uri").html($(this).attr("website"));
                $("#charity_type").html($(this).attr("charity_type"));
                $("#charity_name").html($(this).attr("charity_name"));
                $("#charityDetails").modal("show");
            }
        });
//
        // $("body").on("click",".select",function(e){
        //     e.preventDefault();
        //     $("#noselectedresults").html("");
        //     if($(".selected_charity").length < 1){
        //         text = $("#selected-charity-tmpl").html();
        //         text = text.replace(/XXX/g, row_number + 1);
        //         $('#selected_charity').append( text );
        //         $('#selected_charity').css("display","block");
        //         row_number++;
        //         $('#selected_charity_name').html($(this).attr("name"));
        //         $('#selected_charity').find('[name="charity_id"]').val($(this).attr('org_id'));
        //         $(this).addClass("active");
        //         $(this).html("Selected");
        //         $(this).removeClass("select")
        //         $(this).addClass("selected")
        //         $(".next_button").attr("disabled",false);
        //     }
        //     else{
        //         $(".max-charities-error").show();
        //         $(".charity-error-hook").css("border","red 2px solid")
        //     }
        // });
        // $("body").on("click",".selected",function(e) {
        //     e.preventDefault();
        //     $(":input[value='"+$(this).attr("org_id")+"']").parents("tr").remove();
        //     $(this).html("Select").removeClass("active").addClass("select").removeClass("selected");
        //     if($(".organization").length < 11){
        //         $(".next_button").attr("disabled",false);

        //         $(".charity-error-hook").css("border","none")
        //         $(".max-charities-error").hide();
        //         $("button[org_id='"+$(this).parents(".organization").find("input[name='vendor_id[]']").val() +"'").html("Select").removeClass("active").addClass("select");
        //     }

        //     if($(".organization").length < 1)
        //     {
        //         $("#noselectedresults").html("You have not chosen any charities");
        //         $(".next_button").attr("disabled",true);

        //     }


        // });

        // $("body").on("click",".remove",function(e){
        //     if($(".organization").length < 11){
        //         $(".next_button").attr("disabled",false);

        //         $(".charity-error-hook").css("border","none")
        //         $(".max-charities-error").hide();
        //         $("[org_id='"+$(this).parents(".selected_charity").find("input[name='charity_id']").val() +"'").html("Select").removeClass("active").addClass("select");
        //     }

        //     if($(this).parents("tr").siblings().length < 1)
        //     {
        //         $("#noselectedresults").html("You have not chosen any charities");
        //         $(".next_button").attr("disabled",true);

        //     }
        //     $(this).parents("tr").remove();

        // });

        // $("body").on("click",".view_details",function(){
        //     $("#registration_number").html($(this).attr("registration_number"));
        //     $("#charity_status").html($(this).attr("charity_status"));
        //     $("#effective_date_of_status").html($(this).attr("effective_date_of_status"));
        //     $("#sanction").html($(this).attr("sanction"));
        //     $("#designation").html($(this).attr("designation"));
        //     $("#modalcategory").html($(this).attr("category"));
        //     $("#address").html($(this).attr("address"));
        //     $("#city").html($(this).attr("city"));
        //     $("#province").html($(this).attr("province"));
        //     $("#country").html($(this).attr("country"));
        //     $("#postal_code").html($(this).attr("postal_code"));
        //     $("#uri").html($(this).attr("website"));
        //     $("#charityDetails").modal("show");
        // });

        $("#charity_category").select2();
        $("#charity_province").select2();
        $("#pool_filter").select2();
    });


</script>

