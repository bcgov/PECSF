<?php

namespace App\Http\Requests;

use App\Models\CampaignYear;
use App\Models\Organization;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
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

        $gov = Organization::where('code', 'GOV')->first();

        if ($this->step == 1) {
            if (empty($this->pledge_id) ) {
                $my_rules = array_merge($my_rules,
                    [
                        //
                        'campaign_year_id'    => ['required', 'exists:campaign_years,id',
                                        Rule::unique('pledges')->where(function ($query) use($gov) {
                                            $query->where('organization_id', $this->organization_id)
                                                    ->when($this->organization_id == $gov->id, function($q) {
                                                        return $q->where('user_id', $this->user_id);
                                                    })
                                                    ->when($this->organization_id != $gov->id, function($q) {
                                                        return $q->where('pecsf_id', $this->pecsf_id);
                                                    })
                                                    ->where('campaign_year_id', $this->campaign_year_id)
                                                    ->whereNull('deleted_at')
                                                    ->whereNull('cancelled');
                                        })->ignore($this->pledge_id),
                        ],
                        'organization_id'  => ['required'],
                        'user_id'       => [$this->organization_id == $gov->id ? 'required' : 'nullable',  'exists:users,id' ],

                        'pecsf_id'      => ['digits:6',  $this->organization_id != $gov->id ? 'required' : 'nullable'],
                        'pecsf_first_name'  => [$this->organization_id != $gov->id ? 'required' : 'nullable', 'regex:/^[A-Za-z .,]+$/'],
                        'pecsf_last_name'   => [$this->organization_id != $gov->id ? 'required' : 'nullable', 'regex:/^[A-Za-z .,]+$/'],
                        'pecsf_city'   => [$this->organization_id != $gov->id ? 'required' : 'nullable'],
                        // 'city_id'   => [$this->organization_id != $gov->id ? 'required' : 'nullable'],

                    ]
                );
            } else {
                // Allow edit
                if ($this->organization_id != $gov->id) {

                    $organization = Organization::where('id', $this->organization_id)->first();
                    $org_code = $organization ? $organization->code : null;

                    $cy  = CampaignYear::where('id',  $this->campaign_year_id)->first();
                    $year = $cy ? $cy->calendar_year : null;

                    $my_rules = array_merge($my_rules,
                        [
                            'campaign_year_id'    => ['required', 'exists:campaign_years,id',
                                Rule::unique('pledges')->where(function ($query) use($gov) {
                                    $query->where('organization_id', $this->organization_id)
                                            // ->when($this->organization_id == $gov->id, function($q) {
                                            //     return $q->where('user_id', $this->user_id);
                                            // })
                                            ->when($this->organization_id != $gov->id, function($q) {
                                                return $q->where('pecsf_id', $this->pecsf_id);
                                            })
                                            ->where('campaign_year_id', $this->campaign_year_id)
                                            ->whereNull('deleted_at')
                                            ->whereNull('cancelled');
                                })->ignore($this->pledge_id),
                            ],
                            'organization_id'  => ['required'],
                            'pecsf_id'      => ['digits:6', 'required',
                                    Rule::unique('donations')->where(function ($query) use($org_code, $year) {
                                        $query->where('org_code', $org_code)
                                              ->where('yearcd', $year)
                                              ->whereNotExists(function ($query) {
                                                $query->select(DB::raw(1))
                                                      ->from('pledges')
                                                      ->leftJoin('organizations', 'pledges.organization_id', 'organizations.id')
                                                      ->whereColumn('donations.pecsf_id', 'pledges.pecsf_id')
                                                      ->whereColumn('donations.org_code', 'organizations.code')
                                                      ->where('pledges.id', $this->pledge_id);
                                              });
                                    }),
                            ],
                            'pecsf_first_name'  => ['required', 'regex:/^[A-Za-z .,]+$/'],
                            'pecsf_last_name'   => [ 'required', 'regex:/^[A-Za-z .,]+$/'],
                            'pecsf_city'   => ['required' ],
                        ]
                    );
                }
            }
        }

        if ($this->step >= 2) {
            $my_rules = array_merge($my_rules,
                [
                    'step'          => ['required'],
                    'pay_period_amount_other'    => [ Rule::when( $this->pay_period_amount =='',
                                        ['required','numeric','min:1', 'regex:/^(\d+\.?\d{0,2}|\d*\.?\d{0,2})$/']) ],
                    'one_time_amount_other'    => [ Rule::when( $this->one_time_amount =='',
                                        ['required','numeric','min:1', 'regex:/^(\d+\.?\d{0,2}|\d*\.?\d{0,2})$/']) ],
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
                                'required|numeric|min:0.01|max:100|between:0.01,100.00|regex:/^\d+(\.\d{1,2})?$/' :  '',
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

            if ($step >= 2) {

                $pay_period_amount = $this->pay_period_amount  ? 
                                $this->pay_period_amount : $this->pay_period_amount_other ;
                $one_time_amount = $this->one_time_amount ? 
                                $this ->one_time_amount : $this->one_time_amount_other;

                if (!($pay_period_amount > 0 || $one_time_amount > 0)) {
                    $validator->errors()->add('pay_period_amount_error', 'Either a bi-weekly or one-time deduction amount(s) must be selected.');
                    $validator->errors()->add('one_time_amount_error', 'Either a bi-weekly or one-time deduction amount(s) must be selected.');
                }

            }

            if ($charities && $this->pool_option == 'C' && $step >= 3) {

                // Check 100%
                $sum = 0;
                for ($i=0; $i < count($charities); $i++) {
                    if (is_numeric($percentages[$i]) ) {
                        $sum += $percentages[$i];
                    }
                }
                if ( round($sum,2) != 100) {
                    for ($i=0; $i < count($charities); $i++) {
                            $validator->errors()->add('percentages.' .$i, 'The sum of percentage is not 100.');
                    }
                }

                // Check duplicate charity id
                $combined = [];
                for ($i=0; $i < count($charities); $i++) {
                   array_push($combined, $charities[$i] . '|' . $this->additional[$i]  );
                }

                $dups = array_count_values(
                    // array_filter($charities, fn($value) => !is_null($value) && $value !== '')
                    array_filter($combined, fn($value) => !is_null($value) && $value !== '')
                );

                for ($i=0; $i < count($charities); $i++) {
                    $key = $charities[$i]. '|' . $this->additional[$i]; 
                    // if ( array_key_exists($charities[$i],$dups) && $dups[ $charities[$i] ] > 1) {
                    if ( array_key_exists($key,$dups) && $dups[ $key ] > 1) {
                        // $validator->errors()->add('charities.' .$i, 'The duplicated charity is entered.');
                        $validator->errors()->add('additional.' .$i, 'The duplicated Program or Branch Name is entered.');
                    }
                }
// dd( [$this->request, $dups, $combined, $validator->errors() ]);
            // if ($this->somethingElseIsInvalid()) {
            //     $validator->errors()->add('field', 'Something is wrong with this field!');
            // }
                if (count($charities) > 10) {
                    for ($i=0; $i < count($charities); $i++) {
                        $validator->errors()->add('charities.' .$i, 'Maximum limit exceeded. More than 10 charities entered.');
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

            'campaign_year_id.unique' => 'The campaign year has already been taken',
            'user_id.required'       => 'The Employee field is required',
            'pecsf_id.required'      => 'The PECSF ID field is required',
            'pecsf_id.digits'      => 'The PECSF ID must be 6 digits',
            'pecsf_id.unique'      => 'The Pledge for this PECSF ID has already been taken',
            'pecsf_first_name.required'  => 'The First Name field is required.',
            'pecsf_first_name.regex' => "The First Name must only contain letters, periods, spaces.",
            'pecsf_last_name.required'   => 'The Last Name field is required.',
            'pecsf_last_name.regex' => "The Last Name must only contain letters, periods, spaces.",
            'pecsf_city.required'   => 'The City field is required.',

            'one_time_amount_other.required' => 'The amount is required.',
            'one_time_amount_other.min'      => 'The min amount is $ 1.',
            'one_time_amount_other.regex' => 'The invalid amount, max 2 decimal places.',
            'pay_period_amount_other.required' => 'The amount is required.',
            'pay_period_amount_other.min' => 'The min amount is $ 1.',
            'pay_period_amount_other.regex' => 'The invalid amount, max 2 decimal places.',

            'pool_id.required_if' => "The Fund Supported Pool field is required",
            'charities.*.required_if' => 'The Charity field is required.',

            'percentages.*.required' => 'The Percentage field is required.',
            'percentages.*.max' => 'The Percentage must not be greater than 100.',
            'percentages.*.min' => 'The Percentage must be at least 0.01',
            'percentages.*.numeric' => 'The Percentage must be a number.',
            'percentages.*.between' => 'The percentages must be between 0.01 and 100.',
            'percentages.*.regex' => 'The percentages format is invalid.',

        ];
    }

}
