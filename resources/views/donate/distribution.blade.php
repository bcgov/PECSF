@extends('donate.layout.main')

@section ("step-content")
<h2 class="mt-5">3. Decide on the distribution</h2>
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
        $(document).on('change', '.percent-input', function () {
            let total = 0;
            const section = $(this).parents('.amountDistributionSection');
            section.find(".percent-input").each( function () {
                total += Number($(this).val());
            });
            if (total !== 100) {
                const lastValue = Number(section.find(".percent-input").last().val());
                const difference = 100 - total;
                section.find(".percent-input").last().val(lastValue + difference);
                total = 100;
            }
            section.find(".total-percent").val(total);
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
                section.find(".amount-input").last().val(lastValue + difference);
                total = expectedTotal;
            }
            section.find(".total-amount").val(total);
        });

        $(".percent-input").change();
        $(".amount-input").change();

    </script>
@endpush