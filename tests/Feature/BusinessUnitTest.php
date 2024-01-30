<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\BusinessUnit;
use Illuminate\Testing\Fluent\AssertableJson;

class BusinessUnitTest extends TestCase
{

    // protected static $initialized = FALSE;

    private User $admin;
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::whereHas("roles", function ($q) {$q->where("name", "admin");})->first();
        $this->user  = User::doesntHave('roles')->first();

        BusinessUnit::truncate();
    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_business_unit_index_page()
    {
        $response = $this->get('/settings/business-units');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_business_unit_create_page()
    {
        $response = $this->get('/settings/business-units/create');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_create_the_business_unit()
    {
        $form_data = $this->get_new_record_form_data();

        $response = $this->post('/settings/business-units', $form_data);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_business_unit_view_page()
    {
        $response = $this->get('/settings/business-units/1');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_business_unit_edit_page()
    {
        $response = $this->get('/settings/business-units/1/edit');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_update_the_business_unit()
    {

        $response = $this->put('/settings/business-units/1', [] );

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function test_an_anonymous_user_cannot_delete_the_business_unit()
    {
        $response = $this->delete('/settings/business-units/1');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }


    public function test_an_unauthorized_user_cannot_access_business_unit_index_page()
    {
        $this->actingAs($this->user);

        $response = $this->get('/settings/business-units');
        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_business_unit_create_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/business-units/create');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_create_the_business_unit()
    {
        $form_data = $this->get_new_record_form_data();

        $this->actingAs($this->user);
        $response = $this->post('/settings/business-units', $form_data);

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_business_unit_view_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/business-units/1');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_business_unit_edit_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/business-units/1/edit');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_update_the_business_unit()
    {
        $this->actingAs($this->user);
        $response = $this->put('/settings/business-units/1', [] );

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_delete_the_business_unit()
    {
        $this->actingAs($this->user);
        $response = $this->delete('/settings/business-units/1');

        $response->assertStatus(403);
    }

    public function test_an_administrator_can_access_business_unit_index_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/settings/business-units');

        $response->assertStatus(200);

    }
    public function test_an_administrator_can_access_the_business_unit_create_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/settings/business-units/create');

        $response->assertStatus(200);
    }
    public function test_an_administrator_can_create_business_unit_successful_in_db()
    {

        // $cy = BusinessUnit::factory(1)->create();
        $form_data = $this->get_new_record_form_data();
        
        $this->actingAs($this->admin);
        $response = $this->post('/settings/business-units',  $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('business_units', $form_data );
           
     }

     public function test_an_administrator_can_access_the_business_unit_view_page_contains_valid_record()
    {
        $form_data = $this->get_new_record_form_data();
        $row = BusinessUnit::create( $form_data);

        $this->actingAs($this->admin);
        // $response = $this->get("/settings/business-units/{$cy->id}");
        $response = $this->getJson("/settings/business-units/{$row->id}", ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment( $form_data );
        // $response->assertViewHas('campaign_year', $cy );
    }
    public function test_an_administrator_can_access_business_unit_edit_page_contains_valid_record()
    {
        $form_data = $this->get_new_record_form_data();
        $row = BusinessUnit::create( $form_data ); 

        $this->actingAs($this->admin);
        $response = $this->getJson("/settings/business-units/{$row->id}/edit", ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment( $form_data );
    }
    public function test_an_administrator_can_update_business_unit_successful_in_db()
    {
        // $campignyears = BusinessUnit::factory(1)->create();
        $form_data = $this->get_new_record_form_data();
        $row = BusinessUnit::create( $form_data ); 
        // modify values
        $form_data['effdt'] = '2022-12-10';
        $form_data['status'] = 'I';
        $form_data['name'] = 'Modified Dummy';

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/business-units/' . $row->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment( $form_data );
        $this->assertDatabaseHas('business_units', $form_data );
    }
    public function test_an_administrator_can_delete_the_business_unit_successful_in_db()
    {
        // $campignyears = BusinessUnit::factory(1)->create();
        // $cy = $campignyears->first();
        $form_data = $this->get_new_record_form_data();
        $row = BusinessUnit::create( $form_data ); 

        $this->actingAs($this->admin);
        $response = $this->json('delete', '/settings/business-units/' . $row->id, [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        // $response = $this->delete('/settings/business-units/' . $cy->id);

        $response->assertStatus(204);
        $this->assertSoftDeleted('business_units', $form_data );
    }

    /** Pagination */
    public function test_pagination_on_campaign_years_table_via_ajax_call()
    {
        // Seed data for pagination testing
        // $this->seed(\Database\Seeders\Business-unitseeder::class);
        $form_data = $this->get_new_record_form_data();
        $row = BusinessUnit::create( $form_data ); 

        $this->actingAs($this->admin);
        $response = $this->getJson('/settings/business-units?columns[0][data]=code&order[0][column]=0&order[0][dir]=asc', ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment( $form_data );
    }

    /** Form Validation */
    public function test_validation_rule_fields_are_required_when_create()
    {

        $this->actingAs($this->admin);
        $response = $this->postJson('/settings/business-units', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        // $response = $this->post('/settings/business-units');
        // This should cause errors with the
        // title and content fields as they aren't present

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'code',
            'name',
            'status',
            'linked_bu_code',
        ]);

    }

    public function test_validation_rule_fields_are_required_when_edit()
    {
        $form_data = $this->get_new_record_form_data();
        $row = BusinessUnit::create( $form_data );

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/business-units/' . $row->id, [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        // $response = $this->post('/settings/business-units');
        // This should cause errors with the
        // title and content fields as they aren't present
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'code',
            'name',
            'status',
            'linked_bu_code',
        ]);
    }
    public function test_validation_name_is_over_max_60()
    {
        $form_data = $this->get_new_record_form_data();
        $row = BusinessUnit::create( $form_data );
        //
        $form_data['name'] = "very long name, is over 60 chars,Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, ";

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/business-units/' . $row->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'name',
        ]);
    }
    public function test_validation_status_is_invalid()
    {
        $form_data = $this->get_new_record_form_data();
        $row = BusinessUnit::create( $form_data );
        //
        $form_data['status'] = "C";

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/business-units/' . $row->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'status',
        ]);
    }
    public function test_validation_linked_bu_code_is_invalid()
    {
        $form_data = $this->get_new_record_form_data();
        $row = BusinessUnit::create( $form_data );
        //
        $form_data['linked_bu_code'] = "BC111";

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/business-units/' . $row->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'linked_bu_code',
        ]);
    }


    public function test_validation_invalid_effdt_when_the_status_is_active()
    {

        $form_data = $this->get_new_record_form_data();
        $row = BusinessUnit::create( $form_data );
        //
        $form_data['status'] = "A";
        $form_data['effdt'] = today()->addDays(2);

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/business-units/' . $row->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'effdt',
        ]);
    }
  
    /* Private Function */
    private function get_new_record_form_data() 
    {
        $form_data = [
            "code" => "BC928",
            "effdt" => "2015-01-01",
            "status" => "A",
            "name" => "Dummay data",
            "linked_bu_code" => "BC928",
            "notes" => "testing 123",
        ];

        return $form_data;
    }

}
