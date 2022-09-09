<script>
    $(document).ready(function(){
        var keywordTypingTimer;

        var row_number = 0;
        $(document).on('click', '.pagination a', function(event){
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            fetch_data(page);
        });

        $("#keyword").keyup(function(){
            clearTimeout(keywordTypingTimer);
            keywordTypingTimer = setTimeout(fetch_data,800)
        });

        $("#category").change(function(){
            fetch_data(1);
        });

        $("#charity_province").change(function(){
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
                url:"/bank_deposit_form/organizations?page="+page+"&category="+$("#category").val()+"&province="+$("#charity_province").val()+"&keyword="+$("#keyword").val()+"&selected_vendors="+selected_vendors,
                success:function(data)
                {
                    $("#charities").fadeTo("slow",1);
                    $('#charities').html(data);

                }
            });
        }
        $("body").on("click",".select",function(e){
            e.preventDefault();
            $("#noselectedresults").html("");
            if($(".organization").length < 10){
                text = $("#organization-tmpl").html();
                text = text.replace(/XXX/g, row_number + 1);
                $('#organizations').append( text );
                $("#organizations").css("display","block");
                row_number++;
                $('.organization').last().find(".organization_name").val($(this).attr("name"));
                $('.organization').last().find("[name='id[]']").val($(this).attr('org_id'));
                $('.organization').last().find("[name='vendor_id[]']").val($(this).attr('org_id'));
                $(this).addClass("active");
                $(this).html("Selected");
                $(this).removeClass("select")
                $(this).addClass("selected")
                $(".next_button").attr("disabled",false);

            }
            else{
                $(".max-charities-error").show();
                $(".charity-error-hook").css("border","red 2px solid")
            }
        });
        $("body").on("click",".selected",function(e) {
            e.preventDefault();
            $(":input[value='"+$(this).attr("org_id")+"']").parents("tr").remove();
            $(this).html("Select").removeClass("active").addClass("select").removeClass("selected");
            if($(".organization").length < 11){
                $(".next_button").attr("disabled",false);

                $(".charity-error-hook").css("border","none")
                $(".max-charities-error").hide();
                $("div[org_id='"+$(this).parents(".organization").find("input[name='vendor_id[]']").val() +"'").html("Select").removeClass("active").addClass("select");
            }

            if($(".organization").length < 1)
            {
                $("#noselectedresults").html("You have not chosen any charities");
                $(".next_button").attr("disabled",true);

            }


        });

            $("body").on("click",".remove",function(e){
                if($(".organization").length < 11){
                    $(".next_button").attr("disabled",false);

                    $(".charity-error-hook").css("border","none")
                    $(".max-charities-error").hide();
                    $("[org_id='"+$(this).parents(".organization").find("input[name='vendor_id[]']").val() +"'").html("Select").removeClass("active").addClass("select");
                }

            if($(this).parents("tr").siblings().length < 1)
            {
                $("#noselectedresults").html("You have not chosen any charities");
                $(".next_button").attr("disabled",true);

            }
            $(this).parents("tr").remove();

        });

        $("body").on("click",".view_details",function(){
            $("#registration_number").html($(this).attr("registration_number"));
            $("#charity_status").html($(this).attr("charity_status"));
            $("#effective_date_of_status").html($(this).attr("effective_date_of_status"));
            $("#sanction").html($(this).attr("sanction"));
            $("#designation").html($(this).attr("designation"));
            $("#category").html($(this).attr("category"));
            $("#address").html($(this).attr("address"));
            $("#city").html($(this).attr("city"));
            $("#province").html($(this).attr("province"));
            $("#country").html($(this).attr("country"));
            $("#postal_code").html($(this).attr("postal_code"));
            $("#uri").html($(this).attr("website"));
            $("#charityDetails").modal("show");
        });

    });
    $("#charity_province").select2();
    $("#category").select2();
</script>
