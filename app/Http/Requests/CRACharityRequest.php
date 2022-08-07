<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CRACharityRequest extends FormRequest
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
            // 'alt_address1'    => ['required','numeric','regex:/[0-9][0-9][0-9]/',
            //                 Rule::when($this->getMethod() == 'POST', ['unique:App\Models\Region,code']) 
            //              ],
            
            'alt_address1'   => [ Rule::when( $this->exists('use_alt_address'), ['required','max:60']) ],
            'alt_address2'   => [ Rule::when( $this->exists('use_alt_address'), ['max:60']) ],
            'alt_city'       => [ Rule::when( $this->exists('use_alt_address'), ['required']) ],
            'alt_province'       => [ Rule::when( $this->exists('use_alt_address'), ['required', 'max:2']) ],
            'alt_postal_code'       => [ Rule::when( $this->exists('use_alt_address'), ['required']),
                                            'regex:/^([A-Z]\d[A-Z])\ {1}(\d[A-Z]\d)$/',
                                ],
            'alt_country'   => [ Rule::when( $this->exists('use_alt_address'), ['required']) ],
            'financial_contact_name'  => 'nullable|max:60',
            'financial_contact_title'  => 'nullable|max:60',
            'financial_contact_email'  => 'nullable|email',
            'comments'  => 'nullable',
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
            'alt_address1.required' => 'The Address 1 field is required.',
            'alt_address1.max' => 'The Address 1 must not be greater than 60 characters.',
            'alt_address2.max' => 'The Address 2 must not be greater than 60 characters.',
            'alt_city.required' => 'The City field is required.',
            'alt_province.required' => 'The Province field is required.',
            'alt_province.max' => 'The Province must not be greater than 2 characters.',
            'alt_postal_code.required' => 'The Postal Code field is required.',
            'alt_postal_code.regex' => 'The Postal Code format is invalid. (two, three character sets with space e.g. M6E 1A5).',
            'alt_country.required' => 'The Country field is required.',
            'financial_contact_email.email' => 'The Charity Financial Email must be a valid email address.',
        ];
    }
    
}
