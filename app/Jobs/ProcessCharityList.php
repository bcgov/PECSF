<?php

namespace App\Jobs;

use App\Imports\CharitiesImport;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Maatwebsite\Excel\Facades\Excel;

class ProcessCharityList implements ShouldQueue, ShouldBeUnique
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $uploadFilePath;
    protected int $history_id;

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
        echo "The job (ProcessCharityList) with history id " . $this->history_id . " started at " . now() . PHP_EOL;
        // If you don’t want any overlapping jobs to be released back onto the queue, you can use the dontRelease method
        return [(new WithoutOverlapping($this->history_id))->dontRelease()];
    }

}
