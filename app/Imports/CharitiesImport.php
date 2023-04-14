<?php

namespace App\Imports;

use App\Models\Charity;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;

class CharitiesImport implements ToCollection, WithStartRow, WithChunkReading, WithCustomCsvSettings, WithEvents
{

    protected $history_id;

    protected $row_count;
    protected $done_count;
    protected $created_count;
    protected $updated_count;
    protected $skipped_count;

    public function __construct($history_id)
    {

        $this->history_id = $history_id;

        $this->row_count = 0;
        $this->created_count = 0;
        $this->updated_count = 0;
        $this->skipped_count = 0;

    }



    public function startRow(): int
    {
        return 2;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => "\t",
            'input_encoding' => 'ISO-8859-1',
            'use_bom' => true,
            'line_ending' => PHP_EOL,
        ];
    }

    public function collection(Collection $rows)
    {

        $index = [];

        foreach ($rows as $row) {

            $this->done_count += 1;

            if(empty($row[4]))
            {
                $this->skipped_count += 1;
                $this->logMessage('[SKIPPED] ' . json_encode($row) );
                continue;
            }

            try{
                $charity = Charity::UpdateOrCreate([
                    'registration_number' => $row[0],
                ], [
                    'charity_name' => $row[1],
                    'charity_status' => $row[2],
                    'type_of_qualified_donee' => $row[3],
                    'effective_date_of_status' => $row[4], 
                    'sanction' => $row[5],
                    'designation_code' => $row[6],
                    'charity_type' => $row[7],
                    'category_code' => $row[8],
                    'address' => $row[9],
                    'city' => $row[10],
                    'province' => $row[11],
                    'country' => $row[12],
                    'postal_code' => $row[13],
                ]);
            }
            catch(Exception $e){
                continue;
            }

            if ($charity->wasRecentlyCreated) {
                // $this->logMessage('[CREATED] ' . json_encode($row) );
                $this->created_count += 1;
            } elseif ($charity->wasChanged() ) {
                $changes = $charity->getChanges();
                // $this->logMessage('[UPDATED] ' . json_encode($changes) );
                $this->updated_count += 1;
            } else {
                // No Action
            }

        }

        // Write to log file
        $this->logMessage('-- Importing charity -- ' . number_format( $this->done_count / $this->row_count * 100 ,2)  .
                         '%  (' . number_format($this->done_count,0) . ' / ' . number_format($this->row_count,0) . ')' );            

    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }


    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $totalRows = $event->getReader()->getTotalRows();

                if (filled($totalRows)) {

                    $this->row_count = array_values($totalRows)[0] - 1;  // Note: first row is heading

                    $history = \App\Models\ProcessHistory::where('id', $this->history_id)->first();

                    $message = 'Process ID : ' . $this->history_id  . PHP_EOL;
                    $message .= 'Process parameters : ' . ($history ?  $history->parameters : '')  . PHP_EOL;
                    $message .= PHP_EOL;
                    $message .= 'Import process started at : ' . now()  . PHP_EOL;
    
                    $history->total_count = $this->row_count;
                    $history->done_count = 0;
                    $history->status = 'Processing';
                    $history->message = $history->message . $message;
                    $history->start_at = now();
                    $history->save();

                }
            },
            AfterImport::class => function (AfterImport $event) {

                $message = 'Import process ended at : ' . now()  . PHP_EOL;
                $message .= PHP_EOL;                

                if ($this->skipped_count ==  0) {
                    $status = 'Completed';
                    $message .= 'The process was successfully completed.' . PHP_EOL;
                } else {
                    $status = 'Warning';
                    $message .= 'The process was completed with warning.';
                }

                $message .= PHP_EOL;
                $message .= 'Total Row count     : ' . $this->row_count . PHP_EOL;
                $message .= 'Total Process count : ' . $this->done_count . PHP_EOL;
                $message .= PHP_EOL;                

                $message .= PHP_EOL;                                
                $message .= 'Total Created count : ' . $this->created_count . PHP_EOL;
                $message .= 'Total Updated count : ' . $this->updated_count . PHP_EOL;
                $message .= 'Total Skipped count : ' . $this->skipped_count . PHP_EOL;

                $history = \App\Models\ProcessHistory::where('id', $this->history_id)->first();

                $history->message = $history->message . $message;
                $history->status = $status;
                $history->done_count = $this->row_count;
                $history->end_at = now();
                $history->save();

            },
        ];
    }

    protected function logMessage($text) 
    {

        // write to log message 
        $message = $text . PHP_EOL;

        $history = \App\Models\ProcessHistory::where('id', $this->history_id)->first();
        $history->message = $history->message . $message;
        $history->save();

    }


}
