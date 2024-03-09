<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Setting;
use App\Models\DailyCampaign;
use App\Models\DailyCampaignSummary;
use Illuminate\Testing\Fluent\AssertableJson;

class ChallengeSettingsTest extends TestCase
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
        DailyCampaign::truncate();
        DailyCampaignSummary::truncate();

        DailyCampaign::factory(10)->create([
            'campaign_year' => today()->month <= 2 ? today()->year -1 : today()->year,
            'as_of_date' => today()->subDays(2)->format('Y-m-d'),
        ]);
        DailyCampaign::factory(10)->create([
            'campaign_year' => today()->month <= 2 ? today()->year -1 : today()->year,
            'as_of_date' => today()->format('Y-m-d'),
        ]);

        
        Setting::Create( [
            'as_of_date' => today()->subDays(2)->format('Y-m-d'),
            'challenge_start_date' => today()->year . '-09-01',
            'challenge_end_date' => today()->year . '-11-15',
            'challenge_final_date' => today()->subDays(2)->format('Y-m-d') ,
            'challenge_processed_final_date' => null,
            
            'campaign_start_date' => today()->year . '-09-01',
            'campaign_end_date' => today()->year . '-11-15',
            'campaign_final_date' => today()->year + 1 . '-02-01',
            'campaign_processed_final_date' => null,
        ]);

    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_challenge_setting_index_page()
    {
        $response = $this->get('/settings/challenge');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_challenge_setting_create_page()
    {
        $response = $this->get('/settings/challenge/create');

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_create_the_challenge_setting()
    {
        // $form_data = $this->get_new_record_form_data();

        $response = $this->post('/settings/challenge', []);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_challenge_setting_view_page()
    {
        $response = $this->get('/settings/challenge/1');

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_challenge_setting_edit_page()
    {
        $response = $this->get('/settings/challenge/1/edit');

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_update_the_challenge_setting()
    {

        $response = $this->put('/settings/challenge/1', [] );

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }

    public function test_an_anonymous_user_cannot_delete_the_challenge_setting()
    {
        $response = $this->delete('/settings/challenge/1');

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_finalize_challenge_data()
    {
        $form_data = Setting::select('challenge_start_date',
            'challenge_end_date',
            'challenge_final_date',
            'campaign_start_date',
            'campaign_end_date',
            'campaign_final_date')->first()->toArray();

        $this->actingAs($this->user);
        $response = $this->json('post', '/settings/challenge/finalize_challenge_data', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(403);
    }

    public function test_an_unauthorized_user_cannot_access_challenge_setting_index_page()
    {
        $this->actingAs($this->user);

        $response = $this->get('/settings/challenge');
        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_challenge_setting_create_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/challenge/create');

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_create_the_challenge_setting()
    {
        // $form_data = $this->get_new_record_form_data();

        $this->actingAs($this->user);
        $response = $this->post('/settings/challenge', []);

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_challenge_setting_view_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/challenge/1');

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_access_the_challenge_setting_edit_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/challenge/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_update_the_challenge_setting()
    {
        $this->actingAs($this->user);
        $response = $this->put('/settings/challenge/1', [] );

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_delete_the_challenge_setting()
    {
        $this->actingAs($this->user);
        $response = $this->delete('/settings/challenge/1');

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_finalize_challenge_data()
    {
        $form_data = Setting::select('challenge_start_date',
            'challenge_end_date',
            'challenge_final_date',
            'campaign_start_date',
            'campaign_end_date',
            'campaign_final_date')->first()->toArray();

        $this->actingAs($this->user);
        $response = $this->json('post', '/settings/challenge/finalize_challenge_data', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(403);

    }

    public function test_an_administrator_can_access_challenge_setting_index_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/settings/challenge');

        $response->assertStatus(200);

    }
    public function test_an_administrator_can_access_the_challenge_setting_create_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/settings/challenge/create');

        $response->assertStatus(404);
    }
    public function test_an_administrator_can_create_challenge_setting_successful_in_db()
    {

        // $cy = Setting::factory(1)->create();
        // $form_data = $this->get_new_record_form_data();
        $form_data = [
            'challenge_start_date' => today()->year . '-09-05',
            // 'challenge_end_date' => today()->year . '-12-31',
            'challenge_end_date' => today()->year . '-11-15',
            'challenge_final_date' => today()->year + 1 . '-02-14',
            
            'campaign_start_date' => today()->year . '-09-05',
            // 'campaign_end_date' => today()->year . '-12-31',
            'campaign_end_date' => today()->year . '-11-15',
            'campaign_final_date' => today()->year + 1 . '-02-14',
        ];
        
        $this->actingAs($this->admin);
        $response = $this->post('/settings/challenge',  $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(204);
        $this->assertDatabaseHas('settings', $form_data );
           
     }

     public function test_an_administrator_can_access_the_challenge_setting_view_page_contains_valid_record()
    {

        $this->actingAs($this->admin);
        // $response = $this->get("/settings/challenge/{$cy->id}");
        $response = $this->getJson("/settings/challenge/" . 1, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(404);
        // $response->assertJsonFragment( $form_data );
        // $response->assertViewHas('campaign_year', $cy );
    }
    public function test_an_administrator_can_access_challenge_setting_edit_page_contains_valid_record()
    {
        // $form_data = $this->get_new_record_form_data();
        // $row = Setting::create( $form_data ); 

        $this->actingAs($this->admin);
        $response = $this->getJson("/settings/challenge/". 1 . "/edit", ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(404);
        // $response->assertJsonFragment( $form_data );
    }
    public function test_an_administrator_can_update_challenge_setting_successful_in_db()
    {
        // $campignyears = Setting::factory(1)->create();

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/challenge/1', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(404);
        // $response->assertJsonFragment( $form_data );
        // $this->assertDatabaseHas('settings', $form_data );
    }
    public function test_an_administrator_can_delete_the_challenge_setting_successful_in_db()
    {
        // $campignyears = Setting::factory(1)->create();
        // $cy = $campignyears->first();
        // $form_data = $this->get_new_record_form_data();
        // $row = Setting::create( $form_data ); 

        $this->actingAs($this->admin);
        $response = $this->json('delete', '/settings/challenge/1', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        // $response = $this->delete('/settings/challenge/' . $cy->id);

        $response->assertStatus(404);
        // $this->assertSoftDeleted('settings', $form_data );
    }
    public function test_an_administrator_can_finalize_challenge_data_in_db()
    {
        $setting = Setting::first();

        $setting->challenge_final_date = today()->format('Y-m-d');
        $setting->save();

        $form_data = [
            'challenge_start_date' => $setting->challenge_start_date->format('Y-m-d'),
            'challenge_end_date' => $setting->challenge_end_date->format('Y-m-d'),
            'challenge_final_date' => $setting->challenge_final_date->format('Y-m-d'),
            'campaign_start_date' => $setting->campaign_start_date->format('Y-m-d'),
            'campaign_end_date' => $setting->campaign_end_date->format('Y-m-d'),
            'campaign_final_date' => $setting->campaign_final_date->format('Y-m-d'),
        ]; 
        
        $this->actingAs($this->admin);
        $response = $this->json('post', '/settings/challenge/finalize_challenge_data', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(204);

        $as_of_date = $setting->challenge_final_date;
        $campaign_year = Setting::challenge_page_campaign_year( $as_of_date );   
        $expected_data = [
                'as_of_date' => $as_of_date,
                'campaign_year' => $campaign_year,
            ];


        $this->assertDatabaseHas('daily_campaign_summaries', $expected_data );
        // $this->assertSoftDeleted('settings', $form_data );
    }

    /** Form Validation */
    public function test_validation_rule_fields_are_required_when_create()
    {

        $this->actingAs($this->admin);
        $response = $this->postJson('/settings/challenge', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        // $response = $this->post('/settings/challenge');
        // This should cause errors with the
        // title and content fields as they aren't present

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'challenge_start_date',
            'challenge_end_date',
            'challenge_final_date',
            'campaign_start_date',
            'campaign_end_date',
            'campaign_final_date',
        ]);

    }

    public function test_validation_rule_fields_are_required_when_edit()
    {
        // $form_data = $this->get_new_record_form_data();
        // $row = Setting::create( $form_data );

        $this->actingAs($this->admin);
        $response = $this->postJson('/settings/challenge', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        // $response = $this->post('/settings/challenge');
        // This should cause errors with the
        // title and content fields as they aren't present
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'challenge_start_date',
            'challenge_end_date',
            'challenge_final_date',
            'campaign_start_date',
            'campaign_end_date',
            'campaign_final_date',
        ]);
    }
    public function test_validation_end_date_must_be_later_than_start_date()
    {
        // $form_data = $this->get_new_record_form_data();
        // $row = Setting::create( $form_data );
        //
        $form_data['challenge_start_date'] = today()->year . '-09-05';
        $form_data['challenge_end_date'] = today()->year . '-08-05';
        $form_data['challenge_final_date'] = today()->year . '-12-31';

        $form_data['campaign_start_date'] = today()->year . '-09-05';
        $form_data['campaign_end_date'] = today()->year . '-08-05';
        $form_data['campaign_final_date'] = today()->year . '-12-31';

        $this->actingAs($this->admin);
        $response = $this->postJson('/settings/challenge', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'challenge_end_date',
            'campaign_end_date',
        ]);
    }

    public function test_validation_final_date_is_invaid_when_finalize_challenge_data()
    {

        $form_data = Setting::select('challenge_start_date',
            'challenge_end_date',
            'challenge_final_date',
            'campaign_start_date',
            'campaign_end_date',
            'campaign_final_date')->first()->toArray();

        $form_data['challenge_final_date'] = today()->year + 1 . '-02-01';
        
        $this->actingAs($this->admin);
        $response = $this->json('post', '/settings/challenge/finalize_challenge_data', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'challenge_final_date',
        ]);

    }

    

}
