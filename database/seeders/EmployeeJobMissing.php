<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EmployeeJobMissing extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $path = storage_path('app/uploads/inactive_employee_10.json');
        $json = file_get_contents($path);
        $missed_jobs = json_decode( $json );

        $organization = \App\Models\Organization::where('code', 'GOV')->first();
        $business_units = \App\Models\BusinessUnit::pluck('id','code')->toArray();
        $regions = \App\Models\Region::pluck('id','code')->toArray();

        foreach ($missed_jobs as $row) {

            $old_job = \App\Models\EmployeeJob::where('emplid',  $row->employee_id)
                           ->where('empl_rcd', $row->empl_record)
                            ->first();

            if ($old_job) {
                continue;
            }

             // $regional_district = RegionalDistrict::where('tgb_reg_district', $row->tgb_reg_district)->first();

            \App\Models\EmployeeJob::updateOrCreate([
                'guid' => $row->guid,
            ], [
                'emplid' => $row->employee_id,
                'empl_rcd' => $row->empl_record,
                'organization_id' => $organization ? $organization->id : null,
                'effdt' => $row->effdt,
                'effseq' => $row->effseq,
                'empl_status' => $row->employee_status,
                'empl_ctg' => $row->empl_ctg,
                'empl_class' => $row->empl_class,
                'job_indicator' => $row->job_indicator,
                'position_number' => $row->position_number,
                'position_title' => $row->position_title,
                'appointment_status' => $row->appointment_status,
                'first_name' => $row->employee_first_name,
                'last_name' => $row->employee_last_name,
                'name' => $row->employee_name,
                'email' => $row->employee_email,
                'guid' => trim($row->guid),
                'idir' => trim($row->idir),

                'business_unit' => $row->business_unit,
                'business_unit_id' => array_key_exists( $row->business_unit, $business_units) ? $business_units[$row->business_unit] : null,
                'deptid' => $row->deptid,
                'dept_name' => '',
                'tgb_reg_district' => $row->tgb_reg_district,
                'region_id' => array_key_exists( $row->tgb_reg_district , $regions) ? $regions[$row->tgb_reg_district] : null,

                'office_address1' => $row->office_address,
                'office_address2' => $row->office_address2,
                'office_city' => $row->office_city, 
                'office_stateprovince' => $row->office_stateprovince, 
                'office_country' => $row->office_country,
                'office_postal' => $row->office_postal,

                'city' => $row->city,
                'stateprovince' => $row->stateprovince,
                'country' => $row->country,

                'organization' => trim($row->organization),
                'level1_program' => trim($row->level1_program),
                'level2_division' => trim($row->level2_division),
                'level3_branch' => trim($row->level3_branch),
                'level4' => trim($row->level4),
                'supervisor_emplid' => $row->supervisor_emplid,
                'supervisor_name' => $row->supervisor_name,
                'supervisor_email' => $row->supervisor_email,
                'date_updated' => $row->date_updated ? (substr($row->date_updated,0,10).' '.substr($row->date_updated,11,8)) : null,
                'date_deleted' => $row->date_deleted ? (substr($row->date_deleted,0,10).' '.substr($row->date_deleted,11,8)) : null,

                'created_by_id' => null,
                'updated_by_id' => null,

            ]);

        }


    }
}
