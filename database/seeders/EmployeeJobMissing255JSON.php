<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeJobMissing255JSON extends Seeder
{

    protected const SOURCE_TYPE = 'HCM';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //
        $path = storage_path('app/uploads/inactive_employee_255.json');
        $json = file_get_contents($path);

        $missed_jobs = json_decode( $json );

        // dd($missed_jobs);
        $new_sync_at = Carbon::now();
        $organization = Organization::where('code', 'GOV')->first();
        $password = Hash::make(env('SYNC_USER_PROFILE_SECRET'));

        // Missing Employees
        $organization = \App\Models\Organization::where('code', 'GOV')->first();
        $business_units = \App\Models\BusinessUnit::pluck('id','code')->toArray();
        $regions = \App\Models\Region::pluck('id','code')->toArray();

        foreach ($missed_jobs as $key => $job) {

            $new_job = \App\Models\EmployeeJob::updateOrCreate([
                'emplid' => $job->EMPLID,
                'empl_rcd' => $job->EMPL_RCD,
                'guid' => $job->GUID,
            ], [
                // 'emplid' => $job->EMPLID,
                // 'empl_rcd' => $job['empl_record'],
                'organization_id' => $organization ? $organization->id : null,
                'effdt' => $job->EFFDT,
                'effseq' => $job->EFFSEQ,
                'empl_status' => $job->EMPL_STATUS,
                'empl_ctg' => $job->EMPL_CTG,
                'empl_class' => $job->EMPL_CLASS,
                'job_indicator' => $job->JOB_INDICATOR,
                'position_number' => $job->position_number,
                'position_title' => $job->position_title,
                'appointment_status' => $job->appointment_status,
                'first_name' => $job->first_name,
                'last_name' => $job->last_name,
                'name' => $job->name,
                'email' => $job->email,
                // 'guid' => trim($job['guid']),
                'idir' => trim($job->IDIR),

                'business_unit' => $job->BUSINESS_UNIT,
                'business_unit_id' => array_key_exists( $job->BUSINESS_UNIT, $business_units) ? $business_units[$job->BUSINESS_UNIT ] : null,
                'deptid' => $job->DEPTID,
                'dept_name' => '',
                'tgb_reg_district' => $job->TGB_REG_DISTRICT,
                'region_id' => array_key_exists( $job->TGB_REG_DISTRICT , $regions) ? $regions[$job->TGB_REG_DISTRICT] : null,
                'office_address1' => $job->office_address1,
                'office_address2' => $job->office_address2,
                'office_city' => $job->office_city, 
                'office_stateprovince' => $job->office_stateprovince, 
                'office_country' => $job->office_country,
                'office_postal' => $job->office_postal,

                'city' => $job->city,
                'stateprovince' => $job->stateprovince,
                'country' => $job->country,

                'organization' => trim($job->Organization),
                'level1_program' => trim($job->level1_program),
                'level2_division' => trim($job->level2_division),
                'level3_branch' => trim($job->level3_branch),
                'level4' => trim($job->level4),
                'supervisor_emplid' => $job->supervisor_emplid,
                'supervisor_name' => $job->supervisor_name,
                'supervisor_email' => $job->supervisor_email,
                'date_updated' => '2021-09-01 00:00:00',
                'date_deleted' => null,

                'created_by_id' => 888,
                'updated_by_id' => 888,

            ]);

            // dd ( json_encode($new_job) );


            // Also create/update user profile
            $target_email = $job->EMPLID . '@gov';

            $user = User::updateOrCreate([
                'email' => $target_email,     // key
            ],[ 
                'name' => $new_job->first_name . ' ' . $new_job->last_name,
                'guid' => $new_job->guid,
                'idir' => $new_job->idir,
                'source_type' => self::SOURCE_TYPE,    
                'password' => $password,
                'acctlock' => 0,
                'last_sync_at' => $new_sync_at,
                'organization_id' => $organization->id,
                'employee_job_id' => $new_job->id,
                'emplid' => $new_job->emplid,
            ]);

        }

    }
}
