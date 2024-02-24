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

class DonationsPageTest extends TestCase
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
    public function test_an_anonymous_user_cannot_access_donation_page()
    {
        $response = $this->get('donations');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function test_an_anonymous_user_cannot_access_donation_page_via_post_method()
    {
        $response = $this->post('donations', []);

        $response->assertStatus(405);
    }

    public function test_an_anonymous_user_cannot_access_donation_history_detail()
    {
        // $response = $this->get('donations/pledge-detail', []);
        $response = $this->json('get', 'donations/pledge-detail', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(401);
    }

    public function test_an_anonymous_user_cannot_download_donation_history()
    {
        $response = $this->get('donations?download_pdf=true');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    /**  Authorized User **/
    /** Test Authenication */
    public function test_an_authorized_user_can_access_donation_page()
    {
        [$sum, $expected_rows] = $this->get_new_record_form_data(true, false);

        $this->actingAs($this->user);
        $response = $this->get('donations');

        $response->assertStatus(200);
    }

    public function test_an_authorized_user_cannot_access_donation_page_via_post_method()
    {
        [$sum, $expected_rows] = $this->get_new_record_form_data(true, false);

        $this->actingAs($this->user);
        $response = $this->post('donations', []);

        $response->assertStatus(405);
    }

    public function test_an_authorized_user_cannot_access_donation_history_detail()
    {
        $this->actingAs($this->user);
        $response = $this->json('get', 'donations/pledge-detail', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        // $response = $this->get('donations/pledge-detail', []);

        $response->assertStatus(200);
        
    }

    public function test_an_authorized_user_can_download_donation_history()
    {
        $this->actingAs($this->user);
        $response = $this->get('donations?download_pdf=true');

        $response->assertStatus(200);
    }

    // Test download pdf file 
    public function test_an_authorized_user_can_download_donation_history_in_pdf_format()
    {
        $this->actingAs($this->user);
        $response = $this->get('donations?download_pdf=true');

        $response->assertStatus(200);   

        $filename = "Donation History Summary.pdf";

        $response->assertDownload( $filename ); 
        $response->assertSeeText('PDF-');

        $this->assertTrue($response->headers->get('content-type') == 'application/pdf');
        $this->assertTrue($response->headers->get('content-disposition') == 'attachment; filename="' . $filename .'"');

    }



    /* validate calcualtion */
    public function test_the_gov_annual_campaign_pledge_correctly_calculate()
    {

        [$sum, $expected_rows] = $this->get_new_record_form_data(true, false);

        // run command 
        $this->artisan('command:UpdateDailyCampaign')->assertExitCode(0);

        // check online page 
        $this->actingAs($this->user);
        $response = $this->get('/http://localhost:8000/donations');

        $response->assertStatus(200);
        // $response->assertJsonPath('data', []);
        $response->assertSeeText(number_format(round( $sum, 0)) );

        // check database
        foreach($expected_rows as $row) {
            $this->assertDatabaseHas('daily_campaigns', $row );
        }

    }

    public function test_the_non_gov_annual_campaign_pledge_correctly_calculate()
    {

        [$sum, $expected_rows] = $this->get_new_record_form_data(false, false);

        // run command 
        $this->artisan('command:UpdateDailyCampaign')->assertExitCode(0);

        // check online page 
        $this->actingAs($this->user);
        $response = $this->get('/donations');

        // $response = $this->json('get', '/http://localhost:8000/donations?year=' . $expected_rows[0]['campaign_year'], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $response->assertStatus(200);
        // $response->assertJsonPath('data', []);
        $response->assertSeeText( "$0" );

        // check database
        foreach($expected_rows as $row) {
            $this->assertDatabaseHas('daily_campaigns', $row );
        }

    }


    public function test_the_gov_event_pledge_correctly_calculate()
    {

        [$sum, $expected_rows] = $this->get_new_record_form_data(true, true);

        // run command 
        $this->artisan('command:UpdateDailyCampaign')->assertExitCode(0);

        // check online page 
        $this->actingAs($this->user);
        $response = $this->get('donations');
        // $response = $this->json('get', '/http://localhost:8000/donations?year=' . $expected_rows[0]['campaign_year'], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

// $text  = $response->content();
// $pos = strpos($text, "you've donated $");

// dd([ substr($text, $pos, 200),   $sum]);
        $response->assertStatus(200);
        // $response->assertJsonPath('data', []);
        $response->assertSeeText( number_format(round( $sum, 0)) );

        // check database
        foreach($expected_rows as $row) {
            $this->assertDatabaseHas('daily_campaigns', $row );
        }

    }

    public function test_the_non_gov_event_pledge_correctly_calculate()
    {

        [$sum, $expected_rows] = $this->get_new_record_form_data(false, true);

        // run command 
        $this->artisan('command:UpdateDailyCampaign')->assertExitCode(0);

        // check online page 
        $this->actingAs($this->user);
        $response = $this->get('/donations');
        // $response = $this->json('get', '/http://localhost:8000/donations?year=' . $expected_rows[0]['campaign_year'], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        // $response->assertJsonPath('data', []);
        $response->assertSeeText( "$0" );

        // check database
        foreach($expected_rows as $row) {
            $this->assertDatabaseHas('daily_campaigns', $row );
        }

    }


    // // Special Rule
    // public function the_gov_annual_campaign_pledge_with_Government_Communications_and_Public_Engagement_correctly_calculate()
    // {

    //     [$sum, $expected_rows] = $this->get_new_record_form_data(true, false, true);

    //     // run command 
    //     $this->artisan('command:UpdateDailyCampaign')->assertExitCode(0);

    //     // check online page 
    //     $this->actingAs($this->user);
    //     $response = $this->get('/donations');
    //     // $response = $this->json('get', '/http://localhost:8000/donations?year=' . $expected_rows[0]['campaign_year'], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

    //     $response->assertStatus(200);
    //     // $response->assertJsonPath('data', []);
    //     $response->assertSeeText(number_format(round( $sum, 0)) );

    //     // check database
    //     foreach($expected_rows as $row) {
    //         $this->assertDatabaseHas('daily_campaigns', $row );
    //     }

    // }

    // Daily Campaign -- Export




    /* Private Function */
    private function get_new_record_form_data($is_gov = true, $bEvent = false, $special_bu = false) 
    {

        $user = User::first();

        $year = (today() < today()->year . '-03-01') ? today()->year - 1 : today()->year ;

        $setting = Setting::create([
                'campaign_start_date' => $year . '-09-01',
                'campaign_end_date' => $year . '-11-15',
                'campaign_final_date' => (today() < today()->year . '-03-01') ? today() : $year + 1 . '-02-28',
                'donations_start_date' => $year . '-09-01',
                'donations_end_date' => $year . '-11-15',
                'donations_final_date' => (today() < today()->year . '-03-01') ? today() : $year + 1 . '-02-28',

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
                'event_type' => 'Cheque One-Time Donation',
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
                'bc_gov_id'	=> $user->emplid,
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
