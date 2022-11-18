<div id="step-amount-area">                            

    <div class="mt-5">
        <div class="">
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
        <div class="" id="bi-weekly-section">
            <div class="predefined-amounts-bi-weekly" >
                <div class="btn-group d-flex mt-3 amounts btn-group-toggle" data-toggle="buttons" role="group" aria-label="Select amount">
                    @foreach ($amounts["bi-weekly"] as $amount)
                        <label class="mr-2 btn btn-outline-primary rounded btn-lg d-flex align-items-center justify-content-center" for="amount-bi-weekly-{{$amount['amount']}}">
                            <input type="radio" name="bi_weekly_amount" class="btn-check" id="amount-bi-weekly-{{$amount['amount']}}" autocomplete="off" {{ ($amount['selected'] ? 'checked' : '') }} value="{{$amount['amount']}}" >
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
                    <input type="number" name="bi_weekly_amount_custom" class="form-control" min="0" value="{{ $preselectedData['bi-weekly-amount'] }}">
                </label>
            </div>
        </div>
        <div class="" id="one-time-section">
            <div class="predefined-amounts-one-time">
                <div class="btn-group d-flex mt-3 amounts btn-group-toggle" data-toggle="buttons" role="group" aria-label="Select amount">
                    @foreach ($amounts["one-time"] as $amount)
                        <label class="mr-2 btn btn-outline-primary rounded btn-lg d-flex align-items-center justify-content-center" for="amount-one-time-{{$amount['amount']}}">
                            <input type="radio" name="one_time_amount" class="btn-check" id="amount-one-time-{{$amount['amount']}}" autocomplete="off" {{ ($amount['selected'] ? 'checked' : '') }} value="{{$amount['amount']}}" >
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
                    <input type="number" name="one_time_amount_custom" class="form-control" min="0" value="{{ $preselectedData['one-time-amount'] }}">
                </label>
            </div>
        </div>
    </div>

</div>    


@push('css')
<style>
    
    /* Frequenct and Amount Area */
    #step-amount-area .predefined-amounts {
            overflow: auto;
    }

    #step-amount-area .amounts > div {
            flex-grow: 1;
            flex-basis: 0;
    }

    #step-amount-area .amounts label {
            height: 180px;
    }
    
    #step-amount-area .frequency label {
            width: auto;
    }

</style>
@endpush

@push('js')
    <script>
        
        const decideVisibility = function (frequency) {
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
            prepareForm();
        }
        $(document).ready(() => {
            decideVisibility($('input[name=frequency]:checked').val());
        });

        $(document).on('change', 'input[name=frequency]', function() {
            const frequency = $(this).val();
            decideVisibility(frequency);
            const amount = $("input[type=radio][name=amount]:checked").val() != '' ? $("input[type=radio][name=amount]:checked").val() : $("input[type=number][name=amount]").val();
            // selectAmount(frequency, amount);
            prepareForm();
        });

        $(document).on('change', 'input[type=radio][name=one_time_amount]', function() {
            const amount = $(this).val();
            const frequency = 'one-time';
            prepareForm();
            if (!amount) $(".custom-amount-one-time").show();
            else $(".custom-amount-one-time").hide();
        });
        $(document).on('change', 'input[type=radio][name=bi_weekly_amount]', function() {
            const amount = $(this).val();
            const frequency = 'bi-weekly';
            prepareForm();
            if (!amount) $(".custom-amount-bi-weekly").show();
            else $(".custom-amount-bi-weekly").hide();
        });

        $(document).on('change', 'input[type=number][name=amount]', function() {
            prepareForm();
        });

        $(document).on('change', 'input[type=number][name=bi_weekly_amount_custom]', function() {
            prepareForm();
        });
        $(document).on('change', 'input[type=number][name=one_time_amount_custom]', function() {
            prepareForm();
        });
    
        function prepareForm() {
            let amount = null;
            const frequency = $("input[name=frequency]:checked").val();
            const biWeeklyAmount = $("input[type=radio][name=bi_weekly_amount]:checked").val() || $("input[type=number][name=bi_weekly_amount_custom]").val();
            const oneTimeAmount = $("input[type=radio][name=one_time_amount]:checked").val() || $("input[type=number][name=one_time_amount_custom]").val();
        }
    </script>
@endpush

