<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SpecialCampaignRequest extends FormRequest
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
                    'step' => ['required'],
                    'special_campaign_id'      => ['required',  'exists:special_campaigns,id' ],
                ]
            );
        }

        if ($this->step >= 2) {
            $my_rules = array_merge($my_rules,
                [
                    'one_time_amount_custom'  => [ Rule::when( empty($this->one_time_amount),
                                        ['required','numeric','min:1', 'regex:/^(-?\w+\.?\d{0,2}|\d*\.?\d{0,2})$/']) ],
                ]
            );
        }

        return $my_rules;
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
            // 'one_time_amount_custom.min' => 'The custom amount must be at least 0.01.',

            'one_time_amount_custom.required' => 'The amount is required.',
            'one_time_amount_custom.min'      => 'The minimum One-time custom amount is $1',
            'one_time_amount_custom.regex' => ' The One-time custom amount must have maximum of 2 decimal places.',
            'one_time_amount_custom.numeric' => ' The One-time custom amount must be a number.',
        ];
    }

}
