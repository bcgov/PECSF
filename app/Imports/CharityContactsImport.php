<?php

namespace App\Imports;

use App\Models\Charity;
use App\Models\ScheduleJobAudit;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;


// class CharityContactsImport implements  ToCollection, WithValidation, WithEvents, WithBatchInserts, WithStartRow
class CharityContactsImport implements  ToCollection, WithBatchInserts, WithStartRow
{
    use Importable;

    protected $task_id;
    protected $message;
    protected $status;

    protected $row_count;
    protected $done_count;
    protected $skip_count;

    public function __construct($task_id)
    {

        $this->task_id = $task_id;

        $this->row_count = 0;
        $this->done_count = 0;
        $this->skip_count = 0;
        $this->message = '';
        $this->status = 'Completed';

    }
    
    public function collection(Collection $rows)
    {

        $this->LogMessage('This process to update the alternative address, cobtact information of Charity from the excel file.');
        $this->LogMessage('The process was started at ' . now()  );
        $this->LogMessage('');

        foreach ($rows as $row) {

            $this->row_count += 1;

            // only process the data when the Vendor ID matched with Remit To Vendor ID 
            if ($row[0] == $row[35]) {

                $charity = Charity::where('registration_number', $row[36])->first();
                $old_charity = $charity->replicate();

                if ($charity) {

                    // Update Alternate Address
                    $charity->use_alt_address = 1;
                    $charity->alt_address1 = $row[11];  // Column L -- Address 1
                    $charity->alt_address2 = $row[12];  // Column M -- Address 2	
                    $charity->alt_city = $row[15];  // Column P -- City		
                    $charity->alt_province = $row[23];  // Column X -- State
                    $charity->alt_country	= $row[10];  // Column K -- Country
                    $charity->alt_postal_code = $row[24];  // Column Y -- postal

                    // Update contact information 
                    $charity->financial_contact_name = $row[27];  // Column AB -- Name 
                    $charity->financial_contact_title =  $row[28];  // Column AC -- Title
                    $charity->financial_contact_email = $row[33];  // Column AH -- email 
                    $charity->phone = $row[30];  // Column AE -- Phone 
                    $charity->fax = $row[32];    // Column AF -- Fax 
                    $charity->url = $row[34];    // Column AI -- Fax 

                    $charity->updated_by_id = 999;          // always assign to 999
                    $charity->save();

                    // Log 
                    $this->LogMessage('(UPDATED) => charity | ' . $charity->id . ' | ' . $charity->registration_number  );
                    $changes = $charity->getChanges();
                    unset($changes["updated_at"]);

                    $original = array_intersect_key($old_charity->toArray(),$changes);
                    $this->LogMessage('  summary => ' );
                    $this->LogMessage('      original : '. json_encode( $original ) );
                    $this->LogMessage('      change   : '. json_encode( $changes ) );


                    // echo $row[36] . ' | ' . $row[27] . ' | ' . $row[28] . ' | ' . $row[33] . ' | '  . $row[30] . ' | ' . $row[32] . ' | ' . $row[34] ;

                    $this->done_count += 1;

                } else {

                    echo $row[36] . ' | ' . $row[27] ;
                }
            } else {

                $this->LogMessage('(SKIPPED) => charity | ' .  $row[36] . ' on row ' . $this->row_count . ' | Vendor ID and Remit Vendor are not matched ' . ($row[0] . ' - ' . $row[35] ));

                $this->skip_count += 1;

            }
        }

        $this->LogMessage( '' );
        $this->LogMessage( 'Total row(s) in file  : ' . $this->row_count );
        $this->LogMessage( '' );
        $this->LogMessage( 'Total Updated row(s)  : ' . $this->done_count );
        $this->LogMessage( 'Total Skipped row(s)  : ' . $this->skip_count );
        $this->LogMessage( '' );
        $this->LogMessage(' The process was ended at ' . now()  );

        $task = ScheduleJobAudit::where('id', $this->task_id)->first();

        $task->end_time = now();
        $task->status = $this->status;
        $task->message = $this->message;
        $task->save();

    }

    
    public function startRow(): int
    {
        return 3;
    }

    public function batchSize(): int
    {
        return 10000;
    }

    protected function LogMessage($text) 
    {

        // $this->info( $text );
        echo $text . PHP_EOL;

        // write to log message 
        $this->message .= $text . PHP_EOL;
        
    }



}
