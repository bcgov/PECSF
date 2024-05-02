<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\FSPool;
use App\Models\Pledge;
use App\Models\Region;
use App\Models\Charity;
use App\Models\EmployeeJob;
use App\Models\PayCalendar;
use Illuminate\Support\Arr;
use App\Models\BusinessUnit;
use App\Models\CampaignYear;
use App\Models\Organization;
use App\Models\FSPoolCharity;
use App\Models\PledgeCharity;
use App\Models\DonateNowPledge;
use App\Models\SpecialCampaign;
use Illuminate\Http\UploadedFile;
use App\Models\SpecialCampaignPledge;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;

class AdminDonateNowPledgeTest extends TestCase
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

    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_admin_donate_now_pledge_index_page()
    {
        $response = $this->get('/admin-pledge/donate-now');

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }
    public function test_an_anonymous_user_cannot_access_the_admin_donate_now_pledge_create_page()
    {
        $response = $this->get('/admin-pledge/donate-now/create');

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }
    public function test_an_anonymous_user_cannot_create_the_admin_donate_now_pledge()
    {
        $response = $this->post('/admin-pledge/donate-now', []);

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }
    public function test_an_anonymous_user_cannot_access_the_admin_donate_now_pledge_view_page()
    {
        $response = $this->get('/admin-pledge/donate-now/1');

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }
    public function test_an_anonymous_user_cannot_access_the_admin_donate_now_pledge_edit_page()
    {
        $response = $this->get('/admin-pledge/donate-now/1/edit');

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }
    public function test_an_anonymous_user_cannot_update_the_admin_donate_now_pledge()
    {

        $response = $this->put('/admin-pledge/donate-now/1', [] );

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }

    public function test_an_anonymous_user_cannot_delete_the_admin_donate_now_pledge()
    {
        $response = $this->delete('/admin-pledge/donate-now/1');

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }


    public function test_an_unauthorized_user_cannot_access_admin_donate_now_pledge_index_page()
    {
        $this->actingAs($this->user);

        $response = $this->get('/admin-pledge/donate-now');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_admin_donate_now_pledge_create_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/admin-pledge/donate-now/create');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_create_the_admin_donate_now_pledge()
    {

        $this->actingAs($this->user);
        $response = $this->post('/admin-pledge/donate-now', []);

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_admin_donate_now_pledge_view_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/admin-pledge/donate-now/1');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_admin_donate_now_pledge_edit_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/admin-pledge/donate-now/1/edit');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_update_the_admin_donate_now_pledge()
    {
        $this->actingAs($this->user);
        $response = $this->put('/admin-pledge/donate-now/1', [] );

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_delete_the_admin_donate_now_pledge()
    {
        $this->actingAs($this->user);
        $response = $this->delete('/admin-pledge/donate-now/1');

        $response->assertStatus(403);
    }

    // Administrator 
    public function test_an_administrator_can_access_admin_donate_now_pledge_index_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/admin-pledge/donate-now');

        $response->assertStatus(200);

    }
    public function test_an_administrator_can_access_the_admin_donate_now_pledge_create_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/admin-pledge/donate-now/create');

        $response->assertStatus(200);
    }
    public function test_an_administrator_can_create_admin_donate_now_pledge_successful_in_db()
    {

        // $row = CampaignYear::factory(1)->create();
        [$form_data, $pledge] = $this->get_new_record_form_data(false);
        
        $this->actingAs($this->admin);
        $response = $this->post('/admin-pledge/donate-now', $form_data );

        $response->assertStatus(204);
        // $response->assertRedirect( '/admin-pledge/donate-now');
        $response->assertSessionHas('success');
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('donate_now_pledges', Arr::except( $pledge->attributesToArray(), ['in_support_of']) );
           
     }

     public function test_an_administrator_can_access_the_admin_donate_now_pledge_view_page_contains_valid_record()
    {
        // $row = CampaignYear::factory(1)->create();
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $this->actingAs($this->admin);
        // access Index page
        $response = $this->get('/admin-pledge/donate-now');

        $response->assertStatus(200);
        $this->actingAs($this->admin);
        $response = $this->get("/admin-pledge/donate-now/{$pledge->id}");
        $response->assertStatus(200);
        $response->assertViewHas('pledge', $pledge);
    }
    public function test_an_administrator_can_access_admin_donate_now_pledge_edit_page_contains_valid_record()
    {
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $this->actingAs($this->admin);
        $response = $this->get("/admin-pledge/donate-now/{$pledge->id}/edit");

        $response->assertStatus(200);
        $response->assertViewHas('pledge', $pledge) ;

// $data = $response->getOriginalContent()->getData();
// dd($response->viewData('pool_option'));
// echo( $pledge->charities );
        // if ($pledge->type == 'C') {
        //     $response->assertViewHas('pledges_charities', $pledge->charities);
        // }
        // if ($pledge->type == 'C') {
            // foreach ($pledge->charities as $charity) {
            //     $row = new PledgeCharity([
            //         'charity_id' => $charity->id,
            //         'additional' => $charity->additional,
            //         'percentage' => $charity->percentage,
            //     ]); 
                // $response->assertViewHas('pledges_charities', $pledge->charities);
            // }
        // }
    }
    public function test_an_administrator_can_update_admin_donate_now_pledge_successful_in_db()
    {
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $pledge->one_time_amount = 40;

        $new_form_data = $this->tranform_to_form_data($pledge);

        $this->actingAs($this->admin);
        $response = $this->put('/admin-pledge/donate-now/' . $pledge->id, $new_form_data );

        $response->assertStatus(204);
        // $response->assertRedirect('/admin-pledge/donate-now');
        $response->assertSessionHas('success');
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('donate_now_pledges', Arr::except( $pledge->attributesToArray(), ['in_support_of','updated_at', 'created_at']) );


    }

    public function test_an_administrator_can_delete_the_admin_donate_now_pledge_in_db()
    {
        // $campignyears = CampaignYear::factory(1)->create();
        // $row = $campignyears->first();
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $this->actingAs($this->admin);
        $response = $this->json('delete', '/admin-pledge/donate-now/' . $pledge->id, [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(204);
        // Assert the file was deleted ...
        $this->assertSoftDeleted('donate_now_pledges',  Arr::except($pledge->attributesToArray(), ['in_support_of','updated_at', 'created_at'])  );

    }

    public function test_an_administrator_cannot_delete_the_admin_gov_donate_now_pledge_in_db()
    {
        // $campignyears = CampaignYear::factory(1)->create();
        // $row = $campignyears->first();
        [$form_data, $pledge] = $this->get_new_record_form_data(true, true);

        $this->actingAs($this->admin);
        $response = $this->json('delete', '/admin-pledge/donate-now/' . $pledge->id, [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJson([
            'error' => "You are not allowed to delete this pledge " . $pledge->id . " which was created for 'Gov' organization.",
        ]);

    }

    public function test_an_administrator_can_cancel_the_admin_donate_now_pledge_in_db()
    {
        [$form_data, $pledge] = $this->get_new_record_form_data(true, true);

        $this->actingAs($this->admin);
        $response = $this->json('post', '/admin-pledge/donate-now/' . $pledge->id .'/cancel', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(204);
        // Assert the file was deleted ...
        $this->assertDatabaseHas('donate_now_pledges',  [ 'id' => $pledge->id, 'cancelled' => 'Y'] );

    }


    public function test_an_administrator_cannot_cancel_admin_non_gov_donate_now_pledge_in_db()
    {

        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $this->actingAs($this->admin);
        $response = $this->json('post', '/admin-pledge/donate-now/' . $pledge->id .'/cancel', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(204);
        $this->assertDatabaseHas('donate_now_pledges',  [ 'id' => $pledge->id, 'cancelled' => 'Y'] );

    }

    // /** Pagination */
    public function test_pagination_on_admin_donate_now_pledges_table_via_ajax_call()
    {
        // Seed data for pagination testing
        // $this->seed(\Database\Seeders\CampaignYearSeeder::class);
        [$form_data, $pledge] = $this->get_new_record_form_data(true, true);

        $this->actingAs($this->admin);
        $response = $this->getJson('/admin-pledge/donate-now', ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $pledge->id,
            'user_id' => $pledge->user->id,
            'one_time_amount' => $pledge->one_time_amount,
            'in_support_of' => $pledge->in_support_of,
        ]);
    }

    /** Form Validation */
    public function test_validation_rule_fields_are_required_when_create_or_edit()
    {
        [$form_data, $pledge] = $this->get_new_record_form_data(true, true);
        
        foreach ([0,1] as $i) { 
            $this->actingAs($this->admin);
            if ($i == 0) {
                $response = $this->postJson('/admin-pledge/donate-now', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            } else {
                $response = $this->json('put', '/admin-pledge/donate-now/' . $pledge->id, [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            }

            $response->assertStatus(422);
            $response->assertJsonValidationErrors([
                "organization_id" =>"The organization id field is required.",
                "pecsf_id" => "The pecsf id field is required.",
                "pecsf_first_name" => "The pecsf first name field is required.",
                "pecsf_last_name" => "The pecsf last name field is required.",
                "pecsf_city" => "The pecsf city field is required.",
                "pool_option" =>"The pool option field is required.",
                "one_time_amount" => "The amount is required.",
            ]);
        }
        
    }

    // public function test_validation_rule_invalid_donate_now_id_when_create_or_edit()
    // {
    //     // create the founcdation data 
    //     [$form_data, $pledge] = $this->get_new_record_form_data(true, true);

    //     $form_data["special_campaign_id"] = 99999991;
      
    //     foreach ([0,1] as $i) { 
    //         $this->actingAs($this->admin);
    //         if ($i == 0) {
    //             $response = $this->postJson('/admin-pledge/donate-now', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
    //         } else {
    //             $response = $this->json('put', '/admin-pledge/donate-now/' . $pledge->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
    //         }

    //         $response->assertStatus(422);
    //         $response->assertJsonValidationErrors([
    //             'special_campaign_id' =>  "The selected special campaign is invalid.",
    //         ]);
    //     }

    // }

    public function test_validation_rule_invalid_fund_support_pool_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $form_data['pool_option'] = 'P';
        $form_data['pool_id'] = 1123;      

        $this->actingAs($this->admin);
        $response = $this->postJson('/admin-pledge/donate-now', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            "pool_id" => "The selected pool id is invalid.",
        ]);

    }

    public function test_validation_rule_invalid_charity_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $form_data['pool_option'] = 'C';
        $form_data['charity_id'] = 14445454;
      
        $this->actingAs($this->admin);
        $response = $this->postJson('/admin-pledge/donate-now', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'charity_id' =>  "The selected charity id is invalid.",
        ]);

    }


    public function test_validation_rule_invalid_employee_id_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, true);

        $form_data["user_id"] = 99999991;
      
        foreach ([0,1] as $i) { 
            $this->actingAs($this->admin);
            if ($i == 0) {
                $response = $this->postJson('/admin-pledge/donate-now', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            } else {
                $response = $this->json('put', '/admin-pledge/donate-now/' . $pledge->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            }

            $response->assertStatus(422);
            $response->assertJsonValidationErrors([
                'user_id', 
            ]);
        }

    }

    public function test_validation_rule_invalid_pecsf_id_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $form_data["pecsf_id"] = 'aaghsd';
    
        foreach ([0,1] as $i) {  
            $this->actingAs($this->admin);
            if ($i == 0) {
                $response = $this->postJson('/admin-pledge/donate-now', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            } else {
                $response = $this->json('put', '/admin-pledge/donate-now/' . $pledge->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            }

            $response->assertStatus(422);
            $response->assertJsonValidationErrors([
                'pecsf_id' => "The pecsf id must be 6 digits.",
            ]);
        }

    }


    public function test_validation_rule_invalid_min_amount_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, true);

        $form_data["one_time_amount"] = 0.01;

        foreach ([0,1] as $i) {  
            $this->actingAs($this->admin);
            if ($i == 0) {
                $response = $this->postJson('/admin-pledge/donate-now', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            } else {
                $response = $this->json('put', '/admin-pledge/donate-now/' . $pledge->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            }
        
            $response->assertStatus(422);
            $response->assertJsonValidationErrors([
                'one_time_amount' =>  "The minimum One-time custom amount is $1",
            ]);
        }

    }


    public function test_validation_rule_name_fields_are_required_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $form_data["pecsf_first_name"] = null;
        $form_data["pecsf_last_name"] = null;
        $form_data["pecsf_city"] = null;

        foreach ([0,1] as $i) {    
            $this->actingAs($this->admin);
            if ($i == 0) {
                $response = $this->postJson('/admin-pledge/donate-now', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            } else {
                $response = $this->json('put', '/admin-pledge/donate-now/' . $pledge->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            }
        
            $response->assertStatus(422);
            $response->assertJsonValidationErrors([
                'pecsf_first_name' => "The pecsf first name field is required.",
                "pecsf_last_name" => "The pecsf last name field is required.",
                "pecsf_city" => "The pecsf city field is required.",
            ]);
        }
    }

   

    /* Private Function */
    // private function get_new_record_form_data($bCreate = false, $is_gov = true) 
    // {

    //     $user = User::first();


    //     $organization = Organization::factory()->create([
    //                 'code' => "GOV",
    //     ]);
    //     if (!($is_gov)) {
    //         $organization = Organization::factory()->create([
    //                 'code' => "LDB",
    //             ]);
    //     }

    //     $business = BusinessUnit::factory()->create();
    //     $region = Region::factory()->create();
    //     $charities = Charity::factory(10)->create([
    //         'charity_status' => 'Registered',
    //     ]);

    //     $job = EmployeeJob::factory()->create([
    //         "organization_id" => $organization->id,
    //         "emplid" => $user->emplid,
    //         "business_unit" => $business->code,
    //         "business_unit_id" => $business->id,
    //         "tgb_reg_district" =>  $region->code,
    //         "region_id" => $region->id,
    //     ]);

    //     $charity = Charity::factory()->create();
    //     $file = UploadedFile::fake()->image('avatar.jpg');
             
    //     $specialCampaign = SpecialCampaign::factory()->create([
    //         'charity_id' => $charity->id,
    //         'start_date' => '1990-01-01',
    //         'end_date' => today(),
    //         "image" => $file->name,
    //     ]);

    //     $current = PayCalendar::whereRaw(" ( date(SYSDATE()) between pay_begin_dt and pay_end_dt) ")->first();
    //     $period = PayCalendar::where('check_dt', '>=', $current->check_dt )->skip(2)->take(1)->orderBy('check_dt')->first();

    //     // Test Transaction
    //     $pledge = new SpecialCampaignPledge([
    //         'organization_id' =>  $organization->id,
    //         'emplid' => $is_gov ? $user->emplid : null,
    //         'user_id' => $is_gov ? $user->id : null, 
    //         'pecsf_id' => $is_gov ? null : '123456', 
    //         'yearcd' => today()->year,
    //         'seqno' => 1,
    //         'special_campaign_id' => $specialCampaign->id,
    //         'one_time_amount' => 50.0,  
    //         'deduct_pay_from' => $period->check_dt,
    //         'first_name' => $is_gov ? null : $this->faker->word(),
    //         'last_name' => $is_gov ? null : $this->faker->word(),
    //         'city' => $is_gov ? null : $this->faker->word(),
    //     ]);

    //     $form_data = $this->tranform_to_form_data($pledge);

    //     if ($bCreate) {
    //         // create 
    //         $pledge->save();
    //     }

    //     // var_dump($pledge->charities->all());
    //     // dd('test');

    //     return [$form_data, $pledge];
    // }

    // private function tranform_to_form_data($pledge)
    // {

    //     $form_data = [
    //         "yearcd" => $pledge->yearcd,
    //         "organization_id" => $pledge->organization_id,
    //         "user_id" => $pledge->user_id,
    //         "pecsf_id" => $pledge->pecsf_id,
    //         "pecsf_first_name" => $pledge->first_name,
    //         "pecsf_last_name" => $pledge->last_name,
    //         "pecsf_city" => $pledge->city,
    //         "special_campaign_id" => $pledge->special_campaign_id,
    //         "one_time_amount" => $pledge->one_time_amount,

    //     ];

    //     return $form_data;

    // } 

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
        $pledge = DonateNowPledge::factory()->Make([
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
            'organization_id' => $pledge->organization_id,
            "yearcd" => $pledge->yearcd,
            "pool_option" => $pledge->type,
            "pool_id" => ($pledge->type == 'P') ? $pledge->f_s_pool_id : null,

            "user_id" => $pledge->user_id,
            "pecsf_id" => $pledge->pecsf_id,
            "pecsf_first_name" => $pledge->first_name,
            "pecsf_last_name" => $pledge->last_name,
            "pecsf_city" => $pledge->city,

            "charity_id"  => ($pledge->type == 'C') ? $pledge->charity_id : null,
            "special_program" => ($pledge->type == 'C') ?  $pledge->special_program  : null,

            "one_time_amount" => $pledge->one_time_amount,

        ];

// dd($form_data["charities"]);        
// dd($pledge->charities);        
        return $form_data;

    } 

}
