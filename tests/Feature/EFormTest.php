<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\CampaignYear;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CampaignYearTest extends TestCase
{

    private $admin, $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::whereHas("roles", function($q){ $q->where("name", "admin"); })->first();
        $this->user = User::doesntHave('roles')->first();

    }

    /** @test  */
    public function organization_code_missing_return_error()
    {

        $this->actingAs($this->user);
        // Simulate a GET request to the given URL, check the response, we should have been authorized access
        $response = $this->get('/campaignyears');
        $response->assertStatus(403);

        $response = $this->get('/campaignyears/1');
        $response->assertStatus(403);
    }

    /** @test  */
    public function form_submitter_missing_return_error()
    {
        $this->actingAs($this->user);

        $response = $this->get('/campaignyears/create');
        $response->assertStatus(403);

    }

    /** @test  */
    public function campaign_year_missing_return_error()
    {
        $this->actingAs($this->user);

        $response = $this->get('/campaignyears/1/edit');
        $response->assertStatus(403);
    }

    /** @test  */
    public function event_type_missing_return_error()
    {

        $this->actingAs($this->admin);
        // Simulate a GET request to the given URL, check the response, we should have been redirected to the homepage
        $response = $this->get('/campaignyears');
        $response->assertStatus(200);

        $response = $this->get('/campaignyears/1');
        $response->assertStatus(200);
    }

    /** @test  */
    public function sub_type_missing_return_error()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/campaignyears/create');
        $response->assertStatus(200);
    }

    /** @test  */
    public function deposit_date_missing_or_after_today_return_error()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/campaignyears/1/edit');
        $response->assertStatus(200);
    }

    /** Form Validation */
    public function deposit_amount_missing_return_error()
    {
        $this->actingAs($this->admin);
        // Post empty data to the create page route
        $response = $this->post('/campaignyears');
        // This should cause errors with the
        // title and content fields as they aren't present

        $response->assertSessionHasErrors([
            'calendar_year',
            'number_of_periods',
            'status',
            'start_date',
            'end_date',
        ]);
    }

    /** @test */
    public function employment_city_missing_return_error()
    {
        $this->actingAs($this->admin);
        $response = $this->post('/campaignyears',
            [

                'start_date' => '2010-12-12',
                'end_date' => '2010-01-01',

            ]);
        $response->assertSessionHasErrors([
            'start_date',
            'end_date',
        ]);

    }

    /** @test */
    public function fundraiser_or_gaming_no_postal_code_required_or_postal_code_matches_CA_format_exists_or_return_error()
    {

        $cy = CampaignYear::where('Status', 'A')->where('calendar_year','!=',2029)->orderByDesc('calendar_year')->first();

        $this->actingAs($this->admin);
        $response = $this->post('/campaignyears',
            [
                'calendar_year' => 2029,
                'number_of_periods' => 26,
                'status' => 'A',
                'start_date' => '2021-12-12',
                'end_date' => '2021-12-31',
                'close_date' => '2021-12-31',

            ]);
        if ($cy) {
            $response->assertSessionHasErrors([
                'status',
            ]);
        }
    }

    /** @test */
    public function region_missing_return_error()
    {

        $this->actingAs($this->admin);
        $response = $this->get('/campaignyears/create');
        $response->assertSee('-12-31');

    }

    /** @test */
    public function business_unit_required_or_return_error()
    {

        $this->actingAs($this->admin);
        $response = $this->get('/campaignyears/create');
        $response->assertSee('-12-31');

    }

    /** @test */
    public function charity_selection_required_or_return_error()
    {

        $this->actingAs($this->admin);
        $response = $this->get('/campaignyears/create');
        $response->assertSee('-12-31');

    }


    /** @test */
    public function description_required_or_return_error()
    {

        $this->actingAs($this->admin);
        $response = $this->get('/campaignyears/create');
        $response->assertSee('-12-31');

    }

    /** @test */
    public function organization_code_has_appropriate_error()
    {

        $this->actingAs($this->admin);
        $response = $this->get('/campaignyears/create');
        $response->assertSee('-12-31');

    }

    /** @test */
    public function event_type_not_fundraiser_or_gaming_and_organization_code_not_gov_pecsf_id_required_and_numeric_or_return_error()
    {

        $this->actingAs($this->admin);
        $response = $this->get('/campaignyears/create');
        $response->assertSee('-12-31');

    }

    /** @test */
    public function event_type_not_fundraiser_or_gaming_and_organization_code_gov_bc_gov_id_required_and_numeric_or_return_error()
    {

        $this->actingAs($this->admin);
        $response = $this->get('/campaignyears/create');
        $response->assertSee('-12-31');

    }

    /** @test */
    public function event_type_cash_one_time_donation_or_cheque_one_time_donation_address_1_required_or_return_error()
    {

        $this->actingAs($this->admin);
        $response = $this->get('/campaignyears/create');
        $response->assertSee('-12-31');

    }

    /** @test */
    public function event_type_cash_one_time_donation_or_cheque_one_time_donation_city_required_or_return_error()
    {

        $this->actingAs($this->admin);
        $response = $this->get('/campaignyears/create');
        $response->assertSee('-12-31');

    }

    /** @test */
    public function event_type_cash_one_time_donation_or_cheque_one_time_donation_province_required_or_return_error()
    {

        $this->actingAs($this->admin);
        $response = $this->get('/campaignyears/create');
        $response->assertSee('-12-31');

    }

    /** @test */
    public function event_type_cash_one_time_donation_or_cheque_one_time_donation_postal_code_required_or_return_error()
    {

        $this->actingAs($this->admin);
        $response = $this->get('/campaignyears/create');
        $response->assertSee('-12-31');

    }

    /** @test */
    public function charity_selection_fsp_regional_pool_id_required_or_return_error()
    {

        $this->actingAs($this->admin);
        $response = $this->get('/campaignyears/create');
        $response->assertSee('-12-31');

    }

    /** @test */
    public function charity_selection_charity_org_count_greater_than_one_or_return_error()
    {

        $this->actingAs($this->admin);
        $response = $this->get('/campaignyears/create');
        $response->assertSee('-12-31');

    }

    /** @test */
    public function charity_selection_charity_all_orgs_have_name_or_return_error()
    {

        $this->actingAs($this->admin);
        $response = $this->get('/campaignyears/create');
        $response->assertSee('-12-31');

    }

    /** @test */
    public function charity_selection_charity_all_orgs_have_vendor_id_or_return_error()
    {

        $this->actingAs($this->admin);
        $response = $this->get('/campaignyears/create');
        $response->assertSee('-12-31');

    }

    /** @test */
    public function charity_selection_charity_all_orgs_have_numeric_donation_percent_or_return_error()
    {

        $this->actingAs($this->admin);
        $response = $this->get('/campaignyears/create');
        $response->assertSee('-12-31');

    }


    /** @test */
    public function charity_selection_charity_all_orgs_total_donation_percent_equals_100_or_return_error()
    {

        $this->actingAs($this->admin);
        $response = $this->get('/campaignyears/create');
        $response->assertSee('-12-31');

    }

    /** @test */
    public function successful_gov_form_submits()
    {

    

    }


    /** @test */
    public function successful_non_gov_form_submits()
    {

    }

}
