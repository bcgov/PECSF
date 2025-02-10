<?php

namespace App\Imports;


use App\Models\City;
use App\Models\Pledge;
use App\Models\EmployeeJob;
use App\Models\CampaignYear;
use App\Models\Organization;
use Illuminate\Validation\Rule;
use App\Models\VolunteerProfile;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;


class VolunteerProfilesNonGovImport implements  ToModel, WithValidation, WithEvents, WithBatchInserts, WithStartRow
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

        // Find the historical profile record
        $profile = VolunteerProfile::where('campaign_year', '<', $this->campaign_year)
                        ->where('organization_code', $row[0])
                        ->where('pecsf_id', $row[7])
                        ->orderByDesc('campaign_year')
                        ->first();

        $no_of_years = 1;
        if ($profile) {
            $no_of_years = $profile->no_of_years + 1;
        }

        // preferred role 
        $preferred_roles = array_flip(VolunteerProfile::ROLE_LIST);

        $this->done_count += 1;
        $this->imported_rows .= implode(",",  array_diff($row,['profile' => 0])) . PHP_EOL;

        return new VolunteerProfile([
            'campaign_year'      => $row[3],      
            'organization_code'  => $row[0],    // Organization
            'emplid'             => null,
            'pecsf_id'           => $row[7],

            'first_name'         => $row[2],   // $row[2],
            'last_name'          => $row[1],   // $row[1],

            'employee_city_name'   => $row[9],
            'employee_bu_code'     => $row['employee_bu_code'],
            'employee_region_code' => $row['employee_region_code'],
            'business_unit_code'   => $row['business_unit_code'],

            'no_of_years'        => $no_of_years,
            'preferred_role'     => $preferred_roles[ $row[6] ],

            'address_type'	 => 'S',
            'address'        => $row[8],
            'city' 	         => $row[9],
            'province'       => $row[10],
            'postal_code'    => $row[11],
            'opt_out_recongnition' => 'N',

            'created_by_id'	 => $this->user_id,
            'updated_by_id' => $this->user_id,
            
        ]);

    }

    public function prepareForValidation($data, $index)
    {

        echo PHP_EOL . json_encode($data);

        // $pledge = Pledge::join('organizations','pledges.organization_id','organizations.id')
        //                 ->join('campaign_years','pledges.campaign_year_id','campaign_years.id')
        //                 ->where('organizations.code', $data[0])
        //                 ->where('pledges.pecsf_id', $data[1])
        //                 ->where('campaign_years.calendar_year', $data[2])
        //                 ->first();

        // $donation = Donation::where('org_code', $data[0])
        //                 ->where('pecsf_id', $data[1])
        //                 ->where('yearcd', $data[2])
        //                 ->where('pay_end_date', $data[3])
        //                 ->where('source_type', 10)
        //                 ->where('frequency', $frequency )
        //                 ->first();

        $organization = Organization::where('code', $data[0])
                            ->first();

        $city = City::where('city', $data[9])
                            ->first();                            

        $data['employee_bu_code']     = $organization ? $organization->bu_code : null;
        $data['business_unit_code']   = $organization ? $organization->bu_code : null;
        $data['employee_region_code'] = $city ? $city->TGB_REG_DISTRICT : null;

        // Try to find Historial Volunteer Profile  
        $profile = VolunteerProfile::where('campaign_year', '<', $this->campaign_year)
                        ->where('organization_code', $data[0])
                        ->where('pecsf_id', $data[7])
                        // ->where('last_name', $data[1])
                        // ->where('first_name', $data[2])
                        ->orderByDesc('campaign_year')
                        ->first();

        if ($profile) {

            $data['employee_region_code'] = $data['employee_region_code'] ?? ($profile ? $profile->employee_region_code : null);

            $data[8]  = $data[8] ?? $profile->address;       // Address 
            $data[9]  = $data[9] ?? $profile->city;          // City 
            $data[10] = $data[10] ?? $profile->province;     // Province 
            $data[11] = $data[11] ?? $profile->postal_code;  // Postal Code

        } else {

            // Try to find Annual Campaigm Pledge  
            $pledge = Pledge::join('organizations', 'pledges.organization_id', 'organizations.id')
                            ->where('organizations.code', $data[0])
                            ->where('pecsf_id', $data[7])
                            // ->where('last_name', $data[1])
                            // ->where('last_name', $data[2])
                            ->selectRaw('pledges.*')
                            ->orderByDesc('campaign_year_id')
                            ->first();
                            
            if ($pledge) {

                $data['employee_region_code'] = $data['employee_region_code'] ?? ($pledge ? $pledge->tgb_reg_district : null);

                // $data[8]  = $data[8] ?? $pledge->address;       // Address 
                $data[9]  = $data[9] ?? $pledge->city;          // City 
                // $data[10] = $data[10] ?? $pledge->province;     // Province 
                // $data[11] = $data[11] ?? $pledge->postal_code;  // Postal Code

            }
            
        }

        // Checking any duplication profile for the same campaign year
        $profile = VolunteerProfile::where('campaign_year', $this->campaign_year)
                        ->where('organization_code', $data[0])
                        ->where('pecsf_id', $data[7])
                        ->first();

        // special fields for checking unique or existenance 
        $data['profile'] = $profile ? $profile->id : 0;
        
echo PHP_EOL . (implode(',', $data) );

        return $data;
    }

    public function rules(): array
    {

        $role_values = implode(',', array_values(VolunteerProfile::ROLE_LIST));
        $province_keys = array_keys(VolunteerProfile::PROVINCE_LIST);

        $this->campaign_year;

        return [
            '0' => 'required|exists:organizations,code',          // Organization Code
            '1' => 'required',          // Last Name
            '2' => 'required',          // First Name
            '3' => 'required|in:' . $this->campaign_year . '|exists:campaign_years,calendar_year',
            // '3' => 'required',
            // '4' => 'required',
            '6' => 'required|in:' . $role_values,     

            '7' => 'required|digits:6',     // PECSF ID
            '8' => 'required',     // address
            '9' => 'required|exists:cities,city',      // City
            '10' => ['required', Rule::in( $province_keys )],  // Province
            '11' => ['required', 'regex:/^([a-zA-Z]\d[a-zA-Z])\ {0,1}(\d[a-zA-Z]\d)$/'], // Postal Code

            'business_unit_code' => 'exists:business_units,code',
            'profile' => 'unique:volunteer_profiles,id',
           

        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
  
            '0.exists' => 'Invalid Org Code.',
            '3.in'     => 'The campaign year in the file doesn\'t match the selected year.',
            '3.exists' => 'No campaign year set up.',
            '6.in'     => 'Invalid primary role.',
            '7.required' => 'Missing PECSF ID.',
            '7.digits' => 'PECSF ID must be 6 digits.',
            '8.required' => 'Address is required when no history record is found.',
            '9.exists'   => 'City not found for the given name.',
            '10.required' => 'Province is required when no history record is found.',
            '10.in'       => 'Province is invalid',
            '11.required' => 'Postal code is required when no history record is found.',
            '11.regex' => 'Postal code format is invalid (sample valid code: M5V 2L7)',

            'business_unit_code.exists' => 'No history records found for this PECSF ID.',
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

                    $this->row_count = array_values($totalRows)[0] - 1;  // Note: first 1 row is heading

                      \App\Models\ProcessHistory::UpdateOrCreate([
                            'id' => $this->history_id,
                        ],[
                            'total_count' => $this->row_count,
                            'done_count' => 0,
                            'status' => 'Processing',
                            'start_at' => now(),
                        ]);

                }
            },
            AfterImport::class => function (AfterImport $event) {

                $status = 'Completed';

                $history = \App\Models\ProcessHistory::where('id', $this->history_id)->first();
               
                $messages = 'Process ID : ' . $this->history_id . PHP_EOL;
                $messages .= 'Process parameters : ' . ($history ?  $history->parameters : '')  . PHP_EOL;
                $messages .= PHP_EOL;
                $messages .= 'Success: ' . $this->done_count . ' row(s) were imported. ' . PHP_EOL;
                $messages .= PHP_EOL;
                $messages .= 'The imported data details : '. PHP_EOL;
                $messages .= PHP_EOL;                
                $messages .= $this->imported_rows;
                $messages .= PHP_EOL;

                if ($this->skip_count > 0) {
                    $status = 'Warning';
                    $messages .= 'Warning: ' . $this->skip_count . ' out of ' . $this->row_count . ' row(s) were skipped due to duplication.';
                }

                \App\Models\ProcessHistory::UpdateOrCreate([
                    'id' => $this->history_id,
                ],[
                    'status' => $status,
                    'message' => $messages,
                    'done_count' => ($this->row_count - $this->skip_count),
                    'end_at' => now(),
                ]);

            },
        ];
    }

    public function batchSize(): int
    {
        return 10000;
    }

}
