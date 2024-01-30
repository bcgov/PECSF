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
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;

class AdminCampaignPledgeTest extends TestCase
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

    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_admin_campaign_pledge_index_page()
    {
        $response = $this->get('/admin-pledge/campaign');

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }
    public function test_an_anonymous_user_cannot_access_the_admin_campaign_pledge_create_page()
    {
        $response = $this->get('/admin-pledge/campaign/create');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_create_the_admin_campaign_pledge()
    {
        $response = $this->post('/admin-pledge/campaign', []);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_admin_campaign_pledge_view_page()
    {
        $response = $this->get('/admin-pledge/campaign/1');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_admin_campaign_pledge_edit_page()
    {
        $response = $this->get('/admin-pledge/campaign/1/edit');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_update_the_admin_campaign_pledge()
    {

        $response = $this->put('/admin-pledge/campaign/1', [] );

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function test_an_anonymous_user_cannot_delete_the_admin_campaign_pledge()
    {
        $response = $this->delete('/admin-pledge/campaign/1');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }


    public function test_an_unauthorized_user_cannot_access_admin_campaign_pledge_index_page()
    {
        $this->actingAs($this->user);

        $response = $this->get('/admin-pledge/campaign');
        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_admin_campaign_pledge_create_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/admin-pledge/campaign/create');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_create_the_admin_campaign_pledge()
    {

        $this->actingAs($this->user);
        $response = $this->post('/admin-pledge/campaign', []);

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_admin_campaign_pledge_view_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/admin-pledge/campaign/1');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_admin_campaign_pledge_edit_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/admin-pledge/campaign/1/edit');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_update_the_admin_campaign_pledge()
    {
        $this->actingAs($this->user);
        $response = $this->put('/admin-pledge/campaign/1', [] );

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_delete_the_admin_campaign_pledge()
    {
        $this->actingAs($this->user);
        $response = $this->delete('/admin-pledge/campaign/1');

        $response->assertStatus(403);
    }

    public function test_an_administrator_can_access_admin_campaign_pledge_index_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/admin-pledge/campaign');

        $response->assertStatus(200);

    }
    public function test_an_administrator_can_access_the_admin_campaign_pledge_create_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/admin-pledge/campaign/create');

        $response->assertStatus(200);
    }
    public function test_an_administrator_can_create_admin_campaign_pledge_successful_in_db()
    {

        // $row = CampaignYear::factory(1)->create();
        [$form_data, $pledge] = $this->get_new_record_form_data();
        
        $this->actingAs($this->admin);
        $response = $this->post('/admin-pledge/campaign', $form_data );

        $response->assertStatus(302);
        $response->assertRedirect( '/admin-pledge/campaign');
        $response->assertSessionHas('success');
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('pledges', Arr::except( $pledge->attributesToArray(), ['frequency']) );

        foreach ($pledge->charities as $charity) {
            $this->assertDatabaseHas('pledge_charities', Arr::except($charity->attributesToArray(),['pledge_id'] ));
        }
           
     }

     public function test_an_administrator_can_access_the_admin_campaign_pledge_view_page_contains_valid_record()
    {
        // $row = CampaignYear::factory(1)->create();
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $this->actingAs($this->admin);
        // access Index page
        $response = $this->get('/admin-pledge/campaign');

        $response->assertStatus(200);
        $this->actingAs($this->admin);
        $response = $this->get("/admin-pledge/campaign/{$pledge->id}");
        $response->assertStatus(200);
        $response->assertViewHas('pledge', $pledge);
    }
    public function test_an_administrator_can_access_admin_campaign_pledge_edit_page_contains_valid_record()
    {
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $this->actingAs($this->admin);
        $response = $this->get("/admin-pledge/campaign/{$pledge->id}/edit");

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
    public function test_an_administrator_can_update_admin_campaign_pledge_successful_in_db()
    {
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $pledge->type = "C";
        $pledge->region_id = null;
        $pledge->f_s_pool_id = 0;

        $new_form_data = $this->tranform_to_form_data($pledge);

        $this->actingAs($this->admin);
        $response = $this->put('/admin-pledge/campaign/' . $pledge->id, $new_form_data );

        $response->assertStatus(302);
        $response->assertRedirect('/admin-pledge/campaign');
        $response->assertSessionHas('success');
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('pledges', Arr::except( $pledge->attributesToArray(), ['frequency', 'charities', 'distinct_charities', 'updated_at', 'created_at']) );

        foreach ($pledge->charities as $charity) {
            $this->assertDatabaseHas('pledge_charities', Arr::except($charity->attributesToArray(), ['updated_at', 'created_at']));
        }

    }

    public function test_an_administrator_can_delete_the_admin_campaign_pledge_in_db()
    {
        // $campignyears = CampaignYear::factory(1)->create();
        // $row = $campignyears->first();
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $this->actingAs($this->admin);
        $response = $this->json('delete', '/admin-pledge/campaign/' . $pledge->id, [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(204);
        // Assert the file was deleted ...
        $this->assertSoftDeleted('pledges',  Arr::except($pledge->attributesToArray(), ['frequency', 'charities', 'distinct_charities', 'updated_at', 'created_at'])  );
        foreach ($pledge->charities as $charity) {
            $this->assertSoftDeleted('pledge_charities', Arr::except($charity->attributesToArray(), ['updated_at', 'created_at']));
        }

    }

    public function test_an_administrator_cannot_delete_the_admin_gov_campaign_pledge_in_db()
    {
        // $campignyears = CampaignYear::factory(1)->create();
        // $row = $campignyears->first();
        [$form_data, $pledge] = $this->get_new_record_form_data(true, true);

        $this->actingAs($this->admin);
        $response = $this->json('delete', '/admin-pledge/campaign/' . $pledge->id, [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJson([
            'error' => "You are not allowed to delete this pledge " . $pledge->id . " which was created for 'Gov' organization.",
        ]);

    }

    public function test_an_administrator_can_cancel_the_admin_campaign_pledge_in_db()
    {
        // $campignyears = CampaignYear::factory(1)->create();
        // $row = $campignyears->first();
        [$form_data, $pledge] = $this->get_new_record_form_data(true, true);

        $this->actingAs($this->admin);
        $response = $this->post('/admin-pledge/campaign/'. $pledge->id .'/cancel');

        $response->assertStatus(204);
        // Assert the file was deleted ...
        $this->assertDatabaseHas('pledges',  [ 'id' => $pledge->id, 'cancelled' => 'Y'] );

    }


    public function test_an_administrator_cannot_cancel_admin_non_gov_campaign_pledge_in_db()
    {
        // $campignyears = CampaignYear::factory(1)->create();
        // $row = $campignyears->first();
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $this->actingAs($this->admin);
        $response = $this->post('/admin-pledge/campaign/'. $pledge->id .'/cancel');

        $response->assertStatus(404);

    }

    // /** Pagination */
    public function test_pagination_on_admin_campaign_pledges_table_via_ajax_call()
    {
        // Seed data for pagination testing
        // $this->seed(\Database\Seeders\CampaignYearSeeder::class);
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $this->actingAs($this->admin);
        $response = $this->getJson('/admin-pledge/campaign', ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $pledge->id,
            'campaign_year_id' => $pledge->campaign_year_id,
            'business_unit' => $pledge->business_unit,
                        // 'calendar_year' => $campignyear->calendar_year,
        ]);
    }

    // /** Form Validation */
    // public function test_validation_rule_fields_are_required_when_create()
    // {
    //     // create the founcdation data 
    //     [$form_data, $pledge] = $this->get_new_record_form_data(false, false);

    //     $form_data =  [
    //         "step" => "3",
    //         "campaign_year_id" => $pledge->campaign_year_id,
    //         "organization_id" => $pledge->organization_id,
    //         ];

    //     $this->actingAs($this->admin);
    //     // Post empty data to the create page route
    //     // $response = $this->post('/admin-pledge/campaign', $form_data);
    //     $this->actingAs($this->admin);
    //     $response = $this->postJson('/admin-pledge/campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
    //     // This should cause errors with the
    //     // title and content fields as they aren't present
    //     $response->assertStatus(422);
    //     $response->assertJsonValidationErrors([
    //         'pay_period_amount_other', 
    //         'one_time_amount_other',
    //         'pool_option',
    //         'pay_period_amount_error',
    //         'one_time_amount_error',
    //     ]);
    // }
    // public function test_validation_rule_fields_are_required_when_edit()
    // {
    //     // create the founcdation data 
    //     [$form_data, $pledge] = $this->get_new_record_form_data(true, true);

    //     $form_data =  [
    //         "step" => "3",
    //         "campaign_year_id" => $pledge->campaign_year_id,
    //         "organization_id" => $pledge->organization_id,
    //         ];


    //     $this->actingAs($this->admin);
    //     $response = $this->json('put', '/admin-pledge/campaign/' . $pledge->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        
    //     // This should cause errors with the
    //     // title and content fields as they aren't present
    //     $response->assertStatus(422);
    //     $response->assertJsonValidationErrors([
    //         'pay_period_amount_other', 
    //         'one_time_amount_other',
    //         'pool_option',
    //         'pay_period_amount_error',
    //         'one_time_amount_error',
    //     ]);

    // }


    public function test_validation_rule_fields_are_required_on_step_1_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, true);

        $form_data = [
            "step" => "1",
        ];
       
        foreach ([0,1] as $i) {    

            $this->actingAs($this->admin);
            if ($i == 0) {
                $response = $this->postJson('/admin-pledge/campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            } else {
                $response = $this->json('put', '/admin-pledge/campaign/' . $pledge->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            }

            $response->assertStatus(422);
            $response->assertJsonValidationErrors([
                'campaign_year_id', 
                'organization_id',
                'pecsf_id',
                'pecsf_first_name',
                'pecsf_last_name',
                'pecsf_city',
            ]);
        }

    }

    public function test_validation_rule_fields_are_required_on_step_2_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, true);

        $form_data = [
            "step" => "2",
        ];
       
        foreach ([0,1] as $i) {    

            $this->actingAs($this->admin);
            if ($i == 0) {
                $response = $this->postJson('/admin-pledge/campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            } else {
                $response = $this->json('put', '/admin-pledge/campaign/' . $pledge->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            }

            $response->assertStatus(422);
            $response->assertJsonValidationErrors([
                'pay_period_amount_other', 
                'one_time_amount_other',
                'pay_period_amount_error',
                'one_time_amount_error',
            ]);
        }

    }

    public function test_validation_rule_fields_are_required_on_step_3_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, true);

        $form_data = [
            "step" => "3",
        ];
       
        foreach ([0,1] as $i) {    

            $this->actingAs($this->admin);
            if ($i == 0) {
                $response = $this->postJson('/admin-pledge/campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            } else {
                $response = $this->json('put', '/admin-pledge/campaign/' . $pledge->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            }

            $response->assertStatus(422);
            $response->assertJsonValidationErrors([
                'pay_period_amount_other', 
                'one_time_amount_other',
                'pool_option',
                'pay_period_amount_error',
                'one_time_amount_error',
            ]);
        }

    }

    public function test_validation_rule_invalid_employee_id_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, true);

        $form_data["step"] = 1;
        $form_data["user_id"] = 99999991;

       
        foreach ([0,1] as $i) {    

            $this->actingAs($this->admin);
            if ($i == 0) {
                $response = $this->postJson('/admin-pledge/campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            } else {
                $response = $this->json('put', '/admin-pledge/campaign/' . $pledge->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
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

        $form_data["step"] = 1;
        $form_data["pecsf_id"] = 'aaghsd';

       
        foreach ([0,1] as $i) {    

            $this->actingAs($this->admin);
            if ($i == 0) {
                $response = $this->postJson('/admin-pledge/campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            } else {
                $response = $this->json('put', '/admin-pledge/campaign/' . $pledge->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            }

            $response->assertStatus(422);
            $response->assertJsonValidationErrors([
                'pecsf_id', 
                'pecsf_first_name',
                'pecsf_last_name',
                'pecsf_city',
            ]);
        }

    }


    public function test_validation_rule_invalid_min_amount_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $form_data["step"] = 2;
        $form_data['pay_period_amount'] = '';
        $form_data["pay_period_amount_other"] = 0.5;
        $form_data["one_time_amount"] = '';
        $form_data["one_time_amount_other"] = 0.5;

       
        foreach ([0,1] as $i) {    

            $this->actingAs($this->admin);
            if ($i == 0) {
                $response = $this->postJson('/admin-pledge/campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            } else {
                $response = $this->json('put', '/admin-pledge/campaign/' . $pledge->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            }

            $response->assertStatus(422);
            $response->assertJsonValidationErrors([
                'pay_period_amount_other', 
                'one_time_amount_other',
            ]);
        }

    }

    public function test_validation_rule_invalid_pool_option_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $form_data["step"] = 3;
        $form_data['pool_option'] = 'X';
       
        foreach ([0,1] as $i) {    

            $this->actingAs($this->admin);
            if ($i == 0) {
                $response = $this->postJson('/admin-pledge/campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            } else {
                $response = $this->json('put', '/admin-pledge/campaign/' . $pledge->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            }

            $response->assertStatus(422);
            $response->assertJsonValidationErrors([
                'pool_option', 
            ]);
        }

    }


    public function test_validation_rule_invalid_percentage_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $form_data["step"] = 3;
        // foreach ( $form_data["percentages"] as $key => $percent) {
        //     $form_data["percentages"][$key] = $this->faker->randomElement( [0, 101] );
        // }
        $form_data["percentages"][8] = 0;
        $form_data["percentages"][9] = 20;

        // 'percentages.*' => $this->pool_option == 'C' ?
       
        foreach ([0,1] as $i) {    

            $this->actingAs($this->admin);
            if ($i == 0) {
                $response = $this->postJson('/admin-pledge/campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            } else {
                $response = $this->json('put', '/admin-pledge/campaign/' . $pledge->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            }

            $response->assertStatus(422);
            $response->assertJsonValidationErrors([
                // 'percentages.0', 
                // 'percentages.1', 
                // 'percentages.2',
                // 'percentages.3',
                // 'percentages.4',
                // 'percentages.5',
                // 'percentages.6',
                // 'percentages.7',
                'percentages.8',
                // 'percentages.9',
            ]);
        }

    }

    
    public function test_validation_rule_total_percentage_is_not_100_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $form_data["step"] = 3;
        // foreach ( $form_data["percentages"] as $key => $percent) {
        //     $form_data["percentages"][$key] = $this->faker->randomElement( [0, 101] );
        // }
        $form_data["percentages"][9] = 10.005;

        // 'percentages.*' => $this->pool_option == 'C' ?
       
        foreach ([0,1] as $i) {    

            $this->actingAs($this->admin);
            if ($i == 0) {
                $response = $this->postJson('/admin-pledge/campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            } else {
                $response = $this->json('put', '/admin-pledge/campaign/' . $pledge->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            }

            $response->assertStatus(422);
            $response->assertJsonValidationErrors([
                'percentages.0', 
                'percentages.1', 
                'percentages.2',
                'percentages.3',
                'percentages.4',
                'percentages.5',
                'percentages.6',
                'percentages.7',
                'percentages.8',
                'percentages.9',
            ]);
        }

    }

    public function test_validation_rule_invalid_fund_support_pool_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $form_data["step"] = 3;
        // foreach ( $form_data["percentages"] as $key => $percent) {
        //     $form_data["percentages"][$key] = $this->faker->randomElement( [0, 101] );
        // }
        $form_data['pool_option'] = 'P';
        $form_data['pool_id'] = 1123;
       
        foreach ([0,1] as $i) {    

            $this->actingAs($this->admin);
            if ($i == 0) {
                $response = $this->postJson('/admin-pledge/campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            } else {
                $response = $this->json('put', '/admin-pledge/campaign/' . $pledge->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            }

            $response->assertStatus(422);
            $response->assertJsonValidationErrors([
                'pool_id', 
            ]);
        }

    }

    public function test_validation_rule_invalid_charity_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $form_data["step"] = 3;
        // foreach ( $form_data["percentages"] as $key => $percent) {
        //     $form_data["percentages"][$key] = $this->faker->randomElement( [0, 101] );
        // }
        $form_data['pool_option'] = 'C';
        $form_data['charities'] = [];
        $form_data['percentages'] = [];

        $form_data['charities'][0] = '';
        $form_data['percentages'][0] = 101;
       
        foreach ([0,1] as $i) {    

            $this->actingAs($this->admin);
            if ($i == 0) {
                $response = $this->postJson('/admin-pledge/campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            } else {
                $response = $this->json('put', '/admin-pledge/campaign/' . $pledge->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            }

            $response->assertStatus(422);
            $response->assertJsonValidationErrors([
                'charities.0', 
                'percentages.0',
            ]);
        }

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


        $organizations = Organization::factory(1)->create([
                    'code' => "GOV",
        ]);
        if (!($is_gov)) {
            $organizations = Organization::factory(1)->create([
                    'code' => "LDB",
                ]);
        }
        $businesses = BusinessUnit::factory(1)->create();
        $regions = Region::factory(1)->create();
        $charities = Charity::factory(10)->create([
            'charity_status' => 'Registered',
        ]);

        $fspools = FSPool::factory(1)->create([
            'region_id' => $regions[0]->id,
            'status' => 'A',
        ]);
        $fspool_charities = FSPoolCharity::factory(5)->create([
                'f_s_pool_id' => $fspools[0]->id,
                'charity_id' => $this->faker->randomElement( $charities->pluck('id')->toArray() ),
                'percentage' => 20,
                'status' => 'A',
        ]);

        $jobs = EmployeeJob::factory(1)->create([
            "organization_id" => $organizations [0]->id,
            "emplid" => $user->emplid,
            "business_unit" => $businesses[0]->code,
            "business_unit_id" => $businesses[0]->id,
            "tgb_reg_district" =>  $regions[0]->code,
            "region_id" => $regions[0]->id,
        ]);

        // Test Transaction
        $pledge = new Pledge([
            'organization_id' =>  $organizations[0]->id,
            'emplid' => $is_gov ? $user->emplid : null,
            'user_id' => $is_gov ? $user->id : 0, 
            'pecsf_id' => $is_gov ? null : '882288',
            'business_unit' => $is_gov ? $jobs[0]->business_unit : $businesses[0]->code,
            'tgb_reg_district' => $is_gov ? $jobs[0]->tgb_reg_district : $regions[0]->code,
            'deptid' => $is_gov  ? $jobs[0]->deptid : null,
            'dept_name' => $is_gov  ? $jobs[0]->dept_name : null,
            'first_name' => null,
            'last_name' => null,
            'city' => null,
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
            "step" => "4",
            "campaign_year_id" => $pledge->campaign_year_id,
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
            "pay_period_amount" => in_array($pledge->pay_period_amount, [0,6,12,20,50]) ?  $pledge->pay_period_amount : '',
            "pay_period_amount_other" => in_array($pledge->pay_period_amount, [0,6,12,20,50]) ? 0 : $pledge->pay_period_amount,
            "one_time_amount" => in_array($pledge->one_time_amount, [0,6,12,20,50]) ?  $pledge->one_time_amount : '',
            "one_time_amount_other" => in_array($pledge->one_time_amount, [0,6,12,20,50]) ? 0 : $pledge->one_time_amount,
            "pool_id" => $pledge->f_s_pool_id,
            "pool_option" => $pledge->type,

            "charities" => [],
            "additional" => [],
            "percentages" => [],
        ];

        for ($i = 0; $i < 10; $i++) {
            array_push($form_data["charities"], $pledge->charities[$i]->charity_id);
            array_push($form_data["additional"], $pledge->charities[$i]->additional);
            array_push( $form_data["percentages"], $pledge->charities[$i]->percentage);
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

}
