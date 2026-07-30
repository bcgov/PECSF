<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Imports\VolunteerProfilesUnifiedImport;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\Middleware\WithoutOverlapping;

class VolunteerProfilesImportJob implements ShouldQueue, ShouldBeUnique
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $uploadFilePath;
    protected $history_id;
    protected $campaign_year;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($uploadFilePath, $history_id, $campaign_year)
    {
        $this->uploadFilePath = $uploadFilePath;
        $this->history_id = $history_id;
        $this->campaign_year = $campaign_year;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Use unified importer that handles both Government and Non-Government employees
            Excel::import(new VolunteerProfilesUnifiedImport($this->history_id, $this->campaign_year), $this->uploadFilePath);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            $history = \App\Models\ProcessHistory::where('id', $this->history_id)->first();

            $text = 'Process ID : ' . $this->history_id . PHP_EOL;
            $text .= 'Process parameters : ' . ($history ? $history->parameters : '') . PHP_EOL;
            $text .= PHP_EOL;
            $text .= 'Validation Errors Found: ' . PHP_EOL;
            $text .= 'Note: Field position starts from 0 (0 -> Column A, 1 -> Column B, 2 -> Column C, etc)' . PHP_EOL;
            $text .= PHP_EOL;

            foreach ($failures as $failure) {
                $text .= 'Row : ' . $failure->row();
                $text .= ' - ' . $failure->attribute();
                $text .= ' - ' . implode(', ', $failure->errors());
                $text .= ' - ' . implode(', ', $failure->values());
                $text .= PHP_EOL;
            }

            \App\Models\ProcessHistory::UpdateOrCreate([
                    'id' => $this->history_id,
            ], [
                   'status' => 'Error',
                   'message' => $text,
                   'end_at' => now(),
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
        echo "The job (VolunteerProfilesImport) with process history id " . $this->history_id . " started at " . now() . PHP_EOL;
        // If you don’t want any overlapping jobs to be released back onto the queue, you can use the dontRelease method
        return [(new WithoutOverlapping($this->history_id))->dontRelease()];
    }

}
