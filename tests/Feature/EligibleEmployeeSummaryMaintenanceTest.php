<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Arr;
use App\Models\BusinessUnit;
use App\Models\Organization;
use App\Models\EligibleEmployeeByBU;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;

class EligibleEmployeeSummaryMaintenanceTest extends TestCase
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
        Organization::truncate();
        EligibleEmployeeByBU::truncate();
    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_eligible_employee_summary_index_page()
    {
        $response = $this->get('/settings/eligible-employee-summary');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_eligible_employee_summary_create_page()
    {
        $response = $this->get('/settings/eligible-employee-summary/create');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_create_the_eligible_employee_summary()
    {
        $form_data = $this->get_new_record_form_data();

        $response = $this->post('/settings/eligible-employee-summary', $form_data);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_eligible_employee_summary_view_page()
    {
        $response = $this->get('/settings/eligible-employee-summary/1');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_eligible_employee_summary_edit_page()
    {
        $response = $this->get('/settings/eligible-employee-summary/1/edit');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_update_the_eligible_employee_summary()
    {

        $response = $this->put('/settings/eligible-employee-summary/1', [] );

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function test_an_anonymous_user_cannot_delete_the_eligible_employee_summary()
    {
        $response = $this->delete('/settings/eligible-employee-summary/1');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }


    public function test_an_unauthorized_user_cannot_access_eligible_employee_summary_index_page()
    {
        $this->actingAs($this->user);

        $response = $this->get('/settings/eligible-employee-summary');
        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_eligible_employee_summary_create_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/eligible-employee-summary/create');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_create_the_eligible_employee_summary()
    {
        $form_data = $this->get_new_record_form_data();

        $this->actingAs($this->user);
        $response = $this->post('/settings/eligible-employee-summary', $form_data);

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_eligible_employee_summary_view_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/eligible-employee-summary/1');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_eligible_employee_summary_edit_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/eligible-employee-summary/1/edit');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_update_the_eligible_employee_summary()
    {
        $this->actingAs($this->user);
        $response = $this->put('/settings/eligible-employee-summary/1', [] );

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_delete_the_eligible_employee_summary()
    {
        $this->actingAs($this->user);
        $response = $this->delete('/settings/eligible-employee-summary/1');

        $response->assertStatus(403);
    }

    // Administrator
    public function test_an_administrator_can_access_eligible_employee_summary_index_page()
    {

        $this->actingAs($this->admin);
        $response = $this->get('/settings/eligible-employee-summary');

        $response->assertStatus(200);

    }
    public function test_an_administrator_can_access_the_eligible_employee_summary_create_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/settings/eligible-employee-summary/create');

        $response->assertStatus(200);
    }
    public function test_an_administrator_can_create_eligible_employee_summary_successful_in_db()
    {

        // $cy = BusinessUnit::factory(1)->create();
        $form_data = $this->get_new_record_form_data();
        
        $this->actingAs($this->admin);
        $response = $this->post('/settings/eligible-employee-summary',  $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('eligible_employee_by_bus', $form_data );
           
     }

     public function test_an_administrator_can_access_the_eligible_employee_summary_view_page_contains_valid_record()
    {
        $form_data = $this->get_new_record_form_data();
        $row = EligibleEmployeeByBU::create( $form_data);

        $this->actingAs($this->admin);
        // $response = $this->get("/settings/eligible-employee-summary/{$cy->id}");
        $response = $this->getJson("/settings/eligible-employee-summary/{$row->id}", ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment( $form_data );
    }
    public function test_an_administrator_can_access_eligible_employee_summary_edit_page_contains_valid_record()
    {
        $form_data = $this->get_new_record_form_data();
        $row = EligibleEmployeeByBU::create( $form_data ); 

        $form_data['ee_count'] = (int) $form_data['ee_count'];

        $this->actingAs($this->admin);
        $response = $this->getJson("/settings/eligible-employee-summary/{$row->id}/edit", ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment( $form_data );
    }
    public function test_an_administrator_can_update_eligible_employee_summary_successful_in_db()
    {
        // $campignyears = EligibleEmployeeByBU::factory(1)->create();
        $form_data = $this->get_new_record_form_data();
        $row = EligibleEmployeeByBU::create( $form_data ); 
        // modify values
        $form_data['ee_count'] = 899;
    
        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/eligible-employee-summary/' . $row->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment( $form_data );
        $this->assertDatabaseHas('eligible_employee_by_bus', $form_data );
    }
    public function test_an_administrator_can_delete_the_eligible_employee_summary_successful_in_db()
    {
        // $campignyears = EligibleEmployeeByBU::factory(1)->create();
        // $cy = $campignyears->first();
        $form_data = $this->get_new_record_form_data();
        $row = EligibleEmployeeByBU::create( $form_data ); 

        $this->actingAs($this->admin);
        $response = $this->json('delete', '/settings/eligible-employee-summary/' . $row->id, [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        // $response = $this->delete('/settings/eligible-employee-summary/' . $cy->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('eligible_employee_by_bus', $form_data );
    }

    // /** Pagination */
    public function test_pagination_on_campaign_years_table_via_ajax_call()
    {
        // Seed data for pagination testing
        // $this->seed(\Database\Seeders\Business-unitseeder::class);
        $form_data = $this->get_new_record_form_data();
        $row = EligibleEmployeeByBU::create( $form_data ); 

        $form_data['ee_count'] = (int) $form_data['ee_count'];

        $this->actingAs($this->admin);
        $response = $this->getJson('/settings/eligible-employee-summary?columns[0][data]=campaign_year&order[0][column]=0&order[0][dir]=asc', ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment( $form_data );
    }

    // /** Form Validation */
    public function test_validation_rule_fields_are_required_when_create()
    {

        $this->actingAs($this->admin);
        $response = $this->postJson('/settings/eligible-employee-summary', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        // $response = $this->post('/settings/eligible-employee-summary');
        // This should cause errors with the
        // title and content fields as they aren't present

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            "campaign_year" => "The campaign year field is required.",         
            "organization_code" => "The organization code field is required.",
            "ee_count" => "The employee count field is required.",
        ]);

    }

    public function test_validation_rule_fields_are_required_when_edit()
    {
        $form_data = $this->get_new_record_form_data();
        $row = EligibleEmployeeByBU::create( $form_data );

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/eligible-employee-summary/' . $row->id, [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        // $response = $this->post('/settings/eligible-employee-summary');
        // This should cause errors with the
        // title and content fields as they aren't present
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            "ee_count" => "The employee count field is required.",
        ]);
    }
    
    // public function test_validation_as_of_date_is_invalid()
    // {
    //     $form_data = $this->get_new_record_form_data();
    //     $row = EligibleEmployeeByBU::create( $form_data );
    //     //
    //     $form_data['as_of_date'] = "2002";

    //     $this->actingAs($this->admin);
    //     $response = $this->json('put', '/settings/eligible-employee-summary/' . $row->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

    //     $response->assertStatus(422);
    //     $response->assertJsonValidationErrors([
    //         'as_of_date' =>  "The as of date is not a valid date.",
    //     ]);
    // }
    public function test_validation_ee_count_is_invalid()
    {
        $form_data = $this->get_new_record_form_data();
        $row = EligibleEmployeeByBU::create( $form_data );
        //
        $form_data['ee_count'] = "iue";

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/eligible-employee-summary/' . $row->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            "ee_count" => "The employee count field must be a number.",
        ]);
    }

    public function test_validation_only_unique_transaction_allow_to_store()
    {

        $form_data = $this->get_new_record_form_data();
        $row = EligibleEmployeeByBU::create( $form_data );

        $this->actingAs($this->admin);
        $response = $this->json('post', '/settings/eligible-employee-summary',  $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            "organization_code" => "The business unit of this organization has already been taken for this campaign year."
        ]);
    }
  
    /* Private Function */
    private function get_new_record_form_data() 
    {

        $business = BusinessUnit::factory()->create([
            'code' => 'BC' . $this->faker->randomNumber(3, true),
            'status' => 'A',
        ]);

        $organization = Organization::factory()->create([
            'code' => "LDB",
            'status' => 'A',
            'bu_code' => $business->code,
        ]);

        $form_data = [
            'campaign_year' => 2023,
            // 'as_of_date' =>  $this->faker->date(),
            'organization_code' => $organization->code,
            'business_unit_code' => $business->code,
            'business_unit_name' => $business->name,
            'ee_count' => $this->faker->randomNumber(4, false),
            'notes' => $this->faker->sentence(),
        ];

        return $form_data;
    }

}
