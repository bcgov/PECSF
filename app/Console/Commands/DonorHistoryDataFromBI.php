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

    /* attributes for share in the command */
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
                'status' => 'Processing',
        ]);

        // $this->info("Update/Create - Business Unit");
        // $this->UpdateBusinessUnit();
        // $this->info("Update/Create - Region District");
        // $this->UpdateRegionalDistrict();
        $this->LogMessage( now() );   
        $this->LogMessage("Task-- Update/Create - Department");
        $this->UpdateDepartment();

        $this->LogMessage( now() );   
        $this->LogMessage("Task-- Create - Donor By Business Unit");
        $this->UpdateDonorByBusinessUnit();

        $this->LogMessage( now() );   
        $this->LogMessage("Task-- Create - Donor By Regioinal District");
        $this->UpdateDonorByRegionalDistrict();

        $this->LogMessage( now() );   
        $this->LogMessage("Task-- Create - Donor By Department");
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

        $total_count = 0;
        $created_count = 0;
        $updated_count = 0;
       
        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
                ->get(env('ODS_INBOUND_REPORT_DEPARTMENTS_BI_ENDPOINT'));

            if ($response->successful()) {
                $size = 1000;
                $data = json_decode($response->body())->value;
                $batches = array_chunk($data, $size);

                foreach ($batches as $key => $batch) {
                    $this->LogMessage( '    -- each batch ('.$size.') $key - '. $key );
                    foreach ($batch as $row) {

                        $business_unit = BusinessUnit::where('code', $row->business_unit_code)->first();

                        $rec = Department::updateOrCreate([
                            'bi_department_id' => $row->department_id,
                        ], [
                            'department_name' => $row->department_name,
                            'group' => $row->group,
                            'yearcd' => $row->yearcd,
                            'business_unit_code'=> $row->business_unit_code,
                            'business_unit_name' => $row->business_unit_name,
                            'business_unit_id' => $business_unit ? $business_unit->id : null,
                        ]);

                        $total_count += 1;

                        if ($rec->wasRecentlyCreated) {
                            $created_count += 1;
                        } elseif ($rec->wasChanged() ) {
                            $updated_count += 1;
                        } else {
                            // No Action
                        }                            

                    }
                }

                $this->LogMessage('    Total Row count     : ' . $total_count  );
                $this->LogMessage('    Total Created count : ' . $created_count  );
                $this->LogMessage('    Total Updated count : ' . $updated_count  );

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

        $total_count = 0;
        $created_count = 0;
        $updated_count = 0;

        // Truncate Donar By Reggional District table
        DonorByBusinessUnit::truncate();

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
                ->get(env('ODS_INBOUND_REPORT_DON_DOL_BY_ORG_BI_ENDPOINT'));


            if ($response->successful()) {
                $size = 1000;
                $data = json_decode($response->body())->value;
                $batches = array_chunk($data, $size);
                foreach ($batches as $key => $batch) {
                    $this->LogMessage( '    -- each batch ('.$size.') $key - '. $key );
                    foreach ($batch as $row) {

                        $total_count += 1;

                        $business_unit = BusinessUnit::where('code', $row->business_unit_code)->first();

                        if ( $row->business_unit_code && !empty(trim($row->business_unit_code))  ) {
                            $rec = DonorByBusinessUnit::updateOrCreate([
                                'business_unit_code' => $row->business_unit_code,
                                'yearcd' => $row->year,
                            ],[
                                'business_unit_id' => $business_unit ? $business_unit->id : 0,
                                'dollars' => $row->dollars,
                                'donors' => $row->donors,
                            ]);

                            if ($rec->wasRecentlyCreated) {
                                $created_count += 1;
                            } elseif ($rec->wasChanged() ) {
                                $updated_count += 1;
                                $this->LogMessage('(UPDATED) => '. json_encode( $row ) );
                                $changes = $rec->getChanges();
                                $this->LogMessage('  summary => '. json_encode( $changes ) );
                            } else {
                                // No Action
                            }      
                        } else {

                            // $this->LogMessage('    Exception => Empty Business_unit_code ' . $row->business_unit_code . ' | ' . $row->year . ' | ' . $row->dollars . ' | ' . $row->donors . ' | ' . $row->business_unit_name . ' | ' . $row->cde );
                            $this->LogMessage('    Exception => Empty Business_unit_code ' . json_encode( $row ) );

                        }

                    }
                }

                $this->LogMessage('    Total Row count     : ' . $total_count  );
                $this->LogMessage('    Total Created count : ' . $created_count  );
                $this->LogMessage('    Total Updated count : ' . $updated_count  );
                
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

        $total_count = 0;
        $created_count = 0;
        $updated_count = 0;
       
        // Truncate Donar By Reggional District table
        DonorByRegionalDistrict::truncate();

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
                ->get(env('ODS_INBOUND_REPORT_DON_DOL_BY_REG_DIST_BI_ENDPOINT'));

            if ($response->successful()) {
                $size = 1000;
                $data = json_decode($response->body())->value;
                $batches = array_chunk($data, $size);

                foreach ($batches as $key => $batch) {
                    $this->LogMessage( '    -- each batch ('.$size.') $key - '. $key );
                    foreach ($batch as $row) {

                        $regional_district = RegionalDistrict::where('tgb_reg_district', $row->tgb_reg_district)->first();

                        if ( $row->tgb_reg_district && !empty(trim($row->tgb_reg_district))  ) {
                            $rec = DonorByRegionalDistrict::updateOrCreate([
                                'regional_district_id' => $regional_district ? $regional_district->id : '',
                                'yearcd' => $row->year,
                            ],[
                                'tgb_reg_district' => $row->tgb_reg_district,
                                'dollars' => $row->dollars,
                                'donors' => $row->donors,
                            ]);

                            $total_count += 1;

                            if ($rec->wasRecentlyCreated) {
                                $created_count += 1;
                            } elseif ($rec->wasChanged() ) {
                                
                                $updated_count += 1;

                                $this->LogMessage('(UPDATED) => '. json_encode( $row ) );
                                $changes = $rec->getChanges();
                                $this->LogMessage('  summary => '. json_encode( $changes ) );
                            } else {
                                // No Action
                            }      
                        } else {

                            // $this->LogMessage('    Exception => Empty Business_unit_code ' . $row->business_unit_code . ' | ' . $row->year . ' | ' . $row->dollars . ' | ' . $row->donors . ' | ' . $row->business_unit_name . ' | ' . $row->cde );
                            $this->LogMessage('    Exception => Empty tgb_reg_district ' . json_encode( $row ) );

                        }

                    }
                }

                $this->LogMessage('    Total Row count     : ' . $total_count  );
                $this->LogMessage('    Total Created count : ' . $created_count  );
                $this->LogMessage('    Total Updated count : ' . $updated_count  );

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
        $total_count = 0;
        $created_count = 0;
        $updated_count = 0;

        // Truncate Donar By department table
        DonorByDepartment::truncate();

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withBasicAuth(env('ODS_USERNAME'),env('ODS_TOKEN'))
                ->get(env('ODS_INBOUND_REPORT_DONORS_BY_DEPT_BI_ENDPOINT'));

            if ($response->successful()) {
                $size = 1000;
                $data = json_decode($response->body())->value;
                $batches = array_chunk($data, $size);

                foreach ($batches as $key => $batch) {
                    $this->LogMessage( '    -- each batch ('.$size.') $key - '. $key );
                    foreach ($batch as $row) {

                        $department = Department::where('bi_department_id', $row->department_id)->first();

                        if ( $row->department_id && !empty(trim($row->department_id))  ) {
                            $rec = DonorByDepartment::Create([
                                'department_id' => $department ? $department->id : '',
                                'yearcd' => $row->year,
                                'date' => $row->date,
                            ],[
                                'bi_department_id' => $row->department_id,                            
                                'dollars' => $row->dollars,
                                'donors' => $row->donors,
                            ]);

                            $total_count += 1;

                            if ($rec->wasRecentlyCreated) {
                                $created_count += 1;
                            } elseif ($rec->wasChanged() ) {
                                $updated_count += 1;

                                $this->LogMessage('(UPDATED) => '. json_encode( $row ) );
                                $changes = $rec->getChanges();
                                $this->LogMessage('  summary => '. json_encode( $changes ) );
                            } else {
                                // No Action
                            }  
                        } else {

                            // $this->LogMessage('    Exception => Empty Business_unit_code ' . $row->business_unit_code . ' | ' . $row->year . ' | ' . $row->dollars . ' | ' . $row->donors . ' | ' . $row->business_unit_name . ' | ' . $row->cde );
                            $this->LogMessage('    Exception => Empty department_id ' . json_encode( $row ) );

                        }    

                    }
                }

                $this->LogMessage('    Total Row count     : ' . $total_count  );
                $this->LogMessage('    Total Created count : ' . $created_count  );
                $this->LogMessage('    Total Updated count : ' . $updated_count  );

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
