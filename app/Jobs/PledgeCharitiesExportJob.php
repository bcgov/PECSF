<?php

namespace App\Jobs;

// use App\Models\EmployeeJob;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Models\ScheduleJobAudit;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Exports\PledgeCharitiesExport;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Maatwebsite\Excel\Facades\Excel;

class PledgeCharitiesExportJob implements ShouldQueue, ShouldBeUnique
{
    use  Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $history_id;
    protected $filters;     // array of $this->filters['all() 
    protected $filename;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($history_id, $filename, $filters)
    {
        //
        $this->history_id = $history_id;
        $this->filename = $filename;
        $this->filters = $filters;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        // (new PledgesExport($this->history_id, $this->filters) )->store('public/'.$this->filename);
        Excel::store(new PledgeCharitiesExport($this->history_id, $this->filters), 'public/'.$this->filename);  
        
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
        echo "The job (PledgeCharitiesExportJob) process with history id " . $this->history_id . " started at " . now() . PHP_EOL;
        // If you don’t want any overlapping jobs to be released back onto the queue, you can use the dontRelease method
        return [(new WithoutOverlapping($this->history_id))->dontRelease()];
    }

}
