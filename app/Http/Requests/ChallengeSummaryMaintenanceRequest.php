<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ChallengeSummaryMaintenanceRequest extends FormRequest
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
        $rules = [
           'campaign_year'    => ['required','integer',
                            Rule::when($this->getMethod() == 'POST', [Rule::unique("daily_campaign_summaries")->whereNull("deleted_at")])
                         ],
            'as_of_date'    => 'required|date',
            'donors'  => ['required', 'numeric' ],
            'dollars'  => ['required', 'numeric' ],
        ];

        return $rules;
    }

     /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            // 'effdt.required' => 'The effective date field is required',
            // 'effdt.before' => 'The effective date field must be in the past if the status is Active',

            // 'linked_bu_code.exists' => "The associated business unit is invalid.",
       ];
    }

}
