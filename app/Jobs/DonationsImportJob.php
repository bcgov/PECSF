<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Imports\DonationsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class DonationsImportJob implements ShouldQueue
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
            Excel::import(new DonationsImport( $this->history_id, $this->org_code), $this->uploadFilePath );

            \App\Models\ProcessHistory::UpdateOrCreate([
                'id' => $this->history_id,
            ],[                    
                'status' => 'Completed',
                'end_at'  => now(),
            ]);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            // $text = '';
            // foreach ($failures as $failure) {
            //     $text .= $failure->row(); // row that went wrong
            //     $text .= ' - ' . $failure->attribute(); // either heading key (if using heading row concern) or column index
            //     $text .= ' - ' . $failure->errors(); // Actual error messages from Laravel validator
            //     $text .= ' - ' . $failure->values(); // The values of the row that has failed.
            // }

            \App\Models\ProcessHistory::UpdateOrCreate([
                    'id' => $this->history_id,
            ],[                    
                   'status' => 'Error',
                   'message' => $failures,
                   'end_at'  => now(),
            ]);

        }
    }
}
