<?php

namespace App\Http\Controllers;

use App\Models\BusinessUnit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
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
            ->orderBy("participation_rate",($request->sort ? $request->sort : "desc"))
            ->limit(5)
            ->get();

        if($request->sort == "ASC"){
            $count = BusinessUnit::select(DB::raw('business_units.id,business_units.name, donor_by_business_units.donors,donor_by_business_units.dollars,(donor_by_business_units.donors / count(employee_jobs.business_unit_id)) as participation_rate'))
                ->join("donor_by_business_units","donor_by_business_units.business_unit_id","=","business_units.id")
                ->join("employee_jobs","employee_jobs.business_unit_id","=","business_units.id")
                ->where('donor_by_business_units.yearcd',"=",$year)
                ->where('employee_jobs.effdt',">",Carbon::parse("January 1st ".$year))
                ->where('employee_jobs.effdt',"<",Carbon::parse("December 31st ".$year))
                ->groupBy("employee_jobs.business_unit_id")
                ->count();
        }
        else{
            $count = 1 ;
        }

        foreach($charities as $index => $charity){
            $previousYear = BusinessUnit::select(DB::raw('business_units.name, donor_by_business_units.donors,donor_by_business_units.dollars,(donor_by_business_units.donors / count(employee_jobs.business_unit_id)) as participation_rate'))
                ->join("donor_by_business_units","donor_by_business_units.business_unit_id","=","business_units.id")
                ->join("employee_jobs","employee_jobs.business_unit_id","=","business_units.id")
                ->where('donor_by_business_units.yearcd',"=",($year-1))
                ->where('employee_jobs.effdt',">",Carbon::parse("January 1st ".($year-1)))
                ->where('employee_jobs.effdt',"<",Carbon::parse("December 31st ".($year-1)))
                ->where('business_units.id',"=",$charity->id)
                ->groupBy("employee_jobs.business_unit_id")
                ->orderBy("participation_rate",($request->sort ? $request->sort : "desc"))
                ->first();

            if(!empty($previousYear))
            {
                $charities[$index]->previous_participation_rate = $previousYear->participation_rate;
                $charities[$index]->previous_donors = $previousYear->donors;
                $charities[$index]->change = ($charity->participation_rate*100) - $previousYear->participation_rate;
            }
            else
            {
                $charities[$index]->previous_participation_rate = 0;
                $charities[$index]->previous_donors = 0;
                $charities[$index]->change = 0;
            }
        }

        return view('home' , compact('charities'));
    }
}
