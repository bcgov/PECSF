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
    public function an_unauthorized_user_cannot_view_campaign_year()
    {
    
        $this->actingAs($this->user);
        // Simulate a GET request to the given URL, check the response, we should have been authorized access
        $response = $this->get('/campaignyears');
        $response->assertStatus(403);

        $response = $this->get('/campaignyears/1');
        $response->assertStatus(403);
    }

    /** @test  */
    public function an_unauthorized_user_cannot_create_the_campaign_year()
    {
        $this->actingAs($this->user);

        $response = $this->get('/campaignyears/create');
        $response->assertStatus(403);

    }

    /** @test  */
    public function an_unauthorized_user_cannot_edit_the_campaign_year()
    {
        $this->actingAs($this->user);

        $response = $this->get('/campaignyears/1/edit');
        $response->assertStatus(403);
    }
   
    /** @test  */
    public function an_administrator_can_view_the_campaign_year()
    {
    
        $this->actingAs($this->admin);
        // Simulate a GET request to the given URL, check the response, we should have been redirected to the homepage
        $response = $this->get('/campaignyears');
        $response->assertStatus(200);

        $response = $this->get('/campaignyears/1');
        $response->assertStatus(200);
    }

    /** @test  */
    public function an_administrator_can_create_campaign_year()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/campaignyears/create');
        $response->assertStatus(200);
    }

    /** @test  */
    public function an_administrator_can_edit_campaign_year()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/campaignyears/1/edit');
        $response->assertStatus(200);
    }

    /** Form Validation */
    public function test_fields_are_required_validation()
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
    public function test_start_date_must_not_be_later_than_end_date_validation()
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
    public function test_only_one_active_calendar_year_allow_validation()
    {

        $cy = CampaignYear::where('Status', 'A')->orderByDesc('calendar_year')->first();

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
    
}
