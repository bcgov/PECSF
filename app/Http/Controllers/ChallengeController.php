<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Department;
use App\Models\Region;
use App\Models\BusinessUnit;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChallengeController extends Controller
{
    public function index(Request $request) {

        if(isset($request->year)){
            $year = $request->year;
        }
        else{
            $date = Carbon::now()->subYear();
            $year = $date->format("Y");
        }



        $charities = BusinessUnit::select(DB::raw('business_units.id,business_units.name, donor_by_business_units.donors,donor_by_business_units.dollars,(donor_by_business_units.donors / count(employee_jobs.business_unit_id)) as participation_rate'))
->join("donor_by_business_units","donor_by_business_units.business_unit_id","=","business_units.id")
            ->join("employee_jobs","employee_jobs.business_unit_id","=","business_units.id")
            ->where('donor_by_business_units.yearcd',"=",$year)
            ->where('employee_jobs.effdt',">",Carbon::parse("January 1st ".$year))
            ->where('employee_jobs.effdt',"<",Carbon::parse("December 31st ".$year))
            ->groupBy("employee_jobs.business_unit_id")
            ->orderBy("participation_rate","desc")
        ->paginate(10);

        foreach($charities as $index => $charity){
            $previousYear = BusinessUnit::select(DB::raw('business_units.name, donor_by_business_units.donors,donor_by_business_units.dollars,(donor_by_business_units.donors / count(employee_jobs.business_unit_id)) as participation_rate'))
                ->join("donor_by_business_units","donor_by_business_units.business_unit_id","=","business_units.id")
                ->join("employee_jobs","employee_jobs.business_unit_id","=","business_units.id")
                ->where('donor_by_business_units.yearcd',"=",($year-1))
                ->where('employee_jobs.effdt',">",Carbon::parse("January 1st ".($year-1)))
                ->where('employee_jobs.effdt',"<",Carbon::parse("December 31st ".($year-1)))
                ->where('business_units.id',"=",$charity->id)
                ->groupBy("employee_jobs.business_unit_id")
                ->orderBy("participation_rate","desc")
               ->first();

            if(!empty($previousYear))
            {
                $charities[$index]->previous_participation_rate = $previousYear->participation_rate;
                $charities[$index]->previous_donors = $previousYear->donors;
                $charities[$index]->change = ($charity->participation_rate*100) - $previousYear->participation_rate;
            }
            else
            {
                $charities[$index]->previous_participation_rate = "No Data";
                $charities[$index]->previous_donors = "No Data";
                $charities[$index]->change = "No Data";
            }

        }

        return view('challenge.index', compact('charities','year'));
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function download(Request $request)
    {


        if ($request->sort == "organization") {
            $fileName = 'Stats By Organization.csv';
            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            );
            $charities = BusinessUnit::select(DB::raw('business_units.id,business_units.name, donor_by_business_units.donors,donor_by_business_units.dollars'))
                ->join("donor_by_business_units", "donor_by_business_units.business_unit_id", "=", "business_units.id")
                ->where('donor_by_business_units.yearcd', "=", $request->start_date)
                ->get();

            $row = ["Organization Name", "Donors", "Dollars"];

            $callback = function () use ($charities, $row) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $row);
                foreach ($charities as $charity) {
                    fputcsv($file, [$charity->name, $charity->donors, $charity->dollars]);
                }
                fclose($file);
            };
        } else if ($request->sort == "region") {
            $fileName = 'Stats By Region.csv';
            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            );
            $charities = Region::select(DB::raw('regions.id,regions.name, donor_by_regional_districts.donors,donor_by_regional_districts.dollars'))
                ->join("donor_by_regional_districts", "donor_by_regional_districts.regional_district_id", "=", "regions.id")
                ->where('donor_by_regional_districts.yearcd', "=", $request->start_date)
                ->get();

            $row = ["Organization Name", "Donors", "Dollars"];

            $callback = function () use ($charities, $row) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $row);
                foreach ($charities as $charity) {
                    fputcsv($file, [$charity->name, $charity->donors, $charity->dollars]);
                }
                fclose($file);
            };
        }
else if($request->sort == "department"){
    $fileName = 'Stats By Department.csv';
    $headers = array(
        "Content-type" => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0"
    );
    $charities = Department::select(DB::raw('departments.id,departments.bi_department_id,departments.business_unit_name,departments.department_name, donor_by_departments.donors'))
        ->join("donor_by_departments", "donor_by_departments.department_id", "=", "departments.id")
        ->where('donor_by_departments.yearcd', "=", $request->start_date)
        ->get();

    $row = ["Organization Name", "Dept ID", "Department Name","Donors"];

    $callback = function () use ($charities, $row) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $row);
        foreach ($charities as $charity) {
            fputcsv($file, [$charity->business_unit_name, $charity->bi_department_id, $charity->department_name,$charity->donors]);
        }
        fclose($file);
    };
}
        return response()->stream($callback, 200, $headers);
    }


}
