<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DonateStep3Request extends FormRequest
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
            'distributionByPercentOneTime' => 'nullable|boolean',
            'distributionByPercentBiWeekly' => 'nullable|boolean',
            // TODO: Manage required as per frequency
            'oneTimePercent' => 'required_without:biWeeklyPercent|array',
            'oneTimeAmount' => 'required_without:biWeeklyAmount|array',
            'biWeeklyPercent' => 'required_without:oneTimePercent|array',
            'biWeeklyAmount' => 'required_without:oneTimeAmount|array'
        ];
    }
}
