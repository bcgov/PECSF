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
        return [
            //
            'code'    => ['required','max:5',
                            Rule::when($this->getMethod() == 'POST', ['unique:App\Models\BusinessUnit,code']) 
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
            'effdt.required' => 'The effective date field is required',
        ];
    }
    
}
