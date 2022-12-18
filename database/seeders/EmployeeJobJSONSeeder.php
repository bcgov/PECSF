<?php

namespace Database\Seeders;

use App\Models\EmployeeJob;
use Illuminate\Database\Seeder;

class EmployeeJobJSONSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $path = storage_path('app/uploads/employee_jobs.json');
        $json = file_get_contents($path);

        $in_jobs = json_decode( $json );

        // Missing Employees
        $organization = \App\Models\Organization::where('code', 'GOV')->first();
        $business_units = \App\Models\BusinessUnit::pluck('id','code')->toArray();
        $regions = \App\Models\Region::pluck('id','code')->toArray();

        $total_count = 0;
        $created_count = 0;
        $updated_count = 0;

        foreach ($in_jobs as $job) {
            $total_count += 1;

            $new_job = EmployeeJob::updateOrCreate([
                "emplid" => $job->emplid,
                "empl_rcd" => $job->empl_rcd,
            ],[
                "organization_id" => $organization ? $organization->id : null,
                "effdt" => $job->effdt,
                "effseq" => $job->effseq,
                "empl_status" => $job->empl_status,
                "empl_class" => $job->empl_class,
                "empl_ctg" => $job->empl_ctg,
                "job_indicator" => $job->job_indicator,
                "position_number" => $job->position_number,
                "position_title" => $job->position_title,
                "appointment_status" => $job->appointment_status,
                "first_name" => $job->first_name,
                "last_name" => $job->last_name,
                "name" => $job->name,
                "email" => $job->email,
                "guid" => $job->guid,
                "idir" => $job->idir,
                "business_unit" => $job->business_unit,
                "business_unit_id" => array_key_exists( $job->business_unit , $business_units) ? $business_units[ $job->business_unit ] : null,
                "deptid" => $job->deptid,
                "dept_name" => $job->dept_name,
                "tgb_reg_district" => $job->tgb_reg_district,
                "region_id" => array_key_exists( $job->tgb_reg_district , $regions) ? $regions[$job->tgb_reg_district] : null,
                "office_address1" => $job->office_address1,
                "office_address2" => $job->office_address2,
                "office_city" => $job->office_city,
                "office_stateprovince" => $job->office_stateprovince,
                "office_country" => $job->office_country,
                "office_postal" => $job->office_postal,
                "address1" => $job->address1,
                "address2" => $job->address2,
                "city" => $job->city,
                "stateprovince" => $job->stateprovince,
                "country" => $job->country,
                "postal" => $job->postal,
                "organization" => $job->organization,
                "level1_program" => $job->level1_program,
                "level2_division" => $job->level2_division,
                "level3_branch" => $job->level3_branch,
                "level4" => $job->level4,
                "supervisor_emplid" => $job->supervisor_emplid,
                "supervisor_name" => $job->supervisor_name,
                "supervisor_email" => $job->supervisor_email,
                "date_updated" => $job->date_updated,
                "date_deleted" => $job->date_deleted,
                "created_by_id" => $job->created_by_id,
                "updated_by_id" => $job->updated_by_id,
                
            ]);
            if ($new_job->wasRecentlyCreated) {
                $created_count += 1;
            } elseif ($new_job->wasChanged() ) {
                $updated_count += 1;
                // echo ( json_ecnode($new_job) );
            } else {
                // No change
            }

        }    

        echo 'Total count    ' . $total_count . PHP_EOL;
        echo 'Total created  ' . $created_count . PHP_EOL;
        echo 'Total updated  ' . $updated_count . PHP_EOL;


    }
}

