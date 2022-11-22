<?php

namespace App\Http\Requests;

use App\Models\CampaignYear;
use App\Models\Organization;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\FormRequest;

class AnnualCampaignRequest extends FormRequest
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

        if ($this->step >= 1) { 

            $my_rules = array_merge($my_rules, 
            [
                'step'         => ['required'],
                'pool_option'  => ['required', Rule::in(['C', 'P']) ],
                'number_of_periods' => ['required'],
            ]);

        } 

        if ($this->step >= 2) {
            $my_rules = array_merge($my_rules, 
                [
                    'regional_pool_id'    => ['required_if:pool_option,P', Rule::when( $this->pool_option == 'P', ['exists:f_s_pools,id']) ],
                    'charities'   =>  [ Rule::when( $this->pool_option == 'C', ['required', 'min:1']) ], 
                    'charities.*' =>  [ Rule::when( $this->pool_option == 'C', ['exists:charities,id']) ],

                ]
            );
        }

        if ($this->step >= 3) {
            $my_rules = array_merge($my_rules, 
                [
                    "frequency" => "required|in:bi-weekly,one-time,both",                    

                    'one_time_amount_custom'  => [ Rule::when( empty($this->one_time_amount) && 
                                                        ($this->frequency == 'one-time' || $this->frequency == 'both'), 
                                                        ['required','numeric','min:1', 'regex:/^(\d+\.?\d{0,2}|\d*\.?\d{0,2})$/']) ],
                                                        // ^(\d{1,5}\.?\d{1,2}|\d{1,5}\.?\d{1,2})$
                    'bi_weekly_amount_custom'  => [ Rule::when( $this->bi_weekly_amount =='' &&
                                                        ($this->frequency == 'bi-weekly' || $this->frequency == 'both'),                             
                                                        ['required','numeric','min:1', 'regex:/^(\d+\.?\d{0,2}|\d*\.?\d{0,2})$/']) ],
                ]
            );
        }

        if ($this->step >= 4) {

            $my_rules = array_merge($my_rules, 
                [

                    'oneTimeAmount.*' => Rule::when($this->pool_option == 'C', ['required', 'numeric', 'min:0.01']),
                    'oneTimePercent.*' => Rule::when($this->pool_option == 'C', ['required', 'numeric', 
                                                'between:0.01,100.00']),
                    'biWeeklyAmount.*'  => Rule::when($this->pool_option == 'C', ['required', 'numeric', 'min:0.01']),
                    'biWeeklyPercent.*' => Rule::when($this->pool_option == 'C', ['required', 'numeric', 
                                                'between:0.01,100.00']),
                    // 'percentages.*' => $this->pool_option == 'C' ?
                    //             'required|numeric|min:0|max:100|between:0,100.00|regex:/^\d+(\.\d{1,2})?$/' :  '',


                ]
            );
        }

        if ($this->step >= 5) {
            $my_rules = array_merge($my_rules, 
                [
                    'charityOneTimeAmount' => Rule::when($this->pool_option == 'C', ['required','array']),
                    'charityBiWeeklyAmount' => Rule::when($this->pool_option == 'C', ['required','array']),
                    'charityOneTimePercentage' => Rule::when($this->pool_option == 'C', ['required','array']),
                    'charityBiWeeklyPercentage' => Rule::when($this->pool_option == 'C', ['required','array']),
                    'charityAdditional' => Rule::when($this->pool_option == 'C', ['required','array']),
                    'charityOneTimeAmount.*' => Rule::when($this->pool_option == 'C', ['required','numeric']),
                    'charityBiWeeklyAmount.*' => Rule::when($this->pool_option == 'C', ['required','numeric']),
                    'charityAdditional.*' => 'nullable',
                    'annualBiWeeklyAmount' => 'required|numeric',
                    'annualOneTimeAmount' => 'required|numeric',

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

                // Check duplicate charity id 
                $dups = array_count_values(
                        array_filter($charities, fn($value) => !is_null($value) && $value !== '')
                );

                for ($i=0; $i < count($charities); $i++) {
                    if ( array_key_exists($charities[$i],$dups) && $dups[ $charities[$i] ] > 1) {
                        $validator->errors()->add('organization_name.' .$i, 'The duplicated charity is entered.');
                    }
                }
    
                // check max number of charities
                $max = 10;
                if ( count($charities) > $max) {
                    for ($i= ($max) ; $i < count($charities); $i++) {
                        $validator->errors()->add('organization_name.' .$i, 'Exceeds maximum number of charities.');
                    }
                }
            }

            if ($charities && $this->pool_option == 'C' && $step >= 4) {

                // Check 100% -- biWeekly
                if ($this->frequency === 'bi-weekly' || $this->frequency === 'both') {
                    $sum = 0;
                    foreach ($biWeeklyPercents as $key => $percentage) {
                        if (is_numeric($percentage) ) {
                        $sum += $percentage;
                        }
                    }
                    if ( round($sum,2) != 100) {
                        foreach ($biWeeklyPercents as $key => $percentage) {
                        // for ($i=0; $i < count($charities); $i++) {
                                $validator->errors()->add('biWeeklyPercent.' .$key, 'The sum of percentage is not 100.');
                        }
                    }
                }

                // Check 100% -- oneTimePercents
                if ($this->frequency === 'one-time' || $this->frequency === 'both') {
                    $sum = 0;
                    foreach ($oneTimePercents as $key => $percentage) {
                        if (is_numeric($percentage) ) {
                        $sum += $percentage;
                        }
                    }
                    if ( round($sum,2) != 100) {
                        foreach ($oneTimePercents as $key => $percentage) {    
                        // for ($i=0; $i < count($charities); $i++) {
                                $validator->errors()->add('oneTimePercent.' .$key, 'The sum of percentage is not 100.');
                        }
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

            'charities.required' => 'At least one charity must be specified.',
            'charities.min' => 'At least one charity must be specified.',
            'charities.*.exists' =>  'The invalid charity entered.',

            'one_time_amount_custom.required' => 'The amount is required.',
            'one_time_amount_custom.min'      => 'The min amount is $ 1.0.',
            'one_time_amount_custom.regex' => 'The invalid amount, max 2 decimal places.',
            'bi_weekly_amount_custom.required' => 'The amount is required.',
            'bi_weekly_amount_custom.min' => 'The min amount is $ 1.0.',
            'bi_weekly_amount_custom.regex' => 'The invalid amount, max 2 decimal places.',
 
            'biWeeklyPercent.*.required' => 'The Percentage field is required.',
            'biWeeklyPercent.*.numeric' => 'The Percentage must be a number.',
            'biWeeklyPercent.*.between' => 'The percentage must be between 0.01 and 100.',
            'oneTimePercent.*.required' => 'The Percentage field is required.',
            'oneTimePercent.*.numeric' => 'The Percentage must be a number.',
            'oneTimePercent.*.between' => 'The percentage must be between 0.01 and 100.',
            // 'biWeeklyPercent.*.min' => 'The Percentage must be greater than 0.',
            // 'biWeeklyPercent.*.max' => 'The Percentage must not be greater than 100.',
            // 'biWeeklyPercent.*.regex' => 'The percentage format is invalid.',
            'oneTimeAmount.*.min' => 'The amount must be greater than 0.',
            'oneTimeAmount.*.required' => 'The amount is required.',
            'biWeeklyAmount.*.min' => 'The amount must be greater than 0.',
            'biWeeklyAmount.*.required' => 'The amount is required.',
        ];
    }

}
