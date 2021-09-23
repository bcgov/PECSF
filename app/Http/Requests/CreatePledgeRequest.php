<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePledgeRequest extends FormRequest
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
            'charityAmount' => 'required|array',
            'charityAdditional' => 'required|array',
            'charityAmount.*' => 'required|numeric',
            'charityAdditional.*' => 'nullable',
            'annualAmount' => 'required|numeric',
            'frequency' => 'required'
        ];
    }
}
