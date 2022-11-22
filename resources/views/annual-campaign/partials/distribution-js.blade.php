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
        } else {
            $("#oneTimeSection").find(".by-percent").removeClass("d-none");
            $("#oneTimeSection").find(".by-amount").addClass("d-none");
            $("#oneTimeSection").find(".percent-amount-text").html("Distribute by Dollar Amount");
        }
        $("#oneTimeSection .percent-input").change();
        $("#oneTimeSection .amount-input").change();
    });
    $(document).on('click', '#distributeByPercentageBiWeekly, #distributeByDollarBiWeekly', function (e) {
        const frequency = '#oneTimeSection';

        if ($(this).attr('id') == "distributeByDollarBiWeekly") {
            $("#biWeeklySection").find(".by-amount").removeClass("d-none");
            $("#biWeeklySection").find(".by-percent").addClass("d-none");
            $("#biWeeklySection").find(".percent-amount-text").html("Distribute by Percentage");
        } else {
            $("#biWeeklySection").find(".by-percent").removeClass("d-none");
            $("#biWeeklySection").find(".by-amount").addClass("d-none");
            $("#biWeeklySection").find(".percent-amount-text").html("Distribute by Dollar Amount");
        }
        $("#biWeeklySection .percent-input").change();
        $("#biWeeklySection .amount-input").change();
    });

    function redistribute(type, section)
    {
        let sum = 0.00;
        expectedTotal = section.find(".total-amount").data('expected-total');
        // const section = $(this).parents('.amountDistributionSection');
        if (type == 'amount') {
            rows = section.find(".percent-input");
            target_rows= section.find(".amount-input");
        } else {
            rows = section.find(".amount-input");
            target_rows= section.find(".percent-input");
        }

        $.each(rows, function(i) {
            if (i == (rows.length -1 ) ) {
                newValue = 0;
                if (type == 'amount')
                    newValue = expectedTotal - sum;
                    else
                    newValue = 100 - sum;
                $(target_rows[i]).val( newValue.toFixed(2) );
            } else {
                current = $(this).val();
                newValue = 0;
                if (type == 'amount')
                    newValue = Math.round(( (current / 100 ) * expectedTotal) * 100) / 100;
                else
                    newValue = Math.round(( current / expectedTotal * 100) * 100) / 100;
                $(target_rows[i]).val( newValue.toFixed(2) );
                sum += newValue
            }
        });
    }

    $(document).on('change', '#step-distribution-page .percent-input', function (e) {
        let total = 0;
        const section = $(this).parents('.amountDistributionSection');
        section.find(".percent-input").each( function () {
            total += Number($(this).val());
        });
        if (total !== 100) {
            const lastValue = Number(section.find(".percent-input").last().val());
            const difference = 100 - total;
            section.find(".percent-input").last().val( (lastValue + difference).toFixed(2)  );
            total = 100;
        }
        section.find(".total-percent").val(total.toFixed(2));

        $(this).val(  Number($(this).val()).toFixed(2) );
        // percentage changed, re-calculate the amount distribution
        redistribute('amount', section);
    });


    $(document).on('change', '#step-distribution-page .amount-input', function (e) {
        let total = 0;
        const section = $(this).parents('.amountDistributionSection');

        const expectedTotal = section.find(".total-amount").data('expected-total');
        section.find(".amount-input").each( function () {
            total += Number($(this).val());
        });
        if (total !== expectedTotal) {
            const lastValue = Number(section.find(".amount-input").last().val());
            const difference = expectedTotal - total;
            section.find(".amount-input").last().val( (lastValue + difference).toFixed(2) );
            total = expectedTotal;
        }
        section.find(".total-amount").val(total.toFixed(2));

        $(this).val(  Number($(this).val()).toFixed(2) );
        // amount changed, re-calculate the percentage distribution
        redistribute('percent', section);
        
    });

    $("#step-distribution-page .percent-input").change();
    $("#step-distribution-page .amount-input").change();


    $(document).on('click', '#step-distribution-page .distribute-evenly', function () {

        // calucated and distributed
        function distribute_evenly(expectedTotal, rows)
        {
            sum = 0;
            $.each(rows, function(i) {
                if (i == (rows.length -1 ) ) {
                    newValue = expectedTotal - sum;
                    $(this).val( newValue );
                } else {
                    newValue = Math.round(( expectedTotal / rows.length) * 100) / 100;
                    $( this).val( newValue );
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
        // } else {
            // const section = $(this).parents('.amountDistributionSection');
            expectedTotal = section.find(".total-amount").data('expected-total');
            rows  = section.find(".amount-input");
            distribute_evenly(expectedTotal, rows);
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
