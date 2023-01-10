<?php

namespace App\Http\Controllers;

use App\Models\DonorByBusinessUnit;
use App\Models\HistoricalChallengePage;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Department;
use App\Models\Region;
use App\Models\Pledge;
use App\Models\CampaignYear;

use App\Models\BusinessUnit;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Excel;

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
            $date = Carbon::now();
            $year = $date->format("Y");
        }

        if($year == Carbon::now()->format("Y"))
        {
            return redirect()->route('challege.current');
        }

        $years = DonorByBusinessUnit::select(DB::raw('DISTINCT yearcd'))->where("yearcd",">","2017")->orderBy('yearcd',"desc")->get();


        $charities = HistoricalChallengePage::where("year","=",$year);

        if(strlen($request->organization_name) > 0){
            $charities = $charities->where("organization_name","LIKE",$request->organization_name."%");
        }

        $charities = $charities->orderBy((($request->field && $request->field != 'change' && $request->field != 'previous_participation_rate') ? $request->field : "participation_rate"),($request->sort ? $request->sort : "desc"))
            ->limit(500)
            ->get();

        if($request->sort == "ASC"){
            $count = count($charities);
        }
        else{
            $count = 1 ;
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
        $date = gmdate("Y-m-d H:i:s");

        return view('challenge.index', compact('date','charities','year','request','count','years'));
    }

    public function current(Request $request) {
            $date = new Carbon("first day of january " .CampaignYear::where("status","A")->limit(1)->get()[0]->calendar_year);
        $years = DonorByBusinessUnit::select(DB::raw('DISTINCT yearcd'))->where("yearcd",">","2017")->orderBy('yearcd',"desc")->get();
            $year = $date->format("Y");



        $charities = Pledge::select(DB::raw('business_units.status, COUNT(business_units.name) as employee_count, SUM(pledges.goal_amount) as dollars, COUNT(employee_jobs.emplid) as donors, business_units.id,business_units.name, (COUNT(employee_jobs.emplid) / elligible_employees.ee_count) as participation_rate'))
            ->join("users","pledges.user_id","users.id")
            ->join("employee_jobs","employee_jobs.emplid","users.emplid")
            ->join("business_units","business_units.code","=","employee_jobs.business_unit")
            ->join("elligible_employees","elligible_employees.business_unit","business_units.code")
            ->where("elligible_employees.year","=",$year)
            ->where("employee_jobs.empl_rcd","=","select min(empl_rcd) from employee_jobs J2 where J2.emplid = J.emplid and J2.empl_status = 'A' and J2.date_deleted is null")
            ->where('employee_jobs.empl_status',"=","A")
            ->where('pledges.created_at',">",$date->copy()->startOfYear())
            ->where('pledges.created_at',"<",$date->copy()->endOfYear())
            ->where('business_units.status',"=","A")
            ->whereNull('employee_jobs.date_deleted')
            ->havingRaw('participation_rate < ? and employee_count > ?', [101,4])
            ->groupBy('business_units.name')
            ->limit(500)
            ->get();

        $totals = Pledge::select(DB::raw('business_units.status, COUNT(business_units.name) as employee_count, SUM(pledges.goal_amount) as dollars, COUNT(employee_jobs.emplid) as donors'))
            ->join("users","pledges.user_id","users.id")
            ->join("employee_jobs","employee_jobs.emplid","users.emplid")
            ->join("business_units","business_units.id","=","employee_jobs.business_unit_id")
            ->join("elligible_employees","elligible_employees.business_unit","business_units.code")
            ->where("elligible_employees.year","=",$year)
            ->where("employee_jobs.empl_rcd","=","select min(empl_rcd) from employee_jobs J2 where J2.emplid = J.emplid and J2.empl_status = 'A' and J2.date_deleted is null")
            ->where('employee_jobs.empl_status',"=","A")
            ->where('pledges.created_at',">",$date->copy()->startOfYear())
            ->where('pledges.created_at',"<",$date->copy()->endOfYear())
            ->where('business_units.status',"=","A")
            ->whereNull('employee_jobs.date_deleted')
            ->groupBy('business_units.status')
            ->limit(500)
            ->get();

        if($request->sort == "ASC"){
            $count = BusinessUnit::select(DB::raw('business_units.id,business_units.name, donor_by_business_units.donors,donor_by_business_units.dollars,(donor_by_business_units.donors / elligible_employees.ee_count) as participation_rate'))
                ->join("donor_by_business_units","donor_by_business_units.business_unit_id","=","business_units.id")
                ->join("elligible_employees", function($join){
                    // $join->on("business_units.name", '=', 'elligible_employees.business_unit_name')
                    $join->on("business_units.code", '=', 'elligible_employees.business_unit');
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
        $donorsTotal =  0;
        $dollarsTotal =  0;
        foreach($charities as $index => $charity){
            $previousYear = BusinessUnit::select(DB::raw('business_units.id,business_units.name, donor_by_business_units.donors,donor_by_business_units.dollars,(donor_by_business_units.donors / elligible_employees.ee_count) as participation_rate'))
                ->join("donor_by_business_units","donor_by_business_units.business_unit_id","=","business_units.id")
                ->join("elligible_employees", function($join){
                    //$join->on("business_units.name", '=', 'elligible_employees.business_unit_name')
                    $join->on("business_units.code", '=', 'elligible_employees.business_unit');
                })
                ->where('donor_by_business_units.yearcd',"=",($year-1))
                ->where('elligible_employees.as_of_date',">",Carbon::parse("January 1st ".($year-1)))
                ->where('elligible_employees.as_of_date',"<",Carbon::parse("December 31st ".($year-1)))
                ->where('business_units.id',"=",$charity->id)
                ->orderBy((($request->field && $request->field != 'change' && $request->field != 'previous_participation_rate') ? $request->field : "participation_rate"),($request->sort == "asc" ? $request->sort : "desc"))
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

            $donorsTotal +=  $charities[$index]->donors;
            $dollarsTotal +=  $charities[$index]->dollars;
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

        $date = gmdate("Y-m-d H:i:s");

        if(isset($request->excel) && $request->excel){
            return Excel::download($charities, 'daily_campaign_update_'.$request->start_date.'.xlsx');
        }
        else{
            return view('challenge.index', compact('date','totals','charities','year','request','count','years'));
        }
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
            $date = new Carbon($_GET['start_date']);
            $year = $date->format("Y");
            $charities = Pledge::select(DB::raw('business_units.status, organizations.name as org_name, COUNT(business_units.name) as employee_count, SUM(pledges.goal_amount) as dollars, COUNT(employee_jobs.emplid) as donors, business_units.id,business_units.name, (COUNT(employee_jobs.emplid) / elligible_employees.ee_count) as participation_rate'))
                ->join("organizations","pledges.organization_id","organizations.id")
                ->join("users","pledges.user_id","users.id")
                ->join("employee_jobs","employee_jobs.emplid","users.emplid")
                ->join("business_units","business_units.code","=","employee_jobs.business_unit")
                ->join("elligible_employees","elligible_employees.business_unit","business_units.code")
                ->where("elligible_employees.year","=",$year)
                ->where("employee_jobs.empl_rcd","=","select min(empl_rcd) from employee_jobs J2 where J2.emplid = J.emplid and J2.empl_status = 'A' and J2.date_deleted is null")
                ->where('employee_jobs.empl_status',"=","A")
                ->where('pledges.created_at',">",$date->copy()->startOfYear())
                ->where('pledges.created_at',"<",$date->copy()->endOfYear())
                ->where('business_units.status',"=","A")
                ->whereNull('employee_jobs.date_deleted')
                ->havingRaw('participation_rate < ? and employee_count > ?', [101,4])
                ->groupBy('pledges.organization_id')
                ->limit(500)
                ->get();


            $row = ["","Organization Name", "Donors", "Dollars"];

            $file = fopen('test.csv', 'w');
            fputcsv($file, $row);
            foreach ($charities as $index => $charity) {
                fputcsv($file, [($index + 1),$charity->org_name, $charity->donors, "$".number_format($charity->dollars,2)]);
            }
            $date = new Carbon($_GET['start_date']);
            $year = $date->format("Y");
            $totals  = Pledge::select(DB::raw('business_units.status, COUNT(business_units.name) as employee_count, SUM(pledges.goal_amount) as dollars, COUNT(employee_jobs.emplid) as donors, business_units.id,business_units.name, (COUNT(employee_jobs.emplid) / elligible_employees.ee_count) as participation_rate'))
                ->join("organizations","pledges.organization_id","organizations.id")
                ->join("users","pledges.user_id","users.id")
                ->join("employee_jobs","employee_jobs.emplid","users.emplid")
                ->join("business_units","business_units.code","=","employee_jobs.business_unit")
                ->join("elligible_employees","elligible_employees.business_unit","business_units.code")
                ->where("elligible_employees.year","=",$year)
                ->where("employee_jobs.empl_rcd","=","select min(empl_rcd) from employee_jobs J2 where J2.emplid = J.emplid and J2.empl_status = 'A' and J2.date_deleted is null")
                ->where('employee_jobs.empl_status',"=","A")
                ->where('pledges.created_at',">",$date->copy()->startOfYear())
                ->where('pledges.created_at',"<",$date->copy()->endOfYear())
                ->where('business_units.status',"=","A")
                ->whereNull('employee_jobs.date_deleted')
                ->havingRaw('participation_rate < ? ', [101])

                ->groupBy('pledges.organization_id')
                ->limit(500)
                ->get();
            $totalDonors = 0;
            $totalDollars = 0;
            foreach($totals as $line){
                $totalDonors += $line->donors;
                $totalDollars += $line->dollars;
            }
            fputcsv($file,["totals","",number_format($totalDonors,2),number_format($totalDollars,2)]);
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Csv');
            $objPHPExcel = $reader->load("test.csv");
            $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');
            $objWriter->save('By Organization.xlsx');

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename('By Organization.xlsx').'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize('By Region.xlsx'));
            readfile("By Organization.xlsx");

            fclose($file);
        } else if ($request->sort == "region") {
            $fileName = 'Stats By Region.csv';
            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            );
            $date = new Carbon($_GET['start_date']);
            $year = $date->format("Y");
            $charities = Region::join("donor_by_regional_districts","regions.id","donor_by_regional_districts.regional_district_id")
                ->where('yearcd',"=",$year)
                ->limit(500)
                ->get();

            $row = ["","Regional District Name", "Donors", "Dollars"];

                $file = fopen('test.csv', 'w');
                fputcsv($file, $row);
                foreach ($charities as $index => $charity) {
                    fputcsv($file, [($index + 1),$charity->name, $charity->donors, "$".number_format($charity->dollars,2)]);
                }
                $date = new Carbon($_GET['start_date']);
                $year = $date->format("Y");

                $totalDonors = 0;
                $totalDollars = 0;
                foreach($charities as $line){
                    $totalDonors += $line->donors;
                    $totalDollars += $line->dollars;
                }
                fputcsv($file,["totals","",$totalDonors,$totalDollars]);
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Csv');
            $objPHPExcel = $reader->load("test.csv");
            $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');
            $objWriter->save('By Region.xlsx');

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename('By Region.xlsx').'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize('By Region.xlsx'));
            readfile("By Region.xlsx");

                fclose($file);

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
    $date = new Carbon($_GET['start_date']);
    $year = $date->format("Y");
    $charities = Pledge::select(DB::raw('business_units.status, departments.department_name, departments.bi_department_id, COUNT(business_units.name) as employee_count, SUM(pledges.goal_amount) as dollars, COUNT(employee_jobs.emplid) as donors, business_units.id,business_units.name, (COUNT(employee_jobs.emplid) / elligible_employees.ee_count) as participation_rate'))
        ->join("users","pledges.user_id","users.id")
        ->join("employee_jobs","employee_jobs.emplid","users.emplid")
        ->join("business_units","business_units.code","=","employee_jobs.business_unit")
        ->join("departments","employee_jobs.deptid","departments.bi_department_id")
        ->join("elligible_employees","elligible_employees.business_unit","business_units.code")
        ->where("elligible_employees.year","=",$year)
        ->where("employee_jobs.empl_rcd","=","select min(empl_rcd) from employee_jobs J2 where J2.emplid = J.emplid and J2.empl_status = 'A' and J2.date_deleted is null")
        ->where('employee_jobs.empl_status',"=","A")
        ->where('pledges.created_at',">",$date->copy()->startOfYear())
        ->where('pledges.created_at',"<",$date->copy()->endOfYear())
        ->where('business_units.status',"=","A")
        ->whereNull('employee_jobs.date_deleted')
        ->havingRaw('participation_rate < ? and employee_count > ?', [101,4])
        ->groupBy('employee_jobs.deptid')
        ->limit(500)
        ->get();


    $row = ["","Department Name", "Department Id", "Donors", "Dollars"];

    $file = fopen('test.csv', 'w');
    fputcsv($file, $row);
    foreach ($charities as $index => $charity) {
        fputcsv($file, [($index + 1),$charity->department_name,$charity->bi_department_id, $charity->donors, "$".number_format($charity->dollars,2)]);
    }
    $date = new Carbon($_GET['start_date']);
    $year = $date->format("Y");
    $totals  = Pledge::select(DB::raw('business_units.status, COUNT(business_units.name) as employee_count, SUM(pledges.goal_amount) as dollars, COUNT(employee_jobs.emplid) as donors, business_units.id,business_units.name, (COUNT(employee_jobs.emplid) / elligible_employees.ee_count) as participation_rate'))
        ->join("users","pledges.user_id","users.id")
        ->join("employee_jobs","employee_jobs.emplid","users.emplid")
        ->join("business_units","business_units.code","=","employee_jobs.business_unit")
        ->join("elligible_employees","elligible_employees.business_unit","business_units.code")
        ->join("departments","employee_jobs.deptid","departments.bi_department_id")
        ->where("elligible_employees.year","=",$year)
        ->where("employee_jobs.empl_rcd","=","select min(empl_rcd) from employee_jobs J2 where J2.emplid = J.emplid and J2.empl_status = 'A' and J2.date_deleted is null")
        ->where('employee_jobs.empl_status',"=","A")
        ->where('pledges.created_at',">",$date->copy()->startOfYear())
        ->where('pledges.created_at',"<",$date->copy()->endOfYear())
        ->where('business_units.status',"=","A")
        ->whereNull('employee_jobs.date_deleted')
        ->groupBy('employee_jobs.deptid')
        ->limit(500)
        ->get();
    $totalDonors = 0;
    $totalDollars = 0;
    foreach($totals as $line){
        $totalDonors += $line->donors;
        $totalDollars += $line->dollars;
    }
    fputcsv($file,["totals","","",$totalDonors,$totalDollars]);
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Csv');
    $objPHPExcel = $reader->load("test.csv");
    $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');
    $objWriter->save('By Department.xlsx');

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename('By Department.xlsx').'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize('By Department.xlsx'));
    readfile("By Department.xlsx");

    fclose($file);
}
       // return response()->stream($callback, 200, $headers);
    }




}
