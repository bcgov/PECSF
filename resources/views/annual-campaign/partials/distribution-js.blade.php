<script>
    
$(function () {

    $("#step-distribution-page .frequencybiWeekly").show();
    // if($("#step-distribution-page .frequencyoneTime").length > 0){
    //     $("#step-distribution-page .frequencybiWeekly").hide();
    // }
    $(document).on('click', '#distributeByPercentageOneTime, #distributeByDollarOneTime', function (e) {
        const frequency = '#oneTimeSection';

        if ($(this).attr('id') == "distributeByDollarOneTime") {
            $("#oneTimeSection").find(".by-amount").removeClass("d-none");
            $("#oneTimeSection").find(".by-percent").addClass("d-none");
            $("#oneTimeSection").find(".percent-amount-text").html("Distribute by Percentage");
            // redistribute('amount', $("#oneTimeSection"));
        } else {
            $("#oneTimeSection").find(".by-percent").removeClass("d-none");
            $("#oneTimeSection").find(".by-amount").addClass("d-none");
            $("#oneTimeSection").find(".percent-amount-text").html("Distribute by Dollar Amount");
            // redistribute('percent', $("#oneTimeSection"));
        }
        // $("#oneTimeSection .percent-input").change();
        // $("#oneTimeSection .amount-input").change();
    });
    $(document).on('click', '#distributeByPercentageBiWeekly, #distributeByDollarBiWeekly', function (e) {
        const frequency = '#oneTimeSection';

        if ($(this).attr('id') == "distributeByDollarBiWeekly") {
            $("#biWeeklySection").find(".by-amount").removeClass("d-none");
            $("#biWeeklySection").find(".by-percent").addClass("d-none");
            $("#biWeeklySection").find(".percent-amount-text").html("Distribute by Percentage");
            // redistribute('amount', $("#biWeeklySection"));
        } else {
            $("#biWeeklySection").find(".by-percent").removeClass("d-none");
            $("#biWeeklySection").find(".by-amount").addClass("d-none");
            $("#biWeeklySection").find(".percent-amount-text").html("Distribute by Dollar Amount");
            // redistribute('percent', $("#biWeeklySection"));
        }
        // $("#biWeeklySection .percent-input").change();
        // $("#biWeeklySection .amount-input").change();
    });

    function redistribute(type, section)
    {
        let sum = 0.00;
        total_amount = section.find(".total-amount").val();
        total_percent = section.find(".total-percent").val();
        expectedTotal = section.find(".total-amount").data('expected-total');
        expectedTotalPercent = Math.round(( total_amount / expectedTotal * 100) * 100) / 100;
        // const section = $(this).parents('.amountDistributionSection');
        if (type == 'amount') {
            rows = section.find(".percent-input");
            target_rows= section.find(".amount-input");
            // expectedTotal = (expectedTotal * total_percent) / 100;

        } else {
            rows = section.find(".amount-input");
            target_rows= section.find(".percent-input");
            // expectedTotal =  Math.round(( total_amount / expectedTotal * 100) * 100) / 100;
        }

           
        $.each(rows, function(i) {
            if (i == (rows.length -1 ) ) {
                current = $(this).val();
                newValue = 0;
                if (type == 'amount') {
                    // newValue = expectedTotal - sum;
                    newValue = Math.round(( (current / 100 ) * expectedTotal) * 100) / 100;
                } else {
                    // newValue = 100 - sum;
                    newValue = expectedTotalPercent - sum;
                }
                $(target_rows[i]).val( newValue.toFixed(2) );
                // update total 
            } else {
                current = $(this).val();
                newValue = 0;
                if (type == 'amount') {
                    newValue = Math.round(( (current / 100 ) * expectedTotal) * 100) / 100;
                } else {
                    newValue = Math.round(( current / expectedTotal * 100) * 100) / 100;
                }
                $(target_rows[i]).val( newValue.toFixed(2) );
                sum += newValue
            }
        });

        // // update the total percent to 100%
        // if (type == 'amount') {
        //     section.find(".total-amount").val(expectedTotal.toFixed(2));
        // } else {
        //     // section.find(".total-percent").val( (100).toFixed(2) );
        //     section.find(".total-percent").val(expectedTotalPercent.toFixed(2));
        // }
    }


    function update_percentage_section(row) {

        let total = 0;
        const section = $(row).parents('.amountDistributionSection');
        section.find(".percent-input").each( function () {
            total += Number($(this).val());
        });

        section.find(".total-percent").val(total.toFixed(2));
        expectedTotal = section.find(".total-amount").data('expected-total');
        section.find(".total-amount").val( (expectedTotal * total / 100).toFixed(2));

        $(row).val(  Number($(row).val()).toFixed(2) );
        // percentage changed, re-calculate the amount distribution
        redistribute('amount', section);

    }


    $(document).on('keydown', '#step-distribution-page .percent-input', function (e) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13' || keycode == '9'){
            // alert('You pressed a "enter" key in somewhere');  
        } else {
            return true;
        }
        update_percentage_section( this );
    });

    $(document).on('change', '#step-distribution-page .percent-input', function (event) {

        if (event.originalEvent) {
            update_percentage_section( this );
        }
    });


    function update_amount_section(row) {

        let total = 0;
        const section = $(row).parents('.amountDistributionSection');

        const expectedTotal = section.find(".total-amount").data('expected-total');
        section.find(".amount-input").each( function () {
            total += Number($(this).val());
        });
        // if (total !== expectedTotal) {
        //     const lastValue = Number(section.find(".amount-input").last().val());
        //     const difference = expectedTotal - total;
        //     section.find(".amount-input").last().val( (lastValue + difference).toFixed(2) );
        //     total = expectedTotal;
        // }
        section.find(".total-amount").val(total.toFixed(2));

        expectedTotalPercent = Math.round(( total / expectedTotal * 100) * 100) / 100;
        section.find(".total-percent").val(expectedTotalPercent.toFixed(2));

        $(row).val(  Number($(row).val()).toFixed(2) );
        // amount changed, re-calculate the percentage distribution
        redistribute('percent', section);

    }

    $(document).on('keydown', '#step-distribution-page .amount-input', function (e) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){
            // alert('You pressed a "enter" key in somewhere');  
        } else {
            return true;
        }
        update_amount_section( this );
    });


    $(document).on('change', '#step-distribution-page .amount-input', function (event) {

        if (event.originalEvent) {
            update_amount_section( this );
        }
        
    });

    // $("#step-distribution-page .percent-input").change();
    // $("#step-distribution-page .amount-input").change();


    $(document).on('click', '#step-distribution-page .distribute-evenly', function () {

        // calucated and distributed
        function distribute_evenly(expectedTotal, rows)
        {
            sum = 0;
            $.each(rows, function(i) {
                if (i == (rows.length -1 ) ) {
                    newValue = Math.round(( expectedTotal - sum) * 100) / 100;
                    $(this).val( newValue.toFixed(2) );
                } else {
                    newValue = Math.round(( expectedTotal / rows.length) * 100) / 100;
                    $( this).val( newValue.toFixed(2) );
                    sum += newValue
                }
            });
        }

        section = $(this).parents('.amountDistributionSection');
        distributionBy = section.find("input[name*='distributionByPercent'] ");
        var expectedTotal,  rows;

        // if ($(distributionBy).prop('checked')) {
            expectedTotal = 100;
            rows  = section.find(".percent-input");
            distribute_evenly(expectedTotal, rows);
            section.find(".total-percent").val( expectedTotal.toFixed(2) );
        // } else {
            // const section = $(this).parents('.amountDistributionSection');
            expectedTotal = section.find(".total-amount").data('expected-total');
            rows  = section.find(".amount-input");
            distribute_evenly(expectedTotal, rows);
            section.find(".total-amount").val(expectedTotal.toFixed(2));
        // }

        // calucated and distributed
        // sum = 0;
        // $.each(rows, function(i) {
        //     if (i == (rows.length -1 ) ) {
        //         newValue = expectedTotal - sum;
        //         $(this).val( newValue );
        //         console.log( 'LAST ' + i + ' - ' +  $( this).val() );
        //     } else {
        //         newValue = Math.round(( expectedTotal / rows.length) * 100) / 100;
        //         $( this).val( newValue );
        //         sum += newValue
        //         console.log( i + ' - ' +  $( this).val() );
        //     }
        // });            
    });

});
</script>
