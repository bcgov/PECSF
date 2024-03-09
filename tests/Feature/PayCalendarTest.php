<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

class PayCalendarTest extends TestCase
{

    // protected static $initialized = FALSE;

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
        }

        $this->admin = User::whereHas("roles", function ($q) {$q->where("name", "admin");})->first();
        $this->user  = User::doesntHave('roles')->first();

    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_pay_calendar_index_page()
    {
        $response = $this->get('/settings/pay-calendars');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_pay_calendar_create_page()
    {
        $response = $this->get('/settings/pay-calendars/create');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_create_the_pay_calendar()
    {


        $response = $this->post('/settings/pay-calendars', []);

        $response->assertStatus(405);
    }
    public function test_an_anonymous_user_cannot_access_the_pay_calendar_view_page()
    {
        $response = $this->get('/settings/pay-calendars/1');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_access_the_pay_calendar_edit_page()
    {
        $response = $this->get('/settings/pay-calendars/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_update_the_pay_calendar()
    {

        $response = $this->put('/settings/pay-calendars/1', [] );

        $response->assertStatus(404);
    }

    public function test_an_anonymous_user_cannot_delete_the_pay_calendar()
    {
        $response = $this->delete('/settings/pay-calendars/1');

        $response->assertStatus(404);
    }


    public function test_an_unauthorized_user_cannot_access_pay_calendar_index_page()
    {
        $this->actingAs($this->user);

        $response = $this->get('/settings/pay-calendars');
        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_pay_calendar_create_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/pay-calendars/create');

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_create_the_pay_calendar()
    {
        $this->actingAs($this->user);
        $response = $this->post('/settings/pay-calendars', []);

        $response->assertStatus(405);
    }
    public function test_an_unauthorized_user_cannot_access_the_pay_calendar_view_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/pay-calendars/1');

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_access_the_pay_calendar_edit_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/pay-calendars/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_update_the_pay_calendar()
    {
        $this->actingAs($this->user);
        $response = $this->put('/settings/pay-calendars/1', [] );

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_delete_the_pay_calendar()
    {
        $this->actingAs($this->user);
        $response = $this->delete('/settings/pay-calendars/1');

        $response->assertStatus(404);
    }

    public function test_an_administrator_can_access_pay_calendar_index_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/settings/pay-calendars');

        $response->assertStatus(200);

    }
    public function test_an_administrator_cannot_access_the_pay_calendar_create_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/settings/pay-calendars/create');

        $response->assertStatus(404);
    }
    public function test_an_administrator_cannot_create_pay_calendar_successful_in_db()
    {
        
        $this->actingAs($this->admin);
        $response = $this->post('/settings/pay-calendars', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(405);
           
     }

     public function test_an_administrator_cannot_access_the_pay_calendar_view_page_contains_valid_record()
    {

        $this->actingAs($this->admin);
        // $response = $this->get("/settings/pay-calendars/{$cy->id}");
        $response = $this->getJson("/settings/pay-calendars/1", ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(404);
    }
    public function test_an_administrator_cannot_access_pay_calendar_edit_page_contains_valid_record()
    {

        $this->actingAs($this->admin);
        $response = $this->getJson("/settings/pay-calendars/1/edit", ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(404);
    }
    public function test_an_administrator_cannot_update_pay_calendar_successful_in_db()
    {

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/pay-calendars/1', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(404);
    }
    public function test_an_administrator_cannot_delete_the_pay_calendar_successful_in_db()
    {

        $this->actingAs($this->admin);
        $response = $this->json('delete', '/settings/pay-calendars/1', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        // $response = $this->delete('/settings/pay-calendars/' . $cy->id);

        $response->assertStatus(404);      
    }

    /** Pagination */
    public function test_pagination_on_campaign_years_table_via_ajax_call()
    {
        // Seed data for pagination testing
        // $this->seed(\Database\Seeders\Organizationseeder::class);

        $this->actingAs($this->admin);
        $response = $this->getJson('/settings/pay-calendars?columns[0][data]=code&order[0][column]=0&order[0][dir]=asc', ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
    }



}
