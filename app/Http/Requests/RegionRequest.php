<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class RegionRequest extends FormRequest
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
            'code'    => ['required','numeric','regex:/[0-9][0-9][0-9]/',
                            Rule::when($this->getMethod() == 'POST', ['unique:App\Models\Region,code']) 
                         ],
            'name'    => 'required|max:30',
            'status'  => ['required', Rule::in(['A', 'I']) ],
            'effdt'   => 'required|date',
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
            'code.regex' => 'The code format is invalid, 3 characters long and all digits.',
            'effdt.required' => 'The effective date field is required',
        ];
    }
}
