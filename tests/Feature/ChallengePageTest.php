<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\FSPool;
use App\Models\Pledge;
use App\Models\Region;
use App\Models\Charity;
use App\Models\Setting;
use App\Models\EmployeeJob;
use App\Models\BusinessUnit;
use App\Models\CampaignYear;
use App\Models\Organization;
use App\Models\DailyCampaign;
use App\Models\FSPoolCharity;
use App\Models\PledgeCharity;
use App\Models\BankDepositForm;
use App\Models\ScheduleJobAudit;
use App\Models\DailyCampaignSummary;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChallengePageTest extends TestCase
{

    use WithFaker;

    private User $admin;
    private User $user;

    protected static $db_inited = false;

    public function setUp(): void
    {
        parent::setUp();

        // Run the DatabaseSeeder...
        // $this->seed();
        // $this->seed(\Database\Seeders\RolePermissionTableSeeder::class);
        // $this->seed(\Database\Seeders\CampaignYearSeeder::class);
        // $this->seed(\Database\Seeders\UserTableSeeder::class);
        // $this->admin = User::updateOrCreate([
        //   'email' => 'supervisor@example.com',
        // ],[
        //   'name' => 'Supervisor',
        //   'password' => bcrypt('supervisor@123'),
        //   'emplid' => 'sss130347'
        // ]);
        // $this->admin->assignRole('admin');
        // $this->user = User::updateOrCreate([
        //   'email' => 'employee1@example.com',
        // ],[
        //   'name' => 'Employee A',
        //   'password' => bcrypt('employee1@123'),
        //   'emplid' => 'sss130347'
        // ]);

        // if (!self::$initialized) {
        //     // Do something once here for _all_ test subclasses.
        //     CampaignYear::truncate();

        //     self::$initialized = TRUE;
        // }

        if (!static::$db_inited) {
            static::$db_inited = true;
            // Dependence
            // $this->artisan('migrate:fresh');
            // $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);
            $this->artisan('db:seed', ['--class' => 'RolePermissionTableSeeder']);
            $this->artisan('db:seed', ['--class' => 'UserTableSeeder']);

            $this->artisan('db:seed', ['--class' => 'PayCalendarSeeder']);

            ScheduleJobAudit::truncate();
        }

        $this->admin = User::whereHas("roles", function ($q) {$q->where("name", "admin");})->first();
        $this->user  = User::doesntHave('roles')->first();

        $this->artisan('db:seed', ['--class' => 'PayCalendarSeeder']);

        Setting::truncate();
        CampaignYear::truncate();
        Region::truncate();
        BusinessUnit::truncate();
        Organization::truncate();
        Charity::truncate();
        FSPool::truncate();
        FSPoolCharity::truncate();
        EmployeeJob::truncate();

        BankDepositForm::truncate();

        PledgeCharity::truncate();
        Pledge::truncate();

        DailyCampaign::truncate();
        DailyCampaignSummary::truncate();




    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_challenge_page()
    {
        $response = $this->get('challenge');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function test_an_anonymous_user_cannot_access_challenge_page_via_post_method()
    {
        $response = $this->post('challenge', []);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function test_an_anonymous_user_cannot_access_daily_campaign_page()
    {
        $response = $this->get('challenge/daily_campaign');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function test_an_anonymous_user_cannot_download_daily_campaign()
    {
        $response = $this->get('challenge/download');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function test_an_anonymous_user_cannot_access_org_participation_tracker_page()
    {
        $response = $this->get('challenge/org_participation_tracker');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function test_an_anonymous_user_cannot_download_org_participation_tracker_campaign()
    {
        $response = $this->get('challenge/org_participation_tracker_download');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    /**  Authorized User **/
    /** Test Authenication */
    public function test_an_authorized_user_can_access_challenge_page()
    {
        [$sum, $expected_rows] = $this->get_new_record_form_data(true, false);

        $this->actingAs($this->user);
        $response = $this->get('challenge');

        $response->assertStatus(200);
    }

    public function test_an_authorized_user_can_access_challenge_page_via_post_method()
    {
        [$sum, $expected_rows] = $this->get_new_record_form_data(true, false);

        $this->actingAs($this->user);
        $response = $this->post('challenge', []);

        $response->assertStatus(200);
    }

    public function test_an_authorized_user_can_access_daily_campaign_page()
    {
        [$sum, $expected_rows] = $this->get_new_record_form_data(true, false);

        $this->actingAs($this->user);
        $response = $this->get('challenge/daily_campaign');

        $setting = Setting::first();

        if (today() > $setting->campaign_start_date && today() <= $setting->campaign_final_date) {
            $response->assertStatus(200);
        } else {
            $response->assertStatus(404);
        }

    }
 
    public function test_an_authorized_user_can_download_daily_campaign()
    {
        [$sum, $expected_rows] = $this->get_new_record_form_data(true, false);
        
        $this->actingAs($this->user);
        $response = $this->get('challenge/download');

        $setting = Setting::first();

        if (today() > $setting->campaign_start_date && today() <= $setting->campaign_final_date) {
            $response->assertStatus(200);
        } else {
            $response->assertStatus(404);
        }
        
    }

    public function test_an_authorized_user_can_access_org_participation_tracker_page()
    {
        [$sum, $expected_rows] = $this->get_new_record_form_data(true, false);

        $this->actingAs($this->user);
        $response = $this->get('challenge/org_participation_tracker');

        if (Setting::isCampaignPeriodActive()) 
            $response->assertStatus(200);
        else
            $response->assertStatus(404);
    }
 
    public function test_an_authorized_user_can_download_org_participation_tracker()
    {
        [$sum, $expected_rows] = $this->get_new_record_form_data(true, false);
        
        $this->actingAs($this->user);
        $response = $this->get('challenge/org_participation_tracker_download', []);
 
        if (Setting::isOrgParticipationTrackerActive()) {
            $response->assertStatus(302);
            $response->assertSessionHas( "message" );
        } else {
            $response->assertStatus(404);
        }

    }

    // Daily Campaign -- Export
    public function test_authorized_user_download_daily_campaign_by_bu_report_success()
    {

        [$sum, $expected_rows] = $this->get_new_record_form_data(true, false);

        // run command 
        $this->artisan('command:UpdateDailyCampaign')->assertExitCode(0);

        // export data 
        $this->actingAs($this->user);
        $response = $this->get('/challenge/download?sort=region');

        $filename = 'Daily_Campaign_Update_Region_' . today()->format('Y-m-d') . '.xlsx';

        $setting = Setting::first();

        if (today() > $setting->campaign_start_date && today() <= $setting->campaign_final_date) {
            $response->assertStatus(200);

            $response->assertStatus(200);
            $response->assertDownload( $filename ); 

            $this->assertTrue($response->headers->get('content-type') == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $this->assertTrue($response->headers->get('content-disposition') == "attachment; filename=" . $filename);
        } else {
            $response->assertStatus(404);
        }


    }

    public function test_authorized_user_download_daily_campaign_by_organization_report_success()
    {

        [$sum, $expected_rows] = $this->get_new_record_form_data(true, false);

        // run command 
        $this->artisan('command:UpdateDailyCampaign')->assertExitCode(0);

        // export data 
        $this->actingAs($this->user);
        $response = $this->get('/challenge/download?sort=organization');
                                  
        $filename = 'Daily_Campaign_Update_By_Org_' . today()->format('Y-m-d') . '.xlsx';

        $setting = Setting::first();

        if (today() > $setting->campaign_start_date && today() <= $setting->campaign_final_date) {

            $response->assertStatus(200);
            $response->assertDownload( $filename ); 

            $this->assertTrue($response->headers->get('content-type') == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $this->assertTrue($response->headers->get('content-disposition') == "attachment; filename=" . $filename);
        } else {
            $response->assertStatus(404);
        }

    }

    public function test_authorized_user_download_daily_campaign_by_department_report_success()
    {

        [$sum, $expected_rows] = $this->get_new_record_form_data(true, false);

        // run command 
        $this->artisan('command:UpdateDailyCampaign')->assertExitCode(0);

        // export data 
        $this->actingAs($this->user);
        $response = $this->get('/challenge/download?sort=department');

        $row = Setting::first();

        $campaign_year = Setting::challenge_page_campaign_year();
        $report_date = (today()->month >= 3 && today()->month <= 8) ?  today() : $row->campaign_end_date;
                                  
        $filename = 'Daily_Campaign_Update_By_Dept_' . $report_date->format('Y-m-d') . '.xlsx';

        
        $setting = Setting::first();

        if (today() > $setting->campaign_start_date && today() <= $setting->campaign_final_date) {
            $response->assertStatus(200);
            $response->assertDownload( $filename ); 

            $this->assertTrue($response->headers->get('content-type') == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $this->assertTrue($response->headers->get('content-disposition') == "attachment; filename=" . $filename);
        } else {
            $response->assertStatus(404);
        }

    }





    /* validate calcualtion */
    public function test_the_gov_annual_campaign_pledge_correctly_calculate()
    {

        [$sum, $expected_rows] = $this->get_new_record_form_data(true, false);

        // run command 
        $this->artisan('command:UpdateDailyCampaign')->assertExitCode(0);

        // check online page 
        $this->actingAs($this->user);
        $response = $this->json('get', '/http://localhost:8000/challenge?year=' . $expected_rows[0]['campaign_year'], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonPath('data', []);

        $campaign_year = Setting::challenge_page_campaign_year();        
        if (today()->month >= 3 && today()->month <= 8) {
            // No data proceed by 
        } else {

            $response->assertSeeText(number_format(round( $sum, 0)) );

            // check database
            foreach($expected_rows as $row) {
                $this->assertDatabaseHas('daily_campaigns', $row );
            }
        }
        
    }

    public function test_the_non_gov_annual_campaign_pledge_correctly_calculate()
    {

        [$sum, $expected_rows] = $this->get_new_record_form_data(false, false);

        // run command 
        $this->artisan('command:UpdateDailyCampaign')->assertExitCode(0);

        // check online page 
        $this->actingAs($this->user);
        $response = $this->json('get', '/http://localhost:8000/challenge?year=' . $expected_rows[0]['campaign_year'], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonPath('data', []);

        $campaign_year = Setting::challenge_page_campaign_year();
        if (today()->month >= 3 && today()->month <= 8) {
            // No data proceed by 
        } else {
            $response->assertSeeText( number_format(round( $sum, 0)) );

            // check database
            foreach($expected_rows as $row) {
                $this->assertDatabaseHas('daily_campaigns', $row );
            }
        }

    }


    public function test_the_gov_event_pledge_correctly_calculate()
    {

        [$sum, $expected_rows] = $this->get_new_record_form_data(true, true);

        // run command 
        $this->artisan('command:UpdateDailyCampaign')->assertExitCode(0);

        // check online page 
        $this->actingAs($this->user);
        $response = $this->json('get', '/http://localhost:8000/challenge?year=' . $expected_rows[0]['campaign_year'], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonPath('data', []);
        $campaign_year = Setting::challenge_page_campaign_year();
        if (today()->month >= 3 && today()->month <= 8) {
            // No data proceed by 
        } else {
            $response->assertSeeText( number_format(round( $sum, 0)) );

            // check database
            foreach($expected_rows as $row) {
                $this->assertDatabaseHas('daily_campaigns', $row );
            }
        }

    }

    public function test_the_non_gov_event_pledge_correctly_calculate()
    {

        [$sum, $expected_rows] = $this->get_new_record_form_data(false, true);

        // run command 
        $this->artisan('command:UpdateDailyCampaign')->assertExitCode(0);

        // check online page 
        $this->actingAs($this->user);
        $response = $this->json('get', '/http://localhost:8000/challenge?year=' . $expected_rows[0]['campaign_year'], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonPath('data', []);

        $campaign_year = Setting::challenge_page_campaign_year();
        if (today()->month >= 3 && today()->month <= 8) {
            // No data proceed by 
        } else {
            $response->assertSeeText( number_format(round( $sum, 0)) );

            // check database
            foreach($expected_rows as $row) {
                $this->assertDatabaseHas('daily_campaigns', $row );
            }
        }

    }


    // Special Rule
    public function the_gov_annual_campaign_pledge_with_Government_Communications_and_Public_Engagement_correctly_calculate()
    {

        [$sum, $expected_rows] = $this->get_new_record_form_data(true, false, true);

        // run command 
        $this->artisan('command:UpdateDailyCampaign')->assertExitCode(0);

        // check online page 
        $this->actingAs($this->user);
        $response = $this->json('get', '/http://localhost:8000/challenge?year=' . $expected_rows[0]['campaign_year'], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonPath('data', []);

        $campaign_year = Setting::challenge_page_campaign_year();
        if (today()->month >= 3 && today()->month <= 8) {
            // No data proceed by 
        } else {
            $response->assertSeeText(number_format(round( $sum, 0)) );

            // check database
            foreach($expected_rows as $row) {
                $this->assertDatabaseHas('daily_campaigns', $row );
            }
        }

    }

  
    /* Private Function */
    private function get_new_record_form_data($is_gov = true, $bEvent = false, $special_bu = false) 
    {

        $user = User::first();

        $year = (today() < today()->year . '-03-01') ? today()->year - 1 : today()->year ;

        $setting = Setting::create([
                'campaign_start_date' => $year . '-09-01',
                'campaign_end_date' => $year . '-11-15',
                'campaign_final_date' => (today() < today()->year . '-03-01') ? today() : $year + 1 . '-02-28',
                'challenge_start_date' => $year . '-09-01',
                'challenge_end_date' => $year . '-11-15',
                'challenge_final_date' => (today() < today()->year . '-03-01') ? today() : $year + 1 . '-02-28',
                'ee_snapshot_date_1' => $year . '-09-01',
                'ee_snapshot_date_2' => $year . '-10-15',

        ]);

        $campaign_year = CampaignYear::create([
            'calendar_year' => $year + 1,
            'status' => 'A',
            'start_date' => today(),
            'end_date' => $year . '-12-31',
            'number_of_periods' => 26,
            'close_date' => $year . '-12-31',
        ]);

        $business = BusinessUnit::factory()->create([
            'status' => 'A'
        ]);
        if ($special_bu) {
            BusinessUnit::factory()->create([
                'code' => 'BGCPE',
                'status' => 'A',
                'linked_bu_code' => 'BGCPE',
            ]);
            $business = BusinessUnit::factory()->create([
                'code' => 'BC' . $this->faker->regexify('[0-9]{3}'),
                'status' => 'A',
                'linked_bu_code' => 'BC022',
            ]);
        }

        $organization = Organization::factory()->create([
                    'status' => 'A',
                    'code' => "GOV",
        ]);
        if (!($is_gov)) {
            $organization = Organization::factory()->create([
                    'status' => 'A',
                    'code' => "LDB",
                    'bu_code' => $business->code,
            ]);
        }
        
        $region = Region::factory()->create(['status' => 'A']);
        $charities = Charity::factory(10)->create([
            'charity_status' => 'Registered',
        ]);

        $fspool = FSPool::factory()->create([
            'region_id' => $region->id,
            'status' => 'A',
        ]);
        $fspool_charities = FSPoolCharity::factory(5)->create([
                'f_s_pool_id' => $fspool->id,
                'charity_id' => $this->faker->randomElement( $charities->pluck('id')->toArray() ),
                'percentage' => 20,
                'status' => 'A',
        ]);

        $job = EmployeeJob::factory()->create([
            "organization_id" => $organization->id,
            "emplid" => $user->emplid,
            "business_unit" => $business->code,
            "business_unit_id" => $business->id,
            "tgb_reg_district" =>  $region->code,
            "region_id" => $region->id,
        ]);

        // Test Transaction
   

        if ($bEvent) {

            // Test Transaction
            $pledges = BankDepositForm::factory(10)->create([
                'organization_code' =>  $organization->code,
                'form_submitter_id' =>  $user->id,
                'campaign_year_id' => $campaign_year->id,
                // 'event_type' => 'Gaming',
                // 'sub_type' => '50/50 Draw',
                // 'deposit_date' => Carbon::yesterday(),
                // 'deposit_amount' => 989.00,
                // 'description' => $this->faker->words(3, true),
                'employment_city' => $job->office_city,
                'region_id'	=> $region->id,
                'department_id' => null,
                'regional_pool_id' =>  $fspool->id,
                // 'address_line_1' =>	null,       //substr($this->faker->address(), 0, 60),
                // 'address_line_2' => null,       //substr($this->faker->address(), 0, 60),
                // 'address_city' => null,         //$this->faker->city(),
                // 'address_province' => null,     //$this->faker->regexify('/^[A-Z]{2}$/'),
                // 'address_postal_code' =>null,   // $this->faker->regexify('/^[A-Z]\d[A-Z]\ {1}\d[A-Z]\d$/'),

                // 'pecsf_id' => 'G'. substr($campaign_year->calendar_year,2,2) -1 . '001',
                // 'bc_gov_id'	=> $is_gov ? $user->emplid : null,
                'business_unit'	=> $is_gov ? $job->business_unit_id : $business->id,
                'deptid' => $is_gov ? $job->deptid : null,
                'dept_name' => $is_gov ? $job->dept_name : null,
                // 'employee_name' => $is_gov ? $user->name :  $this->faker->words(2, true),

                'approved' => 1,
            ]);


            $pledge = $pledges[0];
            $bu = BusinessUnit::where('id', $pledge->business_unit)->first();
            $cy = $pledge->campaign_year->calendar_year - 1;
    
            $sum = $pledges->sum('deposit_amount');
            $donor = $pledges->whereNotIn('event_type', ['Gaming', 'Fundraiser'])->count();

            $expected_results = [];
            foreach ([0,1,2] as $type) {
                array_push($expected_results, [
                    'campaign_year' => $cy,
                    'as_of_date'    => today(),
                    'daily_type'    => $type,
                    'business_unit' => ($type == 0 || $type == 2 ) ? $bu->code : null,
                    'region_code' => ($type == 1) ? $region->code : null,
                    'deptid' => ($type == 2) ? $pledge->deptid : null,
                    'dept_name' => ($type == 2) ? $pledge->dept_name : null,
                    'donors' =>  $donor, //( $pledge->event_type == 'Gaming' || $pledge->event_type == 'Fundraiser') ? 0 : 1,
                    'dollars' => $sum,
                   ]);
            }

        } else {

            $pledge = Pledge::create([
                'organization_id' =>  $organization->id,
                'emplid' => $is_gov ? $user->emplid : null,
                'user_id' => $is_gov ? $user->id : 0, 
                'pecsf_id' => $is_gov ? null : '882288',
                'business_unit' => $special_bu ? $business->code : ($is_gov ? $job->business_unit : $business->code),
                'tgb_reg_district' => $is_gov ? $job->tgb_reg_district : $region->code,
                'deptid' => $is_gov ? $job->deptid : null,
                'dept_name' => $special_bu ? 'GCPE Testing 123' : ($is_gov ? $job->dept_name : null),
                'first_name' => $is_gov ? null : $this->faker->word(),
                'last_name' => $is_gov ? null : $this->faker->word(),
                'city' => $is_gov ? null : $this->faker->word(),
                'campaign_year_id' => $campaign_year->id,
                'type' => "P",
                'region_id' => $region->id,
                'f_s_pool_id' => $fspool->id,
                'one_time_amount' => 50.0,
                'pay_period_amount' => 20.0,
                'goal_amount' => 570.0,
            ]);

            $bu = BusinessUnit::where('code', $pledge->business_unit)->first();
            $cy = $pledge->campaign_year->calendar_year - 1;

            $sum = $pledge->goal_amount;
            $expected_results = [
                [   'campaign_year' => $cy,
                    'as_of_date'    => today(),
                    'daily_type'    => 0,
                    'business_unit' => $special_bu ? 'BGCPE' : $pledge->business_unit,
                    'donors' => 1,
                    'dollars' => $pledge->goal_amount,
                ],
                [   'campaign_year' => $cy,
                    'as_of_date'    => today(),
                    'daily_type'    => 1,
                    'region_code' => $pledge->tgb_reg_district,
                    // 'region_name'
                    'donors' => 1,
                    'dollars' => $pledge->goal_amount,
                ],
                [   'campaign_year' => $cy,
                    'as_of_date'    => today(),
                    'daily_type'    => 2,
                    'deptid' => $pledge->deptid,
                    'dept_name' => $pledge->dept_name,
                    'donors' => 1,
                    'dollars' => $pledge->goal_amount,
                ],
            ];

        }


        // var_dump($pledge->charities->all());
        // dd('test');

        return [$sum, $expected_results];
    }

}
