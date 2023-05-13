<div class="card m-0">
    <div class="card-header bg-light">
        <p class="h5">Bi-weekly (pay period) Deduction Amount</p>
    </diV>
    <div class="card-body ">

        <fieldset class="form-group amount-selection">
            <div class="pay_period_amount_error">
            </div>
            <div class="row">
                <div class="col-sm-10">
                     <div class="form-check">
                        <input class="form-check-input" type="radio" name="pay_period_amount" id="pay_period_amt_0"
                            value="0" {{ $pay_period_amount == 0 ? 'checked' : '' }}>
                        <label class="form-check-label" for="pay_period_amt_0">
                            None
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="pay_period_amount" id="pay_period_amt_1"
                            value="6" {{ $pay_period_amount == 6 ? 'checked' : '' }}>
                        <label class="form-check-label" for="pay_period_amt_1">
                            $6 per pay
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="pay_period_amount" id="pay_period_amt_2"
                            value="12" {{ $pay_period_amount == 12 ? 'checked' : '' }}>
                        <label class="form-check-label" for="pay_period_amt_2">
                            $12 per pay
                        </label>
                    </div>
                    <div class="form-check ">
                        <input class="form-check-input" type="radio" name="pay_period_amount" id="pay_period_amt_3"
                            value="20" {{ $pay_period_amount == 20 ? 'checked' : '' }}>
                        <label class="form-check-label" for="pay_period_amt_3">
                            $20 per pay
                        </label>
                    </div>
                    <div class="form-check ">
                        <input class="form-check-input" type="radio" name="pay_period_amount" id="pay_period_amt_4"
                            value="50" {{ $pay_period_amount == 50 ? 'checked' : '' }}>
                        <label class="form-check-label" for="pay_period_amt_4">
                            $50 per pay
                        </label>
                    </div>
                    <div class="form-check ">
                        <input class="form-check-input" type="radio" name="pay_period_amount" id="pay_period_amt_5"
                            value="" {{ $pay_period_amount_other > 0 ? 'checked' : '' }}>
                        <label class="form-check-label" for="pay_period_amt_5">
                        </label>
                        <span>Other:</span>
                        <input class="form-input" type="text" name="pay_period_amount_other" value="{{ $pay_period_amount_other }}" />
                    </div>
                  
                </div>
            </div>
        </fieldset>

    </div>
</div>

<div class="card m-0">
    <div class="card-header bg-light">
        <p class="h5">One-time Deduction Amount</p>
    </diV>
    <div class="card-body">

        <fieldset class="form-group amount-selection">
            <div class="one_time_amount_error">
            </div>
            <div class="row">
                {{-- <legend class="col-form-label col-sm-2 pt-0">Radios</legend> --}}
                <div class="col-sm-10">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="one_time_amount" id="one_time_amount_0"
                            value="0" {{ $one_time_amount == 0 ? 'checked' : '' }}>
                        <label class="form-check-label" for="one_time_amount_0">
                            None
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="one_time_amount" id="one_time_amount_1"
                            value="6" {{ $one_time_amount == 6 ? 'checked' : '' }}>
                        <label class="form-check-label" for="one_time_amount_1">
                            $6 
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="one_time_amount" id="one_time_amount_2"
                            value="12" {{ $one_time_amount == 12 ? 'checked' : '' }}>
                        <label class="form-check-label" for="one_time_amount_2">
                            $12 
                        </label>
                    </div>
                    <div class="form-check ">
                        <input class="form-check-input" type="radio" name="one_time_amount" id="one_time_amount_3"
                            value="20" {{ $one_time_amount == 20 ? 'checked' : '' }}>
                        <label class="form-check-label" for="one_time_amount_3">
                            $20 
                        </label>
                    </div>
                    <div class="form-check ">
                        <input class="form-check-input" type="radio" name="one_time_amount" id="one_time_amount_4"
                            value="50" {{ $one_time_amount == 50 ? 'checked' : '' }}>
                        <label class="form-check-label" for="one_time_amount_4">
                            $50 
                        </label>
                    </div>
                    <div class="form-check ">
                        <input class="form-check-input" type="radio" name="one_time_amount" id="one_time_amount_5"
                        value="" {{ $one_time_amount_other > 0 ? 'checked' : '' }}>
                        <label class="form-check-label" for="one_time_amount_5">
                        </label>
                        <span>Other:</span>
                        <input class="form-input" type="text" name="one_time_amount_other" value="{{ $one_time_amount_other }}" />
                    </div>

                </div>
            </div>
        </fieldset>

    </div>
</div>
