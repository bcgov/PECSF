<?php

namespace App\Imports;

use App\Models\Donation;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DonationsImport implements ToModel, WithHeadingRow, WithValidation, WithEvents
{
    use Importable;

    protected $org_code;
    protected $history_id;

    protected $row_count;
    protected $skip_count;
    protected $errors;


    public function __construct($history_id, $org_code)
    {

        $this->history_id = $history_id;
        $this->org_code = $org_code;

        $this->row_count = 0;
        $this->skip_count = 0;
        $this->errors = [];

        // $this->users = User::all(['id', 'name'])->pluck('id', 'name')->limit(100000);
    }
    
    public function model(array $row)
    {

        $exists = Donation::where('org_code', $row['co'])
                        ->where('emplid', $row['id'])
                        ->where('yearcd', $row['calendar_year'])
                        ->where('pay_end_date', $row['pay_period_end_date'])
                        ->where('source_type', 10)
                        ->where('frequency', $row['frequency_of_pay_period'])
                        ->first();

        if ($exists) {
            $this->skip_count += 1;
            // array_push($this->errors, $row);
            return null;
        }

        return new Donation([
            'org_code'     => $row['co'],
            'emplid'       => $row['id'],
            'name'         => $row['employee_name'],
            'yearcd'       => $row['calendar_year'],
            'pay_end_date' => $row['pay_period_end_date'],
            'source_type'  => '10',
            'frequency'    => $row['frequency_of_pay_period'],
            'amount'       => $row['employee_pecsf_contribution_amount'],

            'process_history_id' => $this->history_id,
            
        ]);
    }

    public function rules(): array
    {

        $orgs = [ $this->org_code ];

        return [
            'co' => ['required', Rule::in( $orgs )],
            'id' => 'required',
            'employee_name' => 'required',
            'calendar_year' => 'required',
            'pay_period_end_date' => 'required',

            'frequency_of_pay_period' => 'required',
            'employee_pecsf_contribution_amount' => 'required',

            // '1' => 'unique:users',
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
                        // DB::commit();


                    // cache()->forever("total_rows_{$this->id}", array_values($totalRows)[0]);
                    // cache()->forever("start_date_{$this->id}", now()->unix());
                }
            },
            AfterImport::class => function (AfterImport $event) {

                $status = 'Completed';
                $message = '';
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

                    // DB::commit();

                // cache(["end_date_{$this->id}" => now()], now()->addMinute());
                // cache()->forget("total_rows_{$this->id}");
                // cache()->forget("start_date_{$this->id}");
                // cache()->forget("current_row_{$this->id}");
            },
        ];
    }

}
