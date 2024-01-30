<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Region;
use Illuminate\Testing\Fluent\AssertableJson;

class RegionTest extends TestCase
{

    // protected static $initialized = FALSE;

    private User $admin;
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::whereHas("roles", function ($q) {$q->where("name", "admin");})->first();
        $this->user  = User::doesntHave('roles')->first();

        Region::truncate();
    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_region_index_page()
    {
        $response = $this->get('/settings/regions');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_region_create_page()
    {
        $response = $this->get('/settings/regions/create');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_create_the_region()
    {
        $form_data = $this->get_new_record_form_data();

        $response = $this->post('/settings/regions', $form_data);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_region_view_page()
    {
        $response = $this->get('/settings/regions/1');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_region_edit_page()
    {
        $response = $this->get('/settings/regions/1/edit');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_update_the_region()
    {

        $response = $this->put('/settings/regions/1', [] );

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function test_an_anonymous_user_cannot_delete_the_region()
    {
        $response = $this->delete('/settings/regions/1');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }


    public function test_an_unauthorized_user_cannot_access_region_index_page()
    {
        $this->actingAs($this->user);

        $response = $this->get('/settings/regions');
        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_region_create_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/regions/create');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_create_the_region()
    {
        $form_data = $this->get_new_record_form_data();

        $this->actingAs($this->user);
        $response = $this->post('/settings/regions', $form_data);

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_region_view_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/regions/1');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_region_edit_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/regions/1/edit');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_update_the_region()
    {
        $this->actingAs($this->user);
        $response = $this->put('/settings/regions/1', [] );

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_delete_the_region()
    {
        $this->actingAs($this->user);
        $response = $this->delete('/settings/regions/1');

        $response->assertStatus(403);
    }

    public function test_an_administrator_can_access_region_index_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/settings/regions');

        $response->assertStatus(200);

    }
    public function test_an_administrator_can_access_the_region_create_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/settings/regions/create');

        $response->assertStatus(200);
    }
    public function test_an_administrator_can_create_region_successful_in_db()
    {

        // $cy = Region::factory(1)->create();
        $form_data = $this->get_new_record_form_data();
        
        $this->actingAs($this->admin);
        $response = $this->post('/settings/regions',  $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('regions', $form_data );
           
     }

     public function test_an_administrator_can_access_the_region_view_page_contains_valid_record()
    {
        $form_data = $this->get_new_record_form_data();
        $row = Region::create( $form_data);

        $this->actingAs($this->admin);
        // $response = $this->get("/settings/regions/{$cy->id}");
        $response = $this->getJson("/settings/regions/{$row->id}", ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment( $form_data );
        // $response->assertViewHas('campaign_year', $cy );
    }
    public function test_an_administrator_can_access_region_edit_page_contains_valid_record()
    {
        $form_data = $this->get_new_record_form_data();
        $row = Region::create( $form_data ); 

        $this->actingAs($this->admin);
        $response = $this->getJson("/settings/regions/{$row->id}/edit", ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment( $form_data );
    }
    public function test_an_administrator_can_update_region_successful_in_db()
    {
        // $campignyears = Region::factory(1)->create();
        $form_data = $this->get_new_record_form_data();
        $row = Region::create( $form_data ); 
        // modify values
        $form_data['effdt'] = '2022-12-10';
        $form_data['status'] = 'I';
        $form_data['name'] = 'Modified Dummy';

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/regions/' . $row->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment( $form_data );
        $this->assertDatabaseHas('regions', $form_data );
    }
    public function test_an_administrator_can_delete_the_region_successful_in_db()
    {
        // $campignyears = Region::factory(1)->create();
        // $cy = $campignyears->first();
        $form_data = $this->get_new_record_form_data();
        $row = Region::create( $form_data ); 

        $this->actingAs($this->admin);
        $response = $this->json('delete', '/settings/regions/' . $row->id, [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        // $response = $this->delete('/settings/regions/' . $cy->id);

        $response->assertStatus(204);
        $this->assertSoftDeleted('regions', $form_data );
    }

    /** Pagination */
    public function test_pagination_on_campaign_years_table_via_ajax_call()
    {
        // Seed data for pagination testing
        // $this->seed(\Database\Seeders\Regionseeder::class);
        $form_data = $this->get_new_record_form_data();
        $row = Region::create( $form_data ); 

        $this->actingAs($this->admin);
        $response = $this->getJson('/settings/regions?columns[0][data]=code&order[0][column]=0&order[0][dir]=asc', ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment( $form_data );
    }

    /** Form Validation */
    public function test_validation_rule_fields_are_required_when_create()
    {

        $this->actingAs($this->admin);
        $response = $this->postJson('/settings/regions', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        // $response = $this->post('/settings/regions');
        // This should cause errors with the
        // title and content fields as they aren't present

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'code',
            'effdt',
            'name',
            'status',
        ]);

    }

    public function test_validation_rule_fields_are_required_when_edit()
    {
        $form_data = $this->get_new_record_form_data();
        $row = Region::create( $form_data );

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/regions/' . $row->id, [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        // $response = $this->post('/settings/regions');
        // This should cause errors with the
        // title and content fields as they aren't present
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'code',
            'effdt',
            'name',
            'status',

        ]);
    }
    public function test_validation_name_is_over_max_30()
    {
        $form_data = $this->get_new_record_form_data();
        $row = Region::create( $form_data );
        //
        $form_data['name'] = "very long name, is over 60 chars,Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, ";

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/regions/' . $row->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'name',
        ]);
    }
    public function test_validation_status_is_invalid()
    {
        $form_data = $this->get_new_record_form_data();
        $row = Region::create( $form_data );
        //
        $form_data['status'] = "C";

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/regions/' . $row->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'status',
        ]);
    }
    public function test_validation_code_is_invalid()
    {
        $form_data = $this->get_new_record_form_data();
        $row = Region::create( $form_data );
        //
        $form_data['code'] = "A12";

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/regions/' . $row->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'code',
        ]);
    }
  
    /* Private Function */
    private function get_new_record_form_data() 
    {
        $form_data = [
            "code" => "041",
            "effdt" => "2015-01-01",
            "status" => "A",
            "name" => "Dummay Name",
            "notes" => "testing 123",
        ];

        return $form_data;
    }

}
