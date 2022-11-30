<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class OrganizationRequest extends FormRequest
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
        return [
            //
            'code'    => ['required','max:3','regex:/^[A-Z]+$/',
                            //'alpha_dash',
                            // Rule::when($this->getMethod() == 'POST', ['unique:App\Models\Organization,code']) 
                            Rule::when($this->getMethod() == 'POST', [Rule::unique("organizations", "code")->whereNull("deleted_at")])
                         ],
            'name'    => 'required|max:50',
            'status'  => ['required', Rule::in(['A', 'I']) ],
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
            'code.regex' => 'The code format is invalid, max 3 characters long and all uppercase, no space.',
        ];
    }
}
