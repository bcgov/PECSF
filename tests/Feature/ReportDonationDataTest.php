<?php

namespace Tests\Feature;

use File;
use finfo;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\FSPool;
use App\Models\Pledge;
use App\Models\Region;
use App\Models\Charity;
use App\Models\Setting;
use App\Models\Donation;
use App\Models\EmployeeJob;
use App\Models\BusinessUnit;
use App\Models\CampaignYear;
use App\Models\Organization;
use App\Models\DailyCampaign;
use App\Models\FSPoolCharity;
use App\Models\PledgeCharity;
use App\Models\BankDepositForm;
use App\Models\ScheduleJobAudit;
use Illuminate\Http\UploadedFile;
use App\Models\DailyCampaignSummary;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReportDonationDataTest extends TestCase
{

    use WithFaker;

    private User $admin;
    private User $user;

    protected static $db_inited = false;

    public function setUp(): void
    {
        parent::setUp();

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


        CampaignYear::truncate();
        Region::truncate();
        BusinessUnit::truncate();
        Organization::truncate();
        Charity::truncate();
        FSPool::truncate();
        FSPoolCharity::truncate();

        PledgeCharity::truncate();
        Pledge::truncate();

        Donation::truncate();
        
    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_the_donation_data_index_page()
    {
        $response = $this->get('reporting/donation-data');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_donation_data_create_page()
    {
        $response = $this->get('reporting/donation-data/create');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_create_the_donation_data()
    {

        $response = $this->post('reporting/donation-data', []);

        $response->assertStatus(405);
    }
    public function test_an_anonymous_user_cannot_access_the_donation_data_view_page()
    {
        $response = $this->get('reporting/donation-data/1');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_access_the_donation_data_edit_page()
    {
        $response = $this->get('reporting/donation-data/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_update_the_donation_data()
    {

        $response = $this->put('reporting/donation-data/1', [] );

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_delete_the_donation_data()
    {
        $response = $this->delete('reporting/donation-data/1');

        $response->assertStatus(404);
    }

    // Authorized User
    public function test_an_authorized_user_cannot_access_the_donation_data_index_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/donation-data');

        $response->assertStatus(403);
    }
    public function test_an_authorized_user_cannot_access_the_donation_data_create_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/donation-data/create');

        $response->assertStatus(404);
    }
    public function test_an_authorized_user_cannot_create_the_donation_data()
    {

		$this->actingAs($this->user);
        $response = $this->post('reporting/donation-data', []);

        $response->assertStatus(405);
    }
    public function test_an_authorized_user_cannot_access_the_donation_data_view_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/donation-data/1');

        $response->assertStatus(404);
    }
    public function test_an_authorized_user_cannot_access_the_donation_data_edit_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/donation-data/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_authorized_user_cannot_update_the_donation_data()
    {
		$this->actingAs($this->user);
        $response = $this->put('reporting/donation-data/1', [] );

        $response->assertStatus(404);
    }
    public function test_an_authorized_user_cannot_delete_the_donation_data()
    {
		$this->actingAs($this->user);
        $response = $this->delete('reporting/donation-data/1');

        $response->assertStatus(404);
    }


    // Administrator User
    public function test_an_administrator_can_access_the_donation_data_index_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/donation-data');

        $response->assertStatus(200);
    }
    public function test_an_administrator_can_access_the_donation_data_create_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/donation-data/create');

        $response->assertStatus(404);
    }
    public function test_an_administrator_can_create_the_donation_data()
    {

        $this->actingAs($this->admin);
        $response = $this->post('reporting/donation-data', []);

        $response->assertStatus(405);
    }
    public function test_an_administrator_cannot_access_the_donation_data_view_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/donation-data/1');

        $response->assertStatus(404);
    }
    public function test_an_administrator_cannot_access_the_donation_data_edit_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/donation-data/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_administrator_cannot_update_the_donation_data()
    {
        $this->actingAs($this->admin);
        $response = $this->put('reporting/donation-data/1', [] );

        $response->assertStatus(404);
    }
    public function test_an_administrator_cannot_delete_the_donation_data()
    {
        $this->actingAs($this->admin);
        $response = $this->delete('reporting/donation-data/1');

        $response->assertStatus(404);
    }


    /** Pagination */
    // public function test_pagination_on_campaign_years_table_via_ajax_call()
    // {
    //     // Seed data for pagination testing
    //     // $this->seed(\Database\Seeders\CampaignYearSeeder::class);
    //     $form_data = $this->get_new_record_form_data();
    //     $campignyear = CampaignYear::create( $form_data ); 

    //     $this->actingAs($this->admin);
    //     $response = $this->getJson('/reporting/donation-data', ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

    //     $response->assertStatus(200);
    //     $response->assertJsonFragment(['id' => $campignyear->id,
    //         'calendar_year' => $campignyear->calendar_year,
    //     ]);
    // }  


    // public function test_non_gov_annual_campaign_pledge_correctly_calculate()
    // {

    //     [$sum, $expected_rows] = $this->get_new_record_form_data(false, false);

    //     // run command 
    //     $this->artisan('command:UpdateDailyCampaign')->assertExitCode(0);

    //     // check online page 
    //     $this->actingAs($this->user);
    //     $response = $this->json('get', '/http://localhost:8000/challenge?year=' . $expected_rows[0]['campaign_year'], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

    //     $response->assertStatus(200);
    //     $response->assertJsonPath('data', []);
    //     $response->assertSeeText( number_format(round( $sum, 0)) );

    //     // check database
    //     foreach($expected_rows as $row) {
    //         $this->assertDatabaseHas('daily_campaigns', $row );
    //     }

    // }


    // public function test_gov_event_pledge_correctly_calculate()
    // {

    //     [$sum, $expected_rows] = $this->get_new_record_form_data(true, true);

    //     // run command 
    //     $this->artisan('command:UpdateDailyCampaign')->assertExitCode(0);

    //     // check online page 
    //     $this->actingAs($this->user);
    //     $response = $this->json('get', '/http://localhost:8000/challenge?year=' . $expected_rows[0]['campaign_year'], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

    //     $response->assertStatus(200);
    //     $response->assertJsonPath('data', []);
    //     $response->assertSeeText( number_format(round( $sum, 0)) );

    //     // check database
    //     foreach($expected_rows as $row) {
    //         $this->assertDatabaseHas('daily_campaigns', $row );
    //     }

    // }

    // public function test_non_gov_event_pledge_correctly_calculate()
    // {

    //     [$sum, $expected_rows] = $this->get_new_record_form_data(false, true);

    //     // run command 
    //     $this->artisan('command:UpdateDailyCampaign')->assertExitCode(0);

    //     // check online page 
    //     $this->actingAs($this->user);
    //     $response = $this->json('get', '/http://localhost:8000/challenge?year=' . $expected_rows[0]['campaign_year'], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

    //     $response->assertStatus(200);
    //     $response->assertJsonPath('data', []);
    //     $response->assertSeeText( number_format(round( $sum, 0)) );

    //     // check database
    //     foreach($expected_rows as $row) {
    //         $this->assertDatabaseHas('daily_campaigns', $row );
    //     }

    // }


    // // Special 
    // public function test_gov_annual_campaign_pledge_with_Government_Communications_and_Public_Engagement_correctly_calculate()
    // {

    //     [$sum, $expected_rows] = $this->get_new_record_form_data(true, false, true);

    //     // run command 
    //     $this->artisan('command:UpdateDailyCampaign')->assertExitCode(0);

    //     // check online page 
    //     $this->actingAs($this->user);
    //     $response = $this->json('get', '/http://localhost:8000/challenge?year=' . $expected_rows[0]['campaign_year'], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

    //     $response->assertStatus(200);
    //     $response->assertJsonPath('data', []);
    //     $response->assertSeeText(number_format(round( $sum, 0)) );

    //     // check database
    //     foreach($expected_rows as $row) {
    //         $this->assertDatabaseHas('daily_campaigns', $row );
    //     }

    // }

    /* Private Function */
    private function get_new_record_form_data($org_code) 
    {

        // $user = User::first();

        // $year = (today() < today()->year . '-03-01') ? today()->year - 1 : today()->year ;

        // $setting = Setting::create([
        //         'campaign_start_date' => $year . '-09-01',
        //         'campaign_end_date' => $year . '-11-15',
        //         'campaign_final_date' => (today() < today()->year . '-03-01') ? today() : $year + 1 . '-02-28',
        //         'challenge_start_date' => $year . '-09-01',
        //         'challenge_end_date' => $year . '-11-15',
        //         'challenge_final_date' => (today() < today()->year . '-03-01') ? today() : $year + 1 . '-02-28',

        // ]);

        $campaign_year = CampaignYear::create([
            'calendar_year' => 2024,
            'status' => 'A',
            'start_date' => '2023-09-01',
            'end_date' => '2023-11-30',
            'number_of_periods' => 26,
            'close_date' => '2023-12-31',
        ]);

        $mapping = [
            'LDB' => 'BCLDB',
            'BCS' => 'BCSC',
            'LA' => 'BC002',
        ];

        $business = BusinessUnit::factory()->create([
            'status' => 'A',
            'code' => $mapping[$org_code],
        ]);

        $organization = Organization::factory()->create([
                    'status' => 'A',
                    'code' => $org_code,
                    'bu_code' => $mapping[$org_code],
        ]);
        
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

        // $job = EmployeeJob::factory()->create([
        //     "organization_id" => $organization->id,
        //     "emplid" => $user->emplid,
        //     "business_unit" => $business->code,
        //     "business_unit_id" => $business->id,
        //     "tgb_reg_district" =>  $region->code,
        //     "region_id" => $region->id,
        // ]);

        // Test Transaction
   
        $pledge = Pledge::create([
            'organization_id' =>  $organization->id,

            'emplid' => null,
            'user_id' =>  0, 
            'pecsf_id' => '782668',
            'business_unit' => $business->code,
            'tgb_reg_district' => $region->code,

            'deptid' => null,
            'dept_name' => null,
            'first_name' => $this->faker->word(),
            'last_name' => $this->faker->word(),
            'city' => $this->faker->word(),
            'campaign_year_id' => $campaign_year->id,

            'type' => "P",
            'f_s_pool_id' => $fspool->id,
            'region_id' => $region->id,
            
            'one_time_amount' => 0.0,
            'pay_period_amount' => 20.0,
            'goal_amount' => 520.0,
        ]);

        // $bu = BusinessUnit::where('code', $pledge->business_unit)->first();
        // $cy = $pledge->campaign_year->calendar_year - 1;

        // $sum = $pledge->goal_amount;
        // $expected_results = [
        //     [   'campaign_year' => $cy,
        //         'as_of_date'    => today(),
        //         'daily_type'    => 0,
        //         'business_unit' => $special_bu ? 'BGCPE' : $pledge->business_unit,
        //         'donors' => 1,
        //         'dollars' => $pledge->goal_amount,
        //     ],
        //     [   'campaign_year' => $cy,
        //         'as_of_date'    => today(),
        //         'daily_type'    => 1,
        //         'region_code' => $pledge->tgb_reg_district,
        //         // 'region_name'
        //         'donors' => 1,
        //         'dollars' => $pledge->goal_amount,
        //     ],
        //     [   'campaign_year' => $cy,
        //         'as_of_date'    => today(),
        //         'daily_type'    => 2,
        //         'deptid' => $pledge->deptid,
        //         'dept_name' => $pledge->dept_name,
        //         'donors' => 1,
        //         'dollars' => $pledge->goal_amount,
        //     ],
        // ];


        // var_dump($pledge->charities->all());
        // dd('test');

        // return [$sum, $expected_results];
        return $pledge;
    }

}
