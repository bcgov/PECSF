<div class="card m-0">
    <div role="radiogroup" class="card-header bg-light"  aria-labelledby="group_label_1">
        <p id="group_label_1" class="h5">Bi-weekly (pay period) Deduction Amount</p>
    </diV>
    <div class="card-body ">

        <fieldset class="form-group amount-selection">
            <div class="pay_period_amount_error">
            </div>
            <div class="row">
                <div class="col-sm-10">
                     <div class="form-check">
                        <input class="form-check-input" type="radio" name="pay_period_amount" id="pay_period_amt_0"
                            value="0" {{ $pay_period_amount == 0 ? 'checked' : '' }}
                            aria-labelledby="pay_period_amt_0_label"
                            aria-describedby="pay_period_amount_error-error"
                            {{ ( isset($pledge) && $pledge->ods_export_status)  ? 'disabled' : '' }} >
                        <label id="pay_period_amt_0_label" class="form-check-label" for="pay_period_amt_0" aria-hidden="true">
                            None
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="pay_period_amount" id="pay_period_amt_1"
                            value="6" {{ $pay_period_amount == 6 ? 'checked' : '' }}
                            aria-labelledby="pay_period_amt_1_label"
                            aria-describedby="pay_period_amount_error-error"
                            {{ ( isset($pledge) && $pledge->ods_export_status)  ? 'disabled' : '' }}>
                        <label id="pay_period_amt_1_label" class="form-check-label" for="pay_period_amt_1" aria-hidden="true">
                            $6 per pay
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="pay_period_amount" id="pay_period_amt_2"
                            value="12" {{ $pay_period_amount == 12 ? 'checked' : '' }}
                            aria-labelledby="pay_period_amt_2_label"
                            aria-describedby="pay_period_amount_error-error"
                            {{ ( isset($pledge) && $pledge->ods_export_status)  ? 'disabled' : '' }}>
                        <label id="pay_period_amt_2_label" class="form-check-label" for="pay_period_amt_2" aria-hidden="true">
                            $12 per pay
                        </label>
                    </div>
                    <div class="form-check ">
                        <input class="form-check-input" type="radio" name="pay_period_amount" id="pay_period_amt_3"
                            value="20" {{ $pay_period_amount == 20 ? 'checked' : '' }}
                            aria-labelledby="pay_period_amt_3_label"
                            aria-describedby="pay_period_amount_error-error"
                            {{ ( isset($pledge) && $pledge->ods_export_status)  ? 'disabled' : '' }}>
                        <label id="pay_period_amt_3_label" class="form-check-label" for="pay_period_amt_3" aria-hidden="true">
                            $20 per pay
                        </label>
                    </div>
                    <div class="form-check ">
                        <input class="form-check-input" type="radio" name="pay_period_amount" id="pay_period_amt_4"
                            value="50" {{ $pay_period_amount == 50 ? 'checked' : '' }}
                            aria-labelledby="pay_period_amt_4_label"
                            aria-describedby="pay_period_amount_error-error"
                            {{ ( isset($pledge) && $pledge->ods_export_status)  ? 'disabled' : '' }}>
                        <label id="pay_period_amt_4_label" class="form-check-label" for="pay_period_amt_4" aria-hidden="true">
                            $50 per pay
                        </label>
                    </div>
                    <div class="form-check ">
                        <input class="form-check-input" type="radio" name="pay_period_amount" id="pay_period_amt_5"
                            value="" {{ $pay_period_amount_other > 0 ? 'checked' : '' }}
                            aria-labelledby="pay_period_amt_5_label"
                            aria-describedby="pay_period_amount_error-error"
                            {{ ( isset($pledge) && $pledge->ods_export_status)  ? 'disabled' : '' }}>
                        <label id="pay_period_amt_5_label" class="form-check-label" for="pay_period_amt_5" aria-hidden="true">
                            <span>Other:</span>
                        </label>
                        <input class="form-control mt-2 ml-1 w-25" type="text" name="pay_period_amount_other" value="{{ $pay_period_amount_other }}" 
                                tabindex="{{ $pay_period_amount_other > 0 ? '0' : '-1' }}"
                                aria-label="enter Pay period deduction amount"
                        {{ ( isset($pledge) && $pledge->ods_export_status)  ? 'disabled' : '' }}>
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
                            value="0" {{ $one_time_amount == 0 ? 'checked' : '' }}
                            aria-labelledby="one_time_amount_0_label"
                            aria-describedby="one_time_amount_error-error"
                            {{ ( isset($pledge) && $pledge->ods_export_status)  ? 'disabled' : '' }}>
                        <label id="one_time_amount_0_label" class="form-check-label" for="one_time_amount_0" aria-hidden="true">
                            None
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="one_time_amount" id="one_time_amount_1"
                            value="6" {{ $one_time_amount == 6 ? 'checked' : '' }}
                            aria-labelledby="one_time_amount_1_label"
                            aria-describedby="one_time_amount_error-error"
                            {{ ( isset($pledge) && $pledge->ods_export_status)  ? 'disabled' : '' }}>
                        <label id="one_time_amount_1_label" class="form-check-label" for="one_time_amount_1" aria-hidden="true">
                            $6 
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="one_time_amount" id="one_time_amount_2"
                            value="12" {{ $one_time_amount == 12 ? 'checked' : '' }}
                            aria-labelledby="one_time_amount_2_label"
                            aria-describedby="one_time_amount_error-error"
                            {{ ( isset($pledge) && $pledge->ods_export_status)  ? 'disabled' : '' }}>
                        <label id="one_time_amount_2_label" class="form-check-label" for="one_time_amount_2" aria-hidden="true">
                            $12 
                        </label>
                    </div>
                    <div class="form-check ">
                        <input class="form-check-input" type="radio" name="one_time_amount" id="one_time_amount_3"
                            value="20" {{ $one_time_amount == 20 ? 'checked' : '' }}
                            aria-labelledby="one_time_amount_3_label"
                            aria-describedby="one_time_amount_error-error"
                            {{ ( isset($pledge) && $pledge->ods_export_status)  ? 'disabled' : '' }}>
                        <label id="one_time_amount_3_label" class="form-check-label" for="one_time_amount_3" aria-hidden="true">
                            $20 
                        </label>
                    </div>
                    <div class="form-check ">
                        <input class="form-check-input" type="radio" name="one_time_amount" id="one_time_amount_4"
                            value="50" {{ $one_time_amount == 50 ? 'checked' : '' }}
                            aria-labelledby="one_time_amount_4_label"
                            aria-describedby="one_time_amount_error-error"
                            {{ ( isset($pledge) && $pledge->ods_export_status)  ? 'disabled' : '' }}>
                        <label id="one_time_amount_4_label" class="form-check-label" for="one_time_amount_4" aria-hidden="true">
                            $50 
                        </label>
                    </div>
                    <div class="form-check ">
                        <input class="form-check-input" type="radio" name="one_time_amount" id="one_time_amount_5"
                        value="" {{ $one_time_amount_other > 0 ? 'checked' : '' }}
                        aria-labelledby="one_time_amount_5_label"
                        aria-describedby="one_time_amount_error-error"
                            {{ ( isset($pledge) && $pledge->ods_export_status)  ? 'disabled' : '' }}>
                        <label id="one_time_amount_5_label" class="form-check-label" for="one_time_amount_5" aria-hidden="true">
                            <span>Other:</span>
                        </label>

                        <input class="form-control mt-2 ml-1 w-25" type="text" name="one_time_amount_other" value="{{ $one_time_amount_other }}"
                                tabindex="{{ $one_time_amount_other > 0 ? '0' : '-1' }}"
                                aria-label="enter one time deduction amount"
                            {{ ( isset($pledge) && $pledge->ods_export_status)  ? 'disabled' : '' }}>
                    </div>

                </div>
            </div>
        </fieldset>

    </div>
</div>
