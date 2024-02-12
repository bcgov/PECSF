<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Setting;
use App\Models\ScheduleJobAudit;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SystemUsersTest extends TestCase
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

            ScheduleJobAudit::truncate();
        }

        $this->admin = User::whereHas("roles", function ($q) {$q->where("name", "admin");})->first();
        $this->user  = User::doesntHave('roles')->first();

        ScheduleJobAudit::truncate();
    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_system_users_index_page()
    {
        $response = $this->get('system/users');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_system_users_create_page()
    {
        $response = $this->get('system/users/create');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_create_the_system_users_in_db()
    {

        $response = $this->post('system/users', []);

        $response->assertStatus(405);
    }
    public function test_an_anonymous_user_cannot_access_the_system_users_view_page()
    {
        $response = $this->get('system/users/1');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_system_users_edit_page()
    {
        $response = $this->get('system/users/1/edit');

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }
    public function test_an_anonymous_user_cannot_update_the_system_settings()
    {

        $response = $this->put('system/users/1', [] );

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }

    public function test_an_anonymous_user_cannot_delete_the_system_settings()
    {
        $response = $this->delete('system/users/1');

        $response->assertStatus(405);

    }

    // Unauthorized user
    public function test_an_unauthorized_user_cannot_access_system_users_index_page()
    {
        $this->actingAs($this->user);

        $response = $this->get('system/users');
        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_system_users_create_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('system/users/create');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_create_the_system_users()
    {
       
        $this->actingAs($this->user);
        $response = $this->post('system/users', []);

        $response->assertStatus(405);
    }
    public function test_an_unauthorized_user_cannot_access_the_system_users_view_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('system/users/1');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_system_users_edit_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('system/users/1/edit');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_update_the_system_users()
    {
        $this->actingAs($this->user);
        $response = $this->put('system/users/1', [] );

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_delete_the_system_settings()
    {
        $this->actingAs($this->user);
        $response = $this->delete('system/users/1');

        $response->assertStatus(405);
    }


     // Administrator
     public function test_an_administrator_can_access_system_users_index_page()
     {

        $this->actingAs($this->admin);
        $response = $this->get('system/users');
  
        $response->assertStatus(200);
     }
    //  public function test_an_administrator_can_access_the_system_users_create_page()
    //  {
    //      $this->actingAs($this->admin);
    //      $response = $this->get('system/users/create');
 
    //      $response->assertStatus(200);
    //  }
     public function test_an_administrator_cannot_create_the_system_users()
     {
        $this->actingAs($this->admin);
        $response = $this->post('system/users',[] );

        $response->assertStatus(405);
 

     }
    //  public function test_an_administrator_can_access_the_system_users_view_page()
    //  {

    //     $this->actingAs($this->admin);
    //     $response = $this->json('get', '/system/users/1', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
 
    //     $response->assertStatus(200);
    //     $response->assertJsonFragment([
    //         'id' => 1,
    //         'job_name' => "command:UpdateDailyCampaign",
    //         'status' => "Completed",             
    //     ]);


    //  }
    //  public function test_an_administrator_cannot_access_the_system_users_edit_page()
    //  {
    //      $this->actingAs($this->admin);
    //      $response = $this->get('system/users/1/edit');
 
    //      $response->assertStatus(404);
    //  }
    //  public function test_an_administrator_cannot_update_the_system_users()
    //  {
    //      $this->actingAs($this->admin);
    //      $response = $this->put('system/users/1', [] );
 
    //      $response->assertStatus(405);
    //  }
     public function test_an_administrator_cannot_delete_the_system_users()
     {

         $this->actingAs($this->admin);
         $response = $this->delete('system/users/1');
 
         $response->assertStatus(405);
     }


    // Test Lock and Unlock user

    public function test_an_administrator_can_lock_users()
    {
        $form_data = [
            'id' => 11,
            'acctlock' => 1,
        ];


        $this->actingAs($this->admin);
        $response = $this->json('post', 'system/users/11/lock', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
 
        $response->assertStatus(204);
        $this->assertDatabaseHas('users',  $form_data );

    }

    public function test_an_administrator_can_unlock_users()
    {
        $form_data = [
            'id' => 11,
            'acctlock' => 0,
        ];


        $this->actingAs($this->admin);
        $response = $this->json('post', 'system/users/11/unlock', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
 
        $response->assertStatus(204);
        $this->assertDatabaseHas('users',  $form_data );

    }

}
