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
                    $("#charities").fadeTo("slow",1);
                    $(".charity-container").html(data);
                }
            });
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

        function resetCharitySelection () {
            $('#charities .select-btn').each(function() {
                $(this).html('Select');
                $(this).removeClass('active');
            });
        }

        $("#charity-selection-section").on("click",".select-btn",function(e){
            e.preventDefault();

            // check if the selection is not previous one
            if ( $(this).attr("org_id") == $("input[name='charity_id']").val()) {

                $('#selected_charity0').remove();
                $("#noselectedresults").html("You have not chosen any charities");
                Toast('Message', 'Charity "' + $(this).attr("name") +  '" was removed.', 'bg-danger' );
                resetCharitySelection();
                return;
            }

            $("#noselectedresults").html('');
            text = $("#selected-charity-tmpl").html();
            text = text.replace(/XXX/g, row_number);

            $('#selected_charity0').remove();           // remove previous selection
            $('#selected_charity').append( text );
            $("#selected_charity").css("display","");

            // row_number++;
            // $('.selected_charity').last().find(".charity_name").val($(this).attr("name"));
            $('#selected_charity_name').html($(this).attr("name"));

            $('.selected_charity').last().find("input[name='charity_id']").val($(this).attr("org_id"));

            // $('.selected_charity').last().append("<input type='hidden' name='id[]' value='"+$(this).attr('org_id')+"'/>");
            // $('.selected_charity').last().append("<input type='hidden' name='vendor_id[]' value='"+$(this).attr('org_id')+"'/>");

            Toast('Message', 'Charity "' + $(this).attr("name") +  '" was selected.', 'bg-success' );

            $('html, body').animate({
                scrollTop: $("#selected_charity").offset().top
            }, 500);

            // Hide and clear the error message
            $('#error-message').html('');
            $('#error-message').hide();

            resetCharitySelection();
            $(this).html('Selected');
            $(this).addClass('active');

        });

        $("#charity-selection-section").on("click",".remove",function(e){
            e.preventDefault();
            name = $('#selected_charity_name').html();
            if (!confirm('Are you sure you want to remove this charity "' + name + '" ?')) {
                    return;
            }
            $("button[org_id='"+$(this).parents(".selected_charity").find("input[name='charity_id']").val() +"'").html("Select").removeClass("active").addClass("select");

            $(this).parents("tr").remove();
            $("#noselectedresults").html("You have not chosen any charities");
            Toast('Message', 'Charity "' + name +  '" was removed.', 'bg-danger' );
                $(".next_button").attr("disabled",false);
                $(".charity-error-hook").css("border","none")
                $(".max-charities-error").hide();
            if($(this).parents("tr").siblings().length < 1)
            {
                $("#noselectedresults").html("You have not chosen any charities");
                $(".next_button").attr("disabled",true);
            }
            $(this).parents("tr").remove();
            resetCharitySelection();
        });

        $("#charity-selection-section").on("click",".view_details",function(e){
            e.preventDefault();

            $("#modal-registration_number").html($(this).attr("registration_number"));
            $("#modal-charity_status").html($(this).attr("charity_status"));
            $("#modal-effective_date_of_status").html($(this).attr("effective_date_of_status"));
            $("#modal-sanction").html($(this).attr("sanction"));
            $("#modal-designation").html($(this).attr("designation"));
            $("#modal-category").html($(this).attr("category"));
            $("#modal-address").html($(this).attr("address"));
            $("#modal-city").html($(this).attr("city"));
            $("#modal-province").html($(this).attr("province"));
            $("#modal-country").html($(this).attr("country"));
            $("#modal-postal_code").html($(this).attr("postal_code"));
            $("#modal-uri").html($(this).attr("website"));

            $("#charityDetails").modal("show");
        });

        $("body").on("click",".select",function(e){
            e.preventDefault();
            $("#noselectedresults").html("");
            if($(".selected_charity").length < 1){
                text = $("#selected-charity-tmpl").html();
                text = text.replace(/XXX/g, row_number + 1);
                $('#selected_charity').append( text );
                $('#selected_charity').css("display","block");
                row_number++;
                $('#selected_charity_name').html($(this).attr("name"));
                $('#selected_charity').find('[name="charity_id"]').val($(this).attr('org_id'));
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
                $("button[org_id='"+$(this).parents(".organization").find("input[name='vendor_id[]']").val() +"'").html("Select").removeClass("active").addClass("select");
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
                $("[org_id='"+$(this).parents(".selected_charity").find("input[name='charity_id']").val() +"'").html("Select").removeClass("active").addClass("select");
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
            $("#modalcategory").html($(this).attr("category"));
            $("#address").html($(this).attr("address"));
            $("#city").html($(this).attr("city"));
            $("#province").html($(this).attr("province"));
            $("#country").html($(this).attr("country"));
            $("#postal_code").html($(this).attr("postal_code"));
            $("#uri").html($(this).attr("website"));
            $("#charityDetails").modal("show");
        });

        // $("#charity_province").select2();
        // $("#category").select2();

    });

</script>

<script type="x-tmpl" id="selected-charity-tmpl">
<tr class="selected_charity" id="selected_charity0">
    <td>
        <div class="container">
            <div class="form-row">
                <div class="col-12">
                    <div>
                        <input type="hidden" name="charity_id" value="">
                        <h6 class="font-weight-bold" id="selected_charity_name"></h6>
                    </div>
                    <span class="selected_charity_name_errors errors"></span>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-11">
                    <input class="form-control" type="text" id="special_program" name="special_program"
                        value=""
                        placeholder="Optional: If you have a specific community or initiative in mind, eneter it here.">
                    <span class="specific_community_or_initiative_errors  errors"></span>
                </div>
                <div class="form-group col-1">
                    <div>
                        <button class="btn btn-danger remove"><i class="fas fa-trash-alt"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
</script>
