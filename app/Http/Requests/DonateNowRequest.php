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
                    'pool_id'      => ['required_if:pool_option,P', Rule::when( $this->pool_option == 'P', ['exists:f_s_pools,id']) ],
                    'charity_id'   => ['required_if:pool_option,C', Rule::when( $this->pool_option == 'P', ['exists:charities,id']) ],
                ]
            );
        }

        if ($this->step >= 3) {
            $my_rules = array_merge($my_rules, 
                [
                    'one_time_amount_custom'  => [ Rule::when( $this->one_time_amount =='', ['required','numeric']) ],
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

            'pool_id.required_if' => 'You have not chosen any Fund Supported Pool. The Fund Supported Pool is required when selected Pool option.',
            'charity_id.required_if' => 'You have not chosen any charities. The charity is required when selected Charity option.',
        ];
    }

}
