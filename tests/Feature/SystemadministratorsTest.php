<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Setting;
use App\Models\ScheduleJobAudit;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SystemadministratorsTest extends TestCase
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

            // revoke administrators 
            $ids = [1,2,3];
            DB::table('model_has_roles')->whereIn('model_id', $ids)->delete();

        }

        $this->admin = User::whereHas("roles", function ($q) {$q->where("name", "admin");})->first();
        $this->user  = User::doesntHave('roles')->first();

     
 
    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_system_administrators_index_page()
    {
        $response = $this->get('system/administrators');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_system_administrators_create_page()
    {
        $response = $this->get('system/administrators/create');

        $response->assertStatus(405);

    }
    public function test_an_anonymous_user_cannot_create_the_system_administrators_in_db()
    {

        $response = $this->post('system/administrators', []);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_system_administrators_view_page()
    {
        $response = $this->get('system/administrators/1');

        $response->assertStatus(405);

    }
    public function test_an_anonymous_user_cannot_access_the_system_administrators_edit_page()
    {
        $response = $this->get('system/administrators/1/edit');

        $response->assertStatus(404);

    }
    public function test_an_anonymous_user_cannot_update_the_system_settings()
    {

        $response = $this->put('system/administrators/1', [] );

        $response->assertStatus(405);

    }

    public function test_an_anonymous_user_cannot_delete_the_system_settings()
    {
        $response = $this->delete('system/administrators/1');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    // Unauthorized user
    public function test_an_unauthorized_user_cannot_access_system_administrators_index_page()
    {
        $this->actingAs($this->user);

        $response = $this->get('system/administrators');
        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_system_administrators_create_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('system/administrators/create');

        $response->assertStatus(405);
    }
    public function test_an_unauthorized_user_cannot_create_the_system_administrators()
    {
       
        $this->actingAs($this->user);
        $response = $this->post('system/administrators', []);

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_system_administrators_view_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('system/administrators/1');

        $response->assertStatus(405);
    }
    public function test_an_unauthorized_user_cannot_access_the_system_administrators_edit_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('system/administrators/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_update_the_system_administrators()
    {
        $this->actingAs($this->user);
        $response = $this->put('system/administrators/1', [] );

        $response->assertStatus(405);
    }
    public function test_an_unauthorized_user_cannot_delete_the_system_settings()
    {
        $this->actingAs($this->user);
        $response = $this->delete('system/administrators/1');

        $response->assertStatus(403);
    }


     // Administrator
     public function test_an_administrator_can_access_system_administrators_index_page()
     {

        $this->actingAs($this->admin);
        $response = $this->get('system/administrators');
  
        $response->assertStatus(200);
     }
    //  public function test_an_administrator_can_access_the_system_administrators_create_page()
    //  {
    //      $this->actingAs($this->admin);
    //      $response = $this->get('system/administrators/create');
 
    //      $response->assertStatus(200);
    //  }
     public function test_an_administrator_can_assign_the_system_administrators()
     {

        $this->actingAs($this->admin);
        $response = $this->post('/system/administrators', ['user_id' => 3] );


        $response->assertStatus(302);
        $response->assertRedirect('/system/administrators');
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('model_has_roles',  ['role_id' => 1,  'model_type' => 'App\Models\User', 'model_id' => 3 ] );

     }
    
     public function test_an_administrator_can_revoke_the_system_administrators()
     {

       
        $this->actingAs($this->admin);
        $response = $this->json('delete', '/system/administrators/2', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
 
        $response->assertStatus(302);
        $response->assertRedirect('/system/administrators');
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('model_has_roles',  ['role_id' => 1,  'model_type' => 'App\Models\User', 'model_id' => 2 ] );
     }


    

}
