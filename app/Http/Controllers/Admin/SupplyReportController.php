<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessUnit;
use App\Models\SupplyOrderForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SupplyReportController extends Controller
{
    public function index(Request $request) {
        $forms = SupplyOrderForm::select(DB::raw("*,supply_order_forms.id as id"))->join("business_units","business_units.id","business_unit_id");
        if(strlen($request->employee_name) > 2){
            $forms = $forms->where("first_name","LIKE",$request->employee_name."%");
            $forms = $forms->orWhere("last_name","LIKE",$request->employee_name."%");
        }
        if(strlen($request->organization_code) > 2){
            $forms = $forms->where("business_units.name","LIKE",$request->organization_code."%");
        }
        if(!empty($request->month) && !empty($request->year)){
            $forms = $forms->where("supply_order_forms.created_at",">=",Carbon::parse($request->month." ".$request->year));
            $forms = $forms->where("supply_order_forms.created_at","<",Carbon::parse($request->month." ".($request->year+1)));

        }

        $months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
        $years = [2023,2022,2021,2020,2019,2018];

        $forms = $forms->get();
        $business_units = BusinessUnit::where("status","=","A")->orderBy("name")->get();
        return view('admin-report.supply-order-form.index', compact('forms','business_units','request','months','years'));
    }

    public function delete(Request $request)
    {
        SupplyOrderForm::whereIn("id",explode("-",$_GET['supply_order_form_selection']))->delete();
        return redirect("/reporting/supply-report");
    }

    public function export(Request $request)
    {
        $fileName = 'supply-report-details.csv';
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . $fileName . "\"");

        $tasks = SupplyOrderForm::whereIn("id",explode("-",$_GET['supply_order_form_selection']))->get();

            $file = fopen($fileName, 'w');
            foreach ($tasks as $task) {
                fputcsv($file, json_decode(json_encode($task),true));
            }

             readfile($fileName);
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
