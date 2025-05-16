<?php

namespace App\Imports;

use Exception;
use App\Models\Charity;
use Illuminate\Support\Str;
use App\Models\CharityStaging;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class CharitiesImport implements ToCollection, WithStartRow, WithChunkReading, WithCustomCsvSettings, WithEvents
{

    protected $history_id;
    protected $history;

    protected $row_count;
    protected $done_count;
    protected $created_count;
    protected $updated_count;
    protected $skipped_count;
    protected $created_by_id;

    public function __construct($history_id)
    {
        $this->history_id = $history_id;
        $this->history = \App\Models\ProcessHistory::where('id', $history_id)->first();
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
                $this->logMessage('[SKIPPED - missing eff date] ' . json_encode($row) );
                continue;
            }

            try{

                $old_charity = Charity::where('registration_number', $row[0])
                                    ->first();

                if ($old_charity && $old_charity->charity_status == 'Pending-Dissolution') {
                    if (($this->created_count + $this->updated_count + $this->skipped_count) <= 1000) {
                        $this->logMessage('[SKIPPED - Pending-Dissolution] ' . json_encode($old_charity->only(['id','registration_number','charity_name','charity_status', 'effective_date_of_status'])) );
                    }
                    $this->skipped_count += 1;
                    continue;
                }

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

                if ($charity->wasRecentlyCreated) {
                    // $this->logMessage('[CREATED] ' . json_encode($row) );
                    if (($this->created_count + $this->updated_count + $this->skipped_count) <= 1000) {
                        $this->logMessage('[CREATED] ' . json_encode( $charity->only(['id','registration_number','charity_name','charity_status', 'effective_date_of_status'])));
                    }
                    $this->created_count += 1;

                    $charity->created_by_id = $this->created_by_id;
                    $charity->updated_by_id = $this->created_by_id;
                    $charity->save();

                } elseif ($charity->wasChanged() ) {

                    $changes = $charity->getChanges();
                    unset($changes["updated_at"]);
                    if (($this->created_count + $this->updated_count + $this->skipped_count) <= 1000) {
                        $this->logMessage('[UPDATED] on RN# ' . $charity->registration_number . ' - ' . json_encode($changes) );
                    }
                    $this->updated_count += 1;

                    $charity->updated_by_id = $this->created_by_id;
                    $charity->save();

                } else {
                    // No Action
                }

                if ($charity && 
                    trim(strtolower($charity->address)) == trim(strtolower($charity->alt_address1)) &&
                    trim(strtolower($charity->city)) == trim(strtolower($charity->alt_city)) &&
                    trim(strtolower($charity->province)) == trim(strtolower($charity->alt_province)) &&
                    trim(strtolower($charity->country)) == trim(strtolower($charity->alt_country)) &&
                    trim(strtolower($charity->postal_code)) == trim(strtolower(str_replace(' ', '', $charity->alt_postal_code)))) {

                    $charity->use_alt_address = null;
                    $charity->alt_address1 = null;
                    $charity->alt_address2 = null;
                    $charity->alt_city = null;
                    $charity->alt_province = null;
                    $charity->alt_country = null;
                    $charity->alt_postal_code = null;
                    $charity->save();

                    $changes = $charity->getChanges();
                    unset($changes["updated_at"]);
                    $this->logMessage('[RESET ALT ADDRESS] on RN# ' . $charity->registration_number . ' - ' . json_encode($changes) );
                }

                $charityStaging = CharityStaging::create([
                    'history_id' => $this->history_id,
                    'registration_number' => $row[0],
                    'charity_name' => $row[1],
                    'charity_status' => $row[2],

                ]);

            }
            catch(Exception $ex){


                $this->history->status = 'Error';
                $this->history->message .= $ex->getMessage() . PHP_EOL;
                $this->history->end_at = now();
                $this->history->save();

                // write message to the log
                throw new Exception($ex);
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

                    $this->created_by_id = $this->history->created_by_id;

                    $message = 'Process ID : ' . $this->history_id  . PHP_EOL;
                    $message .= 'Process parameters : ' . ($this->history ?  $this->history->parameters : '')  . PHP_EOL;
                    $message .= PHP_EOL;
                    $message .= 'Import process started at : ' . now()  . PHP_EOL;

                    $this->history->total_count = $this->row_count;
                    $this->history->done_count = 0;
                    $this->history->status = 'Processing';
                    $this->history->message = $this->history->message . $message;
                    $this->history->start_at = now();
                    $this->history->save();

                }
            },
            AfterImport::class => function (AfterImport $event) {

                // handle the record not in CRA file
                $history_id = $this->history_id;
                $sql = Charity::whereNotExists(function($query) use( $history_id) {
                                     $query->select(DB::raw(1))
                                           ->from('charity_stagings')
                                           ->where('history_id', $history_id)
                                           ->whereColumn('charities.registration_number', 'charity_stagings.registration_number');
                                })
                                ->where('charity_status', '!=', 'No-CRA-match');

                $this->logMessage('');
                $this->logMessage('-- Additional step to mark charity which not found in CRA file : ');
                $this->logMessage('');

                $not_in_cra_count = 0;
                $sql->chunk(1000, function($chuck) use( &$not_in_cra_count ) {

                    foreach($chuck as $charity)  {

                        $charity->charity_status = 'No-CRA-match';
                        $charity->save();

                        $changes = $charity->getChanges();
                        unset($changes["updated_at"]);
                        $this->logMessage('[UPDATED STATUS] on RN# ' . $charity->registration_number . ' - ' . json_encode($changes) );

                        $not_in_cra_count += 1;

                    }

                });

                $this->logMessage('');

                // clean up the staging data
                CharityStaging::where('history_id', $history_id)->delete();

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
                $message .= 'Total Row count        : ' . $this->row_count . PHP_EOL;
                $message .= 'Total Process count    : ' . $this->done_count . PHP_EOL;
                $message .= PHP_EOL;

                $message .= PHP_EOL;
                $message .= 'Total Created count              : ' . $this->created_count . PHP_EOL;
                $message .= 'Total Updated count              : ' . $this->updated_count . PHP_EOL;
                $message .= 'Total Updated (Not-in-CRA) count : ' . $not_in_cra_count . PHP_EOL;
                $message .= 'Total Skipped count              : ' . $this->skipped_count . PHP_EOL;

                if (($this->created_count + $this->updated_count + $this->skipped_count) > 1000) {
                    $message .= PHP_EOL;
                    $message .= 'Note: more than 1,000 changes found, only first 1,000 detail were logged in the log message.' . PHP_EOL;
                    $message .= PHP_EOL;
                }




                $this->history->message = $this->history->message . $message;
                $this->history->status = $status;
                $this->history->done_count = $this->row_count;
                $this->history->end_at = now();
                $this->history->save();

            },
        ];
    }

    protected function logMessage($text)
    {

        // write to log message
        $message = $text . PHP_EOL;

        $this->history->message .= $message;

        // log to the file to share with the front end
        Storage::disk('logs')->put('charities_import_' .  $this->history_id . '.log', $this->history->message);

    }

}
