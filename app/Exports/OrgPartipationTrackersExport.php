<?php

namespace App\Exports;

// use App\Models\EmployeeJob;
use App\Models\BusinessUnit;
use App\Models\ProcessHistory;
use App\Models\EligibleEmployeeDetail;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\BeforeExport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\RowDimension;

use Maatwebsite\Excel\Concerns\WithColumnFormatting;


ini_set('memory_limit', '-1');          // To avoid "PHP Fatal error:  Allowed memory size of xxxxx bytes exhausted"

class OrgPartipationTrackersExport implements FromQuery, WithHeadings, WithColumnWidths, WithStyles,
                ShouldAutoSize, WithColumnFormatting, WithMapping, WithEvents
{
    use Exportable;

    private $current_row = 11;

    public function __construct($history_id, $filters)
    {
        //
        $this->history_id = $history_id;
        $this->filters = $filters;
        $this->total_count = 0;
        $this->row_count = 0;

        $this->year = array_key_exists('year', $this->filters) ? $this->filters['year'] : '';
        $this->title = array_key_exists('title', $this->filters) ? $this->filters['title'] : '';
        
        $this->sql = EligibleEmployeeDetail::select('deptid', 'dept_name', DB::raw('deptid, dept_name, count(*) as count'))
                                    ->where('organization_code', 'GOV')
                                    ->where('business_unit', $this->filters['business_unit_code'])
                                    ->where('as_of_date', $this->filters['as_of_date'])
                                    ->groupBy('deptid', 'dept_name')
                                    ->orderBy('deptid');
    }

    public function query()
    {

        return $this->sql;

    }

    public function headings(): array
    {
        return [    
                    [
                        'Organizational Participation Tracker',
                    ],
                    [
                        ' ',
                        $this->year,
                    ],
                    // [   
                    //     "This tracker is provided to encourage and determine participation by department/branch during the PECSF fall awareness campaign.  

                    // PECSF will recognize departments/branches achieving 2/3 or 100% participation. You can let us know you'd like these certificates by emailing the completed tracker back to PECSF HQ at PECSF@gov.bc.ca."
                    
                    // ],
                    [
                        "This optional tracking document can be used by you to track participation % by Department/Paylist for the remainder of the PECSF campaign!\n\nAt the end of campaign, you may also submit your completed tracker and PECSF HQ will recognize departments/branches achieving 2/3 or 100% participation.",


                    ],
                    [
                        "Completed trackers can be sent to PECSF HQ at PECSF@gov.bc.ca",
                    ],
                    [
                    ],
                    [ 
                    ],
                    [   
                        'Ministry/Organization',
                        $this->title,
                        

                    ],
                    [   
                        'Coordinator',
                        "",
                    ],
                    [   
                    ],
                    [   
                    ],
                    [
                        
                        'Dept ID',
                        'Department/Branch',
                        'Eligible',
                        'Actuals',
                        '%',
                    ],
                ];
    }

    public function map($employee): array
    {

        $this->current_row++;

        return [

            $employee->deptid,
            $employee->dept_name,
            $employee->count,
            '',
            '=IFERROR(round((D'. $this->current_row . ' / C' . $this->current_row . '),2),"")',
        ];
    }

    public function styles(Worksheet $sheet)
    {

        // Tuen Off gridlines
        $sheet->setShowGridlines(false);

        // Heading -- Row 1
        $sheet->mergeCells('A1:F1'); 
        $sheet->getStyle('A1')->applyFromArray(
            [
                'font' => [
                    'name' => 'Arial',
                    'size' =>  16,
                    'bold' => true,
                    'italic' => false,
                    // 'underline' => Font::UNDERLINE_DOUBLE,
                    // 'strikethrough' => false,
                    'color' => [
                        'rgb' => 'FFFFFF'
                    ]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => '003366']
                ],
            ],
        );
        $sheet->getRowDimension(1)->setRowHeight(20.25);

        // Heading -- Row 2
        $sheet->getStyle('A2:F2')->applyFromArray(
            [
                'font' => [
                    'name' => 'Arial',
                    'size' =>  16,
                    'bold' => true,
                    'italic' => false,
                    'color' => [
                        'rgb' => 'FFFFFF'
                    ]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                    'vertical' => Alignment::VERTICAL_BOTTOM,
                    'wrapText' => true,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => '004b8d']
                ],
            ],
        );
        $sheet->getRowDimension(2)->setRowHeight(26.25);


        // Heading -- Row 3
        $sheet->mergeCells('A3:F3');
        $sheet->getStyle('A3:F3')->applyFromArray(
            [
                'font' => [
                    'name' => 'Arial',
                    'size' =>  12,
                    'bold' => false,
                    'italic' => false,
                    'color' => [
                        'rgb' => '000000'
                    ]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => 'dce6f1']
                ],
            ],
        );
        $sheet->getRowDimension(3)->setRowHeight(86);

        // Heading -- Row 4
        $sheet->mergeCells('A4:F4');
        $sheet->getStyle('A4:F4')->applyFromArray(
            [
                'font' => [
                    'name' => 'Arial',
                    'size' =>  12,
                    'bold' => true,
                    'italic' => false,
                    'color' => [
                        'rgb' => '000000'
                    ]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => 'dce6f1']
                ],
            ],
        );
        $sheet->getRowDimension(4)->setRowHeight(27);


        // Heading -- Row 7 Column A
        $sheet->getStyle('A7:A8')->applyFromArray(
            [
                'font' => [
                    'name' => 'Arial',
                    'size' =>  10,
                    'bold' => false,
                    'italic' => true,
                    'color' => [
                        'rgb' => '000000'
                    ]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                    'vertical' => Alignment::VERTICAL_BOTTOM,
                    'wrapText' => false,
                ],
            ],
        );
        // Heading -- Row 7 Column B
        $sheet->mergeCells('B7:C7');
        $sheet->mergeCells('B8:C8');
        $sheet->getStyle('B7:C8')->applyFromArray(
            [
                'font' => [
                    'name' => 'Arial',
                    'size' =>  10,
                    'bold' => false,
                    'italic' => true,
                    'color' => [
                        'rgb' => '000000'
                    ]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_BOTTOM,
                    'wrapText' => true,
                ],
            ],
          
        );
        $sheet->getStyle('B7:C7')->applyFromArray(
            [
                'borders' => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '6c757d'],
                    ]
                ],
            ],
        );
        $sheet->getStyle('B8:C8')->applyFromArray(
            [
                'borders' => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '6c757d'],
                    ]
                ],
            ],
        );
        $sheet->getRowDimension(7)->setRowHeight(47);
        $sheet->getRowDimension(8)->setRowHeight(31.5);

        // Heading -- Row 11
        $sheet->getStyle('A11:E11')->applyFromArray(
            [
                'font' => [
                    'name' => 'Arial',
                    'size' =>  10,
                    'bold' => true,
                    'italic' => false,
                    'color' => [
                        'rgb' => 'FFFFFF'
                    ]
                ],
                'alignment' => [
                    // 'horizontal' => Alignment::HORIZONTAL_RIGHT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    // 'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '6c757d'],
                        
                    ],
                ],      
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => '808080']
                ],
            ],
        );
        $sheet->getRowDimension(11)->setRowHeight(36);

        //
        // Data Rows 
        //
        $start = 12;
        $end = $start + $this->row_count - 1;
        $sheet->getStyle('C11:C' . $end )->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D11:D' . $end )->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E11:E' . $end )->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E'. $start . ':E' . $end )->getNumberFormat()->setFormatCode('0.00%');

        $sheet->getStyle('D'. $start .':D'. $end )->getFill()->applyFromArray(
                    [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['argb' => 'DCE6F1']
                     ]
        );

        $sheet->getStyle('A11:E' . $end )->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '6c757d'],
                ],
            ],
        ])->getAlignment()->setWrapText(true);

        $sheet->getStyle('A'. $end+1 .':E' . $end+1 )->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => '808080']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '6c757d'],
                ],
            ],
        ]);

        return [
            // Style the first row as bold text.
            // 1    => ['font' => ['bold' => true]],
            // 2    => ['font' => ['bold' => true], 'background'],
            // 4    => ['font' => ['bold' => true]],
            // 7    => ['font' => ['bold' => true]],

            // // Styling a specific cell by coordinate.
            // 'B2' => ['font' => ['italic' => true]],

            // Styling an entire column.
            // 'A2'  => ['font' => ['size' => 16]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 19,
            'B' => 30,            
            'C' => 10,            
            'D' => 10,    
            'E' => 10,    
        ];
    }

    public function columnFormats(): array
    {
        return [
            // 'B' => NumberFormat::FORMAT_NUMBER ,
            // 'C' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,  // FORMAT_CURRENCY_USD,
            'C' => NumberFormat::FORMAT_NUMBER ,
            'D' => NumberFormat::FORMAT_NUMBER ,
            'E' => NumberFormat::FORMAT_NUMBER ,
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function (BeforeExport $event) {
                // $totalRows = $event->getReader()->getTotalRows();
                $rows = $this->sql->get();

                $this->total_count = $rows->sum('count');
                $this->row_count = $rows->count();
                
                ProcessHistory::UpdateOrCreate([
                    'id' => $this->history_id,
                ],[
                    'total_count' => $this->total_count,
                    'done_count' => 0,
                    'status' => 'Processing',
                    'start_at' => now(),
                ]);

            },
            AfterSheet::class    => function(AfterSheet $event) {
                // $event->sheet->getDelegate()->setRightToLeft(true);


                // Output Summary 
                $sheet = $event->sheet;
                $t = $this->current_row + 5;

                $sheet->setCellValue('A' . $t, 'Organizational Totals');
                $sheet->setCellValue('A' . $t+1, '# of Eligible Donors');
                $sheet->setCellValue('A' . $t+2, '# of Actual Donors');
                $sheet->setCellValue('A' . $t+3, '% of Participation');

                $sheet->setCellValue('B' . $t, ' ');
                $sheet->setCellValue('B' . $t+1, $this->total_count);
                $sheet->setCellValue('B' . $t+2, '=IFERROR(sum(D12:D'. $this->current_row .'),"")' );
                $sheet->setCellValue('B' . $t+3, '=IFERROR(ROUND((B'. $t+2 .' / B'. $t+1 .'),2),"")' );
                $sheet->getStyle('B'. $t+3)->getNumberFormat()->setFormatCode('0.00%');

                // Styling Summary 
                $row1 = 'A'. $t .':B' .$t;
                $sheet->mergeCells( $row1 );
                $sheet->getStyle( $row1 )->applyFromArray(
                    [
                        'font' => [
                            'name' => 'Arial',
                            'size' =>  12,
                            'bold' => true,
                            'italic' => false,
                            'color' => [
                                'rgb' => '000000'
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                            // 'wrapText' => true,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => '6c757d'],
                            ],
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'color' => ['argb' => 'DCE6F1']
                        ],
                    ],
                );
                $sheet->getRowDimension( $t )->setRowHeight(22);

                $row2 = 'A'. $t+1 .':B'. $t+3;
                $sheet->getStyle( $row2 )->applyFromArray(
                    [
                        'font' => [
                            'name' => 'Arial',
                            'size' =>  10,
                            'bold' => true,
                            'italic' => false,
                            'color' => [
                                'rgb' => '000000'
                            ]
                        ],
                        'alignment' => [
                            // 'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical' => Alignment::VERTICAL_BOTTOM,
                            // 'wrapText' => true,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => '6c757d'],
                            ],
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'color' => ['argb' => 'DCE6F1']
                        ],
                    ],
                );
                $sheet->getRowDimension( $t+1 )->setRowHeight(22);
                $sheet->getRowDimension( $t+2 )->setRowHeight(22);
                $sheet->getRowDimension( $t+3 )->setRowHeight(22);

                $sheet->getStyle('B7');

                // Update Process History 
                ProcessHistory::UpdateOrCreate([
                    'id' => $this->history_id,
                ],[
                    'status' => 'Completed',
                    'message' => 'Exported completed',
                    'end_at' => now(),
                ]);

                // Clean Up files over 14 days
                $retention_days = env('REPORT_RETENTION_DAYS') ?: 14;
                $prcs = ProcessHistory::where('id', $this->history_id)->first();

                $file_names = ProcessHistory::where('process_name', $prcs->process_name)
                                ->whereBetween('updated_at', [ today()->subdays( $retention_days + 90), today()->subdays( $retention_days + 1), ])
                                ->pluck('filename')
                                ->toArray();

                Storage::disk('public')->delete( $file_names );
            },

        ];
    }
   

}
