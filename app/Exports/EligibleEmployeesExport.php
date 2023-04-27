<?php

namespace App\Exports;

// use App\Models\EmployeeJob;
use App\Models\ProcessHistory;
use App\Models\EligibleEmployeeDetail;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;

use Maatwebsite\Excel\Events\BeforeExport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

ini_set('memory_limit', '-1');          // To avoid "PHP Fatal error:  Allowed memory size of xxxxx bytes exhausted"

class EligibleEmployeesExport implements FromQuery, WithHeadings, WithMapping, WithEvents
{
    use Exportable;

    public function __construct($history_id, $filters)
    {
        //
        $this->history_id = $history_id;
        $this->filters = $filters;

        $this->sql = EligibleEmployeeDetail::with('related_region')
                            ->when( $filters['year'], function($query) use($filters) {
                                $query->where('eligible_employee_details.year', $filters['year']);
                            })
                            ->when( $filters['emplid'], function($query) use($filters) {
                                $query->where('eligible_employee_details.emplid', 'like', '%'. $filters['emplid'] .'%');
                            })
                            ->when( $filters['name'], function($query) use($filters) {
                                $query->where('eligible_employee_details.name', 'like', '%'. $filters['name'] .'%');
                            })
                            // ->when( $filters['empl_status'], function($query) use($filters) {
                            //     $query->where('eligible_employee_details.empl_status', $filters['empl_status']);
                            // })
                            ->when( $filters['office_city'], function($query) use($filters) {
                                $query->where('eligible_employee_details.office_city', $filters['office_city']);
                            })
                            ->when( $filters['organization'], function($query) use($filters) {
                                $query->where('eligible_employee_details.organization_name', $filters['organization']);
                            })
                            ->when( $filters['business_unit'], function($query) use($filters) {
                                $query->where( function($q) use($filters) {
                                    $q->where('eligible_employee_details.business_unit', 'like', '%'. $filters['business_unit'] .'%')
                                      ->orWhere('eligible_employee_details.business_unit_name', 'like', '%'. $filters['business_unit'] .'%');
                                });
                            })
                            ->when( $filters['department'], function($query) use($filters) {
                                $query->where( function($q) use($filters) {
                                    return $q->where('eligible_employee_details.deptid', 'like', '%'. $filters['department'] .'%')
                                             ->orWhere('eligible_employee_details.dept_name', 'like', '%'. $filters['department'] .'%');
                                });
                            })
                            ->when( $filters['tgb_reg_district'], function($query) use($filters) {
                                // $query->where('employee_jobs.tgb_reg_district', $request->tgb_reg_district);
                                $query->where( function($q) use($filters) {
                                    $q->where('eligible_employee_details.tgb_reg_district', $filters['tgb_reg_district'])
                                      ->orWhereExists(function ($q) use($filters) {
                                          $q->select(DB::raw(1))
                                            ->from('regions')
                                            ->whereColumn('regions.code', 'eligible_employee_details.tgb_reg_district')
                                            ->where('regions.name', 'like', '%'. $filters['tgb_reg_district'] .'%');
                                        });
                                });
                            })
                            ->select('eligible_employee_details.*');

        $this->total_count = $this->sql->count();

    }

    public function query()
    {

        return $this->sql;

    }

    public function headings(): array
    {
        return ['Emplid', 'Name', 'Status', 'Address1', 'Address2', 
                'City', 'Province', 'Postal', 'Organization_name', 'Business_unit',
                'Business Unit Name', 'Dept ID', 'Dept Name', 'Region', 'Region Name',
               ];
    }

    public function map($employee): array
    {
        return [
            $employee->emplid,
            $employee->name,
            $employee->empl_status,
            $employee->office_address1,
            $employee->office_address2, 
            $employee->office_city,
            $employee->office_stateprovince,
            $employee->office_postal,
            $employee->organization_name,
            $employee->business_unit,
            $employee->business_unit_name,
            $employee->deptid,
            $employee->dept_name,
            $employee->tgb_reg_district,
            $employee->related_region ? $employee->related_region->name : null,
        ];
    }

    public function fields(): array
    {
        return ['emplid', 'name', 'empl_status', 'office_address1', 'office_address2', 
                'office_city', 'office_stateprovince', 'office_postal', 'organization_name', 'business_unit',
                'business_unit_name', 'deptid', 'dept_name', 'tgb_reg_district', 'region_name'
        ];

    }

    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function (BeforeExport $event) {
                // $totalRows = $event->getReader()->getTotalRows();

                ProcessHistory::UpdateOrCreate([
                    'id' => $this->history_id,
                ],[
                    'total_count' => $this->total_count,
                    'done_count' => 0,
                    'status' => 'Processing',
                    'start_at' => now(),
                ]);

            },
        ];
    }


}
