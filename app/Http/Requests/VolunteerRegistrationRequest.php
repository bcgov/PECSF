<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VolunteerRegistrationRequest extends FormRequest
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
            'organization_id' => 'required|exists:organizations,id',
            'address_type' => 'nullable|in:global,new',
            'new_address' => 'required_if:address_type,new',
            'no_of_years' => 'required|integer',
            'preferred_role' => 'required|in:Coordinator,Canvasser,Event Coordinator,Office contact'
        ];
    }
}
