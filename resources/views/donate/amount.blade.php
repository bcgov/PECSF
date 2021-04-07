@extends('donate.layout.main')

@section ("step-content")
<h2 class="mt-5">2. Decide on the frequency and amount</h2>
<div>
    <div>
        <div class="btn-group mt-3 frequency" role="group" aria-label="Select frequency">
            <input type="radio" name="frequency" class="btn-check" id="bi-weekly-btn" autocomplete="off" value="bi-weekly" {{$preselectedData['frequency'] == 'bi-weekly' ? 'checked' : ''}}>
            <label class="btn btn-outline-primary btn-lg" for="bi-weekly-btn">Bi-weekly</label>

            <input type="radio" name="frequency" class="btn-check" id="one-time-btn" autocomplete="off" value="one-time" {{$preselectedData['frequency'] == 'one-time' ? 'checked' : ''}}>
            <label class="btn btn-outline-primary btn-lg" for="one-time-btn">One-time</label>
        </div>
    </div>
    <div class="bi-weekly-amounts" style="{{$preselectedData['frequency'] == 'one-time' ? 'display:none' : ''}}">
        <div class="btn-group d-flex mt-3 amounts" role="group" aria-label="Select amount">
            @foreach ($amounts["bi-weekly"] as $amount)
                <div class="me-2">
                    <input type="radio" name="amount" class="btn-check" id="amount-{{$amount['amount']}}" autocomplete="off" {{ ($amount['selected'] ? 'checked' : '') }} value="{{$amount['amount']}}" >
                    <label class="btn btn-outline-primary btn-lg d-flex align-items-center justify-content-center" for="amount-{{$amount['amount']}}">
                        <div>
                            <div><b>{{$amount['text']}}</b></div>
                            <small>Bi-Weekly</small>
                        </div>
                    </label>
                </div>
            @endforeach
        </div>
    </div>
    <div class="custom-amount mt-3" style="{{ $isCustomAmount ? '' : 'display:none '}}">
        <label>
            Custom amount
            <input type="number" name="amount" class="form-control" min="1" value="{{ $preselectedData['amount'] }}">
        </label>
    </div>
    <div class="mt-5">
        <form action="{{route('donate.save.amount')}}" method="post" onsubmit="prepareForm()">
            @csrf
            <div id="amount-values"></div>
            <a class="btn btn-lg btn-outline-primary" href="{{route('donate')}}">Previous</a>
            <button class="btn btn-lg btn-primary" type="submit">Next</button>
        </form>
    </div>
</div>
@endsection 

@push('css')
    <style>
        .bi-weekly-amounts {
            overflow: auto;
        }
        .amounts > div {
            flex-grow: 1;
            flex-basis: 0;
        }
        .amounts label {
            height: 180px;
        }
        .frequency label {
            width: auto;
        }
    </style>
@endpush

@push('js')
    <script type="x-tmpl" id="amount-tmpl">
        <input type="hidden" name="amount" value="${this.amount}">
        <input type="hidden" name="frequency" value="${this.frequency}">
    </script>
    <script>
        const tmplAmount = $("#amount-tmpl").html();

        const tmplParse = function(templateString, templateVars) {
            return new Function("return `" + templateString + "`;").call(templateVars);
        };

        $(document).on('change', 'input[name=frequency]', function() {
            const frequency = $(this).val();
            const amount = (frequency == 'bi-weekly') ? $("input[type=radio][name=amount]").val() : '';
            selectAmount(frequency, amount);
        });

        $(document).on('change', 'input[type=radio][name=amount]', function() {
            const amount = $(this).val();
            const frequency = $("input[name=frequency]:checked").val();
            selectAmount(frequency, amount);
        });

        $(document).on('change', 'input[type=number][name=amount]', function() {
            prepareForm();
        });

        function selectAmount(frequency, amount) {
            $(".custom-amount").hide();
            switch(frequency) {
                case 'bi-weekly': 
                    $(".bi-weekly-amounts").show();
                    break;
                case 'one-time':
                    $(".bi-weekly-amounts").hide();
                    break;
            }

            if (!amount) {
                $(".custom-amount").show();
            }

            prepareForm();
        }

        function prepareForm() {
            $("#amount-values").html(tmplParse(tmplAmount, {
                amount: $("input[type=radio][name=amount]:checked").val() ? $("input[type=radio][name=amount]:checked").val() : $("input[type=number][name=amount]").val(),
                frequency: $("input[name=frequency]:checked").val()
            }));
        }
    </script>
@endpush
