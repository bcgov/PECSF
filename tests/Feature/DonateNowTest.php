<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\FSPool;
use App\Models\Region;
use App\Models\Charity;
use App\Models\EmployeeJob;
use App\Models\PayCalendar;
use Illuminate\Support\Arr;
use App\Models\BusinessUnit;
use App\Models\CampaignYear;
use App\Models\Organization;
use App\Models\FSPoolCharity;
use App\Models\DonateNowPledge;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Models\PledgeHistorySummary;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;

class DonateNowTest extends TestCase
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

        DonateNowPledge::truncate();

        // PledgeHistory::truncate();
        // PledgeHistorySummary::truncate();


    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_donate_now_pledge_index_page()
    {
        $response = $this->get('/donate-now');

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }
    public function test_an_anonymous_user_cannot_access_the_donate_now_pledge_create_page()
    {
        $response = $this->get('/donate-now/create');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_create_the_donate_now_pledge()
    {
        $response = $this->post('/donate-now', []);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_donate_now_pledge_view_page()
    {
        $response = $this->get('/donate-now/1');

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_donate_now_pledge_edit_page()
    {
        $response = $this->get('/donate-now/1/edit');

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_update_the_donate_now_pledge()
    {

        $response = $this->put('/donate-now/1', [] );

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }

    public function test_an_anonymous_user_cannot_delete_the_donate_now_pledge()
    {
        $response = $this->delete('/donate-now/1');

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }

    public function test_an_anonymous_user_cannot_get_specific_fund_support_pool_detail_via_ajax_call()
    {
        // $this->actingAs($this->admin);
        $response = $this->withHeaders([
            'X-Requested-With'=> 'XMLHttpRequest',
            'Accept' => 'text/html',
        ])->get('/donate-now/regional-pool-detail/1');

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

        $response = $this->get('/donate-now/1/summary?download_pdf=true');

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }

    //
    // Test Authorized User
    //
    public function test_an_authorized_user_can_access_donate_now_pledge_index_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/donate-now');

        $response->assertStatus(302);
        $response->assertRedirectContains("donate-now/create");
    }

    public function test_an_authorized_user_can_access_the_donate_now_pledge_create_page()
    {
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $this->actingAs($this->user);
        $response = $this->get('/donate-now/create');

        $response->assertStatus(200);
        $response->assertSeeText("Make a one-time donation");
    }

    


    public function test_an_authorized_user_can_create_donate_now_pledge_successful_in_db()
    {

        // $row = CampaignYear::factory(1)->create();
        [$form_data, $pledge] = $this->get_new_record_form_data(false);
        
        $this->actingAs($this->user);
        $response = $this->post('/donate-now', $form_data );

        $response->assertStatus(302);
        $response->assertRedirect( '/donate-now/thank-you');
        $response->assertSessionHas('success');
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('donate_now_pledges', Arr::except( $pledge->attributesToArray(), ['in_support_of']) );
           
     }

    //  public function test_an_authorized_user_can_access_the_donate_now_pledge_view_page_contains_valid_record()
    // {
    //     [$form_data, $pledge] = $this->get_new_record_form_data(true);

    //     $this->actingAs($this->user);
    //     // access Index page
    //     $response = $this->get('/donate-now');

    //     $response->assertStatus(302);
    //     $response->assertRedirect('/donate-now/create');
        
    // }
    // public function test_an_authorized_user_can_access_donate_now_pledge_edit_page_contains_valid_record()
    // {
    //     [$form_data, $pledge] = $this->get_new_record_form_data(true);

    //     $this->actingAs($this->user);
    //     $response = $this->get("/donate-now/create");

    //     $response->assertStatus(200);
    //     $response->assertViewHasAll([
    //         'pool_option' => $pledge->pool_option,
    //         'campaign_year' => $pledge->campaign_year,
    //         'last_selected_charities' => $pledge->charities->pluck(['charity_id'])->toArray(),
    //         'regional_pool_id' => null,
    //     ]);

    // }

    public function test_an_authorized_user_cannot_access_the_donate_now_pledge_view_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/donate-now/1');

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }
    public function test_an_authorized_user_cannot_access_the_donate_now_pledge_edit_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/donate-now/1/edit');

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }
    public function test_an_authorized_user_cannot_update_the_donate_now_pledge()
    {

        $this->actingAs($this->user);
        $response = $this->put('/donate-now/1', [] );

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }

    public function test_an_authorized_user_cannot_delete_the_donate_now_pledge()
    {
        $this->actingAs($this->user);
        $response = $this->delete('/donate-now/1');

        $response->assertStatus(404);
        // $response->assertRedirect('login');
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
        $response = $this->call('get','/donate-now/regional-pool-detail/' . $fspool->id, [], [], [],
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

    public function test_an_authorized_user_can_download_summary()
    {

        [$form_data, $pledge] = $this->get_new_record_form_data(true);
 
        $filename = "Donate Now Summary - " . today()->format('Y-m-d') . ".pdf";

        $this->actingAs($this->user);
        $response = $this->get("/donate-now/" . $pledge->id . "/summary?download_pdf=true");
    
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
//     //     // $response = $this->post('/donate-now', $form_data);
//     //     $this->actingAs($this->admin);
//     //     $response = $this->postJson('/donate-now', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
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
//     //     $response = $this->json('put', '/donate-now/' . $pledge->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        
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

            $response = $this->postJson('/donate-now', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'pool_option' =>  "The pool option field is required.",
        ]);

    }

    public function test_validation_rule_fields_are_required_on_step_2a_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $form_data = [
            "step" => "2",
            "pool_option" => "P",
        ];
      
        $this->actingAs($this->user);
        $response = $this->postJson('/donate-now', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            "pool_id" => "A Fund Supported Pool selection is required. Please choose a Fund Supported Pool.",
        ]);

    }

    public function test_validation_rule_fields_are_required_on_step_2b_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $form_data = [
            "step" => "2",
            "pool_option" => "C",
        ];
      
        $this->actingAs($this->user);
        $response = $this->postJson('/donate-now', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            "charities" => "At least one charity must be specified.",
        ]);

    }

    public function test_validation_rule_fields_are_required_on_step_3_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $form_data = [
            "step" => "3",
            "pool_option" => "P",
            "pool_id" => 1,
        ];
       
        $this->actingAs($this->user);
        $response = $this->postJson('/donate-now', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            "one_time_amount_custom" => "The amount is required.",
        ]);

    }

    public function test_validation_rule_invalid_min_amount_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $form_data["step"] = 3;
        $form_data["one_time_amount"] = '';
        $form_data["one_time_amount_custom"] = 0.01;
        
        $this->actingAs($this->user);
        $response = $this->postJson('/donate-now', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'one_time_amount_custom' => "The minimum One-time custom amount is $1",
        ]);
        

    }

    public function test_validation_rule_invalid_pool_option_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $form_data["step"] = 1;
        $form_data['pool_option'] = 'X';
       
        $this->actingAs($this->user);
        $response = $this->postJson('/donate-now', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'pool_option' => "The selected pool option is invalid.",
        ]);

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
        $response = $this->postJson('/donate-now', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            "pool_id" => "The selected pool id is invalid.",
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
        
        $form_data['charities'][0] = '';
        // $form_data['percentages'][0] = 101;
       
        $this->actingAs($this->user);
        $response = $this->postJson('/donate-now', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'charities.0' => "The invalid charity entered."
        ]);
       

    }


    public function test_validation_rule_more_than_one_charity_entered_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $form_data["step"] = 2;
        // foreach ( $form_data["percentages"] as $key => $percent) {
        //     $form_data["percentages"][$key] = $this->faker->randomElement( [0, 101] );
        // }
        $form_data['pool_option'] = 'C';
        $form_data['charities'][0] = 1;
        $form_data['charities'][1] = 2;
        // $form_data['percentages'][0] = 101;
       
        $this->actingAs($this->user);
        $response = $this->postJson('/donate-now', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            "charities" => "More than one charity chosen.",
        ]);
        
    }

   

    /* Private Function */
    private function get_new_record_form_data($bCreate = false, $is_gov = true) 
    {

        $user = User::first();

        $campaign_year = CampaignYear::create([
            'calendar_year' => today()->year + 1,
            'status' => 'I',
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

        $current = PayCalendar::whereRaw(" ( date(SYSDATE()) between pay_begin_dt and pay_end_dt) ")->first();
        $period = PayCalendar::where('check_dt', '>=', $current->check_dt )->skip(2)->take(1)->orderBy('check_dt')->first();

        // Test Transaction
        $pledge = DonateNowPledge::factory()->make([
            'organization_id' =>  $organization->id,
            'emplid' => $is_gov ? $user->emplid : null,
            'user_id' => $is_gov ? $user->id : null, 
            'pecsf_id' => $is_gov ? null : '882288',
            'yearcd' => today()->year,
            'seqno' => 1,

            // 'business_unit' => $is_gov ? $job->business_unit : $business->code,
            // 'tgb_reg_district' => $is_gov ? $job->tgb_reg_district : $region->code,
            // 'deptid' => $is_gov  ? $job->deptid : null,
            // 'dept_name' => $is_gov  ? $job->dept_name : null,
            // 'first_name' => null,
            // 'last_name' => null,
            // 'city' => $job->office_city,
            // 'campaign_year_id' => $campaign_year->id,
            'type' => "C",
            'region_id' => null,
            'f_s_pool_id' => null,
            'charity_id' => $charities[0]->id,
            'special_program' => $this->faker->word(),
            'one_time_amount' => 84.32,
            'deduct_pay_from' => $period->check_dt,
            'first_name' =>  $is_gov ? null : $this->faker->word(),
            'last_name' => $is_gov ? null : $this->faker->word(),
            'city' => $is_gov ? null : $this->faker->word(),
        ]);

        $form_data = $this->tranform_to_form_data($pledge);

        if ($bCreate) {
            // create 
            $pledge->save();
            
        }

        // var_dump($pledge->charities->all());
        // dd('test');

        return [$form_data, $pledge];
    }

    private function tranform_to_form_data($pledge)
    {

        $form_data = [
            "step" => "4",
            "yearcd" => $pledge->yearcd,
            "pool_option" => $pledge->type,
            "pool_id" => ($pledge->type == 'P') ? $pledge->f_s_pool_id : null,

            // "user_id" => $pledge->user_id,
            // "pecsf_id" => $pledge->pecsf_id,
            // "pecsf_first_name" => $pledge->first_name,
            // "pecsf_last_name" => $pledge->last_name,
            // "pecsf_city" => $pledge->first_name,
            // "pecsf_bu" => $pledge->business_unit,
            // "pecsf_region" => $pledge->tgb_reg_district,
            // "user_office_city" => $pledge->city,
            // "pay_period_amount" => in_array($pledge->pay_period_amount, [0,6,12,20,50]) ?  $pledge->pay_period_amount : '',
            // "pay_period_amount_other" => in_array($pledge->pay_period_amount, [0,6,12,20,50]) ? 0 : $pledge->pay_period_amount,
            // "one_time_amount" => in_array($pledge->one_time_amount, [0,6,12,20,50]) ?  $pledge->one_time_amount : '',
            // "one_time_amount_other" => in_array($pledge->one_time_amount, [0,6,12,20,50]) ? 0 : $pledge->one_time_amount,
            // "pool_id" => $pledge->f_s_pool_id,


            "one_time_amount" => in_array($pledge->one_time_amount, [0,6,12,20,50]) ?  $pledge->one_time_amount : '',
            "one_time_amount_custom" => in_array($pledge->one_time_amount, [0,6,12,20,50]) ? 0 : $pledge->one_time_amount,

            "vendor_id"  => ($pledge->type == 'C') ? [ $pledge->charity_id ] : null,  
            "charities"  => ($pledge->type == 'C') ? [ $pledge->charity_id ] : null,
            "additional" => ($pledge->type == 'C') ? [ $pledge->special_program ] : null,

        ];

// dd($form_data["charities"]);        
// dd($pledge->charities);        
        return $form_data;

    } 


}
