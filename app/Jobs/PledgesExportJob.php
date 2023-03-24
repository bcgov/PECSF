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
use App\Exports\PledgesExport;
use Maatwebsite\Excel\Facades\Excel;

class PledgesExportJob implements ShouldQueue
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
        Excel::store(new PledgesExport($this->history_id, $this->filters), 'public/'.$this->filename);  
        
    }
}
