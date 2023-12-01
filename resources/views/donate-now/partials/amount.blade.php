
<h3 class="">3. Decide on the amount</h3>

<div class=""></div>
<div  style="" >
    <br>
    {{-- <div class="">
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
    </div> --}}
    {{-- <div class="" id="bi-weekly-section">
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
    </div> --}}
    <div class="" id="one-time-section">
        <div class="predefined-amounts-one-time">
            <div class="btn-group d-flex mt-3 amounts btn-group-toggle" data-toggle="buttons" role="group" aria-label="Select amount">
                @foreach ($amount_options as $key => $text)
                    <label class="mr-2 btn btn-outline-primary rounded btn-lg d-flex align-items-center justify-content-center" for="amount-one-time-{{ $key}}">
                        @php $isCustom = (!in_array($one_time_amount, [6, 12, 20, 50]))  @endphp
                        <input type="radio" name="one_time_amount" class="btn-check" id="amount-one-time-{{ $key }}" autocomplete="off" {{ (($key == '' && $isCustom ) || ($key == $one_time_amount)) ? 'checked' : '' }} value="{{ $key}}" >
                        <div>
                            <div><b>{{ $text }}</b></div>
                            <small class="frequency-text">One-time</small>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>
        <div class="custom-amount-one-time mt-3" style="{{ (!in_array($one_time_amount, [6, 12, 20, 50])) ? '' : 'display:none '}}">
            <label>
                Custom amount
                <input type="text" name="one_time_amount_custom" class="form-control"  value="{{ $one_time_amount_custom }}">
            </label>
        </div>
    </div>
</div>


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
    {{-- <script type="x-tmpl" id="amount-tmpl">
        <input type="hidden" name="one-time-amount" value="${this.oneTimeAmount}">
        <input type="hidden" name="bi-weekly-amount" value="${this.biWeeklyAmount}">
        <input type="hidden" name="frequency" value="${this.frequency}">
    </script> --}}
    <script>
        // const tmplAmount = $("#amount-tmpl").html();

        // const tmplParse = function(templateString, templateVars) {
        //     return new Function("return `" + templateString + "`;").call(templateVars);
        // };
        // // const decideVisibility = function (frequency) {
        //     if (frequency === 'one-time') {
        //         $('#one-time-section').show();
        //         $('#bi-weekly-section').hide();
        //     } else if(frequency === 'bi-weekly') {
        //         $('#one-time-section').hide();
        //         $('#bi-weekly-section').show();
        //     } else {
        //         $('#one-time-section').show();
        //         $('#bi-weekly-section').show();
        //     }
        // }
        // $(document).ready(() => {
        //     decideVisibility($('input[name=frequency]:checked').val());
        // });

        // $(document).on('change', 'input[name=frequency]', function() {
        //     const frequency = $(this).val();
        //     decideVisibility(frequency);
        //     const amount = $("input[type=radio][name=amount]:checked").val() != '' ? $("input[type=radio][name=amount]:checked").val() : $("input[type=number][name=amount]").val();
        //     selectAmount(frequency, amount);
        // });

        $(document).on('change', 'input[type=radio][name=one_time_amount]', function() {
            const amount = $(this).val();
            // const frequency = 'one-time';
            // selectAmount(frequency, amount);
            if (!amount) $(".custom-amount-one-time").show();
            else $(".custom-amount-one-time").hide();
        });

        // $(document).on('change', 'input[type=number][name=amount]', function() {
        //     prepareForm();
        // });

        // function selectAmount(frequency, amount) {
        //     /* switch(frequency) {
        //         case 'bi-weekly':
        //             $(".frequency-text").html("Bi-Weekly");
        //             break;
        //         case 'one-time':
        //             $(".frequency-text").html("One-time");
        //             break;
        //     } */

        //     prepareForm();
        // }

        // function prepareForm() {
        //     let amount = null;
        //     const frequency = $("input[name=frequency]:checked").val();
        //     const biWeeklyAmount = $("input[type=radio][name=bi-weekly-amount]:checked").val() || $("input[type=number][name=bi-weekly-amount]").val();
        //     const oneTimeAmount = $("input[type=radio][name=one_time_amount]:checked").val() || $("input[type=number][name=one_time_amount]").val();

        //     if (frequency === 'one-time') {
        //         amount = $("input[type=radio][name=bi-weekly-amount]").val();
        //     } else {
        //         amount = $("input[type=radio][name=amount]:checked").val() != '' ? $("input[type=radio][name=amount]:checked").val() : $("input[type=number][name=amount]").val()
        //     }
        //     $("#amount-values").html(tmplParse(tmplAmount, {
        //         biWeeklyAmount: biWeeklyAmount,
        //         oneTimeAmount: oneTimeAmount,
        //         frequency: $("input[name=frequency]:checked").val()
        //     }));
        // }
    </script>
@endpush
