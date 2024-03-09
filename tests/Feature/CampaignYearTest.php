<?php

namespace Tests\Feature;

use App\Models\CampaignYear;
use App\Models\User;
use Tests\TestCase;

class CampaignYearTest extends TestCase
{

    // protected static $initialized = FALSE;

    private User $admin;
    private User $user;

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

        $this->admin = User::whereHas("roles", function ($q) {$q->where("name", "admin");})->first();
        $this->user  = User::doesntHave('roles')->first();

        CampaignYear::truncate();

    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_campaign_year_index_page()
    {
        $response = $this->get('/settings/campaignyears');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_campaign_year_create_page()
    {
        $response = $this->get('/settings/campaignyears/create');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_create_the_campaign_year()
    {
        $form_data = $this->get_new_record_form_data();

        $response = $this->post('/settings/campaignyears', $form_data);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_campaign_year_view_page()
    {
        $response = $this->get('/settings/campaignyears/1');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_campaign_year_edit_page()
    {
        $response = $this->get('/settings/campaignyears/1/edit');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_update_the_campaign_year()
    {

        $response = $this->put('/settings/campaignyears/1', [] );

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function test_an_anonymous_user_cannot_delete_the_campaign_year()
    {
        $response = $this->delete('/settings/campaignyears/1');

        $response->assertStatus(405);
    }


    public function test_an_unauthorized_user_cannot_access_campaign_year_index_page()
    {
        $this->actingAs($this->user);

        $response = $this->get('/settings/campaignyears');
        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_campaign_year_create_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/campaignyears/create');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_create_the_campaign_year()
    {
        $form_data = $this->get_new_record_form_data();

        $this->actingAs($this->user);
        $response = $this->post('/settings/campaignyears', $form_data);

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_campaign_year_view_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/campaignyears/1');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_campaign_year_edit_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/campaignyears/1/edit');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_update_the_campaign_year()
    {
        $this->actingAs($this->user);
        $response = $this->put('/settings/campaignyears/1', [] );

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_delete_the_campaign_year()
    {
        $this->actingAs($this->user);
        $response = $this->delete('/settings/campaignyears/1');

        $response->assertStatus(405);
    }

    public function test_an_administrator_can_access_campaign_year_index_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/settings/campaignyears');

        $response->assertStatus(200);

    }
    public function test_an_administrator_can_access_the_campaign_year_create_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/settings/campaignyears/create');

        $response->assertStatus(200);
    }
    public function test_an_administrator_can_create_campaign_year_successful_in_db()
    {

        // $row = CampaignYear::factory(1)->create();
        $form_data = $this->get_new_record_form_data();
        
        $this->actingAs($this->admin);
        $response = $this->post('/settings/campaignyears', $form_data );

        $response->assertStatus(302);
        $response->assertRedirect( '/settings/campaignyears');
        $response->assertSessionHas('success');
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('campaign_years', $form_data );
           
     }

     public function test_an_administrator_can_access_the_campaign_year_view_page_contains_valid_record()
    {
        $form_data = $this->get_new_record_form_data();
        $row = CampaignYear::create( $form_data ); 

        $this->actingAs($this->admin);
        // access Index page
        $response = $this->get('/settings/campaignyears');

        $response->assertStatus(200);
        $this->actingAs($this->admin);
        $response = $this->get("/settings/campaignyears/{$row->id}");
        $response->assertStatus(200);
        $response->assertViewHas('campaign_year', $row );
    }
    public function test_an_administrator_can_access_campaign_year_edit_page_contains_valid_record()
    {
        $form_data = $this->get_new_record_form_data();
        $row = CampaignYear::create( $form_data ); 

        $this->actingAs($this->admin);
        $response = $this->get("/settings/campaignyears/{$row->id}/edit");

        $response->assertStatus(200);
        $response->assertViewHas('campaign_year', $row );
    }
    public function test_an_administrator_can_update_campaign_year_successful_in_db()
    {
        // $campignyears = CampaignYear::factory(1)->create();
        $form_data = $this->get_new_record_form_data();
        $row = CampaignYear::create( $form_data ); 
        // modify values
        $form_data['number_of_periods'] = 25;
        $form_data['close_date'] = '2027-12-10';

        $this->actingAs($this->admin);
        $response = $this->put('/settings/campaignyears/' . $row->id, $form_data );

        $response->assertStatus(302);
        $response->assertRedirect( '/settings/campaignyears');
        $response->assertSessionHas('success');
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('campaign_years', $form_data );
    }
    public function test_an_administrator_cannot_delete_the_campaign_year()
    {
        // $campignyears = CampaignYear::factory(1)->create();
        // $row = $campignyears->first();
        $form_data = $this->get_new_record_form_data();
        $row = CampaignYear::create( $form_data ); 

        $this->actingAs($this->admin);
        $response = $this->delete('/settings/campaignyears/' . $row->id);

        $response->assertStatus(405);
    }

    /** Pagination */
    public function test_pagination_on_campaign_years_table_via_ajax_call()
    {
        // Seed data for pagination testing
        // $this->seed(\Database\Seeders\CampaignYearSeeder::class);
        $form_data = $this->get_new_record_form_data();
        $campignyear = CampaignYear::create( $form_data ); 

        $this->actingAs($this->admin);
        $response = $this->getJson('/settings/campaignyears', ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $campignyear->id,
            'calendar_year' => $campignyear->calendar_year,
        ]);
    }

    /** Form Validation */
    public function test_validation_rule_fields_are_required()
    {
        $this->actingAs($this->admin);
        // Post empty data to the create page route
        $response = $this->post('/settings/campaignyears');
        // This should cause errors with the
        // title and content fields as they aren't present
        $response->assertSessionHasErrors([
            'calendar_year',
            'number_of_periods',
            'status',
            'start_date',
            'end_date',
        ]);
    }
    public function test_validation_start_date_must_not_be_later_than_end_date()
    {
        $this->actingAs($this->admin);
        $response = $this->post('/settings/campaignyears',
            [
                'start_date' => '2010-12-12',
                'end_date' => '2010-01-01',
            ]);
        $response->assertSessionHasErrors([
            'start_date',
            'end_date',
        ]);
    }
    public function test_validation_only_one_active_calendar_year_allow()
    {
        $this->seed(\Database\Seeders\CampaignYearSeeder::class);
        CampaignYear::query()->update(['status' => 'I']);    

        $row = CampaignYear::first()->update(['status' => 'A']);

        $this->actingAs($this->admin);
        $response = $this->post('/settings/campaignyears',
            [
                'calendar_year' => 2029,
                'number_of_periods' => 26,
                'status' => 'A',
                'start_date' => '2021-12-12',
                'end_date' => '2021-12-31',
                'close_date' => '2021-12-31',
            ]);

        $response->assertSessionHasErrors([
            'status',
        ]);

    }
    public function test_close_date_default_to_last_day_of_the_year()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/settings/campaignyears/create');
        $response->assertSee('-12-31');
    }

    private function get_new_record_form_data() 
    {
        $form_data = [
              "calendar_year" => "2028",
              "status" => "I",
              "start_date" => "2027-09-01",
              "end_date" => "2027-11-21",
              "number_of_periods" => 26,
              "close_date" => "2027-12-31",
        ];

        return $form_data;
    }

}
