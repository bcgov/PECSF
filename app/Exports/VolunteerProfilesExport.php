<?php

namespace App\Exports;

// use App\Models\EmployeeJob;

use App\Models\BusinessUnit;
use App\Models\VolunteerProfile;
use App\Models\ProcessHistory;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\BeforeExport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;




ini_set('memory_limit', '-1');          // To avoid "PHP Fatal error:  Allowed memory size of xxxxx bytes exhausted"

class VolunteerProfilesExport implements FromQuery, WithHeadings, WithMapping, WithEvents, WithColumnFormatting
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

        $filters = $this->filters;

        $sql = VolunteerProfile::with('organization', 'business_unit', 'primary_job')
                    ->when( $filters['year'], function($query) use($filters) {
                        $query->where('campaign_year', $filters['year']);
                    });

        return $sql;

    }

    public function headings(): array
    {
        return [    
                    // [
                    //     'Report title : Annual Pledges and Events',
                    // ],
                    // [
                    //     'Run at : ' . now(),
                    // ],
                    // [
                    //     '',
                    // ],
                    [
                        'Calendar Year',
                        'Organization',
                        'EMPLID',
                        'PECSF ID',
                        'Full Name',

                        'New Registration',
                        'Business Unit',
                        'Business Unit Descr',
                        'Number of years',
                        'Preferred Role',

                        'Use Global Address',
                        'Full Address',
                        'Opt-out recognition',

                        'Created By',
                        'Created At',
                        'Updated By',
                        'Updated At',
                        'Tran ID',
                    ],
                ];
    }

    public function map($row): array
    {
        return [

            $row->campaign_year,
            $row->Organization->name,
            $row->emplid,
            $row->pecsf_id,
            $row->fullname,

            $row->is_renew_profile ? 'No' : 'Yes',

            $row->business_unit_code,
            $row->business_unit ? $row->business_unit->name : '',

            $row->no_of_years,
            $row->preferred_role_name,

            $row->address_type == 'G' ? 'Yes' : 'No',
            $row->full_address,
            $row->opt_out_recongnition == 'Y' ? 'Yes' : 'No',

            $row->created_by ? $row->created_by->name : '',
            $row->created_at,
            $row->updated_by ? $row->updated_by->name : '',
            $row->updated_at,
            $row->id,
        ];
    }

    // public function fields(): array
    // {
    //     return ['emplid', 'name', 'empl_status', 'office_address1', 'office_address2', 
    //             'office_city', 'office_stateprovince', 'office_postal', 'organization_name', 'business_unit',
    //             'business_unit_name', 'deptid', 'dept_name', 'tgb_reg_district', 'region_name'
    //     ];

    // }
    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_TEXT,

        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function (BeforeExport $event) {
                // $totalRows = $event->getReader()->getTotalRows();

                $filters = $this->filters;
                $total_count = VolunteerProfile::when( $filters['year'], function($query) use($filters) {
                    $query->where('campaign_year', $filters['year']);
                })->count();


                ProcessHistory::UpdateOrCreate([
                    'id' => $this->history_id,
                ],[
                    'total_count' => $this->total_count,
                    'done_count' => 0,
                    'status' => 'Processing',
                    'start_at' => now(),
                ]);

                ProcessHistory::UpdateOrCreate([
                    'id' => $this->history_id,
                ],[
                    'total_count' => $this->total_count,
                ]);

            },
            AfterSheet::class    => function(AfterSheet $event) {
                // $event->sheet->getDelegate()->setRightToLeft(true);

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
