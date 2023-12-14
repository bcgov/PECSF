<?php

namespace App\Exports;

// use App\Models\EmployeeJob;
use App\Models\Pledge;
use App\Models\ProcessHistory;
use App\Models\BankDepositForm;
use Illuminate\Support\Facades\DB;

use App\Models\PledgeCharityStaging;
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
                    // [
                    //     'Report title : Amount by charity report',
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
                        'FS Pool Name',
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
                        'Tran ID',

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
            $employee->f_s_pool_id ? $employee->fund_supported_pool->region->name : '',
            
            $employee->type,
            $employee->sub_type,
            $employee->amount,
            $employee->created_at,
            $employee->updated_at,

            ($employee->percentage != 0) ? $employee->percentage : '',
            $employee->charity ? $employee->charity->registration_number : '',
            $employee->charity ? $employee->charity->charity_name : '',
            $employee->supported_program,
            $employee->prorate_amount,
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
                PledgeCharityStaging::where('history_id', $this->history_id)
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

        // Step 1 -- Annual 
        $this->populate_annual_pledges($filters);

        // Step 2 -- Event 
        $this->populate_event_pledges( $this->filters );

        // Total Count
        $this->total_count = PledgeCharityStaging::where('history_id', $this->history_id)->count();    



    }


    protected function populate_annual_pledges($filters) {

        $pledges =  Pledge::selectRaw("pledges.*
                    ,campaign_years.calendar_year
                    ,organizations.code as organization_code
                    ,CASE WHEN organizations.code = 'GOV'
                            THEN employee_jobs.name
                            ELSE CONCAT (pledges.last_name,', ',pledges.first_name)
                    END as name
                    ,pledges.business_unit as business_unit_code
                    ,pledges.tgb_reg_district
                    ,pledges.deptid
                    ,pledges.dept_name
                    ,CASE WHEN organizations.code = 'GOV'
                            THEN employee_jobs.office_city
                            ELSE pledges.city
                    END as city
                    ,pledges.type  AS pool_type
                    ,pledges.f_s_pool_id AS f_s_pool_id
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
            ->whereNull('pledges.cancelled')
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
                    // foreach( $pool->charities as $index => $pool_charity) {

                    //     if ($index === count( $pool->charities ) - 1  ) {
                    //         $calc_amount = $goal_amount - $calc_total;
                    //     } else {
                    //         $calc_amount = round( $pool_charity->percentage * $pledge->goal_amount /100 ,2); 
                    //         $calc_total += $calc_amount;
                    //     }

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
                            'f_s_pool_id' => $pledge->f_s_pool_id,
                            'region_id' => $pledge->region_id,
                            'pledge' => $pledge->pay_period_amount,
                            'amount' => $goal_amount,
                            'created_by_id' => $pledge->created_by_id,
                            'created_at' => $pledge->created_at,
                            'updated_at' => $pledge->updated_at,
                            
                            'charity_id' => null,           // $pool_charity->charity_id,
                            'percentage' => 0,           // $pool_charity->percentage,
                            'supported_program' => null,    // $pool_charity->name,
                            'prorate_amount' => 0,       // $calc_amount,


                        ]);
                    // }

                }

                // One-Time 
                if ($pledge->one_time_amount <> 0) {

                    $calc_total = 0;
                    $goal_amount = $pledge->one_time_amount;

                    // foreach( $pool->charities as $index => $pool_charity) {

                    //     if ($index === count( $pool->charities ) - 1  ) {
                    //         $calc_amount = $goal_amount - $calc_total;
                    //     } else {
                    //         $calc_amount = round( $pool_charity->percentage * $pledge->goal_amount /100 ,2); 
                    //         $calc_total += $calc_amount;
                    //     }

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
                            'f_s_pool_id' => $pledge->f_s_pool_id,
                            'region_id' => $pledge->region_id,
                            'pledge' => $pledge->one_time_amount,
                            'amount' => $pledge->one_time_amount,
                            'created_by_id' => $pledge->created_by_id,
                            'created_at' => $pledge->created_at,
                            'updated_at' => $pledge->updated_at,

                            'charity_id' => null,           // $pool_charity->charity_id,
                            'percentage' => 0,           // $pool_charity->percentage,
                            'supported_program' => null,    // $pool_charity->name,
                            'prorate_amount' => 0,       // $calc_amount,

                        ]);
                    // }

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
                            'f_s_pool_id' => null,
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
                            'f_s_pool_id' => null,
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
                            ,campaign_years.calendar_year as calendar_year
                            ,CASE WHEN bank_deposit_forms.bc_gov_id is not null
                                THEN employee_jobs.name
                                ELSE bank_deposit_forms.employee_name
                            END as name
                            ,(select code from business_units where business_units.id = bank_deposit_forms.business_unit) as business_unit_code
                            ,(select code from regions where regions.id = bank_deposit_forms.region_id) as tgb_reg_district
                            ,bank_deposit_forms.deptid as deptid
                            ,bank_deposit_forms.dept_name as dept_name
                            ,CASE WHEN bank_deposit_forms.bc_gov_id is not null
                                THEN employee_jobs.office_city
                                ELSE bank_deposit_forms.employment_city
                            END as city
                            ,CASE when regional_pool_id is not NULL 
                                THEN 'P'
                                ELSE 'C'
                            END as pool_type 
                            ,bank_deposit_forms.regional_pool_id as f_s_pool_id
                        ")
                ->join('campaign_years', 'campaign_years.id', 'bank_deposit_forms.campaign_year_id')
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
                ->get();

        foreach( $events as $event ) {

            if ($event->f_s_pool_id)  {

                // $pool_by_id = \App\Models\FSPool::where('id', $event->regional_pool_id)->first(); 
                // $pool = \App\Models\FSPool::where('region_id', $pool_by_id->region_id)->asOfDate($event->created_at)->first();
                $pool = $event->fund_supported_pool;

                $calc_total = 0;
                $goal_amount = $event->deposit_amount;

                // foreach( $pool->charities as $index => $pool_charity) {

                //     if ($index === count( $pool->charities ) - 1  ) {
                //         $calc_amount = $goal_amount - $calc_total;
                //     } else {
                //         $calc_amount = round( $pool_charity->percentage * $goal_amount /100 ,2); 
                //         $calc_total += $calc_amount;
                //     }

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
                        'f_s_pool_id' => $event->f_s_pool_id,
                        'region_id' => $event->region_id,
                        'pledge' => $event->deposit_amount,
                        'amount' => $event->deposit_amount,
                        'created_by_id' => $event->form_submitter_id,
                        'created_at' => $event->created_at,
                        'updated_at' => $event->updated_at,

                        'charity_id' => null,               // $pool_charity->charity_id,
                        'percentage' => 0,                  // $pool_charity->percentage,
                        'supported_program' => null,        // $pool_charity->name,
                        'prorate_amount' => 0,              // $calc_amount,

                    ]);
                // }

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
                        'f_s_pool_id' => null,
                        'region_id' => $event->region_id,
                        'pledge' => $event->deposit_amount,
                        'amount' => $event->deposit_amount,
                        'created_by_id' => $event->form_submitter_id,
                        'created_at' => $event->created_at,
                        'updated_at' => $event->updated_at,

                        'charity_id' => $event_charity->vendor_id,
                        'percentage' => $event_charity->donation_percent,
                        'supported_program' => $event_charity->specific_community_or_initiative,
                        'prorate_amount' => round(( $event->deposit_amount * $event_charity->donation_percent) / 100, 2),

                    ]);

                }

            }

        }

    }

}
