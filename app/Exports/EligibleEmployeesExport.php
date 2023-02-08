<?php

namespace App\Exports;

use App\Models\EmployeeJob;
use App\Models\ProcessHistory;
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

        $this->sql = EmployeeJob::with('organization','bus_unit','region')
                            ->where( function($query) {
                                $query->where('employee_jobs.empl_rcd', '=', function($q) {
                                        $q->from('employee_jobs as J2') 
                                            ->whereColumn('J2.emplid', 'employee_jobs.emplid')
                                            // ->where('J2.empl_status', 'A')
                                            ->selectRaw('min(J2.empl_rcd)');
                                    })
                                    ->orWhereNull('employee_jobs.empl_rcd');
                            })
                            ->when( $filters['emplid'], function($query) use($filters) {
                                $query->where('employee_jobs.emplid', 'like', '%'. $filters['emplid'] .'%');
                            })
                            ->when( $filters['name'], function($query) use($filters) {
                                $query->where('employee_jobs.name', 'like', '%'. $filters['name'] .'%');
                            })
                            ->when( $filters['empl_status'], function($query) use($filters) {
                                $query->where('employee_jobs.empl_status', $filters['empl_status']);
                            })
                            ->when( $filters['office_city'], function($query) use($filters) {
                                $query->where('employee_jobs.office_city', $filters['office_city']);
                            })
                            ->when( $filters['organization'], function($query) use($filters) {
                                $query->where('employee_jobs.organization', $filters['organization']);
                            })
                            ->when( $filters['business_unit'], function($query) use($filters) {
                                $query->where( function($q) use($request) {
                                    $q->where('employee_jobs.business_unit', $filters['business_unit'])
                                      ->orWhereExists(function ($q) use($filters) {
                                          $q->select(DB::raw(1))
                                            ->from('business_units')
                                            ->whereColumn('business_units.code', 'employee_jobs.business_unit')
                                            ->where('business_units.name', 'like', '%'. $filters['business_unit'] .'%');
                                        });
                                });
                            })
                            ->when( $filters['department'], function($query) use($filters) {
                                $query->where( function($q) use($request) {
                                    return $q->where('employee_jobs.deptid', 'like', '%'. $filters['department'] .'%')
                                             ->orWhere('employee_jobs.dept_name', 'like', '%'. $filters['department'] .'%');
                                });
                            })
                            ->when( $filters['tgb_reg_district'], function($query) use($filters) {
                                // $query->where('employee_jobs.tgb_reg_district', $request->tgb_reg_district);
                                $query->where( function($q) use($request) {
                                    $q->where('employee_jobs.tgb_reg_district', $filters['tgb_reg_district'])
                                      ->orWhereExists(function ($q) use($filters) {
                                          $q->select(DB::raw(1))
                                            ->from('regions')
                                            ->whereColumn('regions.code', 'employee_jobs.tgb_reg_district')
                                            ->where('regions.name', 'like', '%'. $filters['tgb_reg_district'] .'%');
                                        });
                                });
                            })
                            ->select('employee_jobs.*');

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
            $employee->bus_unit->name,
            $employee->deptid,
            $employee->dept_name,
            $employee->tgb_reg_district,
            $employee->region->name,
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
