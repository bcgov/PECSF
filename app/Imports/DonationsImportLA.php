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
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class DonationsImportLA implements  ToModel, SkipsEmptyRows, WithValidation, WithEvents, WithBatchInserts, WithStartRow
{
    use Importable;

    protected $org_code;
    protected $history_id;

    protected $in_current_row;

    protected $row_count;
    protected $total_amount;

    protected $skip_count;
    protected $errors;

    protected $imported_rows;

    // information on the header rows
    protected $C1_org_name;
    protected $C2_pay_end_date;

    public function __construct($history_id, $org_code)
    {

        $this->history_id = $history_id;
        $this->org_code = $org_code;

        $this->in_current_row = [];

        $this->row_count = 0;
        $this->done_count = 0;
        $this->total_amount = 0;
        $this->skip_count = 0;
        $this->errors = [];

        $this->imported_rows = '';

        // $this->users = User::all(['id', 'name'])->pluck('id', 'name')->limit(100000);
    }
    
    // public function isEmptyWhen(array $row): bool
    // {
    //     return false;
    //     // return empty($row[0]);
    // }

    public function model(array $row)
    {

        if (!preg_match("/^[0-9]{6}$/", $row[0])) {
            return null;
        }

        $this->done_count += 1;
        $this->total_amount += $row[4];

        $this->imported_rows .= implode(",", $row) . PHP_EOL;

        return new Donation([
            'org_code'     => 'LA',   //        $row[0],      // Organization
            'pecsf_id'     => $row[0],
            'name'         => $row[2] . ' ' . $row[1],
            'yearcd'       => $this->C2_pay_end_date->format('Y'),   // Calendar Year (from header row)
            'pay_end_date' => $this->C2_pay_end_date,   // pay end daye (from header row
            'source_type'  => '10',
            'frequency'    => 'Bi-Weekly',      // $row[4],  // Bi-Weekly

            'amount'       => $row[5],

            'process_history_id' => $this->history_id,
            
        ]);

    }

    public function prepareForValidation($data, $index)
    {
        // get and store the current row for validation purpose
        // $data[1] = str_pad($data[1], 6, "0", STR_PAD_LEFT); 
        $data[0] = substr($data[0], 1, 6);

        $this->in_current_row = $data;

        return $data;
    }

    public function rules(): array
    {

        $orgs = [ $this->org_code ];
      
        $org_code = $this->org_code;
        $yearcd = $this->C2_pay_end_date->format('Y');
        $pay_end_date = $this->C2_pay_end_date;
        $frequency = 'Bi-Weekly';

        $input_org = Organization::where('code', $this->org_code )->first();
        $input_cy  = CampaignYear::where('calendar_year', $this->C2_pay_end_date->format('Y') )->first();

        $row = $this->in_current_row;

        return [
            // Heading
            // '0' => ['required', Rule::in( $orgs )],
            '0' => ['required', Rule::exists('pledges', 'pecsf_id')                     
                                    ->where(function ($query) use ($input_org, $input_cy) {                      
                                        $query->where('organization_id', $input_org->id ?? null)
                                              ->where('campaign_year_id', $input_cy->id ?? null);                                   
                                    }),
                                 Rule::unique('donations','pecsf_id')
                                    ->where(function ($query) use ($row, $org_code, $yearcd, $pay_end_date, $frequency) {                      
                                        $query->where('org_code', $org_code )
                                                ->where('yearcd', $yearcd )
                                                ->where('pay_end_date', $pay_end_date)
                                                ->where('source_type', 10)
                                                ->where('frequency', $frequency);
                                 }),
            ],
          
            // '2' => 'required',  // Calendar Year
            // '3' => 'required',  // Pay Period End Date
            // '4' => 'required',  // frequency_of_pay_period
            '1' => 'required',  // Employee Name
            '2' => 'required',  // Employee Name
            '5' => 'required',  // Amount

        ];
    
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            // '0.in' => 'The organization on upload file doesn\'t match with the selected org.',
            '0.exists' => 'No pledge was setup for this pecsf_id.',
            '0.unique' => 'The same pay deduction transactions was loaded',
        ];
    }
    
    public function startRow(): int
    {
        return 7;
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $totalRows = $event->getReader()->getTotalRows();

                $spreadsheet = $event->getReader()->getDelegate();
                // C1 -- Organization Name
                $this->C1_org_name = $spreadsheet->getActiveSheet()->getCell('C1')->getValue();
                // C2 -- Pay End Date
                $value = $spreadsheet->getActiveSheet()->getCell('C2')->getFormattedValue();
                $this->C2_pay_end_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);

                if (filled($totalRows)) {

                    $this->row_count = array_values($totalRows)[0] - 1;  // Note: first 2 rows is heading

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
                
                $messages = 'Success: ' . $this->done_count . ' row(s) were imported. ' . PHP_EOL;
                $messages .= 'Total Amount : ' . number_format($this->total_amount, 2, '.', ',') . PHP_EOL;
                $messages .= PHP_EOL;
                $messages .= 'The imported data details for "' . $this->C1_org_name . '" and pay end date was "' . $this->C2_pay_end_date->format('Y-m-d') . '" :' . PHP_EOL;
                $messages .= PHP_EOL;                
                $messages .= $this->imported_rows;
                $messages .= PHP_EOL;
                
                if ($this->skip_count > 0) {
                    $status = 'Warning';
                    $this->messages = 'Warning: ' . $this->skip_count . ' out of ' . $this->row_count . ' row(s) were skipped due to duplication.';
                }

                \App\Models\ProcessHistory::UpdateOrCreate([
                    'id' => $this->history_id,
                ],[
                    'status' => $status,
                    'message' => $messages,
                    'done_count' => $this->done_count,
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
