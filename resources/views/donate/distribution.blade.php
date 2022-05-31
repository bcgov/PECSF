@extends('donate.layout.main')

@section ("step-content")
<h2 class="mt-5">4. Decide on the distribution</h2>
<p class="mt-3">You can distribute your contributions to each charity here. Start from the top and specify the amount of percentage so that together they are total 100%.</p>
@if($errors->any())
    <div class="alert alert-warning">
        @foreach (array_unique($errors->all()) as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif
<form action="{{route('donate.save.distribution')}}" method="POST">
    @csrf
    @foreach(['one-time', 'bi-weekly'] as $key)
        @if($key === 'one-time' && (session()->get('amount-step')['frequency'] === 'one-time' || session()->get('amount-step')['frequency'] === 'both'))
            @php $key_ = $key; @endphp
            @php $keyCase = 'oneTime'; @endphp
            @include('donate.partials.amount-distribution')
        @endif
        @if($key === 'bi-weekly' && (session()->get('amount-step')['frequency'] === 'bi-weekly' || session()->get('amount-step')['frequency'] === 'both'))
            @php $key_ = $key;@endphp
            @php $keyCase = 'biWeekly'; @endphp
            @include('donate.partials.amount-distribution')
        @endif
    @endforeach
    <div class="mt-5">
        <a class="btn btn-lg btn-outline-primary" href="{{route('donate.amount')}}">Previous</a>
        <button class="btn btn-lg btn-primary" type="submit">Next</button>
    </div>
</form>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('css/custom-switch.css') }}">
@endpush
@push('js')
    <script>

        $(document).on('change', '#distributeByDollarAmountOneTime, #distributeByDollarAmountBiWeekly', function () {
            const frequency = $(this).attr('id') === 'distributeByDollarAmountOneTime' ? '#oneTimeSection' : '#biWeeklySection';
            if (!$(this).prop("checked")) {
                $(frequency).find(".by-amount").removeClass("d-none");
                $(frequency).find(".by-percent").addClass("d-none"); 
                $(frequency).find(".percent-amount-text").html("Distribute by Percentage");
            } else {
                $(frequency).find(".by-percent").removeClass("d-none");
                $(frequency).find(".by-amount").addClass("d-none");
                $(frequency).find(".percent-amount-text").html("Distribute by Dollar Amount");
            }
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

        $(document).on('change', '.percent-input', function () {
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


        $(document).on('change', '.amount-input', function () {
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

        $(".percent-input").change();
        $(".amount-input").change();


        $(document).on('click', '.distribute-evenly', function () {

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

    </script>
@endpush