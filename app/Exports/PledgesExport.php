<?php

namespace App\Exports;

// use App\Models\EmployeeJob;
use App\Models\Pledge;
use App\Models\BusinessUnit;
use App\Models\PledgeStaging;
use App\Models\ProcessHistory;

use App\Models\BankDepositForm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

ini_set('memory_limit', '-1');          // To avoid "PHP Fatal error:  Allowed memory size of xxxxx bytes exhausted"

class PledgesExport implements FromQuery, WithHeadings, WithMapping, WithEvents
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
        $sql = PledgeStaging::where('history_id', $this->history_id)
                        ->with('organization', 'region', 'business_unit', 'created_by');

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
                        'Name',
                        'Org Code',
                        'Org Descr',
                        'Business Unit',
                        'Business Unit Descr',
                        'Statistics Business Unit',
                        'Region',
                        'Region Descr',
                        'City',
                        'Dept',
                        'Dept Descr',
                        'PECSF ID',
                        'Emplid',
                        'Employee name',
                        'Pledge Type',
                        'Pool or Charity',
                        'Type',
                        'Subtype',
                        'Created At',
                        'Updated At',
                        'One-Time Amount',
                        'Bi-Weekly Amount',
                        'Total Amount',
                        'Tran ID',
                    ],
                ];
    }

    public function map($employee): array
    {
        return [

            $employee->calendar_year,
            $employee->created_by ? $employee->created_by->name : '',
            $employee->organization_code,
            $employee->organization->name,
            $employee->business_unit_code,
            $employee->business_unit ? $employee->business_unit->name : '',
            $employee->challenge_bu_code,
            $employee->tgb_reg_district,
            $employee->region ? $employee->region->name : '',
            $employee->city,
            $employee->deptid,
            $employee->dept_name,
            $employee->pecsf_id,
            $employee->emplid,
            $employee->name,
            $employee->pledge_type,
            $employee->pool_type,
            $employee->type,
            $employee->sub_type,
            $employee->created_at,
            $employee->updated_at,
            $employee->one_time_amount,
            $employee->biweekly_amount,
            $employee->amount,
            $employee->pledge_id,
        ];
    }

    // public function fields(): array
    // {
    //     return ['emplid', 'name', 'empl_status', 'office_address1', 'office_address2', 
    //             'office_city', 'office_stateprovince', 'office_postal', 'organization_name', 'business_unit',
    //             'business_unit_name', 'deptid', 'dept_name', 'tgb_reg_district', 'region_name'
    //     ];

    // }

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

                // Populate the staging table
                $this->populate_staging_table( $this->filters );

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

                // Clean up Staging table 
                PledgeStaging::where('history_id', $this->history_id)
                                ->orWhere('updated_at', '<', today() )
                                ->delete(); 

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


    protected function populate_staging_table($filters) 
    {

        // Step 1 -- Annual Biweekly 
        PledgeStaging::insertUsing([
                'history_id','pledge_type','pledge_id','calendar_year','organization_code','emplid','pecsf_id',
                'name','business_unit_code','tgb_reg_district','deptid','dept_name','city',
                'type','sub_type','pool_type','region_id','pledge','one_time_amount', 'biweekly_amount', 'amount',
                'created_by_id','created_at','updated_at',
        ],

            Pledge::selectRaw(" ?, 'Annual', pledges.id
                            ,campaign_years.calendar_year
                            ,organizations.code as organization_code
                            ,pledges.emplid
                            ,pledges.pecsf_id
                            ,CASE WHEN organizations.code = 'GOV'
                                    THEN employee_jobs.name
                                    ELSE CONCAT (pledges.last_name,', ',pledges.first_name)
                            END as name
                            ,pledges.business_unit  
                            ,pledges.tgb_reg_district
                            ,pledges.deptid
                            ,pledges.dept_name
                            ,CASE WHEN organizations.code = 'GOV'
                                    THEN employee_jobs.office_city
                                    ELSE pledges.city
                            END as city
                            ,'Pledge'   AS type
                            ,'Bi-Weekly' AS sub_type
                            ,pledges.type  AS pool_type
                            ,pledges.region_id
                            ,pledges.pay_period_amount AS pledge
                            ,pledges.one_time_amount 
                            ,pledges.goal_amount - pledges.one_time_amount
                            ,pledges.goal_amount
                            ,pledges.created_by_id
                            ,pledges.created_at
                            ,pledges.updated_at
                        ", [ $this->history_id] )
            ->join('campaign_years', 'campaign_years.id', 'pledges.campaign_year_id')
            ->join('organizations', 'organizations.id', 'pledges.organization_id')
            ->leftJoin('employee_jobs', 'employee_jobs.emplid', '=', 'pledges.emplid')
            ->where( function($query) {
                $query->where('employee_jobs.empl_rcd', '=', function($q) {
                        $q->from('employee_jobs as J2') 
                            ->whereColumn('J2.emplid', 'employee_jobs.emplid')
                            ->selectRaw('min(J2.empl_rcd)');
                    })
                    ->orWhereNull('employee_jobs.empl_rcd');
            })
            // ->where('pledges.pay_period_amount', '<>', 0)
            ->whereNull('pledges.deleted_at')
            ->whereNull('pledges.cancelled')
            ->when( $filters['year'], function($query) use($filters) {
                $query->where('campaign_years.calendar_year', $filters['year']);
            })

        );

        // Step 2 -- Annual One-Time
        // PledgeStaging::insertUsing([
        //     'history_id','pledge_type','pledge_id','calendar_year','organization_code','emplid','pecsf_id',
        //     'name','business_unit_code','tgb_reg_district','deptid','dept_name','city',
        //     'type','sub_type','pool_type','region_id','pledge','amount',
        //     'created_by_id','created_at','updated_at',
        // ],
        //     Pledge::selectRaw(" ?, 'Annual', pledges.id
        //                     ,campaign_years.calendar_year
        //                     ,organizations.code as organization_code
        //                     ,pledges.emplid
        //                     ,pledges.pecsf_id
        //                     ,CASE WHEN organizations.code = 'GOV'
        //                             THEN employee_jobs.name
        //                             ELSE CONCAT (pledges.last_name,', ',pledges.first_name)
        //                     END as name
        //                     ,pledges.business_unit  
        //                     ,pledges.tgb_reg_district
        //                     ,employee_jobs.deptid
        //                     ,employee_jobs.dept_name
        //                     ,CASE WHEN organizations.code = 'GOV'
        //                             THEN employee_jobs.office_city
        //                             ELSE pledges.city
        //                     END as city
        //                     ,'Pledge'  AS type
        //                     ,'One-Time'  AS sub_type
        //                     ,pledges.type  AS pool_type
        //                     ,pledges.region_id
        //                     ,pledges.one_time_amount 
        //                     ,pledges.one_time_amount 
        //                     ,pledges.created_by_id
        //                     ,pledges.created_at
        //                     ,pledges.updated_at
        //             ", [ $this->history_id] )
        //     ->join('campaign_years', 'campaign_years.id', 'pledges.campaign_year_id')
        //     ->join('organizations', 'organizations.id', 'pledges.organization_id')
        //     ->leftJoin('employee_jobs', 'employee_jobs.emplid', '=', 'pledges.emplid')
        //     ->where( function($query) {
        //         $query->where('employee_jobs.empl_rcd', '=', function($q) {
        //                 $q->from('employee_jobs as J2') 
        //                     ->whereColumn('J2.emplid', 'employee_jobs.emplid')
        //                     ->selectRaw('min(J2.empl_rcd)');
        //             })
        //             ->orWhereNull('employee_jobs.empl_rcd');
        //     })
        //     ->where('pledges.one_time_amount', '<>', 0)
        //     ->whereNull('pledges.deleted_at')
        //     ->when( $filters['year'], function($query) use($filters) {
        //         $query->where('campaign_years.calendar_year', $filters['year']);
        //     })

        // );

        // Step 3 -- Event 
        PledgeStaging::insertUsing([
            'history_id','pledge_type','pledge_id','calendar_year','organization_code','emplid','pecsf_id',
            'name','business_unit_code','tgb_reg_district','deptid','dept_name','city',
            'type','sub_type','pool_type','region_id','pledge','one_time_amount', 'biweekly_amount', 'amount',
            'created_by_id','created_at','updated_at',
        ],

            BankDepositForm::selectRaw(" ?, 'Event', bank_deposit_forms.id
                            ,campaign_years.calendar_year
                            ,bank_deposit_forms.organization_code
                            ,bank_deposit_forms.bc_gov_id
                            ,bank_deposit_forms.pecsf_id
                            ,CASE WHEN bank_deposit_forms.organization_code = 'GOV'
                                THEN employee_jobs.name
                                ELSE bank_deposit_forms.employee_name
                            END
                            ,(select code from business_units where id = bank_deposit_forms.business_unit limit 1)
                            ,regions.code
                            ,bank_deposit_forms.deptid
                            ,bank_deposit_forms.dept_name
                            ,CASE WHEN bank_deposit_forms.organization_code = 'GOV'
                                THEN employee_jobs.office_city
                                ELSE bank_deposit_forms.address_city
                            END
                            ,bank_deposit_forms.event_type  
                            ,bank_deposit_forms.sub_type    
                            ,CASE when regional_pool_id is not NULL 
                                THEN 'P'
                                ELSE 'C'
                            END                             
                            ,regions.id                              
                            ,bank_deposit_forms.deposit_amount
                            ,bank_deposit_forms.deposit_amount
                            ,0.0
                            ,bank_deposit_forms.deposit_amount
                            ,bank_deposit_forms.form_submitter_id      
                            ,bank_deposit_forms.created_at
                            ,bank_deposit_forms.updated_at
                                    ", [ $this->history_id] )
                ->join('campaign_years', 'campaign_years.id', 'bank_deposit_forms.campaign_year_id')                                    
                ->join('regions', 'regions.id', 'bank_deposit_forms.region_id')
                ->leftJoin('employee_jobs', 'employee_jobs.emplid', '=', 'bank_deposit_forms.bc_gov_id')
                ->where( function($query) {
                    $query->where('employee_jobs.empl_rcd', '=', function($q) {
                            $q->from('employee_jobs as J2') 
                                ->whereColumn('J2.emplid', 'employee_jobs.emplid')
                                ->selectRaw('min(J2.empl_rcd)');
                        })
                        ->orWhereNull('employee_jobs.empl_rcd');
                })
                ->where('bank_deposit_forms.approved', 1)
                ->whereNull('bank_deposit_forms.deleted_at')
                ->when( $filters['year'], function($query) use($filters) {
                    $query->where('campaign_years.calendar_year', $filters['year']);
                })

        );

        // Update Challenge Business Unit
        $pledges = PledgeStaging::where('history_id', $this->history_id)->get();    

        foreach ($pledges as $pledge) {

            $bu = BusinessUnit::where('code', $pledge->business_unit_code)->first();
            $business_unit_code = $bu ? $bu->linked_bu_code : null;
            if ($business_unit_code == 'BC022' && str_starts_with($pledge->dept_name, 'GCPE')) {
                $business_unit_code  = 'BGCPE';
            }
            $pledge->challenge_bu_code = $business_unit_code;
            $pledge->save();

        }

        // Total Count
        $this->total_count = PledgeStaging::where('history_id', $this->history_id)->count();    

    }


}
