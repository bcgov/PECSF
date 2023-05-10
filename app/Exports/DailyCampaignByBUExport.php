<?php

namespace App\Exports;

use App\Models\DailyCampaign;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;


class DailyCampaignByBUExport implements FromCollection, WithHeadings, WithColumnWidths, 
            WithStyles, ShouldAutoSize, WithColumnFormatting, WithEvents
{


    public function __construct($campaign_year, $as_of_date)
    {
        //
        $this->campaign_year = $campaign_year;
        $this->as_of_date = $as_of_date;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
        $rows = DailyCampaign::where('daily_type', 0)
                            ->where('campaign_year', $this->campaign_year)
                            ->where('as_of_date', $this->as_of_date)
                            ->where('donors', '>=', 5)
                            ->select('business_unit_name', 'donors', 'dollars')
                            ->orderBy('business_unit_name')
                            ->get();

        $total_row  = DailyCampaign::where('daily_type', 0)
                            ->where('campaign_year', $this->campaign_year)
                            ->where('as_of_date', $this->as_of_date)
                            ->select( 
                                DB::raw("'Total' as business_unit_name"),
                                DB::raw("SUM(donors) as donors"),
                                DB::raw("SUM(dollars) as dollars")
                            )
                            ->get();
                              
        return $rows->mergeRecursive($total_row);
                            
    }

    public function headings(): array
    {
        return [
            [   'Organizational Report' ],
            [   'Date : ' . $this->as_of_date,    ],
            [   '',  ],
            [
                'Organization Name',
                'Donors',
                'Dollars',
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
            2    => ['font' => ['bold' => true], 'background'],
            4    => ['font' => ['bold' => true]],

            // // Styling a specific cell by coordinate.
            // 'B2' => ['font' => ['italic' => true]],

            // Styling an entire column.
            'A1'  => ['font' => ['size' => 16]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            // 'A' => 60,
            'B' => 20,            
            'C' => 20,            
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER ,
            'C' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,  // FORMAT_CURRENCY_USD,
        ];
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
  
                $event->sheet->getDelegate()->getStyle('A4:C4')
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('dbdbdb');

                $event->sheet->getDelegate()->getStyle('B4:C4')
                        ->getAlignment()
                        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
  
            },
        ];
    }

}
