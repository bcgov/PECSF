<?php

namespace App\Imports;

use App\Models\City;
use App\Models\EmployeeJob;
use App\Models\CampaignYear;
use App\Models\Organization;
use Illuminate\Validation\Rule;
use App\Models\VolunteerProfile;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class VolunteerProfilesUnifiedImport implements ToModel, WithValidation, WithEvents, WithBatchInserts, WithStartRow
{
    use Importable;

    protected $campaign_year;
    protected $history_id;
    protected $user_id;
    protected $row_count;
    protected $skip_count;
    protected $errors;
    protected $imported_rows;

    public function __construct($history_id, $campaign_year)
    {
        $this->history_id = $history_id;
        $this->campaign_year = $campaign_year;
        $this->row_count = 0;
        $this->done_count = 0;
        $this->skip_count = 0;
        $this->errors = [];
        $this->imported_rows = '';

        $history = \App\Models\ProcessHistory::where('id', $this->history_id)->first();
        $this->user_id = $history->created_by_id;
    }

    public function model(array $row)
    {
        if (!isset($row[0])) {
            return null;
        }

        $org_code = strtoupper(trim($row[0]));
        $is_gov = ($org_code === 'GOV');

        // Skip rows with unrecognized organization codes
        if (empty($org_code)) {
            return null;
        }

        if ($is_gov) {
            return $this->processGovEmployee($row);
        } else {
            return $this->processNonGovEmployee($row);
        }
    }

    protected function processGovEmployee(array $row)
    {
        // GOV Logic: [1] contains emplid - match by emplid first, fallback to name
        $job = EmployeeJob::where('emplid', trim($row[1]))->first();

        if (!$job) {
            $job = EmployeeJob::where(DB::raw('LOWER(name)'), 'like', '%' . strtolower(trim($row[2]) . ',' . trim($row[3])) . '%')
                        ->orWhere(function ($query) use ($row) {
                            $query->where(DB::raw('LOWER(last_name)'), 'like', '%' . strtolower(trim($row[2])) . '%')
                                  ->where(DB::raw('LOWER(first_name)'), 'like', '%' . strtolower(trim($row[3])) . '%');
                        })
                        ->first();
        }

        if (!$job) {
            return null;
        }

        $profile = VolunteerProfile::where('campaign_year', '<', $this->campaign_year)
                        ->where('emplid', $job->emplid)
                        ->orderByDesc('campaign_year')
                        ->first();

        $no_of_years = 1;
        if ($profile) {
            $no_of_years = $profile->no_of_years + 1;
        }

        $preferred_roles = array_flip(VolunteerProfile::ROLE_LIST);

        $this->done_count += 1;
        $this->imported_rows .= implode(",", array_diff($row, ['profile' => 0])) . PHP_EOL;

        return new VolunteerProfile([
            'campaign_year'      => $row[4],
            'organization_code'  => 'GOV',
            'emplid'             => $job->emplid,
            'first_name'         => null,
            'last_name'          => null,
            'aad_fullname'       => $job->aad_fullname,
            'employee_city_name' => $job->office_city,
            'employee_bu_code'   => $job->business_unit,
            'employee_region_code' => $job->tgb_reg_district,
            'business_unit_code' => $job->business_unit,
            'no_of_years'        => $no_of_years,
            'preferred_role'     => $preferred_roles[$row[5]] ?? null,
            'address_type'       => 'S',
            'address'            => $job->office_address1,
            'city'               => $job->city,
            'province'           => $job->office_stateprovince,
            'postal_code'        => $job->office_postal,
            'opt_out_recongnition' => 'N',
            'created_by_id'      => $this->user_id,
            'updated_by_id'      => $this->user_id,
        ]);
    }

    protected function processNonGovEmployee(array $row)
    {
        // Non-GOV Logic: [1] contains pecsf_id - match by organization + pecsf_id
        $org_code = strtoupper(trim($row[0]));

        $profile = VolunteerProfile::where('campaign_year', '<', $this->campaign_year)
                        ->where('organization_code', $org_code)
                        ->where('pecsf_id', trim($row[1]))
                        ->orderByDesc('campaign_year')
                        ->first();

        $no_of_years = 1;
        if ($profile) {
            $no_of_years = $profile->no_of_years + 1;
        }

        $preferred_roles = array_flip(VolunteerProfile::ROLE_LIST);

        $this->done_count += 1;
        $this->imported_rows .= implode(",", array_diff($row, ['profile' => 0])) . PHP_EOL;

        return new VolunteerProfile([
            'campaign_year'      => $row[4],
            'organization_code'  => $org_code,
            'emplid'             => null,
            'pecsf_id'           => trim($row[1]),
            'first_name'         => trim($row[3]),
            'last_name'          => trim($row[2]),
            'employee_city_name' => $row[7] ?? null,
            'employee_bu_code'   => null,
            'employee_region_code' => null,
            'business_unit_code' => null,
            'no_of_years'        => $no_of_years,
            'preferred_role'     => $preferred_roles[$row[5]] ?? null,
            'address_type'       => 'S',
            'address'            => $row[6] ?? null,
            'city'               => $row[7] ?? null,
            'province'           => $row[8] ?? null,
            'postal_code'        => $row[9] ?? null,
            'opt_out_recongnition' => 'N',
            'created_by_id'      => $this->user_id,
            'updated_by_id'      => $this->user_id,
        ]);
    }

    public function prepareForValidation($data, $index)
    {
        echo PHP_EOL . json_encode($data);

        $org_code = strtoupper(trim($data[0]));
        $is_gov = ($org_code === 'GOV');

        if ($is_gov) {
            $this->prepareGovValidation($data);
        } else {
            $this->prepareNonGovValidation($data);
        }

        return $data;
    }

    protected function prepareGovValidation(&$data)
    {
        // GOV: [1] contains emplid - match by emplid first, fallback to name
        $job = EmployeeJob::where('emplid', trim($data[1]))->first();

        if (!$job) {
            $job = EmployeeJob::where(DB::raw('LOWER(name)'), 'like', '%' . strtolower(trim($data[2]) . ',' . trim($data[3])) . '%')
                        ->orWhere(function ($query) use ($data) {
                            $query->where(DB::raw('LOWER(last_name)'), 'like', '%' . strtolower(trim($data[2])) . '%')
                                ->where(DB::raw('LOWER(first_name)'), 'like', '%' . strtolower(trim($data[3])) . '%');
                        })
                        ->first();
        }

        $profile = null;
        if ($job) {
            $profile = VolunteerProfile::where('campaign_year', $this->campaign_year)
                        ->where('emplid', $job->emplid)
                        ->first();
        }

        $data['emplid'] = $job ? $job->emplid : 0;
        $data['profile'] = $profile ? $profile->id : 0;
    }

    protected function prepareNonGovValidation(&$data)
    {
        // Non-GOV: [1] contains pecsf_id - match by organization + pecsf_id
        $org_code = strtoupper(trim($data[0]));

        $organization = Organization::where('code', $org_code)->first();
        $city = City::where('city', $data[7] ?? null)->first();

        $profile = VolunteerProfile::where('campaign_year', '<', $this->campaign_year)
                        ->where('organization_code', $org_code)
                        ->where('pecsf_id', trim($data[1]))
                        ->orderByDesc('campaign_year')
                        ->first();

        if ($profile) {
            $data['employee_region_code'] = $city ? $city->TGB_REG_DISTRICT : ($profile->employee_region_code ?? null);
        } else {
            $data['employee_region_code'] = $city ? $city->TGB_REG_DISTRICT : null;
        }

        $data['employee_bu_code'] = $organization ? $organization->bu_code : null;
        $data['business_unit_code'] = $organization ? $organization->bu_code : null;
        $data['organization_code'] = $org_code;
        $data['profile'] = $profile ? $profile->id : 0;
    }

    public function rules(): array
    {
        $role_values = implode(',', array_values(VolunteerProfile::ROLE_LIST));

        return [
            '0' => 'required',                // Organization Code (any value - unrecognized ones will be skipped)
            '1' => 'required',                // Emplid (for GOV) / PECSF ID (for Non-GOV)
            '2' => 'required',                // Last Name
            '3' => 'required',                // First Name
            '4' => 'required|in:' . $this->campaign_year . '|exists:campaign_years,calendar_year',  // Campaign Year
            '5' => 'required|in:' . $role_values,  // Preferred Role

            'emplid' => 'required_if:0,GOV|exists:employee_jobs,emplid',
            'profile' => 'unique:volunteer_profiles,id',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '0.required' => 'Organization Code is required.',
            '1.required' => 'Employee ID is required (Emplid for GOV, PECSF ID for Non-GOV).',
            '2.required' => 'Last Name is required.',
            '3.required' => 'First Name is required.',
            '4.required' => 'Campaign Year is required.',
            '4.in' => 'Campaign Year does not match the selected year.',
            '4.exists' => 'No campaign year set up.',
            '5.required' => 'Preferred Role is required.',
            '5.in' => 'Invalid Preferred Role.',

            'emplid.required_if' => 'Emplid is required for Government employees.',
            'emplid.exists' => 'Emplid not found in employee database.',
            'profile.unique' => 'The volunteer profile is already loaded.',
        ];
    }

    public function startRow(): int
    {
        return 2;
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $totalRows = $event->getReader()->getTotalRows();

                if (filled($totalRows)) {
                    $this->row_count = array_values($totalRows)[0] - 1;

                    \App\Models\ProcessHistory::UpdateOrCreate(
                        ['id' => $this->history_id],
                        [
                            'total_count' => $this->row_count,
                            'done_count' => 0,
                            'status' => 'Processing',
                            'start_at' => now(),
                        ]
                    );
                }
            },
            AfterImport::class => function (AfterImport $event) {
                $status = 'Completed';
                $history = \App\Models\ProcessHistory::where('id', $this->history_id)->first();

                $messages = 'Process ID : ' . $this->history_id . PHP_EOL;
                $messages .= 'Process parameters : ' . ($history ? $history->parameters : '') . PHP_EOL;
                $messages .= PHP_EOL;
                $messages .= 'Success: ' . $this->done_count . ' row(s) were imported. ' . PHP_EOL;
                $messages .= PHP_EOL;
                $messages .= 'The imported data details : ' . PHP_EOL;
                $messages .= PHP_EOL;
                $messages .= $this->imported_rows;
                $messages .= PHP_EOL;

                if ($this->skip_count > 0) {
                    $status = 'Warning';
                    $messages .= 'Warning: ' . $this->skip_count . ' out of ' . $this->row_count . ' row(s) were skipped due to duplication.';
                }

                \App\Models\ProcessHistory::UpdateOrCreate(
                    ['id' => $this->history_id],
                    [
                        'status' => $status,
                        'message' => $messages,
                        'done_count' => ($this->row_count - $this->skip_count),
                        'end_at' => now(),
                    ]
                );
            },
        ];
    }

    public function batchSize(): int
    {
        return 10000;
    }
}
