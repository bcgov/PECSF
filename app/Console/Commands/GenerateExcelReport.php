<?php

namespace App\Console\Commands;

use App\Exports\ChequeReportExport;
use App\Models\Pledge;
use App\Models\PledgeCharity;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class GenerateExcelReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:excelreport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $fileName = $this->generateExcel();
        echo "Generated $fileName\n\r";
        return 0;
    }

    private function generateExcel() {
        $fileName = Carbon::now()->format('YmdHis') . ".xlsx";
        Excel::store(new ChequeReportExport(), $fileName , 'report', \Maatwebsite\Excel\Excel::XLSX);
        return $fileName;
    }

    private function markAsGenerated($pledges) {
        foreach ($pledges as $pledge) {
            $pledge->markAsGenerated()->save();
        }
    }

    public function generateHeader($totalRecords, $totalAmount) {

        $date = Carbon::now()->format('Ymd');
        $fileNumber = str_pad("1", 2, '0', STR_PAD_LEFT);
        
        $record = str_pad($totalRecords, 6, '0', STR_PAD_LEFT);
        $amount = str_pad(
            number_format($totalAmount, 2, ".","")
        , 11, '0', STR_PAD_LEFT);

        return $date.$fileNumber.$record.$amount;
    }

    public function generateRecord($pledgeCharity, $pledge) {
        $companyId = "GOV";
        $employeeId = $pledge->user->id;
        $name = $pledge->user->name;
        $deductionCode = $pledge->frequency === 'one time' ? 'PECSF1' : 'PECSF';
        $startDate = $pledge->created_at->format('Ymd');
        $endDate = $pledge->frequency === 'one time' ? '        ' : $pledge->created_at->addWeeks(26)->format('Ymd');
        $deductionAmount = $pledgeCharity->amount;
        $goalAmount = $pledgeCharity->goal_amount;

        $formatted = [
            'companyId' => $companyId,
            'employeeId' => str_pad(
                str_pad($employeeId, 6, '0', STR_PAD_LEFT),
                12, ' ', STR_PAD_RIGHT
            ),
            'name' => str_pad($name, 50, ' ', STR_PAD_RIGHT),
            'deduction' => str_pad($deductionCode, 6, ' ', STR_PAD_RIGHT),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'deductionAmount' => str_pad(
                number_format($deductionAmount, 2, ".", ""),
                9, '0', STR_PAD_LEFT                     // 6.2 => 9
            ),
            'goalAmount' => str_pad(
                number_format($goalAmount, 2, ".", ""),
                11, '0', STR_PAD_LEFT                    // 8.2 => 11
            )

        ];

        return join("", $formatted);
    }
}
