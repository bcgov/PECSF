<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\City;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

class CitiesTest extends TestCase
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

        City::truncate();

    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_city_index_page()
    {
        $response = $this->get('/settings/cities');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_city_create_page()
    {
        $response = $this->get('/settings/cities/create');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_create_the_city()
    {


        $response = $this->post('/settings/cities', []);

        $response->assertStatus(405);
    }
    public function test_an_anonymous_user_cannot_access_the_city_view_page()
    {
        $response = $this->get('/settings/cities/1');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_access_the_city_edit_page()
    {
        $response = $this->get('/settings/cities/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_update_the_city()
    {

        $response = $this->put('/settings/cities/1', [] );

        $response->assertStatus(404);
    }

    public function test_an_anonymous_user_cannot_delete_the_city()
    {
        $response = $this->delete('/settings/cities/1');

        $response->assertStatus(404);
    }


    public function test_an_unauthorized_user_cannot_access_city_index_page()
    {
        $this->actingAs($this->user);

        $response = $this->get('/settings/cities');
        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_city_create_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/cities/create');

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_create_the_city()
    {
        $this->actingAs($this->user);
        $response = $this->post('/settings/cities', []);

        $response->assertStatus(405);
    }
    public function test_an_unauthorized_user_cannot_access_the_city_view_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/cities/1');

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_access_the_city_edit_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/cities/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_update_the_city()
    {
        $this->actingAs($this->user);
        $response = $this->put('/settings/cities/1', [] );

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_delete_the_city()
    {
        $this->actingAs($this->user);
        $response = $this->delete('/settings/cities/1');

        $response->assertStatus(404);
    }

    public function test_an_administrator_can_access_city_index_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/settings/cities');

        $response->assertStatus(200);

    }
    public function test_an_administrator_cannot_access_the_city_create_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/settings/cities/create');

        $response->assertStatus(404);
    }
    public function test_an_administrator_cannot_create_city_successful_in_db()
    {
        
        $this->actingAs($this->admin);
        $response = $this->post('/settings/cities', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(405);
           
     }

     public function test_an_administrator_cannot_access_the_city_view_page_contains_valid_record()
    {

        $this->actingAs($this->admin);
        // $response = $this->get("/settings/cities/{$cy->id}");
        $response = $this->getJson("/settings/cities/1", ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(404);
    }
    public function test_an_administrator_cannot_access_city_edit_page_contains_valid_record()
    {

        $this->actingAs($this->admin);
        $response = $this->getJson("/settings/cities/1/edit", ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(404);
    }
    public function test_an_administrator_cannot_update_city_successful_in_db()
    {

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/cities/1', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(404);
    }
    public function test_an_administrator_cannot_delete_the_city_successful_in_db()
    {

        $this->actingAs($this->admin);
        $response = $this->json('delete', '/settings/cities/1', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        // $response = $this->delete('/settings/cities/' . $cy->id);

        $response->assertStatus(404);      
    }

    /** Pagination */
    public function test_pagination_on_campaign_years_table_via_ajax_call()
    {
        // Seed data for pagination testing
        // $this->seed(\Database\Seeders\Organizationseeder::class);
        City::factory(20)->create();

        $this->actingAs($this->admin);
        $response = $this->getJson('/settings/cities?columns[0][data]=code&order[0][column]=0&order[0][dir]=asc', ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
    }

    // Database assertion
    public function test_model_exists_in_database() {
        $city = User::factory()->create();
        $this->assertModelExists($city);
    }
  
    public function test_model_deleted_in_database() {
        $city = User::factory()->create();
        $city->delete();

        $this->assertModelMissing($city);
    }


}
