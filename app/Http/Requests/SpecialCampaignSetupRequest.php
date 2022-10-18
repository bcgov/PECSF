<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SpecialCampaignSetupRequest extends FormRequest
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
           
        $my_rules = [
                'name'          => ['required', 'max:50', Rule::unique('special_campaigns')->ignore($this->id) ],
                'charity_id'    => 'required|exists:charities,id',
                'start_date'    => 'required|date|before_or_equal:end_date',
                'end_date'      => 'required|date|after_or_equal:start_date',
                'description'   => 'required|max:2048',
                'banner_text'   => 'required|max:255',
        ];


        if ($this->getMethod() == 'POST') {
            
            $my_rules = array_merge($my_rules, 
                [
                    'logo_image_file' => 'required|mimes:jpg,jpeg,png,bmp,svg|max:2048',
                ]
            );

        } else {

            $my_rules = array_merge($my_rules, 
                [
                    'logo_image_file' => 'required_without:image|mimes:jpg,jpeg,png,bmp,svg|max:2048',
                ]
            );
            
        }

        return $my_rules;

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
            'logo_image_file.required' => 'The logo image file is required.',
            'logo_image_file.required_without' => 'The logo image file is required.',
       ];
    }

}
