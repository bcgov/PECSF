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
use Illuminate\Support\Facades\Auth;

class ChallengeController extends Controller
{

    public function __construct()
    {
         if(empty(Auth::id())){
             redirect("/login");
         }
    }

    public function index(Request $request) {

        if(isset($request->year)){
            $year = $request->year;
        }
        else{
            $date = Carbon::now()->subYear();
            $year = $date->format("Y");
        }

        $charities = BusinessUnit::select(DB::raw('business_units.id,business_units.name, donor_by_business_units.donors,donor_by_business_units.dollars,(donor_by_business_units.donors / elligible_employees.ee_count) as participation_rate'))
->join("donor_by_business_units","donor_by_business_units.business_unit_id","=","business_units.id")
            ->join("elligible_employees", function($join){
                $join->on("business_units.name", '=', 'elligible_employees.business_unit_name')
                    ->on("business_units.code", '=', 'elligible_employees.business_unit');
            })

            ->where('donor_by_business_units.yearcd',"=",$year)
            ->where('elligible_employees.as_of_date',">",Carbon::parse("January 1st ".$year))
            ->where('elligible_employees.as_of_date',"<",Carbon::parse("December 31st ".$year));

        if(strlen($request->organization_name) > 0){
            $charities = $charities->where("business_units.name","LIKE",$request->organization_name."%");
        }

        $charities = $charities->orderBy((($request->field && $request->field != 'change' && $request->field != 'previous_participation_rate') ? $request->field : "participation_rate"),($request->sort ? $request->sort : "desc"))
            ->limit(500)
            ->get();

        if($request->sort == "ASC"){
            $count = BusinessUnit::select(DB::raw('business_units.id,business_units.name, donor_by_business_units.donors,donor_by_business_units.dollars,(donor_by_business_units.donors / elligible_employees.ee_count) as participation_rate'))
                ->join("donor_by_business_units","donor_by_business_units.business_unit_id","=","business_units.id")
                ->join("elligible_employees", function($join){
                    $join->on("business_units.name", '=', 'elligible_employees.business_unit_name')
                        ->on("business_units.code", '=', 'elligible_employees.business_unit');
                })
                ->where('donor_by_business_units.yearcd',"=",$year)
                ->where('elligible_employees.as_of_date',">",Carbon::parse("January 1st ".$year))
                ->where('elligible_employees.as_of_date',"<",Carbon::parse("December 31st ".$year));

            if(strlen($request->organization_name) > 0){
                $count = $count->where("business_units.name","LIKE",$request->organization_name."%");
            }
                $count = $count->orderBy((($request->field && $request->field != 'change' && $request->field != 'previous_participation_rate') ? $request->field : "participation_rate"),($request->sort ? $request->sort : "desc"))
                    ->count();
        }
        else{
            $count = 1 ;
        }

        foreach($charities as $index => $charity){
            $previousYear = BusinessUnit::select(DB::raw('business_units.id,business_units.name, donor_by_business_units.donors,donor_by_business_units.dollars,(donor_by_business_units.donors / elligible_employees.ee_count) as participation_rate'))
                ->join("donor_by_business_units","donor_by_business_units.business_unit_id","=","business_units.id")
                ->join("elligible_employees", function($join){
                    $join->on("business_units.name", '=', 'elligible_employees.business_unit_name')
                        ->on("business_units.code", '=', 'elligible_employees.business_unit');
                })
                ->where('donor_by_business_units.yearcd',"=",($year-1))
                ->where('elligible_employees.as_of_date',">",Carbon::parse("January 1st ".($year-1)))
                ->where('elligible_employees.as_of_date',"<",Carbon::parse("December 31st ".($year-1)))
                ->where('business_units.id',"=",$charity->id)
                ->orderBy((($request->field && $request->field != 'change' && $request->field != 'previous_participation_rate') ? $request->field : "participation_rate"),($request->sort ? $request->sort : "desc"))
                ->first();

            if(!empty($previousYear))
            {
                $charities[$index]->previous_participation_rate = $previousYear->participation_rate;
                $charities[$index]->previous_donors = $previousYear->donors;
                $charities[$index]->change = ($charity->participation_rate*100) - ($previousYear->participation_rate*100);
            }
            else
            {
                $charities[$index]->previous_participation_rate = 0;
                $charities[$index]->previous_donors = 0;
                $charities[$index]->change = 0;
            }
        }

        if($request->field == "change")
        {
            if($request->sort == "ASC")
            {
                $charities = $charities->sortBy("change");
            }
            else
            {
                $charities = $charities->sortByDesc("change");
            }
        }

        if($request->field == "previous_participation_rate")
        {
            if($request->sort == "ASC")
            {
                $charities = $charities->sortBy("previous_participation_rate");
            }
            else
            {
                $charities = $charities->sortByDesc("previous_participation_rate");
            }
        }

        return view('challenge.index', compact('charities','year','request','count'));
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

    public function preview(Request $request)
    {
        $dollarTotal = 0;
        $donorTotal = 0;
        if($request->sort == "region"){
            $charities = Region::report($request)->get();
            $row = ["Organization Name", "Donors", "Dollars"];
            $rows[] = $row;


                foreach ($charities as $charity) {
                    $donorTotal = $donorTotal + $charity->donors;
                    $dollarTotal = $dollarTotal + $charity->dollars;
                   $rows[]=[$charity->name, $charity->donors, "$".number_format($charity->dollars,2)];
                }
        }
        else if($request->sort == "department"){
            $charities = Department::report($request)->get();
            $row = ["Organization Name", "Dept ID", "Department Name","Donors"];
            $rows[] = $row;
                foreach ($charities as $charity) {
                    $donorTotal = $donorTotal + $charity->donors;

                    $rows[] = [$charity->business_unit_name, $charity->bi_department_id, $charity->department_name,$charity->donors];
                }
        }
        else{
            $charities = BusinessUnit::report($request)->get();
            $row = ["Organization Name", "Donors", "Dollars"];
            $rows[] = $row;

            foreach ($charities as $charity) {
                $donorTotal = $donorTotal + $charity->donors;
                $dollarTotal = $dollarTotal + $charity->dollars;
               $rows[] = [$charity->name, $charity->donors,"$".number_format($charity->dollars,2)] ;
            }
        }

        return view('challenge.preview', compact('rows','request','donorTotal','dollarTotal'));


    }

    public function daily_campaign(Request $request){
        return view('challenge.daily_campaign');
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
            $charities = BusinessUnit::report($request)->get();

            $row = ["Organization Name", "Donors", "Dollars"];

            $callback = function () use ($charities, $row) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $row);
                foreach ($charities as $charity) {
                    fputcsv($file, [$charity->name, $charity->donors, "$".number_format($charity->dollars,2)]);
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
            $charities = Region::report($request)->get();

            $row = ["Organization Name", "Donors", "Dollars"];

            $callback = function () use ($charities, $row) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $row);
                foreach ($charities as $charity) {
                    fputcsv($file, [$charity->name, $charity->donors, "$".number_format($charity->dollars,2)]);
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
    $charities = Department::report($request)->get();

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
