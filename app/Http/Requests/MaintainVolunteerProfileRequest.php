<?php

namespace App\Http\Requests;

use App\Models\Organization;
use Illuminate\Validation\Rule;
use App\Models\VolunteerProfile;
use Illuminate\Foundation\Http\FormRequest;

class MaintainVolunteerProfileRequest extends FormRequest
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

        $role_keys = array_keys(VolunteerProfile::ROLE_LIST);
        $province_keys = array_keys(VolunteerProfile::PROVINCE_LIST);

        return [

            'campaign_year' => ['required'],
            'organization_id'  => ['required'],
            'emplid'      => [
                                Rule::when($this->organization_id == $gov->id, 'required|exists:users,emplid'),
                            ],
            'pecsf_id'      => [
                                Rule::when($this->organization_id != $gov->id, 'required|digits:6'),
                                ],
            'pecsf_first_name' => [
                                    Rule::when($this->organization_id != $gov->id, 'required|regex:/^[\pL\s\-]+$/u'),
                                    ],
            'pecsf_last_name'  => [
                                    Rule::when($this->organization_id != $gov->id, 'required|regex:/^[\pL\s\-]+$/u'),
                                  ],
            'pecsf_city'   => [
                                    Rule::when($this->organization_id != $gov->id, 'required'),
                             ],
            'business_unit_code'  => ['required', 
                    Rule::exists("business_units", "code")->where('status', 'A')->whereNull("deleted_at"),
                ],
            'no_of_years' => "required|integer|between:1,50",
            'preferred_role' => ["required", Rule::in( $role_keys )],

            'address_type' => ['required', Rule::in(['G', 'S'])],
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

            ];
    }

    public function withValidator($validator)
    {

        $validator->after(function ($validator)  {

            $org = Organization::where('id', $this->organization_id)->first();

            if ($org) {
            
                if ($org->code != "GOV") {
                    if ($this->address_type == 'G') {
                        $validator->errors()->add('address_type',  "The address cannot be 'Use Global Address Listing' for non-Gov employee.");
                    }
                }

                // Check duplicate records Campaign Year + Empld / PECSF ID
                $profile = VolunteerProfile::where('campaign_year', $this->campaign_year)
                                        ->where('organization_code', $org->code)
                                        ->when($org->code == 'GOV', function($q) {
                                            return $q->where('emplid', $this->emplid);
                                        }) 
                                        ->when($org->code != 'GOV', function($q) {
                                            return $q->where('pecsf_id', $this->pecsf_id);
                                        })
                                        ->first();

                if ($profile && $this->profile_id != $profile->id) {
                    if( $org->code == 'GOV' ) {
                        $validator->errors()->add('emplid',  "This employee have already registered for campaign year " . $this->campaign_year);
                    } else {
                        $validator->errors()->add('pecsf_id',  "This non-government employee have already registered for campaign year " . $this->campaign_year);
                    }
                }

                // If renew record, only 1 is allowed
                $profile = VolunteerProfile::where('campaign_year', '<', $this->campaign_year)
                                        ->where('organization_code', $org->code)
                                        ->when($org->code == 'GOV', function($q) {
                                            return $q->where('emplid', $this->emplid);
                                        }) 
                                        ->when($org->code != 'GOV', function($q) {
                                            return $q->where('pecsf_id', $this->pecsf_id);
                                        })
                                        ->orderBy('campaign_year', 'desc')
                                        ->first();

                if ($profile && $this->no_of_years <> 1) {
                    $validator->errors()->add('no_of_years',  
                        "The registration from the ". $profile->campaign_year . " campaign year has been located. Renewal entries must be for a duration of one year."
                        );
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

            'business_unit_code.required' => 'The business unit field is required',
            'business_unit_code.exists' => 'The selected business unit is invalid',

            'preferred_role.required' => 'The preferred volunteer role field is required',
            'preferred_role.in' => 'The selected preferred volunteer role is invalid',

            'address.required_if' => 'The address field is required',
            'city.required_if' => 'The city field is required',
            'city.exists' =>  'The invalid city entered',
            'province.required_if' => 'The province field is required',
            'province.in' => 'The selected province is invalid',
            'postal_code.required_if' => 'The postal code field is required',
            'postal_code.regex' => 'The postal code format is invalid (sample valid code: M5V 2L7)',

        ];

    }

}
