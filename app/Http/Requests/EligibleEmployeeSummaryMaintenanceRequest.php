<?php

namespace App\Http\Requests;

use App\Models\Organization;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class EligibleEmployeeSummaryMaintenanceRequest extends FormRequest
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

        $organization = Organization::where('code', $this->organization_code)->with('business_unit')->first();

        $bu_code = $organization ? $organization->business_unit->code : '';

        if ($this->getMethod() == 'POST') { // store
            $rules = [
                'campaign_year'    => ['required', 'integer'],
                // 'as_of_date'    => 'required|date',
                'organization_code'    => ['required', 
                                Rule::unique('eligible_employee_by_bus')->where(function ($query) use ($bu_code) {
                                    return $query->where('campaign_year', $this->campaign_year)
                                                 ->where('business_unit_code', $bu_code);
                                    }),
                ],
                'ee_count'  => ['required', 'numeric', 'gt:0' ],
             ];
        } else {
            $rules = [
                'ee_count'  => ['required', 'numeric', 'gt:0' ],
             ];
        }

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
            'organization_code.unique' => 'The business unit of this organization has already been taken for this campaign year.',
            'ee_count.required'  => 'The employee count field is required.',
            'ee_count.numeric'  => 'The employee count field must be a number.',
            'ee_count.gt'  => 'The employee count field must be greater than 0.',

       ];
    }

}
