<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessUnit;
use App\Models\SupplyOrderForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SupplyReportController extends Controller
{
    public function index(Request $request) {
        $forms = SupplyOrderForm::select(DB::raw("*,supply_order_forms.id as id"))->join("business_units","business_units.id","business_unit_id")->get();
        $business_units = BusinessUnit::where("status","=","A")->orderBy("name")->get();
        return view('admin-report.supply-order-form.index', compact('forms','business_units'));
    }

    public function store(Request $request){
        if($request->wantsJson()) {
            $validator = Validator::make(request()->all(), [
                'calendars' => 'required|integer',
                'posters' => 'required|integer',
                'stickers' => 'required|integer',
                'two_rolls' => 'required|integer',
                'five_rolls' => 'required|integer',
                'ten_rolls' => 'required|integer',
                'first_name' => 'required',
                'last_name' => 'required',
                'business_unit_id' => 'required',
                'include_name' => 'required',
                'address_type' => 'required',
                'date_required' => 'required|after:today',
            ], [
                'business_unit_id' => 'The Organization Code is required.',
                'deposit_date.before' => 'The deposit date must be the current date or a date before the current date.'
            ]);

            $validator->after(function ($validator) use ($request) {
                $expression = '/^([a-zA-Z]\d[a-zA-Z])\ {0,1}(\d[a-zA-Z]\d)$/';
                if ($request->address_type == "po") {
                    if (empty($request->po)) {
                        $validator->errors()->add('po', 'Enter a Po Box');
                    }
                    if (empty($request->po_city)) {
                        $validator->errors()->add('po_city', 'Enter a City');
                    }
                    if (empty($request->po_postal_code)) {
                        $validator->errors()->add('po_postal_code', 'Enter a Postal Code');
                    }
                    if (empty($request->po_province)) {
                        $validator->errors()->add('po_province', 'Enter a Province');
                    }
                    if (!preg_match($expression, $request->po_postal_code)) {
                        $validator->errors()->add('po_postal_code', 'Invalid Postal Code | Try L1L 1L1');
                    }
                } else {
                    if (empty($request->unit_suite_floor)) {
                        $validator->errors()->add('unit_suite_floor', 'Unit Suite Floor is Required');
                    }
                    if (empty($request->physical_address)) {
                        $validator->errors()->add('physical_address', 'Physical Address is required');
                    }
                    if (empty($request->city)) {
                        $validator->errors()->add('city', 'City is required');
                    }

                    if (empty($request->province)) {
                        $validator->errors()->add('province', 'Province is required');
                    }
                    if (empty($request->postal_code)) {
                        $validator->errors()->add('postal_code', 'Postal Code is required');
                    }
                    if (!preg_match($expression, $request->postal_code)) {
                        $validator->errors()->add('postal_code', 'Invalid Postal Code | Try L1L 1L1');
                    }
                }

            });

            $validator->validate();
            $form = SupplyOrderform::UpdateOrCreate(
                ["id" => $request->supply_order_form_id], [
                    'calendar' => $request->calendars,
                    'posters' => $request->posters,
                    'stickers' => $request->stickers,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'business_unit_id' => $request->business_unit_id,
                    'include_name' => $request->include_name,
                    'unit_suite_floor' => $request->address_type == "po" ? "" : $request->unit_suite_floor,
                    'physical_address' => $request->address_type == "po" ? "" : $request->physical_address,
                    'city' => $request->address_type == "po" ? $request->po_city : $request->city,
                    'province' => $request->address_type == "po" ? $request->po_province : $request->province,
                    'postal_code' => $request->address_type == "po" ? $request->po_postal_code : $request->postal_code,
                    'po_box' => $request->po ? $request->po : "",
                    'comments' => empty($request->comments) ? "" : $request->comments,
                    'address_type' => $request->address_type,
                    'date_required' => $request->date_required
                ]
            );
            return true;
        }
        return false;
    }
}
