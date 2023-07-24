<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeJobInactive2021_2023JSON extends Seeder
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
        $path = storage_path('app/uploads/inactive_employee_2021_2023.json');
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
                'emplid' => $job->emplid,
                'empl_rcd' => $job->empl_rcd,
                'guid' => $job->guid ?? null ,
            ], [
                // 'emplid' => $job->EMPLID,
                // 'empl_rcd' => $job['empl_record'],
                'organization_id' => $organization ? $organization->id : null,
                'effdt' => $job->effdt,
                'effseq' => $job->effseq,
                'empl_status' => $job->empl_status,
                'empl_ctg' => $job->empl_ctg,
                'empl_class' => $job->empl_class,
                'job_indicator' => $job->job_indicator,
                'position_number' => $job->position_number,
                'position_title' => $job->position_title,
                'appointment_status' => $job->appointment_status,
                'first_name' => $job->first_name,
                'last_name' => $job->last_name,
                'name' => $job->name,
                'email' => $job->email ?? null,
                // 'guid' => trim($job['guid']),
                'idir' => isset( $job->idir) ? trim($job->idir) : null,

                'business_unit' => $job->business_unit,
                'business_unit_id' => array_key_exists( $job->business_unit, $business_units) ? $business_units[$job->business_unit ] : null,
                'deptid' => $job->deptid,
                'dept_name' => '',
                'tgb_reg_district' => $job->tgb_reg_district,
                'region_id' => array_key_exists( $job->tgb_reg_district , $regions) ? $regions[$job->tgb_reg_district] : null,
                'office_address1' => $job->office_address1,
                'office_address2' => $job->office_address2,
                'office_city' => $job->office_city, 
                'office_stateprovince' => $job->office_stateprovince, 
                'office_country' => $job->office_country,
                'office_postal' => $job->office_postal,

                'city' => $job->city,
                'stateprovince' => $job->stateprovince,
                'country' => $job->country,

                'organization' => isset($job->Organization) ? trim($job->Organization) : null,
                'level1_program' => isset($job->level1_program) ?  trim($job->level1_program) : null,
                'level2_division' => isset($job->level2_division) ?  trim($job->level2_division) : null,
                'level3_branch' => isset($job->level3_branch) ? trim($job->level3_branch) : null,
                'level4' => isset($job->level4) ? trim($job->level4) : null,
                'supervisor_emplid' => isset($job->supervisor_emplid) ?? null,
                'supervisor_name' => isset($job->supervisor_name) ?? null,
                'supervisor_email' => isset($job->supervisor_email) ?? null,
                'date_updated' => '2021-09-01 00:00:00',
                'date_deleted' => null,

                'created_by_id' => 888,
                'updated_by_id' => 888,

            ]);

            // dd ( json_encode($new_job) );


            // Also create/update user profile
            $target_email = $job->emplid . '@gov';

            $user = User::updateOrCreate([
                'email' => $target_email,     // key
            ],[ 
                'name' => $new_job->first_name . ' ' . $new_job->last_name,
                'guid' => $new_job->guid ?? null,
                'idir' => $new_job->idir ?? null,
                'source_type' => self::SOURCE_TYPE,    
                'password' => $password,
                'acctlock' => 0,
                'last_sync_at' => $new_sync_at,
                'organization_id' => $organization->id,
                'emplid' => $new_job->emplid,
            ]);

        }

    }
}
