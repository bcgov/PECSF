<?php

namespace App\Imports;


use App\Models\City;
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


class VolunteerProfilesGovImport implements  ToModel, WithValidation, WithEvents, WithBatchInserts, WithStartRow
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

        // Find an employee Job information 
        $job = EmployeeJob::where('last_name', $row[1])
                    ->where('first_name', $row[2])
                    ->first();

        // Find the historical profile record
        $profile = VolunteerProfile::where('campaign_year', '<', $this->campaign_year)
                        ->where('emplid', $job->emplid )
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
            'organization_code'  => 'GOV',
            'emplid'             => $job->emplid,

            'first_name'         => null,   // $row[2],
            'last_name'          => null,   // $row[1],

            'employee_city_name' => $job->office_city,      
            'employee_bu_code'     => $job->business_unit,
            'employee_region_code' => $job->tgb_reg_district,
            'business_unit_code'   => $job->business_unit,

            'no_of_years'        => $no_of_years,
            'preferred_role'     =>  $preferred_roles[ $row[6] ],

            'address_type'	 => 'S',
            'address'  => $job->office_address1,
            'city' 	 => $job->city,
            'province'  => $job->office_stateprovince,
            'postal_code'  => $job->office_postal,
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

        $job = EmployeeJob::where('last_name', $data[1])
                                    ->where('first_name', $data[2])
                                    ->first();

        $profile = null;                                
        if ($job) {
            $profile = VolunteerProfile::where('campaign_year', $this->campaign_year)
                        ->where('emplid', $job->emplid)
                        ->first();
        }


        // special fields for checking unique or existenance 
        $data['emplid']  = $job ? $job->emplid : 0;
        $data['profile'] = $profile ? $profile->id : 0;

        return $data;
    }

    public function rules(): array
    {

        $role_values = implode(',', array_values(VolunteerProfile::ROLE_LIST));

        $this->campaign_year;

        return [
            '0' => 'required|in:GOV',   // Organization Code
            '1' => 'required',          // Last Name
            '2' => 'required',          // First Name
            '3' => 'required|in:' . $this->campaign_year . '|exists:campaign_years,calendar_year',
            // '3' => 'required',
            // '4' => 'required',
            '6' => 'required|in:' . $role_values,     

            'emplid' => 'exists:employee_jobs,emplid',
            'profile' => 'unique:volunteer_profiles,id',
           

        ];
    }


    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            // '0.in' =>    'The organization on upload file doesn\'t match with the selected org.',
            // '1.min' => 'The 1 field must be 6 characters.',
            // '1.max' => 'The 1 field must be 6 characters.',
            // '2.numeric' => 'The 2 field must be a number',
            // '3.date' =>  'The 3 field is not a valid date',
            // '4.in'  =>   'The 4 field is invalid frequency (either Bi-Weekly or One-Time Deduction)',
            // '5.numeric' => 'The 5 field must be a number',

            '0.in'     => 'The organization on upload file is not Government (GOV).',
            '3.in'     => 'The campaign year doesn\'t match the selected year.',
            '3.exists' => 'No campaign year set up.',
            '6.in'     => 'Invalid primary role.',

            'emplid.exists' => 'Emplid not found for the given name.',
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
