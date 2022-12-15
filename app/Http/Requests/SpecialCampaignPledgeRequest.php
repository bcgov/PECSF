<?php

namespace App\Http\Requests;

use App\Models\Organization;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SpecialCampaignPledgeRequest extends FormRequest
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

        $gov = Organization::where('code', 'GOV')->first();

        return [ 
                'organization_id'  => ['required'],
                'user_id'       => [$this->organization_id == $gov->id ? 'required' : 'nullable',  'exists:users,id' ],
                'pecsf_id'      => ['digits:6',  $this->organization_id != $gov->id ? 'required' : 'nullable'],
                'pecsf_first_name'  => [$this->organization_id != $gov->id ? 'required' : 'nullable', 'regex:/^[\pL\s\-]+$/u'],
                'pecsf_last_name'   => [$this->organization_id != $gov->id ? 'required' : 'nullable', 'regex:/^[\pL\s\-]+$/u'],
                'pecsf_city'   => [$this->organization_id != $gov->id ? 'required' : 'nullable'],

                'special_campaign_id'  => [ 'required', Rule::exists("special_campaigns", "id")->whereNull("deleted_at") ],
                
                'one_time_amount'  => [ 'required','numeric','min:1', 'regex:/^(\d+\.?\d{0,2}|\d*\.?\d{0,2})$/' ],
        ];

    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [

            'user_id.required'       => 'The Employee field is required',
            'special_campaign_id.required' => 'The special campaign is required. ',
            'special_campaign_id.exists' => 'The selected special campaign is invalid. ',

            'one_time_amount.required' => 'The amount is required.',
            'one_time_amount.min'      => 'The min amount is $ 1.0.',
            'one_time_amount.regex' => 'The invalid amount, max 2 decimal places.',
        ];
    }

}
