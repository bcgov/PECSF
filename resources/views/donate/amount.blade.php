@extends('donate.layout.main')

@section ("step-content")
<h2 class="mt-5">2. Decide on the frequency and amount</h2>
<div>
    <div>
        <div class="btn-group btn-group-toggle mt-3 frequency" role="group" aria-label="Select frequency" data-toggle="buttons">
            <label class="btn btn-outline-primary btn-lg" for="bi-weekly-btn">
                <input type="radio" name="frequency" class="btn-check" id="bi-weekly-btn" autocomplete="off" value="bi-weekly" {{$preselectedData['frequency'] == 'bi-weekly' ? 'checked' : ''}}>
                Bi-weekly
            </label>
            <label class="btn btn-outline-primary btn-lg" for="one-time-btn">
                <input type="radio" name="frequency" class="btn-check" id="one-time-btn" autocomplete="off" value="one-time" {{$preselectedData['frequency'] == 'one-time' ? 'checked' : ''}}>
                One-time
            </label>
        </div>
    </div>
    <div class="predefined-amounts" >
        <div class="btn-group d-flex mt-3 amounts btn-group-toggle" data-toggle="buttons" role="group" aria-label="Select amount">
            @foreach ($amounts["bi-weekly"] as $amount)
                <label class="mr-2 btn btn-outline-primary rounded btn-lg d-flex align-items-center justify-content-center" for="amount-{{$amount['amount']}}">
                    <input type="radio" name="amount" class="btn-check" id="amount-{{$amount['amount']}}" autocomplete="off" {{ ($amount['selected'] ? 'checked' : '') }} value="{{$amount['amount']}}" >
                    <div>
                        <div><b>{{$amount['text']}}</b></div>
                        <small class="frequency-text">{{ $preselectedData['frequency'] == 'one-time' ? 'One-time' : 'Bi-Weekly'}}</small>
                    </div>
                </label>
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
        .predefined-amounts {
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
            const amount = $("input[type=radio][name=amount]:checked").val() != '' ? $("input[type=radio][name=amount]:checked").val() : $("input[type=number][name=amount]").val();
            selectAmount(frequency, amount);
        });

        $(document).on('change', 'input[type=radio][name=amount]', function() {
            const amount = $(this).val();
            const frequency = $("input[name=frequency]:checked").val();
            selectAmount(frequency, amount);
            if (!amount) $(".custom-amount").show();
            else $(".custom-amount").hide();
        });

        $(document).on('change', 'input[type=number][name=amount]', function() {
            prepareForm();
        });

        function selectAmount(frequency, amount) {
            switch(frequency) {
                case 'bi-weekly': 
                    $(".frequency-text").html("Bi-Weekly");
                    break;
                case 'one-time':
                    $(".frequency-text").html("One-time");
                    break;
            }

            prepareForm();
        }

        function prepareForm() {
            let amount = null;
            const frequency = $("input[name=frequency]:checked").val();
            if (frequency === 'one-time') {
                amount = $("input[type=number][name=amount]").val();
            } else {
                amount = $("input[type=radio][name=amount]:checked").val() != '' ? $("input[type=radio][name=amount]:checked").val() : $("input[type=number][name=amount]").val()
            }
            $("#amount-values").html(tmplParse(tmplAmount, {
                amount: amount,
                frequency: $("input[name=frequency]:checked").val()
            }));
        }
    </script>
@endpush
