<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class DonateNowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $my_rules = [];

        if ($this->step == 1) {
            $my_rules = array_merge($my_rules,
                [
                    'pool_option'  => ['required', Rule::in(['C', 'P']) ],
                ]
            );
        }

        if ($this->step >= 2) {
            $my_rules = array_merge($my_rules,
                [
                    'step' => ['required'],
                    'pool_option'  => ['required', Rule::in(['C', 'P']) ],
                    'pool_id'      => ['required_if:pool_option,P', Rule::when( $this->pool_option == 'P', [ Rule::exists("f_s_pools", "id")->whereNull("deleted_at"), ]) ],
                    // 'charity_id'   => ['required_if:pool_option,C', Rule::when( $this->pool_option == 'P', ['exists:charities,id']) ],
                    'charities'   =>  [ Rule::when( $this->pool_option == 'C', ['required', 'min:1', 'max:1']) ],
                    'charities.*' =>  [ Rule::when( $this->pool_option == 'C', ['exists:charities,id']) ],

                ]
            );
        }

        if ($this->step >= 3) {
            $my_rules = array_merge($my_rules,
                [
                    // 'one_time_amount_custom'  => [ Rule::when( $this->one_time_amount =='', ['required','numeric']) ],
                    'one_time_amount_custom'  => [ Rule::when( empty($this->one_time_amount),
                                                    ['required','numeric','min:1', 'regex:/^(-?\w+\.?\d{0,2}|\d*\.?\d{0,2})$/']) ],
                ]
            );
        }

        return $my_rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator)  {

            $step = $this->step;
            $charities = $this->charities ?? [];
            $biWeeklyPercents = $this->biWeeklyPercent;
            $oneTimePercents = $this->oneTimePercent;

            if ($charities && $this->pool_option == 'C' && $step >= 2) {

                // check max number of charities
                $max = 1;
                if ( count($charities) > $max) {
                    for ($i= ($max) ; $i < count($charities); $i++) {
                        $validator->errors()->add('organization_name.' .$i, 'Exceeds maximum number of charities.');
                    }
                }
            }


        });
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [

            // 'campaign_year_id.unique' => 'The campaign year has already been taken',
            // 'user_id.required'       => 'The Employee field is required',

            'pool_id.required_if' => 'A Fund Supported Pool selection is required. Please choose a Fund Supported Pool.',
            // 'charity_id.required_if' => 'A charity selection is required. Please choose a charity.',
            'charities.required' => 'At least one charity must be specified.',
            'charities.min' => 'At least one charity must be specified.',
            'charities.max' => 'More than one charity chosen.',
            'charities.*.exists' =>  'The invalid charity entered.',
            'one_time_amount_custom.numeric' => ' The One-time custom amount must be a number.',
            'one_time_amount_custom.required' => 'The amount is required.',
            'one_time_amount_custom.min'      => 'The minimum One-time custom amount is $1',
            'one_time_amount_custom.regex' => ' The One-time custom amount must have maximum of 2 decimal places.',
 ];

    }

}
