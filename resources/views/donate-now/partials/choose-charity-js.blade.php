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

        function fetch_data(page=1)
        {
            $("#charities").fadeTo("slow",0.2);
            $(".noresults").html("");
            $.ajax({
                url:"/donate-now/charities?page="+page+"&category="+$("#charity_category").val()+"&province="+$("#charity_province").val()+"&keyword="+$("#charity_keyword").val(),
                success:function(data)
                {
                    $("#charities").fadeTo("slow",1);
                    $('#charities').html(data);

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

        $("#charity-selection-section").on("click",".select",function(e){
            e.preventDefault();
            
            $("#noselectedresults").html('');
            text = $("#selected-charity-tmpl").html();
            text = text.replace(/XXX/g, row_number);

            $('#selected_charity0').remove();           // remove previous selection
            $('#selected_charity').append( text );
            $("#selected_charity").css("display","");

            // row_number++;
            $('.selected_charity').last().find(".charity_name").val($(this).attr("name"));
            $('.selected_charity').last().find("input[name='charity_id']").val($(this).attr("org_id"));

            // $('.selected_charity').last().append("<input type='hidden' name='id[]' value='"+$(this).attr('org_id')+"'/>");
            // $('.selected_charity').last().append("<input type='hidden' name='vendor_id[]' value='"+$(this).attr('org_id')+"'/>");

            Toast('Message', 'Charity "' + $(this).attr("name") +  '" was selected.', 'bg-success' );

        });

        $("body").on("click",".remove",function(e){
            e.preventDefault();

            name = $('#selected_charity0 input[name="charity_name"]').val(); 

            if (!confirm('Are you sure you want to remove this charity "' + name + '" ?')) {
                    return;
            }
            $(this).parents("tr").remove();
            $("#noselectedresults").html("You have not chosen any charities");

            Toast('Message', 'Charity "' + name +  '" was removed.', 'bg-danger' );

        });

        $("body").on("click",".view_details",function(){
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
            $("#modal-charityDetails").modal("show");
        });

        // $("#charity_province").select2();
        // $("#category").select2();


    });

</script>

<script type="x-tmpl" id="selected-charity-tmpl">
<tr class="selected_charity" id="selected_charity0">
    <td>
        <div class="container">        
            <div class="row">
                <div class="form-group col-10">
                    <label for="charity_name">Charity Name:</label>
                    <div>
                        <input type="hidden" name="charity_id" value="">
                        <input type="text" disabled="" class="form-control errors charity_name font-weight-bold" name="charity_name" placeholder="">
                    </div>
                    <span class="selected_charity_name_errors errors"></span>
                </div>
                <div class="form-group col-2">
                    <label for="charity_name">&nbsp;</label>
                    <div>
                        <button class="btn btn-danger remove">Remove</button>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="sub_type">Specific Community Or Initiative (Optional):</label>
                    <input class="form-control" type="text" id="special_program" name="special_program">
                    <span class="specific_community_or_initiative_errors  errors">
                        </span>
                </div>
            </div>
        </div>
    </td>
</tr>
</script>
