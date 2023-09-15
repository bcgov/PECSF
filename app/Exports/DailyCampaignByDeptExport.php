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


class DailyCampaignByDeptExport implements FromCollection, WithHeadings, WithColumnWidths, 
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
        //get business_unit_name
        $business_rows = DailyCampaign::select('business_unit_name')
                        ->where('campaign_year', $this->campaign_year)
                        ->where('as_of_date', $this->as_of_date)
                        ->where('donors', '>=', 1)
                        ->distinct()
                        ->orderBy('business_unit_name')
                        ->get()->toArray();        
        $allRows = collect([]);
        foreach($business_rows as $item){
            if($item['business_unit_name'] != ''){
                $row = $this->deptSubTotal($item['business_unit_name']);
                $allRows = $allRows->concat($row->get());
            }
        }
        $total_row  = DailyCampaign::where('daily_type', 2)
                            ->where('campaign_year', $this->campaign_year)
                            ->where('as_of_date', $this->as_of_date)
                            ->select( 
                                DB::raw("'Total' as business_unit_name"),
                                DB::raw("'' as deptid"),
                                DB::raw("'' as dept_name"),
                                DB::raw("SUM(donors) as donors")
                            )
                            ->get();

            $allRows = $allRows->concat($total_row);
        
        return $allRows;
                            
    }


    private function deptSubTotal($business_unit_name){
        $row = DailyCampaign::where('daily_type', 2)
                            ->where('campaign_year', $this->campaign_year)
                            ->where('as_of_date', $this->as_of_date)
                            ->where('donors', '>=', 1)
                            ->where('business_unit_name', $business_unit_name)
                            ->select('business_unit_name', 'deptid', 'dept_name', 'donors')
                            ->orderBy('business_unit_name')
                            ->orderBy('deptid');
        $sub_total_row  = DailyCampaign::where('daily_type', 2)
                            ->where('campaign_year', $this->campaign_year)
                            ->where('as_of_date', $this->as_of_date)
                            ->where('business_unit_name', $business_unit_name)
                            ->select( 
                                //DB::raw("CONCAT('Sub Total Of ', business_unit_name) as business_unit_name"),
                                DB::raw("'Sub Total' as business_unit_name"),
                                DB::raw("'' as deptid"),
                                DB::raw("'' as dept_name"),
                                DB::raw("SUM(donors) as donors")
                            );
        $blank_row  = DailyCampaign::where('daily_type', 2)
                            ->where('campaign_year', $this->campaign_year)
                            ->where('as_of_date', $this->as_of_date)
                            ->where('business_unit_name', $business_unit_name)
                            ->select( 
                                DB::raw("'' as business_unit_name"),
                                DB::raw("'' as deptid"),
                                DB::raw("'' as dept_name"),
                                DB::raw("'' as donors")
                            )
                            ->distinct();                    
          
        $row = $row->unionAll($sub_total_row)->unionAll($blank_row); 
        return $row;
    }

    public function headings(): array
    {
        return [
            [   'Department Report' ],
            [   'Date : ' . $this->as_of_date,    ],
            [   '',  ],
            [
                'Organization Name',
                'Dept ID',
                'Department Name',
                'Donors',
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
            'D' => 20,            
            // 'E' => 20,            
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER ,
            // 'E' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,  // FORMAT_CURRENCY_USD,
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
  
                $event->sheet->getDelegate()->getStyle('A4:E4')
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('dbdbdb');

                $event->sheet->getDelegate()->getStyle('D4:E4')
                        ->getAlignment()
                        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
  
            },
        ];
    }

}
