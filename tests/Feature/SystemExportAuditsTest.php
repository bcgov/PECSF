<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Setting;
use App\Models\ScheduleJobAudit;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SystemExportAuditsTest extends TestCase
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
    public function test_an_anonymous_user_cannot_access_system_export_audits_index_page()
    {
        $response = $this->get('system/export-audits');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_system_export_audits_create_page()
    {
        $response = $this->get('system/export-audits/create');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_create_the_system_export_audits_in_db()
    {

        $response = $this->post('system/export-audits', []);

        $response->assertStatus(405);
    }
    public function test_an_anonymous_user_cannot_access_the_system_export_audits_view_page()
    {
        $response = $this->get('system/export-audits/1');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_access_the_system_export_audits_edit_page()
    {
        $response = $this->get('system/export-audits/1/edit');

        $response->assertStatus(404);

    }
    public function test_an_anonymous_user_cannot_update_the_system_settings()
    {

        $response = $this->put('system/export-audits/1', [] );

        $response->assertStatus(404);

    }

    public function test_an_anonymous_user_cannot_delete_the_system_settings()
    {
        $response = $this->delete('system/export-audits/1');

        $response->assertStatus(404);

    }

   

    // Unauthorized user
    public function test_an_unauthorized_user_cannot_access_system_export_audits_index_page()
    {
        $this->actingAs($this->user);

        $response = $this->get('system/export-audits');
        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_system_export_audits_create_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('system/export-audits/create');

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_create_the_system_auditing()
    {
       
        $this->actingAs($this->user);
        $response = $this->post('system/export-audits', []);

        $response->assertStatus(405);
    }
    public function test_an_unauthorized_user_cannot_access_the_system_export_audits_view_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('system/export-audits/1');

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_access_the_system_export_audits_edit_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('system/export-audits/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_update_the_system_auditing()
    {
        $this->actingAs($this->user);
        $response = $this->put('system/export-audits/1', [] );

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_delete_the_system_settings()
    {
        $this->actingAs($this->user);
        $response = $this->delete('system/export-audits/1');

        $response->assertStatus(404);
    }

    
   

     // Administrator
     public function test_an_administrator_can_access_system_export_audits_index_page()
     {

        $this->actingAs($this->admin);
        $response = $this->get('system/export-audits');
  
        $response->assertStatus(200);
     }
     public function test_an_administrator_can_access_the_system_export_audits_create_page()
     {
         $this->actingAs($this->admin);
         $response = $this->get('system/export-audits/create');
 
         $response->assertStatus(404);
     }
     public function test_an_administrator_cannot_create_the_system_auditing()
     {
        $this->actingAs($this->admin);
        $response = $this->post('system/export-audits',[] );

        $response->assertStatus(405);
 
     }
     public function test_an_administrator_can_access_the_system_export_audits_view_page()
     {

        $this->actingAs($this->admin);
        $response = $this->json('get', '/system/export-audits/1', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
 
        $response->assertStatus(404);

     }
     public function test_an_administrator_cannot_access_the_system_export_audits_edit_page()
     {
         $this->actingAs($this->admin);
         $response = $this->get('system/export-audits/1/edit');
 
         $response->assertStatus(404);
     }
     public function test_an_administrator_cannot_update_the_system_auditing()
     {
         $this->actingAs($this->admin);
         $response = $this->put('system/export-audits/1', [] );
 
         $response->assertStatus(404);
     }
     public function test_an_administrator_cannot_delete_the_system_auditing()
     {

        $this->actingAs($this->admin);
        $response = $this->delete('system/export-audits/1');

        $response->assertStatus(404);
     }

}
