<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SystemSettingsTest extends TestCase
{
    // protected static $initialized = FALSE;

    private User $admin;
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::whereHas("roles", function ($q) {$q->where("name", "admin");})->first();
        $this->user  = User::doesntHave('roles')->first();

        Setting::truncate();
    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_system_settings_index_page()
    {
        $response = $this->get('/system/settings');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_system_settings_create_page()
    {
        $response = $this->get('/system/settings/create');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_create_the_system_settings()
    {

        $response = $this->post('/system/settings', []);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_system_settings_view_page()
    {
        $response = $this->get('/system/settings/1');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_access_the_system_settings_edit_page()
    {
        $response = $this->get('/system/settings/1/edit');

        $response->assertStatus(404);

    }
    public function test_an_anonymous_user_cannot_update_the_system_settings()
    {

        $response = $this->put('/system/settings/1', [] );

        $response->assertStatus(404);

    }

    public function test_an_anonymous_user_cannot_delete_the_system_settings()
    {
        $response = $this->delete('/system/settings/1');

        $response->assertStatus(404);

    }

    // Unauthorized user
    public function test_an_unauthorized_user_cannot_access_system_settings_index_page()
    {
        $this->actingAs($this->user);

        $response = $this->get('/system/settings');
        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_system_settings_create_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/system/settings/create');

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_create_the_system_settings()
    {
       
        $this->actingAs($this->user);
        $response = $this->post('/system/settings', []);

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_system_settings_view_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/system/settings/1');

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_access_the_system_settings_edit_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/system/settings/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_update_the_system_settings()
    {
        $this->actingAs($this->user);
        $response = $this->put('/system/settings/1', [] );

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_delete_the_system_settings()
    {
        $this->actingAs($this->user);
        $response = $this->delete('/system/settings/1');

        $response->assertStatus(404);
    }


     // Administrator
     public function test_an_administrator_can_access_system_settings_index_page()
     {
        $setting = Setting::create([
            'system_lockdown_start' => '2020-01-01',
            'system_lockdown_end' => today(),
            
        ]); 

         $this->actingAs($this->admin);
         $response = $this->get('/system/settings');
  
         $response->assertStatus(200);
     }
     public function test_an_administrator_cannot_access_the_system_settings_create_page()
     {
         $this->actingAs($this->admin);
         $response = $this->get('/system/settings/create');
 
         $response->assertStatus(404);
     }
     public function test_an_administrator_cannot_create_the_system_settings()
     {

        $setting = Setting::create([
            'system_lockdown_start' => '2020-01-01',
            'system_lockdown_end' => today(),
        ]); 

        $form_data = [
            'system_lockdown_start' => today()->subDays(1)->format('Y-m-d\TH:i'),
            'system_lockdown_end' => now()->format('Y-m-d\TH:i'),
        ];

         $this->actingAs($this->admin);
         $response = $this->post('/system/settings', $form_data );
 
         $response->assertStatus(302);
         $response->assertRedirect('/system/settings');
         $response->assertSessionHas('success', 'The setting was successfully saved.');

        $this->assertDatabaseHas('settings',  $form_data );

     }
     public function test_an_administrator_cannot_access_the_system_settings_view_page()
     {
         $this->actingAs($this->admin);
         $response = $this->get('/system/settings/1');
 
         $response->assertStatus(404);
     }
     public function test_an_administrator_cannot_access_the_system_settings_edit_page()
     {
         $this->actingAs($this->admin);
         $response = $this->get('/system/settings/1/edit');
 
         $response->assertStatus(404);
     }
     public function test_an_administrator_cannot_update_the_system_settings()
     {
         $this->actingAs($this->admin);
         $response = $this->put('/system/settings/1', [] );
 
         $response->assertStatus(404);
     }
     public function test_an_administrator_cannot_delete_the_system_settings()
     {
         $this->actingAs($this->admin);
         $response = $this->delete('/system/settings/1');
 
         $response->assertStatus(404);
     }

    /** Form Validation */
    public function test_validation_rule_fields_are_required_when_edit()
    {

        $this->actingAs($this->admin);
        $response = $this->post('/system/settings', [] );  

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            "system_lockdown_start" =>"The system lockdown start field is required.",
            "system_lockdown_end" => "The system lockdown end field is required.",
        ]);
    }

    public function test_validation_rule_end_dt_must_after_start_dt_when_edit()
    {

        $form_data = [
            'system_lockdown_start' => today()->format('Y-m-d\TH:i'),
            'system_lockdown_end' => today()->subDays(1)->format('Y-m-d\TH:i'),
        ];

        $this->actingAs($this->admin);
        $response = $this->post('/system/settings', $form_data );  

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            "system_lockdown_end" => "The system lockdown end must be a date after system lockdown start.",
        ]);
    }

}
