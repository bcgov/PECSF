<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Department;

use App\Models\BusinessUnit;
use Illuminate\Console\Command;
use App\Models\RegionalDistrict;
use App\Models\ScheduleJobAudit;
use App\Models\DonorByDepartment;
use App\Models\DonorByBusinessUnit;
use Illuminate\Support\Facades\Http;
use App\Models\DonorByRegionalDistrict;

class DonorHistoryDataFromBI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:DonorHistoryDataFromBI';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load Donor History Data From BI';

    protected $message;
    protected $status;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->message = '';
        $this->status = 'Completed';

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $this->task = ScheduleJobAudit::Create([
                'job_name' => $this->signature,
                'start_time' => Carbon::now(),
                'status','Initiated'
        ]);

        // $this->info("Update/Create - Business Unit");
        // $this->UpdateBusinessUnit();
        // $this->info("Update/Create - Region District");
        // $this->UpdateRegionalDistrict();
        $this->LogMessage( now() );   
        $this->LogMessage("Update/Create - Department");
        $this->UpdateDepartment();

        $this->LogMessage( now() );   
        $this->LogMessage("Create - Donor By Business Unit");
        $this->UpdateDonorByBusinessUnit();

        $this->LogMessage( now() );   
        $this->LogMessage("Create - Donor By Regioinal District");
        $this->UpdateDonorByRegionalDistrict();

        $this->LogMessage( now() );   
        $this->LogMessage("Create - Donor By Department");
        $this->UpdateDonorByDepartment();

        $this->LogMessage( now() );   

        // $this->ClearODSHistoryData();

        // Update the Task Audit log
        $this->task->end_time = Carbon::now();
        $this->task->status = $this->status;
        $this->task->message = $this->message;
        $this->task->save();

        return 0;
    }

    protected function UpdateBusinessUnit()
    {
        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
                ->get(env('ODS_INBOUND_REPORT_BUSINESS_UNITS_BI_ENDPOINT'));

            if ($response->successful()) {
                $data = json_decode($response->body())->value;
                $batches = array_chunk($data, 1000);

                foreach ($batches as $batch) {
                    $this->LogMessage( count($batch) );
                    foreach ($batch as $row) {

                        BusinessUnit::updateOrCreate([
                            'business_unit_code' => $row->business_unit_code,
                        ], [
                            'name' => $row->name,
                            'yearcd' => $row->yearcd
                        ]);
                    }
                }
            } else {
                $this->status = 'Error';
                $this->LogMessage( $response->status() . ' - ' . $response->body() );
            }
        } catch (\Exception $ex) {
                            
            // write to log message 
            $this->status = 'Error';
            $this->LogMessage( $ex->getMessage() );

            return 1;
        }

    }

    protected function UpdateRegionalDistrict()
    {

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
                ->get(env('ODS_INBOUND_REPORT_REGIONAL_DISTRICTS_BI_ENDPOINT'));

            if ($response->successful()) {
                $data = json_decode($response->body())->value;
                $batches = array_chunk($data, 1000);

                foreach ($batches as $batch) {
                    $this->LogMessage( count($batch) );
                    foreach ($batch as $row) {

                        RegionalDistrict::updateOrCreate([
                            'tgb_reg_district' => $row->tgb_reg_district,

                        ], [
                            'reg_district_desc' => $row->reg_district_desc,
                            'development_region' => $row->development_region,
                            'provincial_quadrant' => $row->provincial_quadrant,
                        ]);
                    }
                }
            } else {
                $this->status = 'Error';
                $this->LogMessage( $response->status() . ' - ' . $response->body() );
            }
        } catch (\Exception $ex) {
                        
            // write to log message 
            $this->status = 'Error';
            $this->LogMessage( $ex->getMessage() );

            return 1;
        }

    }

    protected function UpdateDepartment()
    {
        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
                ->get(env('ODS_INBOUND_REPORT_DEPARTMENTS_BI_ENDPOINT'));

            if ($response->successful()) {
                $data = json_decode($response->body())->value;
                $batches = array_chunk($data, 1000);

                foreach ($batches as $key => $batch) {
                    $this->LogMessage( $key . ' - ' . count($batch) );
                    foreach ($batch as $row) {

                        $business_unit = BusinessUnit::where('code', $row->business_unit_code)->first();

                        Department::updateOrCreate([
                            'bi_department_id' => $row->department_id,
                        ], [
                            'department_name' => $row->department_name,
                            'group' => $row->group,
                            'yearcd' => $row->yearcd,
                            'business_unit_code'=> $row->business_unit_code,
                            'business_unit_name' => $row->business_unit_name,
                            'business_unit_id' => $business_unit ? $business_unit->id : null,
                        ]);
                    }
                }
            } else {
                $this->status = 'Error';
                $this->LogMessage( $response->status() . ' - ' . $response->body() );
            }
        } catch (\Exception $ex) {
                    
            // write to log message 
            $this->status = 'Error';
            $this->LogMessage( $ex->getMessage() );

            return 1;
        }
    }


    protected function UpdateDonorByBusinessUnit()
    {

        // Truncate Donar By Reggional District table
        DonorByBusinessUnit::truncate();

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
                ->get(env('ODS_INBOUND_REPORT_DON_DOL_BY_ORG_BI_ENDPOINT'));

            if ($response->successful()) {
                $data = json_decode($response->body())->value;
                $batches = array_chunk($data, 1000);
                foreach ($batches as $key => $batch) {
                    $this->LogMessage( $key . ' - ' . count($batch) );
                    foreach ($batch as $row) {
                        if($business_unit = BusinessUnit::where('code', $row->business_unit_code)->first())
                        {
                            DonorByBusinessUnit::updateOrCreate([
                                'business_unit_id' => $business_unit ? $business_unit->id : null,
                                'yearcd' => $row->year,
                                'business_unit_code' => $row->business_unit_code,
                                'dollars' => $row->dollars,
                                'donors' => $row->donors,
                            ]);
                        }
                    }
                }
            } else {
                $this->status = 'Error';
                $this->LogMessage( $response->status() . ' - ' . $response->body() );
            }
        } catch (\Exception $ex) {
                
            // write to log message 
            $this->status = 'Error';
            $this->LogMessage( $ex->getMessage() );

            return 1;
        }
    }


    protected function UpdateDonorByRegionalDistrict()
    {

        // Truncate Donar By Reggional District table
        DonorByRegionalDistrict::truncate();

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
                ->get(env('ODS_INBOUND_REPORT_DON_DOL_BY_REG_DIST_BI_ENDPOINT'));

            if ($response->successful()) {
                $data = json_decode($response->body())->value;
                $batches = array_chunk($data, 1000);

                foreach ($batches as $key => $batch) {
                    $this->LogMessage( $key . ' - ' . count($batch) );
                    foreach ($batch as $row) {

                        $regional_district = RegionalDistrict::where('tgb_reg_district', $row->tgb_reg_district)->first();

                        DonorByRegionalDistrict::updateOrCreate([
                            'regional_district_id' => $regional_district ? $regional_district->id : '',
                            'yearcd' => $row->year,
                            'tgb_reg_district' => $row->tgb_reg_district,
                            'dollars' => $row->dollars,
                            'donors' => $row->donors,
                        ]);
                    }
                }
            } else {
                $this->status = 'Error';
                $this->LogMessage( $response->status() . ' - ' . $response->body() );
            }
        } catch (\Exception $ex) {
            
            // write to log message 
            $this->status = 'Error';
            $this->LogMessage( $ex->getMessage() );

            return 1;
        }
    }

    protected function UpdateDonorByDepartment()
    {

        // Truncate Donar By department table
        DonorByDepartment::truncate();

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
                ->get(env('ODS_INBOUND_REPORT_DONORS_BY_DEPT_BI_ENDPOINT'));

            if ($response->successful()) {
                $data = json_decode($response->body())->value;
                $batches = array_chunk($data, 1000);

                foreach ($batches as $key => $batch) {
                    $this->LogMessage( $key . ' - ' . count($batch) );
                    foreach ($batch as $row) {

                        $department = Department::where('bi_department_id', $row->department_id)->first();

                        DonorByDepartment::Create([
                            'department_id' => $department ? $department->id : '',
                            'yearcd' => $row->year,
                            'date' => $row->date,
                            'bi_department_id' => $row->department_id,
                            'dollars' => $row->dollars,
                            'donors' => $row->donors,
                        ]);
                    }
                }
            } else {
                $this->LogMessage( $response->status() . ' - ' . $response->body() );
            }

        } catch (\Exception $ex) {
        
            // write to log message 
            $this->status = 'Error';
            $this->LogMessage( $ex->getMessage() );

            return 1;
        }
    }


    protected function ClearODSHistoryData() {

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
            ->post(env('ODS_INBOUND_REPORT_DON_DOL_BY_ORG_TRUNCATE_BI_ENDPOINT') );

        if ($response->successful()) {
            $this->LogMessage( 'Clear Donor by Business Unit History ' );
        } else {
            $this->LogMessage( $response->status() . ' - ' .  $response->body() );
        }

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
            ->post(env('ODS_INBOUND_REPORT_DON_DOL_BY_REG_DIST_TRUNCATE_BI_ENDPOINT'));

        if ($response->successful()) {
            $this->LogMessage( 'Cleared Donor by Regional District History' );
        } else {
            $this->LogMessage( $response->status() . ' - ' .  $response->body() );
        }

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
            ->post(env('ODS_INBOUND_REPORT_DONORS_BY_DEPT_TRUNCATE_BI_ENDPOINT'));

        if ($response->successful()) {
            $this->LogMessage( 'Clear Donor by Department History' );
        } else {
            $this->LogMessage( $response->status() . ' - ' .  $response->body() );
        }

    }

    protected function LogMessage($text) 
    {

        $this->info( $text );

        // write to log message 
        $this->message .= $text . PHP_EOL;

        $this->task->message = $this->message;
        $this->task->save();
        
    }




}
