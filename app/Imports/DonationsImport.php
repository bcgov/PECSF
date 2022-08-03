<?php

namespace App\Imports;

use App\Models\Donation;
use App\Models\CampaignYear;
use App\Models\Organization;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DonationsImport implements ToModel, WithHeadingRow, WithValidation, WithEvents
{
    use Importable;

    protected $org_code;
    protected $history_id;

    protected $in_current_row;

    protected $row_count;
    protected $skip_count;
    protected $errors;


    public function __construct($history_id, $org_code)
    {

        $this->history_id = $history_id;
        $this->org_code = $org_code;

        $this->in_current_row = [];

        $this->row_count = 0;
        $this->skip_count = 0;
        $this->errors = [];

        // $this->users = User::all(['id', 'name'])->pluck('id', 'name')->limit(100000);
    }
    
    public function model(array $row)
    {

        return new Donation([
            'org_code'     => $row['co'],
            'pecsf_id'     => $row['id'],
            'name'         => $row['employee_name'],
            'yearcd'       => $row['calendar_year'],
            'pay_end_date' => $row['pay_period_end_date'],
            'source_type'  => '10',
            'frequency'    => $row['frequency_of_pay_period'],
            'amount'       => $row['employee_pecsf_contribution_amount'],

            'process_history_id' => $this->history_id,
            
        ]);
    }

    public function prepareForValidation($data, $index)
    {
        // get and store the current row for validation purpose
        $this->in_current_row = $data;

        return $data;
    }

    public function rules(): array
    {

        $orgs = [ $this->org_code ];

        $input_org = Organization::where('code', $this->in_current_row['co'])->first();
        $input_cy  = CampaignYear::where('calendar_year', $this->in_current_row['calendar_year'])->first();

        $row = $this->in_current_row;

        return [
            'co' => ['required', Rule::in( $orgs )],
            'id' => ['required', Rule::exists('pledges', 'pecsf_id')                     
                                    ->where(function ($query) use ($input_org, $input_cy) {                      
                                        $query->where('organization_id', $input_org->id)
                                              ->where('campaign_year_id', $input_cy->id);                                   
                                    }),
                                 Rule::unique('donations','pecsf_id')
                                    ->where(function ($query) use ($row) {                      
                                        $query->where('org_code', $row['co'])
                                                ->where('yearcd', $row['calendar_year'])
                                                ->where('pay_end_date', $row['pay_period_end_date'])
                                                ->where('source_type', 10)
                                                ->where('frequency', $row['frequency_of_pay_period']);
                                 }),
            ],
            'employee_name' => 'required',
            'calendar_year' => 'required',
            'pay_period_end_date' => 'required',

            'frequency_of_pay_period' => 'required',
            'employee_pecsf_contribution_amount' => 'required',

        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'co.in' => 'The organization on upload file doesn\'t match with the selected org.',
            'id.exists' => 'No pledge was setup for this pecsf_id.',
            'id.unique' => 'The same pay deduction transactions was loaded',
        ];
    }
    
  
    public function headingRow(): int
    {
        return 2;
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $totalRows = $event->getReader()->getTotalRows();

                if (filled($totalRows)) {

                    $this->row_count = array_values($totalRows)[0] - 2;  // Note: first 2 rows is heading

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
                $message = 'Success: ' . $this->row_count . ' row(s) were imported.';;
                if ($this->skip_count > 0) {
                    $status = 'Warning';
                    $message = 'Warning: ' . $this->skip_count . ' out of ' . $this->row_count . ' row(s) were skipped due to duplication.';
                }

                \App\Models\ProcessHistory::UpdateOrCreate([
                    'id' => $this->history_id,
                ],[
                    'status' => $status,
                    'message' => $message,
                    'done_count' => ($this->row_count - $this->skip_count),
                    'end_at' => now(),
                ]);

            },
        ];
    }

}
