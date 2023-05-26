<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\CampaignYear;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class EFormTest extends TestCase
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
        $this->json('post', '/bank_deposit_form',[
            'organization_code'         => null,
            'form_submitter'         => $this->user->id,
            'campaign_year'         => 20,
            'event_type'         => 'Cash One-time donation',
            'sub_type'         => null,
            'deposit_date'         => Carbon::now(),
            'deposit_amount'         => 1024,
            'employment_city'         => 'Quebec',
            'postal_code'         => 'L1D 5D9',
            'region'         => 'Cariboo',
            'business_unit'         => '26',
            'charity_selection' => 'fsp',
            'description' => 'None',
            'attachments.*' => UploadedFile::fake()->image('avatar.jpg', 250, 250)->size(100),
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor("organization_code");
    }

    /** @test  */
    public function form_submitter_missing_return_error()
    {
        $this->actingAs($this->user);
        $this->json('post', '/bank_deposit_form',[
            'organization_code'         => null,
            'form_submitter'         => null,
            'campaign_year'         => 20,
            'event_type'         => 'Cash One-time donation',
            'sub_type'         => null,
            'deposit_date'         => Carbon::now(),
            'deposit_amount'         => 1024,
            'employment_city'         => 'Quebec',
            'postal_code'         => 'L1D 5D9',
            'region'         => 'Cariboo',
            'business_unit'         => '26',
            'charity_selection' => 'fsp',
            'description' => 'None',
            'attachments.*' => UploadedFile::fake()->image('avatar.jpg', 250, 250)->size(100),
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor("form_submitter");
    }

    /** @test  */
    public function campaign_year_missing_return_error()
    {
        $this->actingAs($this->user);
        $this->json('post', '/bank_deposit_form',[
            'organization_code'         => null,
            'form_submitter'         => $this->user->id,
            'campaign_year'         => null,
            'event_type'         => 'Cash One-time donation',
            'sub_type'         => null,
            'deposit_date'         => Carbon::now(),
            'deposit_amount'         => 1024,
            'employment_city'         => 'Quebec',
            'postal_code'         => 'L1D 5D9',
            'region'         => 'Cariboo',
            'business_unit'         => '26',
            'charity_selection' => 'fsp',
            'description' => 'None',
            'attachments.*' => UploadedFile::fake()->image('avatar.jpg', 250, 250)->size(100),
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor("campaign_year");
    }

    /** @test  */
    public function event_type_missing_return_error()
    {

        $this->actingAs($this->user);
        $this->json('post', '/bank_deposit_form',[
            'organization_code'         => null,
            'form_submitter'         => $this->user->id,
            'campaign_year'         => 20,
            'event_type'         => null,
            'sub_type'         => null,
            'deposit_date'         => Carbon::now(),
            'deposit_amount'         => 1024,
            'employment_city'         => 'Quebec',
            'postal_code'         => 'L1D 5D9',
            'region'         => 'Cariboo',
            'business_unit'         => '26',
            'charity_selection' => 'fsp',
            'description' => 'None',
            'attachments.*' => UploadedFile::fake()->image('avatar.jpg', 250, 250)->size(100),
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor("event_type");
    }

    /** @test  */
    public function sub_type_missing_return_error()
    {
        $this->actingAs($this->user);
        $this->json('post', '/bank_deposit_form',[
            'organization_code'         => null,
            'form_submitter'         => $this->user->id,
            'campaign_year'         => 20,
            'event_type'         => 'Cash One-time donation',
            'sub_type'         => null,
            'deposit_date'         => Carbon::now(),
            'deposit_amount'         => 1024,
            'employment_city'         => 'Quebec',
            'postal_code'         => 'L1D 5D9',
            'region'         => 'Cariboo',
            'business_unit'         => '26',
            'charity_selection' => 'fsp',
            'description' => 'None',
            'attachments.*' => UploadedFile::fake()->image('avatar.jpg', 250, 250)->size(100),
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor("sub_type");
    }

    /** @test  */
    public function deposit_date_missing_or_after_today_return_error()
    {
        $this->actingAs($this->user);
        $this->json('post', '/bank_deposit_form',[
            'organization_code'         => null,
            'form_submitter'         => $this->user->id,
            'campaign_year'         => 20,
            'event_type'         => 'Cash One-time donation',
            'sub_type'         => null,
            'deposit_date'         => null,
            'deposit_amount'         => 1024,
            'employment_city'         => 'Quebec',
            'postal_code'         => 'L1D 5D9',
            'region'         => 'Cariboo',
            'business_unit'         => '26',
            'charity_selection' => 'fsp',
            'description' => 'None',
            'attachments.*' => UploadedFile::fake()->image('avatar.jpg', 250, 250)->size(100),
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor("organization_code");
    }

    /** Form Validation */
    public function deposit_amount_missing_return_error()
    {
        $this->actingAs($this->user);
        $this->json('post', '/bank_deposit_form',[
            'organization_code'         => null,
            'form_submitter'         => $this->user->id,
            'campaign_year'         => 20,
            'event_type'         => 'Cash One-time donation',
            'sub_type'         => null,
            'deposit_date'         => Carbon::now(),
            'deposit_amount'         => null,
            'employment_city'         => 'Quebec',
            'postal_code'         => 'L1D 5D9',
            'region'         => 'Cariboo',
            'business_unit'         => '26',
            'charity_selection' => 'fsp',
            'description' => 'None',
            'attachments.*' => UploadedFile::fake()->image('avatar.jpg', 250, 250)->size(100),
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor("organization_code");
    }

    /** @test */
    public function employment_city_missing_return_error()
    {
        $this->actingAs($this->user);
        $this->json('post', '/bank_deposit_form',[
            'organization_code'         => null,
            'form_submitter'         => $this->user->id,
            'campaign_year'         => 20,
            'event_type'         => 'Cash One-time donation',
            'sub_type'         => null,
            'deposit_date'         => Carbon::now(),
            'deposit_amount'         => 1024,
            'employment_city'         => null,
            'postal_code'         => 'L1D 5D9',
            'region'         => 'Cariboo',
            'business_unit'         => '26',
            'charity_selection' => 'fsp',
            'description' => 'None',
            'attachments.*' => UploadedFile::fake()->image('avatar.jpg', 250, 250)->size(100),
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor("employment_city");
    }

    /** @test */
    public function fundraiser_or_gaming_no_postal_code_required_or_postal_code_matches_CA_format_exists_or_return_error()
    {

        $this->actingAs($this->user);
        $this->json('post', '/bank_deposit_form',[
            'organization_code'         => null,
            'form_submitter'         => $this->user->id,
            'campaign_year'         => 20,
            'event_type'         => 'Fundraiser',
            'sub_type'         => null,
            'deposit_date'         => Carbon::now(),
            'deposit_amount'         => 1024,
            'employment_city'         => 'Quebec',
            'postal_code'         => 'GGGGGGGG',
            'region'         => 'Cariboo',
            'business_unit'         => '26',
            'charity_selection' => 'fsp',
            'description' => 'None',
            'attachments.*' => UploadedFile::fake()->image('avatar.jpg', 250, 250)->size(100),
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor("postal_code");
    }

    /** @test */
    public function region_missing_return_error()
    {
        $this->actingAs($this->user);
        $this->json('post', '/bank_deposit_form',[
            'organization_code'         => null,
            'form_submitter'         => $this->user->id,
            'campaign_year'         => 20,
            'event_type'         => 'Cash One-time donation',
            'sub_type'         => null,
            'deposit_date'         => Carbon::now(),
            'deposit_amount'         => 1024,
            'employment_city'         => 'Quebec',
            'postal_code'         => 'L1D 5D9',
            'region'         => null,
            'business_unit'         => '26',
            'charity_selection' => 'fsp',
            'description' => 'None',
            'attachments.*' => UploadedFile::fake()->image('avatar.jpg', 250, 250)->size(100),
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor("organization_code");
    }

    /** @test */
    public function business_unit_required_or_return_error()
    {
        $this->actingAs($this->user);
        $this->json('post', '/bank_deposit_form',[
            'organization_code'         => null,
            'form_submitter'         => $this->user->id,
            'campaign_year'         => 20,
            'event_type'         => 'Cash One-time donation',
            'sub_type'         => null,
            'deposit_date'         => Carbon::now(),
            'deposit_amount'         => 1024,
            'employment_city'         => 'Quebec',
            'postal_code'         => 'L1D 5D9',
            'region'         => 'Cariboo',
            'business_unit'         => null,
            'charity_selection' => 'fsp',
            'description' => 'None',
            'attachments.*' => UploadedFile::fake()->image('avatar.jpg', 250, 250)->size(100),
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor("business_unit");
    }

    /** @test */
    public function charity_selection_required_or_return_error()
    {
        $this->actingAs($this->user);
        $this->json('post', '/bank_deposit_form',[
            'organization_code'         => null,
            'form_submitter'         => $this->user->id,
            'campaign_year'         => 20,
            'event_type'         => 'Cash One-time donation',
            'sub_type'         => null,
            'deposit_date'         => Carbon::now(),
            'deposit_amount'         => 1024,
            'employment_city'         => 'Quebec',
            'postal_code'         => 'L1D 5D9',
            'region'         => 'Cariboo',
            'business_unit'         => '26',
            'charity_selection' => null,
            'description' => 'None',
            'attachments.*' => UploadedFile::fake()->image('avatar.jpg', 250, 250)->size(100),
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor("organization_code");
    }


    /** @test */
    public function description_required_or_return_error()
    {

        $this->actingAs($this->user);
        $this->json('post', '/bank_deposit_form',[
            'organization_code'         => null,
            'form_submitter'         => $this->user->id,
            'campaign_year'         => 20,
            'event_type'         => 'Cash One-time donation',
            'sub_type'         => null,
            'deposit_date'         => Carbon::now(),
            'deposit_amount'         => 1024,
            'employment_city'         => 'Quebec',
            'postal_code'         => 'L1D 5D9',
            'region'         => 'Cariboo',
            'business_unit'         => '26',
            'charity_selection' => 'fsp',
            'description' => null,
            'attachments.*' => UploadedFile::fake()->image('avatar.jpg', 250, 250)->size(100),
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor("organization_code");

    }

    /** @test */
    public function organization_code_has_appropriate_error()
    {

        $this->actingAs($this->user);
        $this->json('post', '/bank_deposit_form',[
            'organization_code'         => null,
            'form_submitter'         => $this->user->id,
            'campaign_year'         => 20,
            'event_type'         => 'Cash One-time donation',
            'sub_type'         => null,
            'deposit_date'         => Carbon::now(),
            'deposit_amount'         => 1024,
            'employment_city'         => 'Quebec',
            'postal_code'         => 'L1D 5D9',
            'region'         => 'Cariboo',
            'business_unit'         => '26',
            'charity_selection' => 'fsp',
            'description' => 'None',
            'attachments.*' => UploadedFile::fake()->image('avatar.jpg', 250, 250)->size(100),
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor("organization_code");

    }

    /** @test */
    public function event_type_not_fundraiser_or_gaming_and_organization_code_not_gov_pecsf_id_required_and_numeric_or_return_error()
    {

        $this->actingAs($this->user);
        $this->json('post', '/bank_deposit_form',[
            'organization_code'         => "LA",
            'form_submitter'         => $this->user->id,
            'campaign_year'         => 20,
            'event_type'         => 'Cash One-time donation',
            'sub_type'         => null,
            'deposit_date'         => Carbon::now(),
            'deposit_amount'         => 1024,
            'employment_city'         => 'Quebec',
            'postal_code'         => 'L1D 5D9',
            'region'         => 'Cariboo',
            'business_unit'         => '26',
            'charity_selection' => 'fsp',
            'description' => 'None',
            'attachments.*' => UploadedFile::fake()->image('avatar.jpg', 250, 250)->size(100),
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor("pecsf_id");

    }

    /** @test */
    public function event_type_not_fundraiser_or_gaming_and_organization_code_gov_bc_gov_id_required_and_numeric_or_return_error()
    {

        $this->actingAs($this->user);
        $this->json('post', '/bank_deposit_form',[
            'organization_code'         => "LA",
            'form_submitter'         => $this->user->id,
            'campaign_year'         => 20,
            'event_type'         => 'Cash One-time donation',
            'sub_type'         => null,
            'deposit_date'         => Carbon::now(),
            'deposit_amount'         => 1024,
            'employment_city'         => 'Quebec',
            'postal_code'         => 'L1D 5D9',
            'region'         => 'Cariboo',
            'business_unit'         => '26',
            'charity_selection' => 'fsp',
            'description' => 'None',
            'attachments.*' => UploadedFile::fake()->image('avatar.jpg', 250, 250)->size(100),
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor("bc_gov_id");

    }

    /** @test */
    public function event_type_cash_one_time_donation_or_cheque_one_time_donation_address_1_required_or_return_error()
    {

        $this->actingAs($this->user);
        $this->json('post', '/bank_deposit_form',[
            'organization_code'         => null,
            'form_submitter'         => $this->user->id,
            'campaign_year'         => 20,
            'event_type'         => 'Cash One-time donation',
            'sub_type'         => null,
            'deposit_date'         => Carbon::now(),
            'deposit_amount'         => 1024,
            'employment_city'         => 'Quebec',
            'postal_code'         => 'L1D 5D9',
            'region'         => 'Cariboo',
            'business_unit'         => '26',
            'charity_selection' => 'fsp',
            'description' => 'None',
            'attachments.*' => UploadedFile::fake()->image('avatar.jpg', 250, 250)->size(100),
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor("address_1");

    }

    /** @test */
    public function event_type_cash_one_time_donation_or_cheque_one_time_donation_city_required_or_return_error()
    {

        $this->actingAs($this->user);
        $this->json('post', '/bank_deposit_form',[
            'organization_code'         => null,
            'form_submitter'         => $this->user->id,
            'campaign_year'         => 20,
            'event_type'         => 'Cash One-time donation',
            'sub_type'         => null,
            'deposit_date'         => Carbon::now(),
            'deposit_amount'         => 1024,
            'employment_city'         => 'Quebec',
            'postal_code'         => 'L1D 5D9',
            'region'         => 'Cariboo',
            'business_unit'         => '26',
            'charity_selection' => 'fsp',
            'description' => 'None',
            'attachments.*' => UploadedFile::fake()->image('avatar.jpg', 250, 250)->size(100),
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor("city");

    }

    /** @test */
    public function event_type_cash_one_time_donation_or_cheque_one_time_donation_province_required_or_return_error()
    {

        $this->actingAs($this->user);
        $this->json('post', '/bank_deposit_form',[
            'organization_code'         => null,
            'form_submitter'         => $this->user->id,
            'campaign_year'         => 20,
            'event_type'         => 'Cash One-time donation',
            'sub_type'         => null,
            'deposit_date'         => Carbon::now(),
            'deposit_amount'         => 1024,
            'employment_city'         => 'Quebec',
            'postal_code'         => 'L1D 5D9',
            'region'         => 'Cariboo',
            'business_unit'         => '26',
            'charity_selection' => 'fsp',
            'description' => 'None',
            'attachments.*' => UploadedFile::fake()->image('avatar.jpg', 250, 250)->size(100),
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor("province");

    }

    /** @test */
    public function event_type_cash_one_time_donation_or_cheque_one_time_donation_postal_code_required_or_return_error()
    {

        $this->actingAs($this->user);
        $this->json('post', '/bank_deposit_form',[
            'organization_code'         => null,
            'form_submitter'         => $this->user->id,
            'campaign_year'         => 20,
            'event_type'         => 'Cash One-time donation',
            'sub_type'         => null,
            'deposit_date'         => Carbon::now(),
            'deposit_amount'         => 1024,
            'employment_city'         => 'Quebec',
            'postal_code'         => 'L1D 5D9',
            'region'         => 'Cariboo',
            'business_unit'         => '26',
            'charity_selection' => 'fsp',
            'description' => 'None',
            'attachments.*' => UploadedFile::fake()->image('avatar.jpg', 250, 250)->size(100),
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor("postal_code");
    }

    /** @test */
    public function charity_selection_fsp_regional_pool_id_required_or_return_error()
    {

        $this->actingAs($this->user);
        $this->json('post', '/bank_deposit_form',[
            'organization_code'         => null,
            'form_submitter'         => $this->user->id,
            'campaign_year'         => 20,
            'event_type'         => 'Cash One-time donation',
            'sub_type'         => null,
            'deposit_date'         => Carbon::now(),
            'deposit_amount'         => 1024,
            'employment_city'         => 'Quebec',
            'postal_code'         => 'L1D 5D9',
            'region'         => 'Cariboo',
            'business_unit'         => '26',
            'charity_selection' => 'fsp',
            'description' => 'None',
            'attachments.*' => UploadedFile::fake()->image('avatar.jpg', 250, 250)->size(100),
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor("regional_pool_id");

    }

    /** @test */
    public function charity_selection_charity_org_count_greater_than_one_or_return_error()
    {

        $this->actingAs($this->user);
        $this->json('post', '/bank_deposit_form',[
            'organization_code'         => null,
            'form_submitter'         => $this->user->id,
            'campaign_year'         => 20,
            'event_type'         => 'Cash One-time donation',
            'sub_type'         => null,
            'deposit_date'         => Carbon::now(),
            'deposit_amount'         => 1024,
            'employment_city'         => 'Quebec',
            'postal_code'         => 'L1D 5D9',
            'region'         => 'Cariboo',
            'business_unit'         => '26',
            'charity_selection' => 'charity',
            'description' => 'None',
            'attachments.*' => UploadedFile::fake()->image('avatar.jpg', 250, 250)->size(100),
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor("org_count");

    }

    /** @test */
    public function charity_selection_charity_all_orgs_have_name_or_return_error()
    {

        $this->actingAs($this->user);
        $this->json('post', '/bank_deposit_form',[
            'organization_code'         => null,
            'form_submitter'         => $this->user->id,
            'campaign_year'         => 20,
            'event_type'         => 'Cash One-time donation',
            'sub_type'         => null,
            'deposit_date'         => Carbon::now(),
            'deposit_amount'         => 1024,
            'employment_city'         => 'Quebec',
            'postal_code'         => 'L1D 5D9',
            'region'         => 'Cariboo',
            'business_unit'         => '26',
            'charity_selection' => 'fsp',
            'description' => 'None',
            'attachments.*' => UploadedFile::fake()->image('avatar.jpg', 250, 250)->size(100),
            'orgs' => ["id" => 1]
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor("orgs"); $this->actingAs($this->admin);
        $response = $this->get('/campaignyears/create');
        $response->assertSee('-12-31');

    }

    /** @test */
    public function charity_selection_charity_all_orgs_have_vendor_id_or_return_error()
    {

        $this->actingAs($this->user);
        $this->json('post', '/bank_deposit_form',[
            'organization_code'         => null,
            'form_submitter'         => $this->user->id,
            'campaign_year'         => 20,
            'event_type'         => 'Cash One-time donation',
            'sub_type'         => null,
            'deposit_date'         => Carbon::now(),
            'deposit_amount'         => 1024,
            'employment_city'         => 'Quebec',
            'postal_code'         => 'L1D 5D9',
            'region'         => 'Cariboo',
            'business_unit'         => '26',
            'charity_selection' => 'fsp',
            'description' => 'None',
            'attachments.*' => UploadedFile::fake()->image('avatar.jpg', 250, 250)->size(100),
            'orgs' => ["id" => 1]
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor("vendor_id"); $this->actingAs($this->admin);
        $response = $this->get('/campaignyears/create');
        $response->assertSee('-12-31');


    }

    /** @test */
    public function charity_selection_charity_all_orgs_have_numeric_donation_percent_or_return_error()
    {

        $this->actingAs($this->user);
        $this->json('post', '/bank_deposit_form',[
            'organization_code'         => null,
            'form_submitter'         => $this->user->id,
            'campaign_year'         => 20,
            'event_type'         => 'Cash One-time donation',
            'sub_type'         => null,
            'deposit_date'         => Carbon::now(),
            'deposit_amount'         => 1024,
            'employment_city'         => 'Quebec',
            'postal_code'         => 'L1D 5D9',
            'region'         => 'Cariboo',
            'business_unit'         => '26',
            'charity_selection' => 'fsp',
            'description' => 'None',
            'attachments.*' => UploadedFile::fake()->image('avatar.jpg', 250, 250)->size(100),
            'orgs' => ["id" => 1]
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor("donation_percent"); $this->actingAs($this->admin);
        $response = $this->get('/campaignyears/create');
        $response->assertSee('-12-31');


    }


    /** @test */
    public function charity_selection_charity_all_orgs_total_donation_percent_equals_100_or_return_error()
    {

        $this->actingAs($this->user);
        $this->json('post', '/bank_deposit_form',[
            'organization_code'         => null,
            'form_submitter'         => $this->user->id,
            'campaign_year'         => 20,
            'event_type'         => 'Cash One-time donation',
            'sub_type'         => null,
            'deposit_date'         => Carbon::now(),
            'deposit_amount'         => 1024,
            'employment_city'         => 'Quebec',
            'postal_code'         => 'L1D 5D9',
            'region'         => 'Cariboo',
            'business_unit'         => '26',
            'charity_selection' => 'fsp',
            'description' => 'None',
            'attachments.*' => UploadedFile::fake()->image('avatar.jpg', 250, 250)->size(100),
            'orgs' => ["id" => 1]
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor("donation_percent"); $this->actingAs($this->admin);
        $response = $this->get('/campaignyears/create');
        $response->assertSee('-12-31');


    }

    /** @test */
    public function successful_gov_form_submits()
    {
        return true;
    }


    /** @test */
    public function successful_non_gov_form_submits()
    {
        return true;
    }

}
