<?php

namespace App\Exports;

// use App\Models\EmployeeJob;
use App\Models\Pledge;
use App\Models\PledgeCharityStaging;
use App\Models\ProcessHistory;
use App\Models\BankDepositForm;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

ini_set('memory_limit', '-1');          // To avoid "PHP Fatal error:  Allowed memory size of xxxxx bytes exhausted"

class PledgeCharitiesExport implements FromQuery, WithHeadings, WithMapping, WithEvents
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
        $sql = PledgeCharityStaging::where('history_id', $this->history_id)
                        ->with('organization', 'region', 'business_unit', 'created_by');

        return $sql;



    }

    public function headings(): array
    {
        return [
                    [
                        'Report title : Amount by charity report',
                    ],
                    [
                        'Run at : ' . now(),
                    ],
                    [
                        '',
                    ],
                    [    
                        'Calander Year',
                        'Name',
                        'Org Code',
                        'Org Descr',
                        'Business Unit',
                        'Business Unit Descr',
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
                        'Goal Amount',
                        'Created At',
                        'Updated At',
                        
                        'Percentage',
                        'CRA business number',
                        'CRA org name',
                        'Specific Community Or Initiative',
                        'Charity Amount',

                    ]
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
            $employee->amount,
            $employee->created_at,
            $employee->updated_at,

            $employee->percentage,
            $employee->charity ? $employee->charity->registration_number : '',
            $employee->charity ? $employee->charity->charity_name : '',
            $employee->supported_program,
            $employee->prorate_amount,
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

            },

        ];
    }


    protected function populate_staging_table($filters) 
    {

        // Step 1 -- Annual 
        $this->populate_annual_pledges($filters);

        // Step 2 -- Event 
        $this->populate_event_pledges( $this->filters );

        // Total Count
        $this->total_count = PledgeCharityStaging::where('history_id', $this->history_id)->count();    



        // $pledges =  Pledge::selectRaw("pledges.*
        //                     ,campaign_years.calendar_year
        //                     ,CASE WHEN organizations.code = 'GOV'
        //                             THEN employee_jobs.name
        //                             ELSE CONCAT (pledges.last_name,', ',pledges.first_name)
        //                     END as name
        //                     ,employee_jobs.business_unit as business_unit_code
        //                     ,employee_jobs.tgb_reg_district
        //                     ,employee_jobs.deptid
        //                     ,employee_jobs.dept_name
        //                     ,CASE WHEN organizations.code = 'GOV'
        //                             THEN employee_jobs.office_city
        //                             ELSE pledges.city
        //                     END as city
        //                     ,pledges.type  AS pool_type
        //                 ")
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
        //     ->where('type', 'P')
        //     // ->where('pledges.pay_period_amount', '<>', 0)
        //     ->whereNull('pledges.deleted_at')
        //     ->when( $filters['year'], function($query) use($filters) {
        //         $query->where('campaign_years.calendar_year', $filters['year']);
        //     });


        

        // // Step 2 -- Annual One-Time
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
        //                     ,employee_jobs.business_unit as business_unit_code
        //                     ,employee_jobs.tgb_reg_district
        //                     ,employee_jobs.deptid
        //                     ,employee_jobs.dept_name
        //                     ,CASE WHEN organizations.code = 'GOV'
        //                             THEN employee_jobs.office_city
        //                             ELSE pledges.city
        //                     END as city
        //                     ,'One-Time'   AS type
        //                     ,''            AS sub_type
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

        // // Step 3 -- Event 
        // PledgeStaging::insertUsing([
        //     'history_id','pledge_type','pledge_id','calendar_year','organization_code','emplid','pecsf_id',
        //     'name','business_unit_code','tgb_reg_district','deptid','dept_name','city',
        //     'type','sub_type','pool_type','region_id','pledge','amount',
        //     'created_by_id','created_at','updated_at',
        // ],

        //     BankDepositForm::selectRaw(" ?, 'Event', bank_deposit_forms.id
        //                     ,year(bank_deposit_forms.created_at)
        //                     ,bank_deposit_forms.organization_code
        //                     ,bank_deposit_forms.bc_gov_id
        //                     ,bank_deposit_forms.pecsf_id
        //                     ,CASE WHEN bank_deposit_forms.organization_code = 'GOV'
        //                         THEN employee_jobs.name
        //                         ELSE ''
        //                     END
        //                     ,employee_jobs.business_unit
        //                     ,regions.code
        //                     ,employee_jobs.deptid
        //                     ,employee_jobs.dept_name
        //                     ,CASE WHEN bank_deposit_forms.organization_code = 'GOV'
        //                         THEN employee_jobs.office_city
        //                         ELSE bank_deposit_forms.address_city
        //                     END
        //                     ,bank_deposit_forms.event_type  
        //                     ,bank_deposit_forms.sub_type    
        //                     ,CASE when regional_pool_id is not NULL 
        //                         THEN 'P'
        //                         ELSE 'C'
        //                     END                             
        //                     ,regions.id                              
        //                     ,bank_deposit_forms.deposit_amount
        //                     ,bank_deposit_forms.deposit_amount
        //                     ,bank_deposit_forms.form_submitter_id      
        //                     ,bank_deposit_forms.created_at
        //                     ,bank_deposit_forms.updated_at
        //                             ", [ $this->history_id] )
        //         ->join('regions', 'regions.id', 'bank_deposit_forms.region_id')
        //         ->leftJoin('employee_jobs', 'employee_jobs.emplid', '=', 'bank_deposit_forms.bc_gov_id')
        //         ->where( function($query) {
        //             $query->where('employee_jobs.empl_rcd', '=', function($q) {
        //                     $q->from('employee_jobs as J2') 
        //                         ->whereColumn('J2.emplid', 'employee_jobs.emplid')
        //                         ->selectRaw('min(J2.empl_rcd)');
        //                 })
        //                 ->orWhereNull('employee_jobs.empl_rcd');
        //         })
        //         ->where('bank_deposit_forms.approved', 1)
        //         ->whereNull('bank_deposit_forms.deleted_at')
        //         ->when( $filters['year'], function($query) use($filters) {
        //             $query->whereRaw('year(bank_deposit_forms.created_at) = ' . $filters['year']);
        //         })

        // );


    }


    protected function populate_annual_pledges($filters) {

        $pledges =  Pledge::selectRaw("pledges.*
                    ,campaign_years.calendar_year
                    ,organizations.code as organization_code
                    ,CASE WHEN organizations.code = 'GOV'
                            THEN employee_jobs.name
                            ELSE CONCAT (pledges.last_name,', ',pledges.first_name)
                    END as name
                    ,employee_jobs.business_unit as business_unit_code
                    ,employee_jobs.tgb_reg_district
                    ,employee_jobs.deptid
                    ,employee_jobs.dept_name
                    ,CASE WHEN organizations.code = 'GOV'
                            THEN employee_jobs.office_city
                            ELSE pledges.city
                    END as city
                    ,pledges.type  AS pool_type
                ")
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
            // ->where('type', 'P')
            // ->where('pledges.pay_period_amount', '<>', 0)
            ->whereNull('pledges.deleted_at')
            ->when( $filters['year'], function($query) use($filters) {
                        $query->where('campaign_years.calendar_year', $filters['year']);
                    })
            ->get();

        foreach( $pledges as $pledge ) {

            // dd ( \App\Models\FSPool::where('region_id', 1)->asOfDate('2022-09-01')->first()->charities ); 
            //   $pool = $pledge->fund_supported_pool->asOfDate( '2021-09-01')->first();


            //      dd ( $pool );
            //   dd ( $pool->charities );
            if ($pledge->type == 'P')  {

                // $pool = \App\Models\FSPool::where('region_id', $pledge->region_id)->asOfDate($pledge->created_at)->first();
                $pool = $pledge->fund_supported_pool;

                // Bi-weekly
                if ($pledge->pay_period_amount <> 0) {

                    $calc_total = 0;
                    $goal_amount = $pledge->goal_amount - $pledge->one_time_amount;

                    if (!$pool) {
                        dd( $pledge);
                    }
                    foreach( $pool->charities as $index => $pool_charity) {

                        if ($index === count( $pool->charities ) - 1  ) {
                            $calc_amount = $goal_amount - $calc_total;
                        } else {
                            $calc_amount = round( $pool_charity->percentage * $pledge->goal_amount /100 ,2); 
                            $calc_total += $calc_amount;
                        }

                        \App\Models\PledgeCharityStaging::insert([
                            'history_id' => $this->history_id,
                            'pledge_type' => 'Annual',
                            'pledge_id' => $pledge->id,
                            'calendar_year' => $pledge->calendar_year,
                            'organization_code' => $pledge->organization_code,
                            'emplid' => $pledge->emplid,
                            'pecsf_id' => $pledge->pecsf_id,
                            'name' => $pledge->name,
                            'business_unit_code' => $pledge->business_unit_code,
                            'tgb_reg_district' => $pledge->tgb_reg_district,
                            'deptid' => $pledge->deptid,
                            'dept_name' => $pledge->dept_name,
                            'city' => $pledge->city,
                            'type' => 'Bi-Weekly',
                            'sub_type' => '',
                            'pool_type' => $pledge->type,
                            'region_id' => $pledge->region_id,
                            'pledge' => $pledge->pay_period_amount,
                            'amount' => $goal_amount,
                            'created_by_id' => $pledge->created_by_id,
                            'created_at' => $pledge->created_at,
                            'updated_at' => $pledge->updated_at,
                            
                            'charity_id' => $pool_charity->charity_id,
                            'percentage' => $pool_charity->percentage,
                            'supported_program' => $pool_charity->name,
                            'prorate_amount' => $calc_amount,


                        ]);
                    }

                }

                // One-Time 
                if ($pledge->one_time_amount <> 0) {

                    $calc_total = 0;
                    $goal_amount = $pledge->one_time_amount;

                    foreach( $pool->charities as $index => $pool_charity) {

                        if ($index === count( $pool->charities ) - 1  ) {
                            $calc_amount = $goal_amount - $calc_total;
                        } else {
                            $calc_amount = round( $pool_charity->percentage * $pledge->goal_amount /100 ,2); 
                            $calc_total += $calc_amount;
                        }

                        \App\Models\PledgeCharityStaging::insert([
                            'history_id' => $this->history_id,
                            'pledge_type' => 'Annual',
                            'pledge_id' => $pledge->id,
                            'calendar_year' => $pledge->calendar_year,
                            'organization_code' => $pledge->organization_code,
                            'emplid' => $pledge->emplid,
                            'pecsf_id' => $pledge->pecsf_id,
                            'name' => $pledge->name,
                            'business_unit_code' => $pledge->business_unit_code,
                            'tgb_reg_district' => $pledge->tgb_reg_district,
                            'deptid' => $pledge->deptid,
                            'dept_name' => $pledge->dept_name,
                            'city' => $pledge->city,
                            'type' => 'One-Time',
                            'sub_type' => '',
                            'pool_type' => $pledge->type,
                            'region_id' => $pledge->region_id,
                            'pledge' => $pledge->one_time_amount,
                            'amount' => $pledge->one_time_amount,
                            'created_by_id' => $pledge->created_by_id,
                            'created_at' => $pledge->created_at,
                            'updated_at' => $pledge->updated_at,

                            'charity_id' => $pool_charity->charity_id,
                            'percentage' => $pool_charity->percentage,
                            'supported_program' => $pool_charity->name,
                            'prorate_amount' => $calc_amount,

                        ]);
                    }

                }

            } else {

                // Bi-weekly
                if ($pledge->pay_period_amount <> 0) {

                   $pledge_charities = $pledge->bi_weekly_charities;

                    foreach( $pledge_charities as $index => $pledge_charity) {

                        \App\Models\PledgeCharityStaging::insert([
                            'history_id' => $this->history_id,
                            'pledge_type' => 'Annual',
                            'pledge_id' => $pledge->id,
                            'calendar_year' => $pledge->calendar_year,
                            'organization_code' => $pledge->organization_code,
                            'emplid' => $pledge->emplid,
                            'pecsf_id' => $pledge->pecsf_id,
                            'name' => $pledge->name,
                            'business_unit_code' => $pledge->business_unit_code,
                            'tgb_reg_district' => $pledge->tgb_reg_district,
                            'deptid' => $pledge->deptid,
                            'dept_name' => $pledge->dept_name,
                            'city' => $pledge->city,
                            'type' => 'Bi-Weekly',
                            'sub_type' => '',
                            'pool_type' => $pledge->type,
                            'region_id' => $pledge->region_id,
                            'pledge' => $pledge->pay_period_amount,
                            'amount' =>  $pledge->goal_amount - $pledge->one_time_amount,
                            'created_by_id' => $pledge->created_by_id,
                            'created_at' => $pledge->created_at,
                            'updated_at' => $pledge->updated_at,

                            'charity_id' => $pledge_charity->charity_id,
                            'percentage' => $pledge_charity->percentage,
                            'supported_program' => $pledge_charity->additional,
                            'prorate_amount' => $pledge_charity->goal_amount,

                        ]);
                    }

                }

                // One-Time 
                if ($pledge->one_time_amount <> 0) {

                    $pledge_charities = $pledge->bi_weekly_charities;

                    foreach( $pledge_charities as $index => $pledge_charity) {

                        \App\Models\PledgeCharityStaging::insert([
                            'history_id' => $this->history_id,
                            'pledge_type' => 'Annual',
                            'pledge_id' => $pledge->id,
                            'calendar_year' => $pledge->calendar_year,
                            'organization_code' => $pledge->organization_code,
                            'emplid' => $pledge->emplid,
                            'pecsf_id' => $pledge->pecsf_id,
                            'name' => $pledge->name,
                            'business_unit_code' => $pledge->business_unit_code,
                            'tgb_reg_district' => $pledge->tgb_reg_district,
                            'deptid' => $pledge->deptid,
                            'dept_name' => $pledge->dept_name,
                            'city' => $pledge->city,
                            'type' => 'One-Time',
                            'sub_type' => '',
                            'pool_type' => $pledge->type,
                            'region_id' => $pledge->region_id,
                            'pledge' => $pledge->one_time_amount,
                            'amount' => $pledge->one_time_amount,
                            'created_by_id' => $pledge->created_by_id,
                            'created_at' => $pledge->created_at,
                            'updated_at' => $pledge->updated_at,

                            'charity_id' => $pledge_charity->charity_id,
                            'percentage' => $pledge_charity->percentage,
                            'supported_program' => $pledge_charity->additional,
                            'prorate_amount' => $pledge_charity->goal_amount,

                        ]);

                    }

                }

            }
        }   
    }

    protected function populate_event_pledges($filters) {


        $events = BankDepositForm::selectRaw("bank_deposit_forms.* 
                            ,year(bank_deposit_forms.created_at) as calendar_year
                            ,CASE WHEN bank_deposit_forms.organization_code = 'GOV'
                                THEN employee_jobs.name
                                ELSE ''
                            END as name
                            ,CASE WHEN bank_deposit_forms.organization_code = 'GOV'
                                THEN employee_jobs.business_unit
                                ELSE (select code from business_units where business_units.id = bank_deposit_forms.business_unit)
                            END as business_unit_code
                            ,CASE WHEN bank_deposit_forms.organization_code = 'GOV'
                                THEN employee_jobs.tgb_reg_district
                                ELSE (select code from regions where regions.id = bank_deposit_forms.region_id)
                            END as tgb_reg_district
                            ,employee_jobs.deptid as deptid
                            ,employee_jobs.dept_name as dept_name
                            ,CASE WHEN bank_deposit_forms.organization_code = 'GOV'
                                THEN employee_jobs.office_city
                                ELSE bank_deposit_forms.address_city
                            END as city
                            ,CASE when regional_pool_id is not NULL 
                                THEN 'P'
                                ELSE 'C'
                            END as pool_type ")
                // ->join('regions', 'regions.id', 'bank_deposit_forms.region_id')
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
                            $query->whereRaw('year(bank_deposit_forms.created_at) = ' . $filters['year']);
                        })
                ->get();

        foreach( $events as $event ) {

            if ($event->pool_type == 'P')  {

                // $pool_by_id = \App\Models\FSPool::where('id', $event->regional_pool_id)->first(); 
                // $pool = \App\Models\FSPool::where('region_id', $pool_by_id->region_id)->asOfDate($event->created_at)->first();
                $pool = $event->fund_supported_pool;

                $calc_total = 0;
                $goal_amount = $event->deposit_amount;

                foreach( $pool->charities as $index => $pool_charity) {

                    if ($index === count( $pool->charities ) - 1  ) {
                        $calc_amount = $goal_amount - $calc_total;
                    } else {
                        $calc_amount = round( $pool_charity->percentage * $goal_amount /100 ,2); 
                        $calc_total += $calc_amount;
                    }

                    \App\Models\PledgeCharityStaging::insert([
                        'history_id' => $this->history_id,
                        'pledge_type' => 'Event',
                        'pledge_id' => $event->id,
                        'calendar_year' => $event->calendar_year,
                        'organization_code' => $event->organization_code,
                        'emplid' => $event->emplid,
                        'pecsf_id' => $event->pecsf_id,
                        'name' => $event->name,
                        'business_unit_code' => $event->business_unit_code,
                        'tgb_reg_district' => $event->tgb_reg_district,
                        'deptid' => $event->deptid,
                        'dept_name' => $event->dept_name,
                        'city' => $event->city,
                        'type' => $event->event_type,
                        'sub_type' => $event->sub_type,
                        'pool_type' => $event->pool_type,
                        'region_id' => $event->region_id,
                        'pledge' => $event->deposit_amount,
                        'amount' => $event->deposit_amount,
                        'created_by_id' => $event->form_submitter_id,
                        'created_at' => $event->created_at,
                        'updated_at' => $event->updated_at,

                        'charity_id' => $pool_charity->charity_id,
                        'percentage' => $pool_charity->percentage,
                        'supported_program' => $pool_charity->name,
                        'prorate_amount' => $calc_amount,

                    ]);
                }

            } else {

                $event_charities = $event->charities;

                foreach( $event_charities as $index => $event_charity) {

                    \App\Models\PledgeCharityStaging::insert([
                        'history_id' => $this->history_id,
                        'pledge_type' => 'Event',
                        'pledge_id' => $event->id,
                        'calendar_year' => $event->calendar_year,
                        'organization_code' => $event->organization_code,
                        'emplid' => $event->emplid,
                        'pecsf_id' => $event->pecsf_id,
                        'name' => $event->name,
                        'business_unit_code' => $event->business_unit_code,
                        'tgb_reg_district' => $event->tgb_reg_district,
                        'deptid' => $event->deptid,
                        'dept_name' => $event->dept_name,
                        'city' => $event->city,
                        'type' => $event->event_type,
                        'sub_type' => $event->sub_type,
                        'pool_type' => $event->pool_type,
                        'region_id' => $event->region_id,
                        'pledge' => $event->deposit_amount,
                        'amount' => $event->deposit_amount,
                        'created_by_id' => $event->form_submitter_id,
                        'created_at' => $event->created_at,
                        'updated_at' => $event->updated_at,

                        'charity_id' => $event_charity->vendor_id,
                        'percentage' => $event_charity->donation_percent,
                        'supported_program' => $event_charity->specific_community_or_initiative,
                        'prorate_amount' => round(($event_charity->goal_amount * $event->donation_percent) / 100, 2),

                    ]);

                }

            }

        }

    }

}
