<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\FSPool;
use App\Models\Pledge;
use App\Models\Region;
use App\Models\Charity;
use App\Models\EmployeeJob;
use Illuminate\Support\Arr;
use App\Models\BusinessUnit;
use App\Models\CampaignYear;
use App\Models\Organization;
use App\Models\FSPoolCharity;
use App\Models\PledgeCharity;
use App\Models\PledgeHistory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Models\PledgeHistorySummary;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;

class AnnualCampaignTest extends TestCase
{
    use WithFaker;

    // protected static $initialized = FALSE;

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
        EmployeeJob::truncate();

        PledgeCharity::truncate();
        Pledge::truncate();

        PledgeHistory::truncate();
        PledgeHistorySummary::truncate();


    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_annual_campaign_pledge_index_page()
    {
        $response = $this->get('/annual-campaign');

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }
    public function test_an_anonymous_user_cannot_access_the_annual_campaign_pledge_create_page()
    {
        $response = $this->get('/annual-campaign/create');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_create_the_annual_campaign_pledge()
    {
        $response = $this->post('/annual-campaign', []);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_annual_campaign_pledge_view_page()
    {
        $response = $this->get('/annual-campaign/1');

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_annual_campaign_pledge_edit_page()
    {
        $response = $this->get('/annual-campaign/1/edit');

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_update_the_annual_campaign_pledge()
    {

        $response = $this->put('/annual-campaign/1', [] );

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }

    public function test_an_anonymous_user_cannot_delete_the_annual_campaign_pledge()
    {
        $response = $this->delete('/annual-campaign/1');

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }

    public function test_an_anonymous_user_cannot_duplicate_pledge()
    {
        
        $response = $this->call('post', '/annual-campaign/duplicate/1',[]);

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }

    public function test_an_anonymous_user_cannot_get_specific_fund_support_pool_detail_via_ajax_call()
    {
        // $this->actingAs($this->admin);
        $response = $this->withHeaders([
            'X-Requested-With'=> 'XMLHttpRequest',
            'Accept' => 'text/html',
        ])->get('/annual-campaign/regional-pool-detail/1');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
  

    public function test_an_anonymous_user_cannot_search_charity_via_ajax_call()
    {
        
        $response = $this->call('get', '/bank_deposit_form/organizations',[]);

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }

    public function test_an_anonymous_user_cannot_download_summary()
    {

        $response = $this->get('/annual-campaign/1/summary?download_pdf=true');

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }

    //
    // Test Authorized User
    //
    public function test_an_authorized_user_can_access_annual_campaign_pledge_index_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/annual-campaign');

        $response->assertStatus(302);
        $response->assertRedirectContains("annual-campaign/create");
    }

    public function test_an_authorized_user_can_access_the_annual_campaign_pledge_create_page()
    {
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $this->actingAs($this->user);
        $response = $this->get('/annual-campaign/create');

        $response->assertStatus(200);
        $response->assertSeeText("Make a Donation");
    }

    


    public function test_an_authorized_user_can_create_annual_campaign_pledge_successful_in_db()
    {

        // $row = CampaignYear::factory(1)->create();
        [$form_data, $pledge] = $this->get_new_record_form_data(false);
        
        $this->actingAs($this->user);
        $response = $this->post('/annual-campaign', $form_data );

        $response->assertStatus(302);
        $response->assertRedirect( '/annual-campaign/thank-you');
        $response->assertSessionHas('success');
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('pledges', Arr::except( $pledge->attributesToArray(), ['frequency']) );

        foreach ($pledge->charities as $charity) {
            $this->assertDatabaseHas('pledge_charities', Arr::except($charity->attributesToArray(),['pledge_id'] ));
        }
           
     }

    

     public function test_an_authorized_user_can_access_the_annual_campaign_pledge_view_page_contains_valid_record()
    {
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $this->actingAs($this->user);
        // access Index page
        $response = $this->get('/annual-campaign');

        $response->assertStatus(302);
        $response->assertRedirect('/annual-campaign/create');
        
    }
    public function test_an_authorized_user_can_access_annual_campaign_pledge_edit_page_contains_valid_record()
    {
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $this->actingAs($this->user);
        $response = $this->get("/annual-campaign/create");

        $response->assertStatus(200);
        $response->assertViewHasAll([
            'pool_option' => $pledge->pool_option,
            'campaign_year' => $pledge->campaign_year,
            'last_selected_charities' => $pledge->charities->pluck(['charity_id'])->toArray(),
            'regional_pool_id' => null,
        ]);

    }
    public function test_an_authorized_user_can_update_annual_campaign_pledge_successful_in_db()
    {
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $region = Region::factory()->create();
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

        $pledge->type = "P";
        $pledge->f_s_pool_id = $fspool->id;
        $pledge->region_id = $region->id;

        $new_form_data = $this->tranform_to_form_data($pledge);

        $this->actingAs($this->user);
        $response = $this->post('/annual-campaign', $new_form_data );

        $response->assertStatus(302);
        $response->assertRedirect('/annual-campaign/thank-you');
        // $response->assertSessionHas('success');
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('pledges', Arr::except( $pledge->attributesToArray(), ['frequency', 'charities', 'distinct_charities', 'updated_at', 'created_at']) );

        foreach ($pledge->charities as $charity) {
            $this->assertDatabaseHas('pledge_charities', Arr::except($charity->attributesToArray(), ['updated_at', 'created_at']));
        }

    }

    public function test_an_authorized_user_cannot_access_the_annual_campaign_pledge_view_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/annual-campaign/1');

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }
    public function test_an_authorized_user_cannot_access_the_annual_campaign_pledge_edit_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/annual-campaign/1/edit');

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }
    public function test_an_authorized_user_cannot_update_the_annual_campaign_pledge()
    {

        $this->actingAs($this->user);
        $response = $this->put('/annual-campaign/1', [] );

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }

    public function test_an_authorized_user_cannot_delete_the_annual_campaign_pledge()
    {
        $this->actingAs($this->user);
        $response = $this->delete('/annual-campaign/1');

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }

    public function test_an_authorized_user_can_duplicate_the_annual_campaign_pledge_from_bi()
    {

        $this->create_pledge_history();

        $summary = PledgeHistorySummary::first();

        $this->actingAs($this->user);
        $response = $this->call('post', '/annual-campaign/duplicate/' .$summary->id, [
            'source' => 'BI',
            'type' => $summary->campaign_type,
            'id' => $summary->id,
            'frequency' => $summary->frequency,
            'yearcd' => $summary->yearcd,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/annual-campaign/create');
        $response->assertSessionHas('new_pledge');

    }

    public function test_an_authorized_user_can_get_specific_fund_support_pool_detail_via_ajax_call()
    {
        // Seed data for pagination testing
        // $this->seed(\Database\Seeders\CampaignYearSeeder::class);
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $region = Region::factory()->create();
        $charities = Charity::factory(10)->create([
            'charity_status' => 'Registered',
        ]);
        $fspool = FSPool::factory()->create([
            'region_id' => $region->id,
            'status' => 'A',
        ]);
        $fspool_charities = FSPoolCharity::factory(1)->create([
                'f_s_pool_id' => $fspool->id,
                'charity_id' => $this->faker->randomElement( $charities->pluck('id')->toArray() ),
                'percentage' => 100,
                'status' => 'A',
        ]);

        $this->actingAs($this->user);
        $response = $this->call('get','/annual-campaign/regional-pool-detail/' . $fspool->id, [], [], [],
            [
                'HTTP_X-Requested-With'=> 'XMLHttpRequest',
                'Accept' => 'text/html',
            ]);

        $response->assertStatus(200);
        foreach ($fspool->charities as $pool_charity) {
            $response->assertSeeText( $pool_charity->charity->charity_name );
        }
    }


    public function test_an_authorized_user_can_search_charity_via_ajax_call()
    {
        // Seed data for pagination testing
        // $this->seed(\Database\Seeders\CampaignYearSeeder::class);
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $charities = Charity::factory(50)->create([
            'charity_status' => 'Registered',
        ]);
        // $region = Region::factory()->create();
        // $charities = Charity::factory(10)->create([
        //     'charity_status' => 'Registered',
        // ]);
        // $fspool = FSPool::factory()->create([
        //     'region_id' => $region->id,
        //     'status' => 'A',
        // ]);
        // $fspool_charities = FSPoolCharity::factory(1)->create([
        //         'f_s_pool_id' => $fspool->id,
        //         'charity_id' => $this->faker->randomElement( $charities->pluck('id')->toArray() ),
        //         'percentage' => 100,
        //         'status' => 'A',
        // ]);

        $this->actingAs($this->user);
        $response = $this->call('get', '/bank_deposit_form/organizations', [
            'page' => 1,
            'category' => '',
            'province' => '',
            'keyword' => $charities[0]->charity_name,
            'pool_filter' => '', 
            'selected_vendors' => '',
        ]);

        $response->assertStatus(200);
        $response->assertSeeText( "1 results" );
        $response->assertSeeText( $charities[0]->charity_name );

    }


    
    public function test_an_authoried_user_can_download_summary()
    {

        [$form_data, $pledge] = $this->get_new_record_form_data(true);
 
        $filename = "Annual Campaign Summary - " . $pledge->campaign_year->calendar_year - 1 . ".pdf";

        $this->actingAs($this->user);
        $response = $this->get("/annual-campaign/" . $pledge->id . "/summary?download_pdf=true");
    
        $response->assertStatus(200);

        $response->assertDownload( $filename ); 
        $response->assertSeeText('PDF-');

        $this->assertTrue($response->headers->get('content-type') == 'application/pdf');
        $this->assertTrue($response->headers->get('content-disposition') == 'attachment; filename="' . $filename .'"');
    }

//     // /** Form Validation */
//     // public function test_validation_rule_fields_are_required_when_create()
//     // {
//     //     // create the founcdation data 
//     //     [$form_data, $pledge] = $this->get_new_record_form_data(false, false);

//     //     $form_data =  [
//     //         "step" => "3",
//     //         "campaign_year_id" => $pledge->campaign_year_id,
//     //         "organization_id" => $pledge->organization_id,
//     //         ];

//     //     $this->actingAs($this->admin);
//     //     // Post empty data to the create page route
//     //     // $response = $this->post('/annual-campaign', $form_data);
//     //     $this->actingAs($this->admin);
//     //     $response = $this->postJson('/annual-campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
//     //     // This should cause errors with the
//     //     // title and content fields as they aren't present
//     //     $response->assertStatus(422);
//     //     $response->assertJsonValidationErrors([
//     //         'pay_period_amount_other', 
//     //         'one_time_amount_other',
//     //         'pool_option',
//     //         'pay_period_amount_error',
//     //         'one_time_amount_error',
//     //     ]);
//     // }
//     // public function test_validation_rule_fields_are_required_when_edit()
//     // {
//     //     // create the founcdation data 
//     //     [$form_data, $pledge] = $this->get_new_record_form_data(true, true);

//     //     $form_data =  [
//     //         "step" => "3",
//     //         "campaign_year_id" => $pledge->campaign_year_id,
//     //         "organization_id" => $pledge->organization_id,
//     //         ];


//     //     $this->actingAs($this->admin);
//     //     $response = $this->json('put', '/annual-campaign/' . $pledge->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        
//     //     // This should cause errors with the
//     //     // title and content fields as they aren't present
//     //     $response->assertStatus(422);
//     //     $response->assertJsonValidationErrors([
//     //         'pay_period_amount_other', 
//     //         'one_time_amount_other',
//     //         'pool_option',
//     //         'pay_period_amount_error',
//     //         'one_time_amount_error',
//     //     ]);

//     // }


    public function test_validation_rule_fields_are_required_on_step_1_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $form_data = [
            "step" => "1",
        ];
       
        $this->actingAs($this->user);

            $response = $this->postJson('/annual-campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'pool_option', 
            'number_of_periods',
        ]);

    }

    public function test_validation_rule_fields_are_required_on_step_2a_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $form_data = [
            "step" => "2",
            "pool_option" => "P",
            'number_of_periods' => 26,
        ];
      
        $this->actingAs($this->user);
        $response = $this->postJson('/annual-campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'regional_pool_id',
        ]);

    }

    public function test_validation_rule_fields_are_required_on_step_2b_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $form_data = [
            "step" => "2",
            "pool_option" => "C",
            'number_of_periods' => 26,
        ];
      
        $this->actingAs($this->user);
        $response = $this->postJson('/annual-campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'charities',
        ]);

    }

    public function test_validation_rule_fields_are_required_on_step_3a_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $form_data = [
            "step" => "3",
            "pool_option" => "P",
            'number_of_periods' => 26,
            'frequency' => 'both',
        ];
       
        $this->actingAs($this->user);
        $response = $this->postJson('/annual-campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'regional_pool_id', 
            'one_time_amount_custom',
            'bi_weekly_amount_custom',
        ]);

    }

    public function test_validation_rule_fields_are_required_on_step_3b_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $form_data = [
            "step" => "3",
            "pool_option" => "C",
            'number_of_periods' => 26,
            'frequency' => 'both',
            'one_time_amount' => '',
            'bi_weekly_amount' => '',
        ];
       
        $this->actingAs($this->user);
        $response = $this->postJson('/annual-campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'charities', 
            'one_time_amount_custom',
            'bi_weekly_amount_custom',
        ]);

    }

    public function test_validation_rule_fields_are_required_on_step_4_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $form_data["charityOneTimeAmount"] = [];
        $form_data["charityOneTimePercentage"] = [];
        $form_data["charityBiWeeklyAmount"] = [];
        $form_data["charityBiWeeklyPercentage"] = [];

        $form_data["biWeeklyAmount"] = [];
        $form_data["biWeeklyPercent"] = [];
        $form_data["oneTimeAmount"] =[];
        $form_data["oneTimePercent"] =[];

       
        $this->actingAs($this->user);
        $response = $this->postJson('/annual-campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'charityOneTimeAmount',
            'charityBiWeeklyAmount',
            'charityOneTimePercentage',
            'charityBiWeeklyPercentage',
        ]);

    }

//     public function test_validation_rule_invalid_employee_id_when_create_or_edit()
//     {
//         // create the founcdation data 
//         [$form_data, $pledge] = $this->get_new_record_form_data(true, true);

//         $form_data["step"] = 1;
//         $form_data["user_id"] = 99999991;

       
//         foreach ([0,1] as $i) {    

//             $this->actingAs($this->admin);
//             if ($i == 0) {
//                 $response = $this->postJson('/annual-campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
//             } else {
//                 $response = $this->json('put', '/annual-campaign/' . $pledge->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
//             }

//             $response->assertStatus(422);
//             $response->assertJsonValidationErrors([
//                 'user_id', 
//             ]);
//         }

//     }

    public function test_validation_rule_invalid_frequency_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $form_data["step"] = 3;
        $form_data["frequency"] = 'Other';

        $this->actingAs($this->user);
        $response = $this->postJson('/annual-campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            "frequency" => "The selected frequency is invalid.",
        ]);

    }


    public function test_validation_rule_invalid_min_amount_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $form_data["step"] = 3;
        $form_data['bi_weekly_amount'] = '';
        $form_data["bi_weekly_amount_custom"] = 0.5;
        $form_data["one_time_amount"] = '';
        $form_data["one_time_amount_custom"] = 0.5;
        
        $this->actingAs($this->user);
        $response = $this->postJson('/annual-campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'one_time_amount_custom' => "The minimum One-time custom amount is $1.",
            'bi_weekly_amount_custom' => "The minimum Bi-weekly custom amount is $1.",
        ]);
        

    }

    public function test_validation_rule_invalid_pool_option_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $form_data["step"] = 1;
        $form_data['pool_option'] = 'X';
       
        $this->actingAs($this->user);
        $response = $this->postJson('/annual-campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'pool_option' => "The selected pool option is invalid.",
        ]);

    }


    public function test_validation_rule_invalid_percentage_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $form_data["step"] = 4;

        // foreach ( $form_data["percentages"] as $key => $percent) {
        //     $form_data["percentages"][$key] = $this->faker->randomElement( [0, 101] );
        // }
        $field_names = [];
        foreach ($form_data["biWeeklyPercent"] as $key => $precentage) {
            $form_data["biWeeklyPercent"][$key] = 0;
            $form_data["oneTimePercent"][$key] = 101;

            array_push($field_names, 'oneTimePercent.'.$key);
            array_push($field_names, 'biWeeklyPercent.'.$key);

        }
       
        $this->actingAs($this->admin);
        $response = $this->postJson('/annual-campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(
                $field_names
        );

    }

    
    public function test_validation_rule_total_percentage_is_not_100_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $form_data["step"] = 4;
        // foreach ( $form_data["percentages"] as $key => $percent) {
        //     $form_data["percentages"][$key] = $this->faker->randomElement( [0, 101] );
        // }
        $field_names = [];
        foreach ($form_data["biWeeklyPercent"] as $key => $precentage) {

            if ($key == array_key_last($form_data["biWeeklyPercent"])) {
                $form_data["biWeeklyPercent"][$key] = 10.99;
                $form_data["oneTimePercent"][$key] = 10.99;
            }

            array_push($field_names, 'oneTimePercent.'.$key);
            array_push($field_names, 'biWeeklyPercent.'.$key);

        }
       
        $this->actingAs($this->user);
        $response = $this->postJson('/annual-campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(
                $field_names
        );

    }

    public function test_validation_rule_invalid_fund_support_pool_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $form_data["step"] = 2;
        // foreach ( $form_data["percentages"] as $key => $percent) {
        //     $form_data["percentages"][$key] = $this->faker->randomElement( [0, 101] );
        // }
        $form_data['pool_option'] = 'P';
        $form_data['pool_id'] = 1123;      

        $this->actingAs($this->user);
        $response = $this->postJson('/annual-campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'regional_pool_id' =>  "The selected regional pool id is invalid.",
        ]);


    }

    public function test_validation_rule_invalid_charity_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $form_data["step"] = 2;
        // foreach ( $form_data["percentages"] as $key => $percent) {
        //     $form_data["percentages"][$key] = $this->faker->randomElement( [0, 101] );
        // }
        $form_data['pool_option'] = 'C';
        $form_data['charities'] = [];
        $form_data['percentages'] = [];

        $form_data['charities'][0] = '';
        // $form_data['percentages'][0] = 101;
       
        $this->actingAs($this->user);
        $response = $this->postJson('/annual-campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'charities.0' => "The invalid charity entered."
        ]);
       

    }

   

    /* Private Function */
    private function get_new_record_form_data($bCreate = false, $is_gov = true) 
    {

        $user = User::first();

        $campaign_year = CampaignYear::create([
            'calendar_year' => today()->year + 1,
            'status' => 'A',
            'start_date' => today(),
            'end_date' => today()->year . '-12-31',
            'number_of_periods' => 26,
            'close_date' => today()->year . '-12-31',
        ]);


        $organization = Organization::factory()->create([
                    'code' => "GOV",
        ]);
        if (!($is_gov)) {
            $organization = Organization::factory()->create([
                    'code' => "LDB",
                ]);
        }
        $business = BusinessUnit::factory()->create();
        $region = Region::factory()->create();
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
        $pledge = new Pledge([
            'organization_id' =>  $organization->id,
            'emplid' => $is_gov ? $user->emplid : null,
            'user_id' => $is_gov ? $user->id : 0, 
            'pecsf_id' => $is_gov ? null : '882288',
            'business_unit' => $is_gov ? $job->business_unit : $business->code,
            'tgb_reg_district' => $is_gov ? $job->tgb_reg_district : $region->code,
            'deptid' => $is_gov  ? $job->deptid : null,
            'dept_name' => $is_gov  ? $job->dept_name : null,
            'first_name' => null,
            'last_name' => null,
            'city' => $job->office_city,
            'campaign_year_id' => $campaign_year->id,
            'type' => "C",
            'region_id' => null,
            'f_s_pool_id' => 0,
            'one_time_amount' => 50.0,
            'pay_period_amount' => 20.0,
            'goal_amount' => 570.0,
        ]);

        foreach ($charities as $key => $charity) {
            $text = $this->faker->words(2, true);

            $item = new PledgeCharity([
                        'charity_id' => $charity->id,
                        'pledge_id' => $pledge->id ?? 1,
                        'frequency' => 'one-time',
                        'additional' => $text,
                        'percentage' => 10,
                        'amount' => $pledge->one_time_amount * 0.1,
                        'goal_amount' => $pledge->one_time_amount * 0.1,
                    ]);
            $pledge->charities[$key] = $item; 

            $item = new PledgeCharity([
                'charity_id' => $charity->id,
                    'pledge_id' => $pledge->id ?? 1,
                    'frequency' => 'bi-weekly',
                    'additional' => $text,
                    'percentage' => 10,
                    'amount' => $pledge->pay_period_amount * 0.1,
                    'goal_amount' => ($pledge->goal_amount - $pledge->one_time_amount) * 0.1,
                ]);
            $pledge->charities[$key + 10] = $item; 
        }

        $form_data = $this->tranform_to_form_data($pledge);


        if ($bCreate) {
            // create 
            // $pledge->save();
            $pledge->push();  // save your model and all of its associated relationships
        }

        // var_dump($pledge->charities->all());
        // dd('test');

        return [$form_data, $pledge];
    }

    private function tranform_to_form_data($pledge)
    {

        $form_data = [
            "step" => "5",
            "campaign_year_id" => $pledge->campaign_year_id,
            "number_of_periods" => 26,
            "organization_id" => $pledge->organization_id,
            "emplid" => $pledge->emplid,
            "user_id" => $pledge->user_id,
            "pecsf_id" => $pledge->pecsf_id,
            "pecsf_first_name" => $pledge->first_name,
            "pecsf_last_name" => $pledge->last_name,
            "pecsf_city" => $pledge->first_name,
            "pecsf_bu" => $pledge->business_unit,
            "pecsf_region" => $pledge->tgb_reg_district,
            "user_office_city" => $pledge->city,
            // "pay_period_amount" => in_array($pledge->pay_period_amount, [0,6,12,20,50]) ?  $pledge->pay_period_amount : '',
            // "pay_period_amount_other" => in_array($pledge->pay_period_amount, [0,6,12,20,50]) ? 0 : $pledge->pay_period_amount,
            // "one_time_amount" => in_array($pledge->one_time_amount, [0,6,12,20,50]) ?  $pledge->one_time_amount : '',
            // "one_time_amount_other" => in_array($pledge->one_time_amount, [0,6,12,20,50]) ? 0 : $pledge->one_time_amount,
            // "pool_id" => $pledge->f_s_pool_id,
            "pool_option" => $pledge->type,

            "regional_pool_id" => $pledge->f_s_pool_id,
            "frequency" => ($pledge->one_time_amount > 0 && $pledge->pay_period_amount > 0) ? 'both' : 
                            (($pledge->one_time_amount > 0) ? 'one-time' : 
                                (($pledge->pay_period_amount > 0) ? 'bi-weekly' : '')),

            "bi_weekly_amount" => in_array($pledge->pay_period_amount, [0,6,12,20,50]) ?  $pledge->pay_period_amount : '',
            "bi_weekly_amount_custom" => in_array($pledge->pay_period_amount, [0,6,12,20,50]) ? 0 : $pledge->pay_period_amount,
            "one_time_amount" => in_array($pledge->one_time_amount, [0,6,12,20,50]) ?  $pledge->one_time_amount : '',
            "one_time_amount_custom" => in_array($pledge->one_time_amount, [0,6,12,20,50]) ? 0 : $pledge->one_time_amount,

            "annualOneTimeAmount" => $pledge->one_time_amount,
            "annualBiWeeklyAmount" =>  $pledge->goal_amount - $pledge->one_time_amount,

            "charities" => [],
            "charityAdditional" => [],
            
            // "additional" => [],
            // "percentages" => [],
            "charityOneTimeAmount" => [],
            "charityOneTimePercentage" => [],
            "charityBiWeeklyAmount" =>  [],
            "charityBiWeeklyPercentage" =>  [],

            "biWeeklyAmount" => [],
            "biWeeklyPercent" => [],
            "oneTimeAmount" => [],
            "oneTimePercent" => [],

        ];

        if ($pledge->type == 'C') {
            for ($i = 0; $i < 10; $i++) {
                // array_push($form_data["charities"], $pledge->charities[$i]->charity_id);
                // array_push($form_data["additional"], $pledge->charities[$i]->additional);
                // array_push( $form_data["percentages"], $pledge->charities[$i]->percentage);

                $charity_id = $pledge->charities[$i]->charity_id;
                $percentage = $pledge->charities[$i]->percentage;
                $additional = $pledge->charities[$i]->additional;

                array_push($form_data["charities"], $charity_id);
                $form_data["charityAdditional"][$charity_id] = $additional;

                $form_data["biWeeklyAmount"][$charity_id] = $pledge->pay_period_amount * ($percentage / 100);
                $form_data["biWeeklyPercent"][$charity_id] = $percentage;

                $form_data["oneTimeAmount"][$charity_id] = $pledge->one_time_amount * ($percentage / 100);
                $form_data["oneTimePercent"][$charity_id] = $percentage;



                $form_data["charityOneTimeAmount"][$charity_id] = $pledge->one_time_amount * ($percentage / 100);
                $form_data["charityOneTimePercentage"][$charity_id] = $percentage;
                $form_data["charityBiWeeklyAmount"][$charity_id] = $pledge->pay_period_amount * ($percentage / 100);
                $form_data["charityBiWeeklyPercentage"][$charity_id] = $percentage;
            }

        }

        // foreach ($pledge->charities() as $key => $pool_charity) {

        //     array_push($form_data["charities"], $pool_charity->charity_id);
        //     array_push($form_data["additional"], $pool_charity->additional);
        //     array_push( $form_data["percentages"], $percentages, $pool_charity->percentage);

        // }

// dd($form_data["charities"]);        
// dd($pledge->charities);        
        return $form_data;

    } 

    private function create_pledge_history()  {


        $user = User::first();

        $campaign_year = CampaignYear::create([
            'calendar_year' => today()->year + 1,
            'status' => 'A',
            'start_date' => today(),
            'end_date' => today()->year . '-12-31',
            'number_of_periods' => 26,
            'close_date' => today()->year . '-12-31',
        ]);

        $last_campaign_year = CampaignYear::create([
            'calendar_year' => today()->year,
            'status' => 'I',
            'start_date' => (today()->year - 1) . '-09-01',
            'end_date' => (today()->year - 1) . '-12-31',
            'number_of_periods' => 26,
            'close_date' => (today()->year - 1 ). '-12-31',
        ]);


        $organization = Organization::factory()->create([
                    'code' => "GOV",
        ]);
      
        $business = BusinessUnit::factory()->create();
        $region = Region::factory()->create();
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
        
        foreach ( $charities as $key => $charity) {
            PledgeHistory::factory()->create([
                'campaign_type' => 'Annual',
                'source' => 'Non-Pool',
                'tgb_reg_district' => $region->code,
                'region_id' => $region->id,
                'charity_bn' => $charity->registration_number,
                'charity_id' => $charity->id,
                'yearcd' => $last_campaign_year->calendar_year,
                'campaign_year_id' => $last_campaign_year->id,
                'name1' => $charity->charity_name,
                'name2' => $region->name,
                'emplid' => $user->emplid,

                'frequency' =>  'Bi-Weekly',

                'per_pay_amt' => (156 / 26) * (100 / $charities->count() / 100),
                'pledge' => 156,
                'percent' => (100 / $charities->count()),
                'amount' => 156 * (100 / $charities->count() / 100),


                'vendor_name1' => $charity->charity_name,
                'vendor_name2' => '',
                'vendor_bn' => $charity->registration_number,
                'business_unit' => $job->business_unit,
                'event_type' =>  'PECSF',
                'event_sub_type' => '',
                'event_deposit_date' => $last_campaign_year->calendar_year . '-01-01',
            ]);
        }

        DB::statement( $this->getInsertAnnualSummarySQL() );

    }


    private function getInsertAnnualSummarySQL(): string
    {
        return <<<SQL
            insert into pledge_history_summaries
                (pledge_history_id,emplid,yearcd,source,campaign_type,frequency,per_pay_amt,pledge,region, event_type, event_sub_type, event_deposit_date)
                select min(pledge_histories.id), emplid, yearcd, case when max(source) = 'Pool' then 'P' else 'C' end,  
                    campaign_type, frequency, 
                    case when frequency = 'Bi-Weekly' then max(pledge / 26) else 0 end per_pay_amt, max(pledge) as pledge, 
                    case when max(source) = 'Pool' then max(pledge_histories.tgb_reg_district) else '' end,

                    null as event_type,
                    null as event_sub_type,
                    null as event_deposit_date

                from pledge_histories  
                where campaign_type in ('Annual') 
                group by emplid, yearcd, campaign_type, frequency;
        SQL;

    }
}
