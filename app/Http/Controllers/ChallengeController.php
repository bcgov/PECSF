<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\CampaignYear;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\DailyCampaign;
use App\Models\DailyCampaignView;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\HistoricalChallengePage;
use Illuminate\Support\Facades\Storage;
use App\Exports\DailyCampaignByBUExport;
use App\Exports\DailyCampaignByDeptExport;
use App\Exports\DailyCampaignByRegionExport;
use Yajra\Datatables\Datatables;

class ChallengeController extends Controller
{

    public function __construct()
    {
         if(empty(Auth::id())){
             redirect("/login");
         }
    }

    public function index(Request $request) {

        $campaign_year = $request->year ? $request->year : today()->year;

        $history = HistoricalChallengePage::select('year')
                                ->where('year', '<', $campaign_year)
                                ->orderBy('year', 'desc')
                                ->first(); 

        $prior_year = $history ? $history->year : $campaign_year - 1;                                

        if($request->ajax()) {

            $is_current = $campaign_year == today()->year;

            if ($is_current) {

                $parameters = [
                    $campaign_year,
                    $campaign_year,
                    $prior_year,
                    $campaign_year,
                    $prior_year,
                    $campaign_year,
                    $campaign_year,
                ];

                $sql = <<<SQL
                    select 1 as current, business_unit_code, organization_name,
                            -- 0 as participation_rate, 
                            case when (select ee_count from eligible_employee_by_bus where eligible_employee_by_bus.campaign_year = ?
                                and eligible_employee_by_bus.organization_code = 'GOV' 
                                and eligible_employee_by_bus.business_unit_code = A.business_unit_code
                                ) > 0 then 
                                    A.donors / (select ee_count from eligible_employee_by_bus where eligible_employee_by_bus.campaign_year = ?
                                        and eligible_employee_by_bus.organization_code = 'GOV' 
                                        and eligible_employee_by_bus.business_unit_code = A.business_unit_code
                                    ) * 100 
                                else 0 end as participation_rate,
                            -- 0 as previous_participation_rate, 
                            (select participation_rate from historical_challenge_pages where year = ? 
                                and historical_challenge_pages.organization_name = A.organization_name
                            ) as previous_participation_rate,
                            (A.donors / (select ee_count from eligible_employee_by_bus where eligible_employee_by_bus.campaign_year = ?
                                and eligible_employee_by_bus.organization_code = 'GOV' 
                                and eligible_employee_by_bus.business_unit_code = A.business_unit_code
                            ) * 100) - COALESCE((select participation_rate from historical_challenge_pages where year = ? 
                                and historical_challenge_pages.organization_name = A.organization_name
                                ),0)
                            as 'change_rate', 
                            A.donors, A.dollars, (@row_number:=@row_number + 1) AS rank
                            ,(select ee_count from eligible_employee_by_bus where eligible_employee_by_bus.campaign_year = ?
                                and eligible_employee_by_bus.organization_code = 'GOV' 
                                and eligible_employee_by_bus.business_unit_code = A.business_unit_code
                            ) as ee_count
                     from 
                        (select business_unit_code, name as organization_name, sum(donors) as donors, sum(dollars) as dollars 
                        from daily_campaign_view  
                        left outer join business_units on business_units.code = business_unit_code
                        where campaign_year = ?
                        group by business_unit_code
                        order by sum(donors) desc) 
                        as A, (SELECT @row_number:=0) AS temp
                    where donors >= 5
                    order by donors desc; 
                SQL;


                $challenges = DB::select($sql, $parameters);

            } else {

                // SELECT (@row_number:=@row_number + 1) AS row_num, Name, Country, Year  
                // FROM Person, (SELECT @row_number:=0) AS temp ORDER BY Year;  
                $parameters = [
                    $campaign_year,
                ];

                $sql = <<<SQL
                    select 0 as current, organization_name, participation_rate, previous_participation_rate, `change` as change_rate, 
                                donors, dollars, (@row_number:=@row_number + 1) AS rank,
                                0 as ee_count
                      from historical_challenge_pages, (SELECT @row_number:=0) AS temp
                     where year = ?                      
                       and donors >= 5
                     order by donors desc;     
                SQL;
                
                $challenges = DB::select($sql, $parameters);

            }

            if ($request->organization_name) {
                $challenges = array_filter($challenges, function($v, $k) use($request) {
                    return str_contains($v->organization_name, $request->organization_name);
                }, ARRAY_FILTER_USE_BOTH);
            }

            return Datatables::of($challenges)
                    // ->addColumn('current_', function ($special_campaign) {
                    //     return '<button class="btn btn-info btn-sm  show-bu" data-id="'. $special_campaign->id .'" >Show</button>' .
                    //         '<button class="btn btn-primary btn-sm ml-2 edit-bu" data-id="'. $special_campaign->id .'" >Edit</button>' .
                    //         '<button class="btn btn-danger btn-sm ml-2 delete-bu" data-id="'. $special_campaign->id .
                    //         '" data-name="'. $special_campaign->name . '">Delete</button>';
                    // })
            // ->rawColumns(['action'])
                    ->make(true);
        }

        $year_options = HistoricalChallengePage::select('year')->distinct()->orderBy('year', 'desc')->pluck('year')->toArray();
        array_unshift($year_options , strval( today()->year ) );

        $year = today()->year;

        return view('challenge.index', compact('year_options', 'year'));
    }


    // public function downloadFile(Request $request, $id) {

    //     $history = BankDepositFormAttachments::where('id', $id)->first();
    //     // $path = Student::where("id", $id)->value("file_path");



    //    header('Content-Type: application/octet-stream');
    //     header("Content-Transfer-Encoding: Binary");
    //     header("Content-disposition: attachment; filename=\"" . basename($history->local_path) . "\"");
    //     readfile($history->local_path);
    // }


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // public function paginate($items, $perPage = 5, $page = null, $options = [])
    // {
    //     $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
    //     $items = $items instanceof Collection ? $items : Collection::make($items);
    //     return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    // }

    // public function preview(Request $request)
    // {
    //     $dollarTotal = 0;
    //     $donorTotal = 0;
    //     if($request->sort == "region"){
    //         $charities = Region::report($request)->get();
    //         $row = ["Organization Name", "Donors", "Dollars"];
    //         $rows[] = $row;


    //             foreach ($charities as $charity) {
    //                 $donorTotal = $donorTotal + $charity->donors;
    //                 $dollarTotal = $dollarTotal + $charity->dollars;
    //                $rows[]=[$charity->name, $charity->donors, "$".number_format($charity->dollars,2)];
    //             }
    //     }
    //     else if($request->sort == "department"){
    //         $charities = Department::report($request)->get();
    //         $row = ["Organization Name", "Dept ID", "Department Name","Donors"];
    //         $rows[] = $row;
    //             foreach ($charities as $charity) {
    //                 $donorTotal = $donorTotal + $charity->donors;

    //                 $rows[] = [$charity->business_unit_name, $charity->bi_department_id, $charity->department_name,$charity->donors];
    //             }
    //     }
    //     else{
    //         $charities = BusinessUnit::report($request)->get();
    //         $row = ["Organization Name", "Donors", "Dollars"];
    //         $rows[] = $row;

    //         foreach ($charities as $charity) {
    //             $donorTotal = $donorTotal + $charity->donors;
    //             $dollarTotal = $dollarTotal + $charity->dollars;
    //            $rows[] = [$charity->name, $charity->donors,"$".number_format($charity->dollars,2)] ;
    //         }
    //     }

    //     return view('challenge.preview', compact('rows','request','donorTotal','dollarTotal'));


    // }

    public function daily_campaign(Request $request){

        $campaign_year = today()->year;

        $date_options = \App\Models\DailyCampaign::select('as_of_date')
                        ->crossJoin('settings')
                        ->where('campaign_year', $campaign_year)
                        ->whereBetweenColumns('as_of_date',['settings.campaign_start_date', 'settings.campaign_end_date'])
                        // ->where('as_of_date', '<>', today() )
                        ->distinct()
                        ->orderBy('as_of_date', 'desc')
                        ->pluck('as_of_date');

        return view('challenge.daily_campaign', compact('date_options'));
    }

    public function download(Request $request)
    {
        
        $campaign_year = today()->year;

        $as_of_date = DailyCampaign::where('campaign_year', $campaign_year)    
                            ->where('as_of_date', '<=', today() ) 
                            ->max('as_of_date');
        $as_of_date = $request->start_date ? $request->start_date : $as_of_date;

        switch ($request->sort) {
            case 'region': 
                return \Maatwebsite\Excel\Facades\Excel::download(new DailyCampaignByRegionExport($campaign_year, $as_of_date),
                         'daily_campaign_update_region_'. $as_of_date .'.xlsx');
                break;
            case 'organization':
                return \Maatwebsite\Excel\Facades\Excel::download(new DailyCampaignByBUExport($campaign_year, $as_of_date),
                         'daily_campaign_update_by_org_'. $as_of_date .'.xlsx');
                break;
            case 'department':
                return \Maatwebsite\Excel\Facades\Excel::download(new DailyCampaignByDeptExport($campaign_year, $as_of_date),
                         'daily_campaign_update_by_dept_'. $as_of_date .'.xlsx');
                break;
        } 

    }

// dd('test');
     
//         if ($request->sort == "organization") {
//             $fileName = 'Stats By Organization.csv';
//             $headers = array(
//                 "Content-type" => "text/csv",
//                 "Content-Disposition" => "attachment; filename=$fileName",
//                 "Pragma" => "no-cache",
//                 "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
//                 "Expires" => "0"
//             );
//             $date = new Carbon($_GET['start_date']);
//             $year = $date->format("Y");
//             $charities = Pledge::select(DB::raw('business_units.status, organizations.name as org_name, COUNT(business_units.name) as employee_count, SUM(pledges.goal_amount) as dollars, COUNT(employee_jobs.emplid) as donors, business_units.id,business_units.name, (COUNT(employee_jobs.emplid) / elligible_employees.ee_count) as participation_rate'))
//                 ->join("organizations","pledges.organization_id","organizations.id")
//                 ->join("users","pledges.user_id","users.id")
//                 ->join("employee_jobs","employee_jobs.emplid","users.emplid")
//                 ->join("business_units","business_units.code","=","employee_jobs.business_unit")
//                 ->join("elligible_employees","elligible_employees.business_unit","business_units.code")
//                 ->where("elligible_employees.year","=",$year)
//                 ->where("employee_jobs.empl_rcd","=","select min(empl_rcd) from employee_jobs J2 where J2.emplid = J.emplid and J2.empl_status = 'A' and J2.date_deleted is null")
//                 ->where('employee_jobs.empl_status',"=","A")
//                 ->where('pledges.created_at',">",$date->copy()->startOfYear())
//                 ->where('pledges.created_at',"<",$date->copy())
//                 ->where('business_units.status',"=","A")
//                 ->whereNull('employee_jobs.date_deleted')
//                 ->havingRaw('participation_rate < ? and employee_count > ?', [101,4])
//                 ->groupBy('pledges.organization_id')
//                 ->limit(500)
//                 ->get();


//             $row = ["","Organization Name", "Donors", "Dollars"];

//             $file = fopen('test.csv', 'w');
//             fputcsv($file, $row);
//             foreach ($charities as $index => $charity) {
//                 fputcsv($file, [($index + 1),$charity->org_name, $charity->donors, "$".number_format($charity->dollars,2)]);
//             }
//             $date = new Carbon($_GET['start_date']);
//             $year = $date->format("Y");
//             $totals  = Pledge::select(DB::raw('business_units.status, COUNT(business_units.name) as employee_count, SUM(pledges.goal_amount) as dollars, COUNT(employee_jobs.emplid) as donors, business_units.id,business_units.name, (COUNT(employee_jobs.emplid) / elligible_employees.ee_count) as participation_rate'))
//                 ->join("organizations","pledges.organization_id","organizations.id")
//                 ->join("users","pledges.user_id","users.id")
//                 ->join("employee_jobs","employee_jobs.emplid","users.emplid")
//                 ->join("business_units","business_units.code","=","employee_jobs.business_unit")
//                 ->join("elligible_employees","elligible_employees.business_unit","business_units.code")
//                 ->where("elligible_employees.year","=",$year)
//                 ->where("employee_jobs.empl_rcd","=","select min(empl_rcd) from employee_jobs J2 where J2.emplid = J.emplid and J2.empl_status = 'A' and J2.date_deleted is null")
//                 ->where('employee_jobs.empl_status',"=","A")
//                 ->where('pledges.created_at',">",$date->copy()->startOfYear())
//                 ->where('pledges.created_at',"<",$date->copy())
//                 ->where('business_units.status',"=","A")
//                 ->whereNull('employee_jobs.date_deleted')
//                 ->havingRaw('participation_rate < ? ', [101])
//                 ->groupBy('pledges.organization_id')
//                 ->limit(500)
//                 ->get();
//             $totalDonors = 0;
//             $totalDollars = 0;
//             foreach($totals as $line){
//                 $totalDonors += $line->donors;
//                 $totalDollars += $line->dollars;
//             }
//             fputcsv($file,["totals","",number_format($totalDonors,2),"$".number_format($totalDollars,2)]);
//             $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Csv');
//             $objPHPExcel = $reader->load("test.csv");
//             $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');
//             $objWriter->save('By Organization.xlsx');

//             header('Content-Description: File Transfer');
//             header('Content-Type: application/octet-stream');
//             header('Content-Disposition: attachment; filename="'.basename('By Organization.xlsx').'"');
//             header('Expires: 0');
//             header('Cache-Control: must-revalidate');
//             header('Pragma: public');
//             header('Content-Length: ' . filesize('By Region.xlsx'));
//             readfile("By Organization.xlsx");

//             fclose($file);
//         } else if ($request->sort == "region") {
//             $fileName = 'Stats By Region.csv';
//             $headers = array(
//                 "Content-type" => "text/csv",
//                 "Content-Disposition" => "attachment; filename=$fileName",
//                 "Pragma" => "no-cache",
//                 "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
//                 "Expires" => "0"
//             );
//             $date = new Carbon($_GET['start_date']);
//             $year = $date->format("Y");
//             $charities = Pledge::select(DB::raw('business_units.status,regions.name as name, departments.department_name, departments.bi_department_id, COUNT(business_units.name) as employee_count, SUM(pledges.goal_amount) as dollars, COUNT(employee_jobs.emplid) as donors, business_units.id, (COUNT(employee_jobs.emplid) / elligible_employees.ee_count) as participation_rate'))
//                 ->join("users","pledges.user_id","users.id")
//                 ->join("employee_jobs","employee_jobs.emplid","users.emplid")
//                 ->join("regions","employee_jobs.region_id","regions.id")
//                 ->join("business_units","business_units.code","=","employee_jobs.business_unit")
//                 ->join("departments","employee_jobs.deptid","departments.bi_department_id")
//                 ->join("elligible_employees","elligible_employees.business_unit","business_units.code")
//                 ->where("elligible_employees.year","=",$year)
//                 ->where("employee_jobs.empl_rcd","=","select min(empl_rcd) from employee_jobs J2 where J2.emplid = J.emplid and J2.empl_status = 'A' and J2.date_deleted is null")
//                 ->where('employee_jobs.empl_status',"=","A")
//                 ->where('pledges.created_at',">",$date->copy()->startOfYear())
//                 ->where('pledges.created_at',"<",$date->copy())
//                 ->where('business_units.status',"=","A")
//                 ->whereNull('employee_jobs.date_deleted')
//                 ->havingRaw('participation_rate < ? and employee_count > ?', [101,4])
//                 ->groupBy('employee_jobs.region_id')
//                 ->limit(500)
//                 ->get();

//             $row = ["","Regional District Name", "Donors", "Dollars"];

//                 $file = fopen('test.csv', 'w');
//                 fputcsv($file, $row);
//                 foreach ($charities as $index => $charity) {
//                     fputcsv($file, [($index + 1),$charity->name, $charity->donors, "$".number_format($charity->dollars,2)]);
//                 }
//                 $date = new Carbon($_GET['start_date']);
//                 $year = $date->format("Y");

//                 $totalDonors = 0;
//                 $totalDollars = 0;
//                 foreach($charities as $line){
//                     $totalDonors += $line->donors;
//                     $totalDollars += $line->dollars;
//                 }
//                 fputcsv($file,["totals","",$totalDonors,"$".number_format($totalDollars)]);
//             $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Csv');
//             $objPHPExcel = $reader->load("test.csv");
//             $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');
//             $objWriter->save('By Region.xlsx');

//             header('Content-Description: File Transfer');
//             header('Content-Type: application/octet-stream');
//             header('Content-Disposition: attachment; filename="'.basename('By Region.xlsx').'"');
//             header('Expires: 0');
//             header('Cache-Control: must-revalidate');
//             header('Pragma: public');
//             header('Content-Length: ' . filesize('By Region.xlsx'));
//             readfile("By Region.xlsx");

//                 fclose($file);

//         }
// else if($request->sort == "department"){
//     $fileName = 'Stats By Department.csv';
//     $headers = array(
//         "Content-type" => "text/csv",
//         "Content-Disposition" => "attachment; filename=$fileName",
//         "Pragma" => "no-cache",
//         "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
//         "Expires" => "0"
//     );
//     $date = new Carbon($_GET['start_date']);
//     $year = $date->format("Y");
//     $charities = Pledge::select(DB::raw('business_units.status, departments.department_name, departments.bi_department_id, COUNT(business_units.name) as employee_count, SUM(pledges.goal_amount) as dollars, COUNT(employee_jobs.emplid) as donors, business_units.id,business_units.name, (COUNT(employee_jobs.emplid) / elligible_employees.ee_count) as participation_rate'))
//         ->join("users","pledges.user_id","users.id")
//         ->join("employee_jobs","employee_jobs.emplid","users.emplid")
//         ->join("business_units","business_units.code","=","employee_jobs.business_unit")
//         ->join("departments","employee_jobs.deptid","departments.bi_department_id")
//         ->join("elligible_employees","elligible_employees.business_unit","business_units.code")
//         ->where("elligible_employees.year","=",$year)
//         ->where("employee_jobs.empl_rcd","=","select min(empl_rcd) from employee_jobs J2 where J2.emplid = J.emplid and J2.empl_status = 'A' and J2.date_deleted is null")
//         ->where('employee_jobs.empl_status',"=","A")
//         ->where('pledges.created_at',">",$date->copy()->startOfYear())
//         ->where('pledges.created_at',"<",$date->copy())
//         ->where('business_units.status',"=","A")
//         ->whereNull('employee_jobs.date_deleted')
//         ->havingRaw('participation_rate < ? and employee_count > ?', [101,4])
//         ->groupBy('employee_jobs.deptid')
//         ->limit(500)
//         ->get();


//     $row = ["","Department Name", "Department Id", "Donors", "Dollars"];

//     $file = fopen('test.csv', 'w');
//     fputcsv($file, $row);
//     foreach ($charities as $index => $charity) {
//         fputcsv($file, [($index + 1),$charity->department_name,$charity->bi_department_id, $charity->donors, "$".number_format($charity->dollars,2)]);
//     }
//     $date = new Carbon($_GET['start_date']);
//     $year = $date->format("Y");
//     $totals  = Pledge::select(DB::raw('business_units.status, COUNT(business_units.name) as employee_count, SUM(pledges.goal_amount) as dollars, COUNT(employee_jobs.emplid) as donors, business_units.id,business_units.name, (COUNT(employee_jobs.emplid) / elligible_employees.ee_count) as participation_rate'))
//         ->join("users","pledges.user_id","users.id")
//         ->join("employee_jobs","employee_jobs.emplid","users.emplid")
//         ->join("business_units","business_units.code","=","employee_jobs.business_unit")
//         ->join("elligible_employees","elligible_employees.business_unit","business_units.code")
//         ->join("departments","employee_jobs.deptid","departments.bi_department_id")
//         ->where("elligible_employees.year","=",$year)
//         ->where("employee_jobs.empl_rcd","=","select min(empl_rcd) from employee_jobs J2 where J2.emplid = J.emplid and J2.empl_status = 'A' and J2.date_deleted is null")
//         ->where('employee_jobs.empl_status',"=","A")
//         ->where('pledges.created_at',">",$date->copy()->startOfYear())
//         ->where('pledges.created_at',"<",$date->copy())
//         ->where('business_units.status',"=","A")
//         ->whereNull('employee_jobs.date_deleted')
//         ->groupBy('employee_jobs.deptid')
//         ->limit(500)
//         ->get();
//     $totalDonors = 0;
//     $totalDollars = 0;
//     foreach($totals as $line){
//         $totalDonors += $line->donors;
//         $totalDollars += $line->dollars;
//     }
//     fputcsv($file,["totals","","",$totalDonors,"$".number_format($totalDollars)]);
//     $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Csv');
//     $objPHPExcel = $reader->load("test.csv");
//     $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');
//     $objWriter->save('By Department.xlsx');

//     header('Content-Description: File Transfer');
//     header('Content-Type: application/octet-stream');
//     header('Content-Disposition: attachment; filename="'.basename('By Department.xlsx').'"');
//     header('Expires: 0');
//     header('Cache-Control: must-revalidate');
//     header('Pragma: public');
//     header('Content-Length: ' . filesize('By Department.xlsx'));
//     readfile("By Department.xlsx");

//     fclose($file);
// }
       // return response()->stream($callback, 200, $headers);
    // }




}
