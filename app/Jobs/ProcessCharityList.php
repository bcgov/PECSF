<?php

namespace App\Jobs;

use App\Imports\CharitiesImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ProcessCharityList implements ShouldQueue, ShouldBeUniqueUntilProcessing
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The Unique Name of the File.
     *
     * @string FileName
     */
    public $file_name;

    /**
     * The Path of the File.
     *
     * @string FileName
     */
    public $file_path;

    /**
     * The Size of the File.
     *
     * @string FileName
     */
    public $file_size;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($file_path,$file_name,$file_size)
    {
        $this->file_name = $file_name;
        $this->file_path = $file_path;
        $this->file_size = $file_size;

        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        try {
            Excel::import(new CharitiesImport, $this->file_path);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
        }
        echo now() . PHP_EOL;

        return 0;
    }


    public function uniqueId()
    {
        return $this->file_name;
    }
}
