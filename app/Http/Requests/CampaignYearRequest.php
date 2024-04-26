<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CampaignYearRequest extends FormRequest
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
        // Use for checking no more than one active Calendar Year 
        $arr = [];
        if ($this->getMethod() == 'POST') { // store
            $count = \App\Models\CampaignYear::where('Status', 'A')->count();
            if ($count== 0) {
                array_push($arr, 'A');
            }
        } else { // Update 
            $count = \App\Models\CampaignYear::where('Status', 'A')
                        ->where('calendar_year','!=', $this->calendar_year)
                        ->count();
            if ($count == 0) {
                array_push($arr, 'A');
            }
        }

        return [
            //
            'calendar_year'       => ['required','numeric',Rule::when($this->getMethod() == 'POST', 
                            ['unique:App\Models\CampaignYear,calendar_year']) ],
            'number_of_periods'   => 'required|numeric|min:1|max:99',
            'status'              => ['required',Rule::when($this->status == 'A', [Rule::in($arr)]) ],
            'start_date'          => 'required|date|before_or_equal:end_date',
            'end_date'            => 'required|date|after_or_equal:start_date',
            'volunteer_start_date'  => 'required|date|before_or_equal:volunteer_end_date',
            'volunteer_end_date'    => 'required|date|after_or_equal:volunteer_start_date',
            'close_date'          => 'required|date',
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
            'status.in' => 'More than one active Calendar Year is not allowed',
        ];
    }
}
