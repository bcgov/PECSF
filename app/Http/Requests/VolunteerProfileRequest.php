<?php

namespace App\Http\Requests;

use App\Models\VolunteerProfile;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class VolunteerProfileRequest extends FormRequest
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

        $role_keys = array_keys(VolunteerProfile::ROLE_LIST);
        $province_keys = array_keys(VolunteerProfile::PROVINCE_LIST);

        if ($this->step == 1) {
            $my_rules = array_merge($my_rules,
                [
                    'business_unit_code'  => ['required', 
                                                Rule::exists("business_units", "code")->where('status', 'A')->whereNull("deleted_at"),
                                            ],
                    'no_of_years' => "required|integer|between:1,50",
                    'preferred_role' => ["required", Rule::in( $role_keys )],
                ]
            );
        }

        if ($this->step >= 2) {
            $my_rules = array_merge($my_rules,
                [
                    'address_type' => ['required', Rule::in(['G', 'S']) ],
                    'address'  => ['required_if:address_type,S'],
                    'city'  => ['required_if:address_type,S',
                                    Rule::when( $this->address_type == 'S', ['exists:cities,id']),
                            ],
                    'province' => ['required_if:address_type,S',
                                     Rule::when( $this->address_type == 'S', Rule::in( $province_keys )) 
                                 ],
                    'postal_code'  => ['required_if:address_type,S',
                            Rule::when( $this->address_type == 'S', 'regex:/^([a-zA-Z]\d[a-zA-Z])\ {0,1}(\d[a-zA-Z]\d)$/'),
                                     ],
          
                    // 'pool_id'      => ['required_if:pool_option,P', Rule::when( $this->pool_option == 'P', [ Rule::exists("f_s_pools", "id")->whereNull("deleted_at"), ]) ],
                    

                    // 'step' => ['required'],
                    // 'pool_option'  => ['required', Rule::in(['C', 'P']) ],
                    // 'pool_id'      => ['required_if:pool_option,P', Rule::when( $this->pool_option == 'P', [ Rule::exists("f_s_pools", "id")->whereNull("deleted_at"), ]) ],
                    // // 'charity_id'   => ['required_if:pool_option,C', Rule::when( $this->pool_option == 'P', ['exists:charities,id']) ],
                    // 'charities'   =>  [ Rule::when( $this->pool_option == 'C', ['required', 'min:1', 'max:1']) ],
                    // 'charities.*' =>  [ Rule::when( $this->pool_option == 'C', ['exists:charities,id']) ],

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

            'business_unit_code.required' => 'The organization field is required',
            'business_unit_code.exists' => 'The selected organization is invalid',

            'no_of_years.required' => 'The number of years field is required',
            'no_of_years.between' => 'The number of years must be between 1 and 50',
            'no_of_years.integer' => 'The number of years must be an integer',

            'preferred_role.required' => 'The preferred volunteer role field is required',
            'preferred_role.in' => 'The selected preferred volunteer role is invalid',

            'address.required_if' => 'The street address field is required',
            'city.required_if' => 'The city field is required',
            'city.exists' =>  'The invalid city entered',
            'province.required_if' => 'The province field is required',
            'province.in' => 'The selected province is invalid',
            'postal_code.required_if' => 'The postal code field is required',
            'postal_code.regex' => 'The postal code format is invalid (sample valid code: M5V 2L7)',

            // 'charity_id.required_if' => 'A charity selection is required. Please choose a charity.',
            // 'charities.required' => 'At least one charity must be specified.',
            // 'charities.min' => 'At least one charity must be specified.',
            // 'charities.max' => 'More than one charity chosen.',
            // 'charities.*.exists' =>  'The invalid charity entered.',
            // 'one_time_amount_custom.numeric' => ' The One-time custom amount must be a number.',
            // 'one_time_amount_custom.required' => 'The amount is required.',
            // 'one_time_amount_custom.min'      => 'The minimum One-time custom amount is $1',
            // 'one_time_amount_custom.regex' => ' The One-time custom amount must have maximum of 2 decimal places.',
        ];

    }

}
