<?php

namespace App\Http\Requests;

use App\Models\Organization;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CampaignPledgeRequest extends FormRequest
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

        $organization = Organization::where('code', 'GOV')->first();

        if ($this->step == 1 && empty($this->pledge_id) ) {
            $my_rules = array_merge($my_rules, 
                [
                    //
                    'campaign_year_id'    => ['required', 'exists:campaign_years,id',
                                    Rule::unique('pledges')->where(function ($query) use($organization) {
                                          $query->where('organization_id', $this->organization_id)
                                                ->when($this->organization_id == $organization->id, function($q) {
                                                    return $q->where('user_id', $this->user_id);
                                                }) 
                                                ->when($this->organization_id != $organization->id, function($q) {
                                                    return $q->where('pecsf_id', $this->pecsf_id);
                                                }) 
                                                ->where('campaign_year_id', $this->campaign_year_id);
                                    })->ignore($this->pledge_id),
                    ],
                    'organization_id'  => ['required'],
                    'user_id'       => [$this->organization_id == $organization->id ? 'required' : 'nullable',  'exists:users,id' ],
                    
                    'pecsf_id'      => ['digits:6',  $this->organization_id != $organization->id ? 'required' : 'nullable'],
                    'pecsf_first_name'  => [$this->organization_id != $organization->id ? 'required' : 'nullable'],
                    'pecsf_last_name'   => [$this->organization_id != $organization->id ? 'required' : 'nullable'],
                    'pecsf_city'   => [$this->organization_id != $organization->id ? 'required' : 'nullable'],
                    // 'city_id'   => [$this->organization_id != $organization->id ? 'required' : 'nullable'],
                
                ]
            );
        }

        if ($this->step >= 2) {
            $my_rules = array_merge($my_rules, 
                [
                    'step'          => ['required'],
                    'pay_period_amount_other'    => [ Rule::when( $this->pay_period_amount =='', ['required','numeric']) ],
                    'one_time_amount_other'    => [ Rule::when( $this->one_time_amount =='', ['required','numeric']) ],
                ]
            );
        }

        if ($this->step >= 3) {
            $my_rules = array_merge($my_rules, 
                [
                    //
                    'pool_option'   => ['required', Rule::in(['C', 'P']) ],
                    'pool_id'       => ['required_if:pool_option,P', Rule::when( $this->pool_option == 'P', ['exists:f_s_pools,id']) ],
                    'charities.*'   => ['required_if:pool_option,C'], 
                    'percentages.*' => $this->pool_option == 'C' ?
                                'required|numeric|min:0|max:100|between:0,100.00|regex:/^\d+(\.\d{1,2})?$/' :  '',
                ]
            );
        }

        return $my_rules;

    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator)  {

            $step = $this->step;
            $charities = $this->charities;
            $percentages = $this->percentages;

            if ($charities && $this->pool_option == 'C' && $step >= 3) {

                // Check 100%
                $sum = 0;
                for ($i=0; $i < count($charities); $i++) {
                    if (is_numeric($percentages[$i]) ) {
                        $sum += $percentages[$i];
                    }
                }
                if ($sum != 100) {
                    for ($i=0; $i < count($charities); $i++) {
                            $validator->errors()->add('percentages.' .$i, 'The sum of percentage is not 100.');
                    }
                }

                // Check duplicate charity id 
                $dups = array_count_values(
                    array_filter($charities, fn($value) => !is_null($value) && $value !== '')
                );

                for ($i=0; $i < count($charities); $i++) {
                    if ( array_key_exists($charities[$i],$dups) && $dups[ $charities[$i] ] > 1) {
                        $validator->errors()->add('charities.' .$i, 'The duplicated charity is entered.');
                    }
                }

            // if ($this->somethingElseIsInvalid()) {
            //     $validator->errors()->add('field', 'Something is wrong with this field!');
            // }

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

            'campaign_year_id.unique' => 'The campaign year has already been taken',
            'user_id.required'       => 'The Employee field is required.',
            'pecsf_id.required'      => 'The PECSF ID field is required.',
            'pecsf_first_name.required'  => 'The First Name field is required.',
            'pecsf_last_name.required'   => 'The Last Name field is required.',
            'pecsf_city.required'   => 'The City field is required.',

            'charities.*.required_if' => 'The Charity field is required.',

            'percentages.*.required' => 'The Percentage field is required.',
            'percentages.*.max' => 'The Percentage must not be greater than 100.',
            'percentages.*.min' => 'The Percentage must be at least 0.',
            'percentages.*.numeric' => 'The Percentage must be a number.',
            'percentages.*.between' => 'The percentages.0 must be between 0 and 100.',
            'percentages.*.regex' => 'The percentages format is invalid.',

        ];
    }

}
