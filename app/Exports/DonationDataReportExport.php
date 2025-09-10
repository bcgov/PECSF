<?php

namespace App\Exports;

// use App\Models\EmployeeJob;

use App\Models\Donation;

use App\Models\ProcessHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithMapping;

use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;


ini_set('memory_limit', '-1');          // To avoid "PHP Fatal error:  Allowed memory size of xxxxx bytes exhausted"

class DonationDataReportExport implements FromQuery, WithHeadings, WithMapping, WithEvents,
                WithColumnWidths, WithStyles, ShouldAutoSize, WithColumnFormatting

{
    use Exportable;

    public function __construct($history_id, $filters)
    {
        //
        $this->history_id = $history_id;
        $this->filters = $filters;
        $this->total_count = 0;

    }

    public function query()
    {

        // return $this->sql;
        // $sql = PledgeCharityStaging::where('history_id', $this->history_id)
        //                 ->with('organization', 'region', 'business_unit', 'created_by');
        $filters = $this->filters;

        $sql = Donation::with('organization', 'process_history', 'process_history.created_by', 'process_history.updated_by')
                    ->when( $filters['year'], function($query) use($filters) {
                        $query->where('yearcd', $filters['year']);
                    });

        return $sql;

    }

    public function headings(): array
    {
        return [
                    [
                        'Report title : Donation Data Report',
                    ],
                    [
                        'Run at : ' . now(),
                    ],
                    [
                        '',
                    ],
                    [    
                        'Tran ID',
                        'Org Code',
                        'Org Descr',
                        'PECSF ID',
                        'Name',
                        'Calendar Year',
                        'Pay End Date',
                        'Source Type',
                        'Frequency',
                        'Amount',
                        'Process ID',
                        'Process Status',
                        'Process End Time',
                        'Created by',
                        'Created at',
                        'Updated by',
                        'Updated at',
                    ]
                ];
    }

    public function map($row): array
    {
        return [

            $row->id,
            $row->org_code,
            $row->organization->name,
            $row->pecsf_id,
            $row->name,

            $row->yearcd,
            $row->pay_end_date,
            $row->source_type_descr,
            $row->frequency,
            $row->amount,
            $row->process_history_id,
            $row->process_history->status,
            $row->process_history->end_at,
            $row->process_history ? $row->process_history->created_by->name : '',
            $row->created_at,
            $row->process_history ? $row->process_history->updated_by->name : '',
            $row->updated_at
        ];
    }

    // public function fields(): array
    // {
    //     return ['emplid', 'name', 'empl_status', 'office_address1', 'office_address2', 
    //             'office_city', 'office_stateprovince', 'office_postal', 'organization_name', 'business_unit',
    //             'business_unit_name', 'deptid', 'dept_name', 'tgb_reg_district', 'region_name'
    //     ];

    // }
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
            'A' => 10,
            'B' => 20,            
            'C' => 20,            
        ];
    }

    public function columnFormats(): array
    {
        return [
            // 'J' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,  // FORMAT_CURRENCY_USD,
            // 'K' => NumberFormat::FORMAT_NUMBER ,
        ];
    }


    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function (BeforeExport $event) {
                // $totalRows = $event->getReader()->getTotalRows();

                $filters = $this->filters;
                $total_count = Donation::when( $filters['year'], function($query) use($filters) {
                    $query->where('yearcd', $filters['year']);
                })->count();


                ProcessHistory::UpdateOrCreate([
                    'id' => $this->history_id,
                ],[
                    'total_count' => $total_count,
                    'done_count' => 0,
                    'status' => 'Processing',
                    'start_at' => now(),
                ]);



                // ProcessHistory::UpdateOrCreate([
                //     'id' => $this->history_id,
                // ],[
                //     'total_count' => $this->total_count,
                // ]);

            },
            AfterSheet::class    => function(AfterSheet $event) {
                // $event->sheet->getDelegate()->setRightToLeft(true);

                $event->sheet->getDelegate()->getStyle('A4:C4')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('dbdbdb');

                $event->sheet->getDelegate()->getStyle('B4:C4')
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


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
