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
            $.ajax({
                url:"/bank_deposit_form/organizations?page="+page+"&category="+$("#category").val()+"&province="+$("#charity_province").val()+"&keyword="+$("#keyword").val(),
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
            text = $("#organization-tmpl").html();
            text = text.replace(/XXX/g, row_number + 1);
            $('#organizations').append( text );
            $("#organizations").css("display","block");
            row_number++;
            $('.organization').last().find(".organization_name").val($(this).attr("name"));
            $('.organization').last().append("<input type='hidden' name='id[]' value='"+$(this).attr('org_id')+"'/>");
            $('.organization').last().append("<input type='hidden' name='vendor_id[]' value='"+$(this).attr('org_id')+"'/>");

        });
        $("body").on("click",".remove",function(e){
            e.preventDefault();

            if($(this).parents("tr").siblings().length < 1)
            {
                $("#noselectedresults").html("You have not chosen any charities");
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


</script>
