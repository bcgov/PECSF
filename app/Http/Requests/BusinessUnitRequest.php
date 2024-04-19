<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class BusinessUnitRequest extends FormRequest
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
           'code'    => ['required','max:5',
                            Rule::when($this->getMethod() == 'POST', [Rule::unique("business_units", "code")->whereNull("deleted_at")])
                         ],
            'name'    => 'required|max:60',
            'status'  => ['required', Rule::in(['A', 'I']) ],
            'acronym' => 'required|max:5|alpha|uppercase',
            'linked_bu_code' => [ 'required', 'max:5', 
                                    Rule::when( $this->code <> $this->linked_bu_code,
                                    [Rule::exists('business_units')->where(function ($query) {
                                                   return $query->where('code', $this->linked_bu_code)
                                                                ->whereNull("deleted_at");

                                    })])
            ],
        ];
        if ($this->request->get('status') == "A") {
            $rules['effdt'] = 'before:today';
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
            'effdt.required' => 'The effective date field is required',
            'effdt.before' => 'The effective date field must be in the past if the status is Active',

            'linked_bu_code.exists' => "The associated business unit is invalid.",
       ];
    }

}
