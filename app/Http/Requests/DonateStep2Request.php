<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DonateStep2Request extends FormRequest
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
            "one-time-amount" => "required_unless:frequency,bi-weekly|numeric|min:1",
            "bi-weekly-amount" => "required_unless:frequency,one-time|numeric|min:1",
            "frequency" => "required|in:bi-weekly,one-time,both"
        ];
    }
}
