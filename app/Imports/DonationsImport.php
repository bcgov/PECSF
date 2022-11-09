<?php

namespace App\Imports;

use App\Models\Pledge;
use App\Models\Donation;
use App\Models\CampaignYear;
use App\Models\Organization;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;


class DonationsImport implements  ToModel, WithValidation, WithEvents, WithBatchInserts, WithStartRow
{
    use Importable;

    protected $org_code;
    protected $history_id;

    protected $row_count;
    protected $total_amount;

    protected $skip_count;
    protected $errors;

    protected $imported_rows;

    public function __construct($history_id, $org_code)
    {

        $this->history_id = $history_id;
        $this->org_code = $org_code;

        $this->row_count = 0;
        $this->done_count = 0;
        $this->total_amount = 0;
        $this->skip_count = 0;
        $this->errors = [];

        $this->imported_rows = '';

    }
    
    

    public function model(array $row)
    {

        if (!isset($row[0])) {
            return null;
        }

        $this->done_count += 1;
        $this->total_amount += $row[5];  // Employee PECSF Contribution Amount

        $this->imported_rows .= implode(",", $row) . PHP_EOL;

        $frequency = 'bi-weekly';    
        switch ( strtolower($row[4]) ) {
            case 'bi-weekly':
                $frequency = 'bi-weekly';    
                break;
            case 'one-time deduction':     
                $frequency = 'one-time'; 
                break;
        }

        return new Donation([
            'org_code'     => $row[0],      // Co
            'pecsf_id'     => $row[1],      // ID
            'name'         => $row[6],      // Employee Name

            'yearcd'       => $row[2],      // calendar_year
            'pay_end_date' => $row[3],      // pay_period_end_date
            'source_type'  => '10',
            'frequency'    => $frequency,      // frequency_of_pay_period -- "Bi-Weely" or "One-Time Deduction"
            'amount'       => $row[5],      // employee_pecsf_contribution_amount

            'process_history_id' => $this->history_id,
            
        ]);
    }

    public function prepareForValidation($data, $index)
    {

        // Preapre Data for checking exists and unique 
        $frequency = '';
        switch ( strtolower($data[4]) ) {
            case 'bi-weekly':
                $frequency = 'bi-weekly';    
                break;
            case 'one-time deduction':     
                $frequency = 'one-time'; 
                break;
        }

        $pledge = Pledge::join('organizations','pledges.organization_id','organizations.id')
                        ->join('campaign_years','pledges.campaign_year_id','campaign_years.id')
                        ->where('organizations.code', $data[0])
                        ->where('pledges.pecsf_id', $data[1])
                        ->where('campaign_years.calendar_year', $data[2])
                        ->first();

        $donation = Donation::where('org_code', $data[0])
                        ->where('pecsf_id', $data[1])
                        ->where('yearcd', $data[2])
                        ->where('pay_end_date', $data[3])
                        ->where('source_type', 10)
                        ->where('frequency', $frequency )
                        ->first();

                     
        // // special fields for checking unique 
        $data['pledge'] = $pledge ? $pledge->id : '';
        $data['donation'] = $donation ? $donation->id : '';

        return $data;
    }

    public function rules(): array
    {

        $orgs = [ $this->org_code ];

        return [
            '0' => ['required', Rule::in( $orgs )],
            '1' => 'required',
            '2' => 'required|numeric',            // calendar_year
            '3' => 'required|date',               // pay_period_end_date
            '4' => ['required', Rule::in(["Bi-Weekly", "One-time Deduction"]) ],   //frequency_of_pay_period
            '5' => 'required|numeric',                  // employee_pecsf_contribution_amount' 
            '6' => 'required',                      //employee_name

            'pledge' => 'exists:pledges,id',
            'donation' => 'unique:donations,id',

        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            '0.in' =>    'The organization on upload file doesn\'t match with the selected org.',
            '2.numeric' => 'The 2 field must be a number',
            '3.date' =>  'The 3 field is not a valid date',
            '4.in'  =>   'The 4 field is invalid frequency (either Bi-Weekly or On-Time Deduction)',
            '5.numeric' => 'The 5 field must be a number',

            'pledge.exists' => 'No pledge was setup for this pecsf_id.',
            'donation.unique' => 'The same pay deduction transactions has been loaded.',
        ];
    }
    
    public function startRow(): int
    {
        return 3;
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

                $history = \App\Models\ProcessHistory::where('id', $this->history_id)->first();
               
                $messages = 'Process ID : ' . $this->history_id . PHP_EOL;
                $messages .= 'Process parameters : ' . ($history ?  $history->parameters : '')  . PHP_EOL;
                $messages .= PHP_EOL;
                $messages .= 'Success: ' . $this->done_count . ' row(s) were imported. ' . PHP_EOL;
                $messages .= 'Total Amount : ' . number_format($this->total_amount, 2, '.', ',') . PHP_EOL;
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
