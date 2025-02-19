<?php

namespace App\Http\Requests;

use App\Models\Organization;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class DonateNowPledgeRequest extends FormRequest
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

        $gov = Organization::where('code', 'GOV')->first();

        return [
                'organization_id'  => ['required'],
                'user_id'       => [$this->organization_id == $gov->id ? 'required' : 'nullable',  'exists:users,id' ],
                'pecsf_id'      => ['digits:6',  $this->organization_id != $gov->id ? 'required' : 'nullable'],
                'pecsf_first_name'  => [$this->organization_id != $gov->id ? 'required' : 'nullable', 'regex:/^[A-Za-z .,]+$/'],
                'pecsf_last_name'   => [$this->organization_id != $gov->id ? 'required' : 'nullable', 'regex:/^[A-Za-z .,]+$/'],
                'pecsf_city'   => [$this->organization_id != $gov->id ? 'required' : 'nullable'],

                'pool_option'   => ['required', Rule::in(['C', 'P']) ],
                'pool_id'       => ['required_if:pool_option,P', Rule::when( $this->pool_option == 'P', [ Rule::exists("f_s_pools", "id")->whereNull("deleted_at"),]) ],
                'charity_id'      => ['required_if:pool_option,C', Rule::when( $this->pool_option == 'C', ['exists:charities,id']) ],
                'one_time_amount'  => [ 'required','numeric','min:1', 'regex:/^(\d+\.?\d{0,2}|\d*\.?\d{0,2})$/' ],
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

            // 'campaign_year_id.unique' => 'The campaign year has already been taken',
            // 'user_id.required'       => 'The Employee field is required',
            'pecsf_first_name.regex' => "The First Name must only contain letters, periods, spaces.",
            'pecsf_last_name.regex' => "The Last Name must only contain letters, periods, spaces.",

            'pool_id.required_if' => 'A Fund Supported Pool selection is required. Please choose a Fund Supported Pool.',
            'charity_id.required_if' => 'A charity selection is required. Please choose a charity.',

            'one_time_amount.required' => 'The amount is required.',
            'one_time_amount.min'      => 'The minimum One-time custom amount is $1',
            'one_time_amount.regex' => 'The invalid amount, max 2 decimal places.',
        ];
    }

}
