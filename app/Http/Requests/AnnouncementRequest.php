<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class AnnouncementRequest extends FormRequest
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
            'title'   => 'required|max:100', 
            'body'    => 'required',
            'status'  => ['required', Rule::in(['A', 'I']) ],
            'start_date'   => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
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
            // 'code.regex' => 'The code format is invalid, 3 characters long and all digits.',
            // 'effdt.required' => 'The effective date field is required',
        ];
    }
}
