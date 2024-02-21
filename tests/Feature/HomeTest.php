<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\City;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

class HomeTest extends TestCase
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

        
        }

        $this->admin = User::whereHas("roles", function ($q) {$q->where("name", "admin");})->first();
        $this->user  = User::doesntHave('roles')->first();

    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_home_index_page()
    {
        $response = $this->get('/');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_home_create_page()
    {
        $response = $this->get('/create');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_create_the_city()
    {


        $response = $this->post('/', []);

        $response->assertStatus(405);
    }
    public function test_an_anonymous_user_cannot_access_the_home_view_page()
    {
        $response = $this->get('/1');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_access_the_home_edit_page()
    {
        $response = $this->get('/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_update_the_city()
    {

        $response = $this->put('/1', [] );

        $response->assertStatus(404);
    }

    public function test_an_anonymous_user_cannot_delete_the_city()
    {
        $response = $this->delete('/1');

        $response->assertStatus(404);
    }


    public function test_an_authorized_user_can_access_home_index_page()
    {
        $this->actingAs($this->user);

        $response = $this->get('/');
        $response->assertStatus(200);
    }
    public function test_an_authorized_user_cannot_access_the_home_create_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/create');

        $response->assertStatus(404);
    }
    public function test_an_authorized_user_cannot_create_the_city()
    {
        $this->actingAs($this->user);
        $response = $this->post('/', []);

        $response->assertStatus(405);
    }
    public function test_an_authorized_user_cannot_access_the_home_view_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/1');

        $response->assertStatus(404);
    }
    public function test_an_authorized_user_cannot_access_the_home_edit_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_authorized_user_cannot_update_the_city()
    {
        $this->actingAs($this->user);
        $response = $this->put('/1', [] );

        $response->assertStatus(404);
    }
    public function test_an_authorized_user_cannot_delete_the_city()
    {
        $this->actingAs($this->user);
        $response = $this->delete('/1');

        $response->assertStatus(404);
    }


}
