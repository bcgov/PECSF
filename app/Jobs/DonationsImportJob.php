<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Imports\DonationsImport;
use App\Imports\DonationsImportLA;
use App\Imports\DonationsImportBCP;
use App\Imports\DonationsImportBCS;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\Middleware\WithoutOverlapping;

class DonationsImportJob implements ShouldQueue, ShouldBeUnique
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $uploadFilePath;
    protected $history_id;
    protected $org_code;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($uploadFilePath, $history_id, $org_code)
    {
        //

        $this->uploadFilePath = $uploadFilePath;
        $this->history_id = $history_id;
        $this->org_code = $org_code;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        try {
            
            switch ($this->org_code) {
                case 'LDB':     // BC Liquor Distribution Branch
                    Excel::import(new DonationsImport( $this->history_id, $this->org_code), $this->uploadFilePath );
                    break;
                case 'BCS':     // BC Securities Commission
                    Excel::import(new DonationsImportBCS( $this->history_id, $this->org_code), $this->uploadFilePath );
                    break;
                case 'BCP':     // BC Pension Corporation
                    Excel::import(new DonationsImportBCP( $this->history_id, $this->org_code), $this->uploadFilePath );
                    break;
                case 'LA':      // Legislative Assembly of BC
                    Excel::import(new DonationsImportLA( $this->history_id, $this->org_code), $this->uploadFilePath );
                    break;
                default:
                    Excel::import(new DonationsImport( $this->history_id, $this->org_code), $this->uploadFilePath );
                    break;

            }



            // \App\Models\ProcessHistory::UpdateOrCreate([
            //     'id' => $this->history_id,
            // ],[                    
            //     'status' => 'Completed',
            //     'end_at'  => now(),
            // ]);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            $history = \App\Models\ProcessHistory::where('id', $this->history_id)->first();

            $text = 'Process ID : ' . $this->history_id . PHP_EOL;
            $text .= 'Process parameters : ' . ($history ?  $history->parameters : '')  . PHP_EOL;
            $text .= PHP_EOL;
            $text .= 'Exceptional found: ' . PHP_EOL;
            $text .= 'Note: The field position is start from 0 (0 -> Column A, 1 -> Column B, 2 -> Column C etc)' . PHP_EOL;
            $text .= PHP_EOL;

            foreach ($failures as $failure) {
                $text .= 'Row : ' . $failure->row(); // row that went wrong
                $text .= ' - ' . $failure->attribute(); // either heading key (if using heading row concern) or column index
                $text .= ' - ' . implode(', ', $failure->errors()) ; // Actual error messages from Laravel validator
                $text .= ' - ' . implode(', ', $failure->values()); // The values of the row that has failed.
                $text .= PHP_EOL;
            }

            \App\Models\ProcessHistory::UpdateOrCreate([
                    'id' => $this->history_id,
            ],[                    
                   'status' => 'Error',
                   'message' => $text,
                   'end_at'  => now(),
            ]);

        }
    }

    public function uniqueId()
    {
        return $this->history_id;
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware()
    {
        echo "The job (DonationsImportJob) with process history id " . $this->history_id . " started at " . now() . PHP_EOL;
        // If you don’t want any overlapping jobs to be released back onto the queue, you can use the dontRelease method
        return [(new WithoutOverlapping($this->history_id))->dontRelease()];
    }

}
