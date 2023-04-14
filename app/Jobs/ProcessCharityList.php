<?php

namespace App\Jobs;

use App\Imports\CharitiesImport;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ProcessCharityList implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $uploadFilePath;
    protected $history_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($uploadFilePath, $history_id)    
    {

        $this->uploadFilePath = $uploadFilePath;
        $this->history_id = $history_id;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        try {
            Excel::import(new CharitiesImport($this->history_id), $this->uploadFilePath);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            $history = \App\Models\ProcessHistory::where('id', $this->history_id)->first();

            $text = 'Process ID : ' . $this->history_id . PHP_EOL;
            $text .= 'Process parameters : ' . ($history ?  $history->parameters : '')  . PHP_EOL;
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

}
