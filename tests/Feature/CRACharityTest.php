<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Charity;
use Illuminate\Support\Arr;
use Illuminate\Testing\Fluent\AssertableJson;

class CRACharityTest extends TestCase
{

    // protected static $initialized = FALSE;

    private User $admin;
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::whereHas("roles", function ($q) {$q->where("name", "admin");})->first();
        $this->user  = User::doesntHave('roles')->first();


        Charity::truncate();
    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_charity_index_page()
    {
        $response = $this->get('/settings/charities');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_charity_create_page()
    {
        $response = $this->get('/settings/charities/create');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_create_the_charity()
    {
        $form_data = $this->get_new_record_form_data();

        $response = $this->post('/settings/charities', $form_data);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_charity_view_page()
    {
        $response = $this->get('/settings/charities/1');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_charity_edit_page()
    {
        $response = $this->get('/settings/charities/1/edit');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_update_the_charity()
    {

        $response = $this->put('/settings/charities/1', [] );

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function test_an_anonymous_user_cannot_delete_the_charity()
    {
        $response = $this->delete('/settings/charities/1');

        $response->assertStatus(405);
        // $response->assertRedirect('login');
    }


    public function test_an_unauthorized_user_cannot_access_charity_index_page()
    {
        $this->actingAs($this->user);

        $response = $this->get('/settings/charities');
        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_charity_create_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/charities/create');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_create_the_charity()
    {

        $this->actingAs($this->user);
        $response = $this->post('/settings/charities', []);

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_charity_view_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/charities/1');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_charity_edit_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/charities/1/edit');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_update_the_charity()
    {
        $this->actingAs($this->user);
        $response = $this->put('/settings/charities/1', [] );

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_delete_the_charity()
    {
        $this->actingAs($this->user);
        $response = $this->delete('/settings/charities/1');

        $response->assertStatus(405);
    }

    public function test_an_administrator_can_access_charity_index_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/settings/charities');

        $response->assertStatus(200);

    }
    public function test_an_administrator_can_access_the_charity_create_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/settings/charities/create');

        $response->assertStatus(200);
    }
    public function test_an_administrator_cannot_create_charity_successful_in_db()
    {

        // $cy = Charity::factory(1)->create();
        $form_data = $this->get_new_record_form_data();
        
        $this->actingAs($this->admin);
        $response = $this->post('/settings/charities',  $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        // $this->assertDatabaseHas('charities', $form_data );
           
     }

     public function test_an_administrator_can_access_the_charity_view_page_contains_valid_record()
    {
        $form_data = $this->get_new_record_form_data();

        $this->actingAs($this->admin);
        // $response = $this->get("/settings/charities/{$cy->id}");
        $response = $this->getJson("/settings/charities/" . $form_data['id'], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment( $form_data );
        // $response->assertViewHas('campaign_year', $cy );
    }
    public function test_an_administrator_can_access_charity_edit_page_contains_valid_record()
    {
        $form_data = $this->get_new_record_form_data();

        $this->actingAs($this->admin);
        $response = $this->getJson("/settings/charities/{$form_data['id']}/edit", ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment( $form_data );
    }
    public function test_an_administrator_can_update_charity_successful_in_db()
    {
        // $campignyears = Charity::factory(1)->create();
        $form_data = $this->get_new_record_form_data();

        // modify values
        $form_data['use_alt_address'] = true;
        $form_data['alt_address1'] = "Dummy change";
        $form_data['financial_contact_name'] = "Dummy change";
        $form_data['financial_contact_title'] = "Dummy change";

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/charities/' . $form_data['id'], $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment( $form_data );
        $this->assertDatabaseHas('charities', $form_data );
    }
    public function test_an_administrator_cannot_delete_the_charity_successful_in_db()
    {
        // $campignyears = Charity::factory(1)->create();
        // $cy = $campignyears->first();
        $form_data = $this->get_new_record_form_data();

        $this->actingAs($this->admin);
        $response = $this->json('delete', '/settings/charities/' . $form_data['id'], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        // $response = $this->delete('/settings/charities/' . $cy->id);

        $response->assertStatus(405);
        // $this->assertSoftDeleted('charities', $form_data );
    }

    /** Pagination */
    public function test_pagination_on_campaign_years_table_via_ajax_call()
    {
        // Seed data for pagination testing
        // $this->seed(\Database\Seeders\Charityseeder::class);
        $form_data = $this->get_new_record_form_data();

        $this->actingAs($this->admin);
        $response = $this->getJson('/settings/charities', ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment( $form_data );
    }

    /** Form Validation */
    public function test_validation_rule_fields_are_required_when_create()
    {

        $this->actingAs($this->admin);
        $response = $this->postJson('/settings/charities', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        // $response = $this->post('/settings/charities');
        // This should cause errors with the
        // title and content fields as they aren't present

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'charity_status',
        ]);

    }

    public function test_validation_rule_fields_are_required_when_edit()
    {
        $form_data = $this->get_new_record_form_data();

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/charities/' . $form_data['id'], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        // $response = $this->post('/settings/charities');
        // This should cause errors with the
        // title and content fields as they aren't present
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'charity_status',
        ]);
    }
    public function test_validation_address_and_name_is_over_max_60()
    {
        $form_data = $this->get_new_record_form_data();

        //
        $form_data['use_alt_address'] = true;
        $form_data['alt_address1'] = "very long name, is over 60 chars,Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, ";
        $form_data['alt_address2'] = "very long name, is over 60 chars,Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, ";
        $form_data['financial_contact_name'] = "very long name, is over 60 chars,Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, ";
        $form_data['financial_contact_title'] = "very long name, is over 60 chars,Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, ";

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/charities/' . $form_data['id'], $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'alt_address1',
            'alt_address2',
            'financial_contact_name',
            'financial_contact_title',
        ]);
    }
    public function test_validation_fields_required_when_alt_address_use()
    {
        $form_data = $this->get_new_record_form_data();

        //
        $form_data['use_alt_address'] = true;
        $form_data['alt_address1'] = null;
        $form_data['alt_address2'] = null;
        $form_data['alt_city'] = null;
        $form_data['alt_province'] = null;
        $form_data['alt_postal_code'] = null;
        $form_data['alt_country'] = null;

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/charities/' . $form_data['id'], $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'alt_address1',
            'alt_city',
            'alt_province',
            'alt_postal_code',
            'alt_country',
        ]);
    }
  
    /* Private Function */
    private function get_new_record_form_data() 
    {

        $charities = Charity::factory(1)->create();

        $form_data = $charities[0]->toArray();
        $form_data = Arr::except($form_data, ['category_name', 'designation_name', 'created_by_id', 'updated_by_id', 'updated_at', 'created_at']);
// dd($form_data);
        return $form_data;
    }

}
