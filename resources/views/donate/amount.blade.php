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

            <label class="btn btn-outline-primary btn-lg" for="both-btn">
                <input type="radio" name="frequency" class="btn-check" id="both-btn" autocomplete="off" value="both" {{$preselectedData['frequency'] == 'both' ? 'checked' : ''}}>
                Both
            </label>
        </div>
    </div>
    <div id="bi-weekly-section">
        <div class="predefined-amounts-bi-weekly" >
            <div class="btn-group d-flex mt-3 amounts btn-group-toggle" data-toggle="buttons" role="group" aria-label="Select amount">
                @foreach ($amounts["bi-weekly"] as $amount)
                    <label class="mr-2 btn btn-outline-primary rounded btn-lg d-flex align-items-center justify-content-center" for="amount-bi-weekly-{{$amount['amount']}}">
                        <input type="radio" name="bi-weekly-amount" class="btn-check" id="amount-bi-weekly-{{$amount['amount']}}" autocomplete="off" {{ ($amount['selected'] ? 'checked' : '') }} value="{{$amount['amount']}}" >
                        <div>
                            <div><b>{{$amount['text']}}</b></div>
                            <small class="frequency-text">Bi-weekly</small>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>
        <div class="custom-amount-bi-weekly mt-3" style="{{ $isCustomAmountBiWeekly ? '' : 'display:none '}}">
            <label>
                Custom amount
                <input type="number" name="bi-weekly-amount" class="form-control" min="1" value="{{ $preselectedData['bi-weekly-amount'] }}">
            </label>
        </div>
    </div>
    <div id="one-time-section" style="display: none;" >
        <div class="predefined-amounts-one-time">
            <div class="btn-group d-flex mt-3 amounts btn-group-toggle" data-toggle="buttons" role="group" aria-label="Select amount">
                @foreach ($amounts["one-time"] as $amount)
                    <label class="mr-2 btn btn-outline-primary rounded btn-lg d-flex align-items-center justify-content-center" for="amount-one-time-{{$amount['amount']}}">
                        <input type="radio" name="one-time-amount" class="btn-check" id="amount-one-time-{{$amount['amount']}}" autocomplete="off" {{ ($amount['selected'] ? 'checked' : '') }} value="{{$amount['amount']}}" >
                        <div>
                            <div><b>{{$amount['text']}}</b></div>
                            <small class="frequency-text">One-time</small>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>
        <div class="custom-amount-one-time mt-3" style="{{ $isCustomAmountOneTime ? '' : 'display:none '}}">
            <label>
                Custom amount
                <input type="number" name="one-time-amount" class="form-control" min="1" value="{{ $preselectedData['one-time-amount'] }}">
            </label>
        </div>
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
            if (frequency === 'one-time') {
                $('#one-time-section').show();
                $('#bi-weekly-section').hide();
            } else if(frequency === 'bi-weekly') {
                $('#one-time-section').hide();
                $('#bi-weekly-section').show();
            } else {
                $('#one-time-section').show();
                $('#bi-weekly-section').show();
            }
            const amount = $("input[type=radio][name=amount]:checked").val() != '' ? $("input[type=radio][name=amount]:checked").val() : $("input[type=number][name=amount]").val();
            selectAmount(frequency, amount);
        });

        $(document).on('change', 'input[type=radio][name=one-time-amount]', function() {
            const amount = $(this).val();
            const frequency = 'one-time';
            selectAmount(frequency, amount);
            if (!amount) $(".custom-amount-one-time").show();
            else $(".custom-amount-one-time").hide();
        });
        $(document).on('change', 'input[type=radio][name=bi-weekly-amount]', function() {
            const amount = $(this).val();
            const frequency = 'bi-weekly';
            selectAmount(frequency, amount);
            if (!amount) $(".custom-amount-bi-weekly").show();
            else $(".custom-amount-bi-weekly").hide();
        });

        $(document).on('change', 'input[type=number][name=amount]', function() {
            prepareForm();
        });

        function selectAmount(frequency, amount) {
            /* switch(frequency) {
                case 'bi-weekly': 
                    $(".frequency-text").html("Bi-Weekly");
                    break;
                case 'one-time':
                    $(".frequency-text").html("One-time");
                    break;
            } */

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
