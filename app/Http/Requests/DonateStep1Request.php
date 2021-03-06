<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DonateStep1Request extends FormRequest
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
            "id" => "required|array|min:1|max:10",
            "additional" => "required|array"
        ];
    }

    public function messages() 
    {
        return [
            "id.required" => "Please select at least one charity from the list",
            "id.max" => "The charities must not have more than 10 items."
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->request->get('id')) {
            $charities = [];
            $charities['id'] = $this->request->get('id');
            $charities['additional'] = $this->request->get('additional');
            Session()->put('charities', $charities);
        } else {
            session()->forget('charities');
        }
    }

}
