<?php

namespace App\Exports;

use App\Models\PledgeExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ChequeReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnWidths {

    use Exportable;

    public function collection() {
        /* return PledgeCharity::select('charities.*', DB::raw('sum(amount) as CHQAMOUNT'))
        ->groupBy('charity_id')
        ->join('charities', 'charity_id', '=', 'charities.id')->get(); */
        return PledgeExport::query()->get();
    }

    public function headings(): array {
        return [
            'GUID',
            'Employee ID',
            'Employee Name',
            'Type',
            'Date/Time',
            'Goal Amount',
            'Year',
            'Donation Amount',
            'Percent',
            'ORG CRA NAME',
            'CRABN',
            'Supported Program',
            'TEXTSTRE1',
            'TEXTSTRE2',
            'NAMECITY',
            'CODESTTE',
            'CODEPSTL',
            'NAMECTAC',
            'TITLECTAC'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 15,
            'G' => 15,
            'H' => 15,
            'I' => 15,
            'J' => 15,
            'K' => 15,
            'L' => 15,
            'M' => 15,
        ];
    }
}