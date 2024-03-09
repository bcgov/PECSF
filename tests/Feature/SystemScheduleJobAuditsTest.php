<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Setting;
use App\Models\ScheduleJobAudit;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SystemScheduleJobAuditsTest extends TestCase
{
    // protected static $initialized = FALSE;

    private User $admin;
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::whereHas("roles", function ($q) {$q->where("name", "admin");})->first();
        $this->user  = User::doesntHave('roles')->first();

        ScheduleJobAudit::truncate();
    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_system_schedule_job_audits_index_page()
    {
        $response = $this->get('system/schedule-job-audits');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_system_schedule_job_audits_create_page()
    {
        $response = $this->get('system/schedule-job-audits/create');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_create_the_system_schedule_job_audits_in_db()
    {

        $response = $this->post('system/schedule-job-audits', []);

        $response->assertStatus(405);
    }
    public function test_an_anonymous_user_cannot_access_the_system_schedule_job_audits_view_page()
    {
        $response = $this->get('system/schedule-job-audits/1');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_system_schedule_job_audits_edit_page()
    {
        $response = $this->get('system/schedule-job-audits/1/edit');

        $response->assertStatus(404);

    }
    public function test_an_anonymous_user_cannot_update_the_system_settings()
    {

        $response = $this->put('system/schedule-job-audits/1', [] );

        $response->assertStatus(405);

    }

    public function test_an_anonymous_user_cannot_delete_the_system_settings()
    {
        $response = $this->delete('system/schedule-job-audits/1');

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }

    // Unauthorized user
    public function test_an_unauthorized_user_cannot_access_system_schedule_job_audits_index_page()
    {
        $this->actingAs($this->user);

        $response = $this->get('system/schedule-job-audits');
        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_system_schedule_job_audits_create_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('system/schedule-job-audits/create');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_create_the_system_schedule_job_audits()
    {
       
        $this->actingAs($this->user);
        $response = $this->post('system/schedule-job-audits', []);

        $response->assertStatus(405);
    }
    public function test_an_unauthorized_user_cannot_access_the_system_schedule_job_audits_view_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('system/schedule-job-audits/1');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_system_schedule_job_audits_edit_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('system/schedule-job-audits/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_update_the_system_schedule_job_audits()
    {
        $this->actingAs($this->user);
        $response = $this->put('system/schedule-job-audits/1', [] );

        $response->assertStatus(405);
    }
    public function test_an_unauthorized_user_cannot_delete_the_system_settings()
    {
        $this->actingAs($this->user);
        $response = $this->delete('system/schedule-job-audits/1');

        $response->assertStatus(403);
    }


     // Administrator
     public function test_an_administrator_can_access_system_schedule_job_audits_index_page()
     {

        $year = (today() < today()->year . '-03-01') ? today()->year - 1 : today()->year ;
        $setting = Setting::create([
            'campaign_start_date' => $year . '-09-01',
            'campaign_end_date' => $year . '-11-15',
            'campaign_final_date' => (today() < today()->year . '-03-01') ? today() : $year + 1 . '-02-28',
            'challenge_start_date' => $year . '-09-01',
            'challenge_end_date' => $year . '-11-15',
            'challenge_final_date' => (today() < today()->year . '-03-01') ? today() : $year + 1 . '-02-28',
         ]);
        // run command 
        $this->artisan('command:UpdateDailyCampaign')->assertExitCode(0);

        $this->actingAs($this->admin);
        $response = $this->get('system/schedule-job-audits');
  
        $response->assertStatus(200);
     }
     public function test_an_administrator_can_access_the_system_schedule_job_audits_create_page()
     {
         $this->actingAs($this->admin);
         $response = $this->get('system/schedule-job-audits/create');
 
         $response->assertStatus(200);
     }
     public function test_an_administrator_cannot_create_the_system_schedule_job_audits()
     {
        $this->actingAs($this->admin);
        $response = $this->post('system/schedule-job-audits',[] );

        $response->assertStatus(405);
 

     }
     public function test_an_administrator_can_access_the_system_schedule_job_audits_view_page()
     {

        $year = (today() < today()->year . '-03-01') ? today()->year - 1 : today()->year ;
        $setting = Setting::create([
            'campaign_start_date' => $year . '-09-01',
            'campaign_end_date' => $year . '-11-15',
            'campaign_final_date' => (today() < today()->year . '-03-01') ? today() : $year + 1 . '-02-28',
            'challenge_start_date' => $year . '-09-01',
            'challenge_end_date' => $year . '-11-15',
            'challenge_final_date' => (today() < today()->year . '-03-01') ? today() : $year + 1 . '-02-28',
         ]);
        // run command 
        $this->artisan('command:UpdateDailyCampaign')->assertExitCode(0);

        $this->actingAs($this->admin);
        $response = $this->json('get', '/system/schedule-job-audits/1', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
 
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => 1,
            'job_name' => "command:UpdateDailyCampaign",
            'status' => "Completed",             
        ]);


     }
     public function test_an_administrator_cannot_access_the_system_schedule_job_audits_edit_page()
     {
         $this->actingAs($this->admin);
         $response = $this->get('system/schedule-job-audits/1/edit');
 
         $response->assertStatus(404);
     }
     public function test_an_administrator_cannot_update_the_system_schedule_job_audits()
     {
         $this->actingAs($this->admin);
         $response = $this->put('system/schedule-job-audits/1', [] );
 
         $response->assertStatus(405);
     }
     public function test_an_administrator_cannot_delete_the_system_schedule_job_audits()
     {

        $year = (today() < today()->year . '-03-01') ? today()->year - 1 : today()->year ;
        $setting = Setting::create([
            'campaign_start_date' => $year . '-09-01',
            'campaign_end_date' => $year . '-11-15',
            'campaign_final_date' => (today() < today()->year . '-03-01') ? today() : $year + 1 . '-02-28',
            'challenge_start_date' => $year . '-09-01',
            'challenge_end_date' => $year . '-11-15',
            'challenge_final_date' => (today() < today()->year . '-03-01') ? today() : $year + 1 . '-02-28',
         ]);
        // run command 
        $this->artisan('command:UpdateDailyCampaign')->assertExitCode(0);

        $audit =  ScheduleJobAudit::first();

         $this->actingAs($this->admin);
         $response = $this->delete('system/schedule-job-audits/1');
 
         $response->assertStatus(204);
     }

}
