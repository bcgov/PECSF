<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Arr;
use App\Models\BusinessUnit;
use App\Models\DailyCampaignSummary;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;

class ChallengeSummaryMaintenanceTest extends TestCase
{

    use WithFaker;

    // protected static $initialized = FALSE;

    private User $admin;
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::whereHas("roles", function ($q) {$q->where("name", "admin");})->first();
        $this->user  = User::doesntHave('roles')->first();

        BusinessUnit::truncate();
        DailyCampaignSummary::truncate();
    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_challenge_summary_index_page()
    {
        $response = $this->get('/settings/challenge-summary');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_challenge_summary_create_page()
    {
        $response = $this->get('/settings/challenge-summary/create');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_create_the_challenge_summary()
    {
        $form_data = $this->get_new_record_form_data();

        $response = $this->post('/settings/challenge-summary', $form_data);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_challenge_summary_view_page()
    {
        $response = $this->get('/settings/challenge-summary/1');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_challenge_summary_edit_page()
    {
        $response = $this->get('/settings/challenge-summary/1/edit');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_update_the_challenge_summary()
    {

        $response = $this->put('/settings/challenge-summary/1', [] );

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function test_an_anonymous_user_cannot_delete_the_challenge_summary()
    {
        $response = $this->delete('/settings/challenge-summary/1');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }


    public function test_an_unauthorized_user_cannot_access_challenge_summary_index_page()
    {
        $this->actingAs($this->user);

        $response = $this->get('/settings/challenge-summary');
        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_challenge_summary_create_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/challenge-summary/create');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_create_the_challenge_summary()
    {
        $form_data = $this->get_new_record_form_data();

        $this->actingAs($this->user);
        $response = $this->post('/settings/challenge-summary', $form_data);

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_challenge_summary_view_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/challenge-summary/1');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_challenge_summary_edit_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/challenge-summary/1/edit');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_update_the_challenge_summary()
    {
        $this->actingAs($this->user);
        $response = $this->put('/settings/challenge-summary/1', [] );

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_delete_the_challenge_summary()
    {
        $this->actingAs($this->user);
        $response = $this->delete('/settings/challenge-summary/1');

        $response->assertStatus(403);
    }

    // Administrator
    public function test_an_administrator_can_access_challenge_summary_index_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/settings/challenge-summary');

        $response->assertStatus(200);

    }
    public function test_an_administrator_can_access_the_challenge_summary_create_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/settings/challenge-summary/create');

        $response->assertStatus(200);
    }
    public function test_an_administrator_can_create_challenge_summary_successful_in_db()
    {

        // $cy = BusinessUnit::factory(1)->create();
        $form_data = $this->get_new_record_form_data();
        
        $this->actingAs($this->admin);
        $response = $this->post('/settings/challenge-summary',  $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('daily_campaign_summaries', $form_data );
           
     }

     public function test_an_administrator_can_access_the_challenge_summary_view_page_contains_valid_record()
    {
        $form_data = $this->get_new_record_form_data();
        $row = DailyCampaignSummary::create( $form_data);

        $this->actingAs($this->admin);
        // $response = $this->get("/settings/challenge-summary/{$cy->id}");
        $response = $this->getJson("/settings/challenge-summary/{$row->id}", ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment( $form_data );
    }
    public function test_an_administrator_can_access_challenge_summary_edit_page_contains_valid_record()
    {
        $form_data = $this->get_new_record_form_data();
        $row = DailyCampaignSummary::create( $form_data ); 

        $form_data['dollars'] =  (int) $form_data['dollars'];
        $form_data['donors'] =  (int) $form_data['donors'];

        $this->actingAs($this->admin);
        $response = $this->getJson("/settings/challenge-summary/{$row->id}/edit", ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment( $form_data );
    }
    public function test_an_administrator_can_update_challenge_summary_successful_in_db()
    {
        // $campignyears = DailyCampaignSummary::factory(1)->create();
        $form_data = $this->get_new_record_form_data();
        $row = DailyCampaignSummary::create( $form_data ); 
        // modify values
        $form_data['donors'] = 20;
        $form_data['dollars'] = 200;
    
        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/challenge-summary/' . $row->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment( $form_data );
        $this->assertDatabaseHas('daily_campaign_summaries', $form_data );
    }
    public function test_an_administrator_can_delete_the_challenge_summary_successful_in_db()
    {
        // $campignyears = DailyCampaignSummary::factory(1)->create();
        // $cy = $campignyears->first();
        $form_data = $this->get_new_record_form_data();
        $row = DailyCampaignSummary::create( $form_data ); 

        $this->actingAs($this->admin);
        $response = $this->json('delete', '/settings/challenge-summary/' . $row->id, [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        // $response = $this->delete('/settings/challenge-summary/' . $cy->id);

        $response->assertStatus(204);
        $this->assertSoftDeleted('daily_campaign_summaries', $form_data );
    }

    // /** Pagination */
    public function test_pagination_on_campaign_years_table_via_ajax_call()
    {
        // Seed data for pagination testing
        // $this->seed(\Database\Seeders\Business-unitseeder::class);
        $form_data = $this->get_new_record_form_data();
        $row = DailyCampaignSummary::create( $form_data ); 

        $form_data['dollars'] =  (int) $form_data['dollars'];
        $form_data['donors'] =  (int) $form_data['donors'];

        $this->actingAs($this->admin);
        $response = $this->getJson('/settings/challenge-summary?columns[0][data]=code&order[0][column]=0&order[0][dir]=asc', ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment( $form_data );
    }

    // /** Form Validation */
    public function test_validation_rule_fields_are_required_when_create()
    {

        $this->actingAs($this->admin);
        $response = $this->postJson('/settings/challenge-summary', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        // $response = $this->post('/settings/challenge-summary');
        // This should cause errors with the
        // title and content fields as they aren't present

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            "campaign_year" => "The campaign year field is required.",         
            "as_of_date" => "The as of date field is required.",
            "donors" => "The donors field is required.",  
            "dollars" => "The dollars field is required.", 
        ]);

    }

    public function test_validation_rule_fields_are_required_when_edit()
    {
        $form_data = $this->get_new_record_form_data();
        $row = DailyCampaignSummary::create( $form_data );

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/challenge-summary/' . $row->id, [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        // $response = $this->post('/settings/challenge-summary');
        // This should cause errors with the
        // title and content fields as they aren't present
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            "campaign_year" => "The campaign year field is required.",         
            "as_of_date" => "The as of date field is required.",
            "donors" => "The donors field is required.",  
            "dollars" => "The dollars field is required.", 
        ]);
    }
    // public function test_validation_name_is_over_max_60()
    // {
    //     $form_data = $this->get_new_record_form_data();
    //     $row = BusinessUnit::create( $form_data );
    //     //
    //     $form_data['name'] = "very long name, is over 60 chars,Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, ";

    //     $this->actingAs($this->admin);
    //     $response = $this->json('put', '/settings/challenge-summary/' . $row->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

    //     $response->assertStatus(422);
    //     $response->assertJsonValidationErrors([
    //         'name',
    //     ]);
    // }
    public function test_validation_as_of_date_is_invalid()
    {
        $form_data = $this->get_new_record_form_data();
        $row = DailyCampaignSummary::create( $form_data );
        //
        $form_data['as_of_date'] = "2002";

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/challenge-summary/' . $row->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'as_of_date' =>  "The as of date is not a valid date.",
        ]);
    }
    public function test_validation_donors_and_dollars_are_invalid()
    {
        $form_data = $this->get_new_record_form_data();
        $row = DailyCampaignSummary::create( $form_data );
        //
        $form_data['donors'] = "iue";
        $form_data['dollars'] = "sss";

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/challenge-summary/' . $row->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            "donors" => "The donors must be a number.",
            "dollars" => "The dollars must be a number.",
        ]);
    }

    public function test_validation_only_unique_transaction_allow_to_store()
    {

        $form_data = $this->get_new_record_form_data();
        $row = DailyCampaignSummary::create( $form_data );

        $this->actingAs($this->admin);
        $response = $this->json('post', '/settings/challenge-summary',  $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            "campaign_year" => "The campaign year has already been taken.",
        ]);
    }
  
    /* Private Function */
    private function get_new_record_form_data() 
    {
        $form_data = [
            'campaign_year' => 2023,
            'as_of_date' =>  $this->faker->date(),
            'donors' => "10",
            'dollars' =>  "100",
            'notes' =>  $this->faker->sentence(),
        ];

        return $form_data;
    }

}
