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
            'address_type' => 'nullable|in:Global,New,Opt-out',
            'new_address' => 'required_if:address_type,new',
            'no_of_years' => 'nullable|required_if:no_of_years_opt_out,0|integer',
            'preferred_role' => 'required|in:Lead Coordinator,Canvasser,Event Planner,Office contact'
        ];
    }
}
