<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Setting;
use App\Models\AccessLog;
use App\Models\ScheduleJobAudit;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SystemAccessLogsTest extends TestCase
{
    // protected static $initialized = FALSE;

    private User $admin;
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::whereHas("roles", function ($q) {$q->where("name", "admin");})->first();
        $this->user  = User::doesntHave('roles')->first();

    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_system_access_logs_index_page()
    {
        $response = $this->get('system/access-logs');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_system_access_logs_create_page()
    {
        $response = $this->get('system/access-logs/create');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_create_the_system_access_logs_in_db()
    {

        $response = $this->post('system/access-logs', []);

        $response->assertStatus(405);
    }
    public function test_an_anonymous_user_cannot_access_the_system_access_logs_view_page()
    {
        $response = $this->get('system/access-logs/1');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_access_the_system_access_logs_edit_page()
    {
        $response = $this->get('system/access-logs/1/edit');

        $response->assertStatus(404);

    }
    public function test_an_anonymous_user_cannot_update_the_system_settings()
    {

        $response = $this->put('system/access-logs/1', [] );

        $response->assertStatus(404);

    }

    public function test_an_anonymous_user_cannot_delete_the_system_settings()
    {
        $response = $this->delete('system/access-logs/1');

        $response->assertStatus(404);

    }

       // Get user listing
       public function test_an_anonymous_user_cannot_access_the_system_user_listing_view_page()
       {
   
          $response = $this->json('get', '/system/access-logs-user', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
   
          $response->assertStatus(401);
   
       }
   
       // Get User Detail
       public function test_an_anonymous_user_cannot_access_the_system_user_detail_view_page()
       {
   
          $response = $this->json('get', "/system/access-logs-user-detail/1", [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
   
          $response->assertStatus(401);
   
       }
   

    // Unauthorized user
    public function test_an_unauthorized_user_cannot_access_system_access_logs_index_page()
    {
        $this->actingAs($this->user);

        $response = $this->get('system/access-logs');
        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_system_access_logs_create_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('system/access-logs/create');

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_create_the_system_access_logs()
    {
       
        $this->actingAs($this->user);
        $response = $this->post('system/access-logs', []);

        $response->assertStatus(405);
    }
    public function test_an_unauthorized_user_cannot_access_the_system_access_logs_view_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('system/access-logs/1');

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_access_the_system_access_logs_edit_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('system/access-logs/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_update_the_system_access_logs()
    {
        $this->actingAs($this->user);
        $response = $this->put('system/access-logs/1', [] );

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_delete_the_system_settings()
    {
        $this->actingAs($this->user);
        $response = $this->delete('system/access-logs/1');

        $response->assertStatus(404);
    }

    
    // Get user listing
    public function test_an_authorized_user_can_access_the_system_user_listing_view_page()
    {

       $this->actingAs($this->user);
       $response = $this->json('get', '/system/access-logs-user', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

       $response->assertStatus(403);

    }

    // Get User Detail
    public function test_an_authorized_user_can_access_the_system_user_detail_view_page()
    {

       $this->actingAs($this->user);
       $response = $this->json('get', "/system/access-logs-user-detail/1", [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

       $response->assertStatus(403);

    }

     // Administrator
     public function test_an_administrator_can_access_system_access_logs_index_page()
     {

        $this->actingAs($this->admin);
        $response = $this->get('system/access-logs');
  
        $response->assertStatus(200);
     }
     public function test_an_administrator_can_access_the_system_access_logs_create_page()
     {
         $this->actingAs($this->admin);
         $response = $this->get('system/access-logs/create');
 
         $response->assertStatus(404);
     }
     public function test_an_administrator_cannot_create_the_system_access_logs()
     {
        $this->actingAs($this->admin);
        $response = $this->post('system/access-logs',[] );

        $response->assertStatus(405);
 
     }
     public function test_an_administrator_can_access_the_system_access_logs_view_page()
     {

        $this->actingAs($this->admin);
        $response = $this->json('get', '/system/access-logs/1', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
 
        $response->assertStatus(404);

     }
     public function test_an_administrator_cannot_access_the_system_access_logs_edit_page()
     {
         $this->actingAs($this->admin);
         $response = $this->get('system/access-logs/1/edit');
 
         $response->assertStatus(404);
     }
     public function test_an_administrator_cannot_update_the_system_access_logs()
     {
         $this->actingAs($this->admin);
         $response = $this->put('system/access-logs/1', [] );
 
         $response->assertStatus(404);
     }
     public function test_an_administrator_cannot_delete_the_system_access_logs()
     {

        $this->actingAs($this->admin);
        $response = $this->delete('system/access-logs/1');

        $response->assertStatus(404);
     }



    // Get user listing
    public function test_an_administrator_can_access_the_system_user_listing_view_page()
    {

       $this->actingAs($this->admin);
       $response = $this->json('get', '/system/access-logs-user', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

       $response->assertStatus(200);
       $response->assertJsonFragment([
        'id' => 998,
        'text' => "Adele Vance (121100)",          
        ]);


    }

    // Get User Detail
    public function test_an_administrator_can_access_the_system_user_detail_view_page()
    {

       $user = User::first();


       $this->actingAs($this->admin);
       $response = $this->json('get', "/system/access-logs-user-detail/" . $user->id, [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

       $response->assertStatus(200);
       $response->assertSeeText( $user->name);

    }

}
