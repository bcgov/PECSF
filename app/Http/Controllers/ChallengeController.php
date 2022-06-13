<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Department;
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



        $charities = Department::select(DB::raw('departments.id,departments.department_name,donor_by_departments.donors,donor_by_departments.dollars,(donor_by_business_units.donors / count(employee_jobs.business_unit_id)) as participation_rate'))
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
}
