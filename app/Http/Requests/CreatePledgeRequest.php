<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
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
            'charityOneTimeAmount' => Rule::when($this->pool_option == 'C', ['required','array']),
            'charityBiWeeklyAmount' => Rule::when($this->pool_option == 'C', ['required','array']),
            'charityOneTimePercentage' => Rule::when($this->pool_option == 'C', ['required','array']),
            'charityBiWeeklyPercentage' => Rule::when($this->pool_option == 'C', ['required','array']),
            'charityAdditional' => Rule::when($this->pool_option == 'C', ['required','array']),
            'charityOneTimeAmount.*' => Rule::when($this->pool_option == 'C', ['required','numeric']),
            'charityBiWeeklyAmount.*' => Rule::when($this->pool_option == 'C', ['required','numeric']),
            'charityAdditional.*' => 'nullable',
            'annualBiWeeklyAmount' => 'required|numeric',
            'annualOneTimeAmount' => 'required|numeric',
            'frequency' => 'required',
            'pool_option' => 'required',
            'regional_pool_id' => 'sometimes',
        ];
    }
}
