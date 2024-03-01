<?php

namespace Tests\Feature;

use File;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\City;
use App\Models\User;
use App\Models\FSPool;
use App\Models\Pledge;
use App\Models\Region;
use App\Models\Charity;
use App\Models\EmployeeJob;
use Illuminate\Support\Arr;
use App\Models\BusinessUnit;
use App\Models\CampaignYear;
use App\Models\Organization;
use App\Models\FSPoolCharity;
use App\Models\PledgeCharity;
use App\Models\PledgeHistory;
use App\Models\BankDepositForm;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Models\PledgeHistorySummary;
use Illuminate\Support\Facades\Storage;
use App\Models\BankDepositFormAttachments;
use App\Models\BankDepositFormOrganizations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;

class BankDepositFormTest extends TestCase
{
    use WithFaker;

    // protected static $initialized = FALSE;

    private User $admin;
    private User $user;

    protected static $db_inited = false;

       public function setUp(): void
    {
        parent::setUp();

        // Run the DatabaseSeeder...
        // $this->seed();
        // $this->seed(\Database\Seeders\RolePermissionTableSeeder::class);
        // $this->seed(\Database\Seeders\CampaignYearSeeder::class);
        // $this->seed(\Database\Seeders\UserTableSeeder::class);
        // $this->admin = User::updateOrCreate([
        //   'email' => 'supervisor@example.com',
        // ],[
        //   'name' => 'Supervisor',
        //   'password' => bcrypt('supervisor@123'),
        //   'emplid' => 'sss130347'
        // ]);
        // $this->admin->assignRole('admin');
        // $this->user = User::updateOrCreate([
        //   'email' => 'employee1@example.com',
        // ],[
        //   'name' => 'Employee A',
        //   'password' => bcrypt('employee1@123'),
        //   'emplid' => 'sss130347'
        // ]);

        // if (!self::$initialized) {
        //     // Do something once here for _all_ test subclasses.
        //     CampaignYear::truncate();

        //     self::$initialized = TRUE;
        // }

        if (!static::$db_inited) {
            static::$db_inited = true;
            // Dependence
            // $this->artisan('migrate:fresh');
            // $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);
            $this->artisan('db:seed', ['--class' => 'RolePermissionTableSeeder']);
            $this->artisan('db:seed', ['--class' => 'UserTableSeeder']);

            $this->artisan('db:seed', ['--class' => 'PayCalendarSeeder']);
        }

        $this->admin = User::whereHas("roles", function ($q) {$q->where("name", "admin");})->first();
        $this->user  = User::doesntHave('roles')->first();

        $this->artisan('db:seed', ['--class' => 'PayCalendarSeeder']);

        CampaignYear::truncate();
        Region::truncate();
        BusinessUnit::truncate();
        Organization::truncate();
        City::truncate();
        Charity::truncate();
        FSPool::truncate();
        FSPoolCharity::truncate();
        EmployeeJob::truncate();

        BankDepositForm::truncate();
        BankDepositFormOrganizations::truncate();
        BankDepositFormAttachments::truncate();

    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_bank_deposit_form_pledge_index_page()
    {
        $response = $this->get('/bank_deposit_form');

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }
    public function test_an_anonymous_user_cannot_access_the_bank_deposit_form_pledge_create_page()
    {
        $response = $this->get('/bank_deposit_form/create');

        $response->assertStatus(404);
        
    }
    public function test_an_anonymous_user_cannot_create_the_bank_deposit_form_pledge()
    {
        $response = $this->post('/bank_deposit_form', []);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_bank_deposit_form_pledge_view_page()
    {
        $response = $this->get('/bank_deposit_form/1');

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_bank_deposit_form_pledge_edit_page()
    {
        $response = $this->get('/bank_deposit_form/1/edit');

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_update_the_bank_deposit_form_pledge()
    {

        $response = $this->post('/bank_deposit_form/update', [] );

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function test_an_anonymous_user_cannot_delete_the_bank_deposit_form_pledge()
    {
        $response = $this->delete('/bank_deposit_form/1');

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }

    public function test_an_anonymous_user_cannot_search_bc_gov_id_via_ajax_call()
    {
        // $this->actingAs($this->admin);
        $response = $this->call('get', '/bank_deposit_form/bc_gov_id',[]);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
  
    public function test_an_anonymous_user_cannot_search_business_unit_via_ajax_call()
    {
        
        $response = $this->call('get', '/bank_deposit_form/business_unit',[]);

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }

    public function test_an_anonymous_user_cannot_download_file_via_ajax_call()
    {
        
        $response = $this->call('get', '/bank_deposit_form/download/1',[]);

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }

    public function test_an_anonymous_user_cannot_upload_media_via_ajax_call()
    {
        
        $response = $this->call('post', '/bank_deposit_form/media',[]);

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }

    public function test_an_anonymous_user_cannot_search_organization_code_via_ajax_call()
    {
        
        $response = $this->call('get', '/bank_deposit_form/organization_code',[]);

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }

    public function test_an_anonymous_user_cannot_search_organization_name_via_ajax_call()
    {
        
        $response = $this->call('get', '/bank_deposit_form/organization_name',[]);

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }

    public function test_an_anonymous_user_cannot_search_organizations_via_ajax_call()
    {
        
        $response = $this->call('get', '/bank_deposit_form/organizations',[]);

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }


    //
    // Test Authorized User
    //
    public function test_an_authorized_user_cannot_access_the_bank_deposit_form_pledge_create_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/bank_deposit_form/create');

        $response->assertStatus(404);
        
    }

    public function test_an_authorized_user_can_access_bank_deposit_form_pledge_index_page()
    {
        // [$form_data, $pledge] = $this->get_new_record_form_data(false);
        $campaign_year = CampaignYear::create([
            'calendar_year' => today()->year + 1,
            'status' => 'A',
            'start_date' => today(),
            'end_date' => today()->year . '-12-31',
            'number_of_periods' => 26,
            'close_date' => today()->year . '-12-31',
        ]);

        $this->actingAs($this->user);
        $response = $this->get('/bank_deposit_form');

        $response->assertStatus(200);
        $response->assertSeeText("Event bank deposit form");

    }

    public function test_an_authorized_user_can_create_bank_deposit_form_pledge_successful_in_db()
    {

        [$form_data, $pledge] = $this->get_new_record_form_data(false);
        
        $this->actingAs($this->user)->from('bank_deposit_form');
        $response = $this->json('post', '/bank_deposit_form', $form_data, 
                                    ['HTTP_X-Requested-With' => 'XMLHttpRequest',
                                    ]);

        $response->assertStatus(200);
        // $response->assertRedirect( '/bank_deposit_form');
        // $response->assertSessionHas('success');
        // $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('bank_deposit_forms', Arr::except( $pledge->attributesToArray(), []) );

        foreach ($pledge->charities as $charity) {
            $this->assertDatabaseHas('bank_deposit_form_organizations', Arr::except($charity->attributesToArray(),[] ));
        }
        foreach ($pledge->attachments as $attachment) {
            $this->assertDatabaseHas('bank_deposit_form_attachments', Arr::except($attachment->attributesToArray(),['local_path'] ));

            $file_path = Storage::path('tmp/') . $attachment->filename;
            $this->assertTrue( File::exists( $file_path) );
        }

        $this->delete_file_in_temp_folder($pledge);
     }

    public function test_an_authorized_user_cannot_access_the_bank_deposit_form_pledge_view_page()
    {

        $this->actingAs($this->user);
        $response = $this->get('/bank_deposit_form/1');

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }
    public function test_an_authorized_user_cannot_access_the_bank_deposit_form_pledge_edit_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/bank_deposit_form/1/edit');

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }

// jp

    public function test_an_authorized_user_cannot_update_the_bank_deposit_form_pledge()
    {

        $response = $this->post('/bank_deposit_form/update', [] );

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function test_an_authorized_user_cannot_delete_the_bank_deposit_form_pledge()
    {
        $this->actingAs($this->user);
        $response = $this->get('/bank_deposit_form/1/detele/filename');

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }

    public function test_an_authorized_user_can_search_bc_gov_id_via_ajax_call()
    { 
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $job = EmployeeJob::where('emplid', $pledge->bc_gov_id  )->first();

        $this->actingAs($this->user);
        $response = $this->json('get', '/bank_deposit_form/bc_gov_id', [ 'id' => $pledge->bc_gov_id ], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment([ 
                'first_name' => $job->first_name, 
                'last_name' => $job->last_name, 
         ]);

        $this->delete_file_in_temp_folder($pledge);

    }
  
    public function test_an_authorized_user_can_search_business_unit_via_ajax_call()
    {
        $business = BusinessUnit::factory()->create();
        $organization = Organization::factory()->create([
                'code' => "GOV",
                'bu_code' => $business->code,
        ]);

        $this->actingAs($this->user);
        $response = $this->json('get', '/bank_deposit_form/business_unit', [ 'id' => $organization->code ], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment([ 
            'bu_code' => $organization->bu_code, 
        ]);

    }

    public function test_an_authorized_user_can_download_file_via_ajax_call()
    {
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $this->actingAs($this->user);
        $response = $this->call('get', '/bank_deposit_form/download/1',[]);

        $response->assertStatus(200);

        $this->delete_file_in_temp_folder($pledge);

    }

    public function test_an_authorized_user_can_upload_media_via_ajax_call()
    {

        $file = UploadedFile::fake()->image('document.jpg');        
        
        $this->actingAs($this->user);
        $response = $this->json('post', '/bank_deposit_form/media', [ 'file' => $file ], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $filename = $response->decodeResponseJson()['name'];
        $file_path = Storage::path('tmp/') . $filename;
        $this->assertTrue( File::exists( $file_path) );

        // clean up 
        File::delete( $file_path );
        
    }

    public function test_an_authorized_user_can_search_organization_code_via_ajax_call()
    {
        $this->actingAs($this->user);
        $response = $this->call('get', '/bank_deposit_form/organization_code',[]);

        $response->assertStatus(200);

    }

    public function test_an_authorized_user_can_search_organization_name_via_ajax_call()
    {
        
        $this->actingAs($this->user);
        $response = $this->call('get', '/bank_deposit_form/organization_name',[]);

        $response->assertStatus(200);

    }

    public function test_an_authorized_user_can_search_organizations_via_ajax_call()
    {
        $this->actingAs($this->user);
        $response = $this->call('get', '/bank_deposit_form/organizations',[]);

        $response->assertStatus(200);

    }


    public function test_an_authorized_user_can_get_specific_fund_support_pool_detail_via_ajax_call()
    {
        // Seed data for pagination testing
        // $this->seed(\Database\Seeders\CampaignYearSeeder::class);
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $region = Region::factory()->create();
        $charities = Charity::factory(10)->create([
            'charity_status' => 'Registered',
        ]);
        $fspool = FSPool::factory()->create([
            'region_id' => $region->id,
            'status' => 'A',
        ]);
        $fspool_charities = FSPoolCharity::factory(1)->create([
                'f_s_pool_id' => $fspool->id,
                'charity_id' => $this->faker->randomElement( $charities->pluck('id')->toArray() ),
                'percentage' => 100,
                'status' => 'A',
        ]);

        $this->actingAs($this->user);
        $response = $this->call('get','/donate-now/regional-pool-detail/' . $fspool->id, [], [], [],
            [
                'HTTP_X-Requested-With'=> 'XMLHttpRequest',
                'Accept' => 'text/html',
            ]);

        $response->assertStatus(200);
        foreach ($fspool->charities as $pool_charity) {
            $response->assertSeeText( $pool_charity->charity->charity_name );
        }

        $this->delete_file_in_temp_folder($pledge);
    }


    public function test_an_authorized_user_can_search_charity_via_ajax_call()
    {
        // Seed data for pagination testing
        // $this->seed(\Database\Seeders\CampaignYearSeeder::class);
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $charities = Charity::factory(50)->create([
            'charity_status' => 'Registered',
        ]);

        $this->actingAs($this->user);
        $response = $this->call('get', '/bank_deposit_form/organizations', [
            'page' => 1,
            'category' => '',
            'province' => '',
            'keyword' => $charities[0]->charity_name,
            'pool_filter' => '', 
            'selected_vendors' => '',
        ]);

        $response->assertStatus(200);
        $response->assertSeeText( "1 results" );
        $response->assertSeeText( $charities[0]->charity_name );

        $this->delete_file_in_temp_folder($pledge);
    }

    // /** Form Validation */
    // public function test_validation_rule_fields_are_required_when_create()
    // {
    //     // create the founcdation data 
    //     [$form_data, $pledge] = $this->get_new_record_form_data(false, false);

    //     $form_data =  [
    //         "step" => "3",
    //         "campaign_year_id" => $pledge->campaign_year_id,
    //         "organization_id" => $pledge->organization_id,
    //         ];

    //     $this->actingAs($this->admin);
    //     // Post empty data to the create page route
    //     // $response = $this->post('/bank_deposit_form', $form_data);
    //     $this->actingAs($this->admin);
    //     $response = $this->postJson('/bank_deposit_form', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
    //     // This should cause errors with the
    //     // title and content fields as they aren't present
    //     $response->assertStatus(422);
    //     $response->assertJsonValidationErrors([
    //         'pay_period_amount_other', 
    //         'one_time_amount_other',
    //         'pool_option',
    //         'pay_period_amount_error',
    //         'one_time_amount_error',
    //     ]);
    // }
// //     // public function test_validation_rule_fields_are_required_when_edit()
// //     // {
// //     //     // create the founcdation data 
// //     //     [$form_data, $pledge] = $this->get_new_record_form_data(true, true);

// //     //     $form_data =  [
// //     //         "step" => "3",
// //     //         "campaign_year_id" => $pledge->campaign_year_id,
// //     //         "organization_id" => $pledge->organization_id,
// //     //         ];


// //     //     $this->actingAs($this->admin);
// //     //     $response = $this->json('put', '/bank_deposit_form/' . $pledge->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        
// //     //     // This should cause errors with the
// //     //     // title and content fields as they aren't present
// //     //     $response->assertStatus(422);
// //     //     $response->assertJsonValidationErrors([
// //     //         'pay_period_amount_other', 
// //     //         'one_time_amount_other',
// //     //         'pool_option',
// //     //         'pay_period_amount_error',
// //     //         'one_time_amount_error',
// //     //     ]);

// //     // }


//     public function test_validation_rule_fields_are_required_on_step_1_when_create_or_edit()
//     {
//         // create the founcdation data 
//         [$form_data, $pledge] = $this->get_new_record_form_data(true);

//         $form_data = [
//             "step" => "1",
//         ];
       
//         $this->actingAs($this->user);

//             $response = $this->postJson('/bank_deposit_form', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

//         $response->assertStatus(422);
//         $response->assertJsonValidationErrors([
//             'pool_option', 
//             'number_of_periods',
//         ]);

//     }

//     public function test_validation_rule_fields_are_required_on_step_2a_when_create_or_edit()
//     {
//         // create the founcdation data 
//         [$form_data, $pledge] = $this->get_new_record_form_data(true);

//         $form_data = [
//             "step" => "2",
//             "pool_option" => "P",
//             'number_of_periods' => 26,
//         ];
      
//         $this->actingAs($this->user);
//         $response = $this->postJson('/bank_deposit_form', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

//         $response->assertStatus(422);
//         $response->assertJsonValidationErrors([
//             'regional_pool_id',
//         ]);

//     }

//     public function test_validation_rule_fields_are_required_on_step_2b_when_create_or_edit()
//     {
//         // create the founcdation data 
//         [$form_data, $pledge] = $this->get_new_record_form_data(true);

//         $form_data = [
//             "step" => "2",
//             "pool_option" => "C",
//             'number_of_periods' => 26,
//         ];
      
//         $this->actingAs($this->user);
//         $response = $this->postJson('/bank_deposit_form', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

//         $response->assertStatus(422);
//         $response->assertJsonValidationErrors([
//             'charities',
//         ]);

//     }

//     public function test_validation_rule_fields_are_required_on_step_3a_when_create_or_edit()
//     {
//         // create the founcdation data 
//         [$form_data, $pledge] = $this->get_new_record_form_data(true);

//         $form_data = [
//             "step" => "3",
//             "pool_option" => "P",
//             'number_of_periods' => 26,
//             'frequency' => 'both',
//         ];
       
//         $this->actingAs($this->user);
//         $response = $this->postJson('/bank_deposit_form', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

//         $response->assertStatus(422);
//         $response->assertJsonValidationErrors([
//             'regional_pool_id', 
//             'one_time_amount_custom',
//             'bi_weekly_amount_custom',
//         ]);

//     }

//     public function test_validation_rule_fields_are_required_on_step_3b_when_create_or_edit()
//     {
//         // create the founcdation data 
//         [$form_data, $pledge] = $this->get_new_record_form_data(true);

//         $form_data = [
//             "step" => "3",
//             "pool_option" => "C",
//             'number_of_periods' => 26,
//             'frequency' => 'both',
//             'one_time_amount' => '',
//             'bi_weekly_amount' => '',
//         ];
       
//         $this->actingAs($this->user);
//         $response = $this->postJson('/bank_deposit_form', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

//         $response->assertStatus(422);
//         $response->assertJsonValidationErrors([
//             'charities', 
//             'one_time_amount_custom',
//             'bi_weekly_amount_custom',
//         ]);

//     }

    public function test_validation_rule_fields_are_required_when_create()
    {
        // create the founcdation data 
        $campaign_year = CampaignYear::create([
            'calendar_year' => today()->year + 1,
            'status' => 'A',
            'start_date' => today(),
            'end_date' => today()->year . '-12-31',
            'number_of_periods' => 26,
            'close_date' => today()->year . '-12-31',
        ]);

        $form_data = [];
       
        $this->actingAs($this->user);
        $response = $this->postJson('/bank_deposit_form', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'organization_code',
            'form_submitter',
            'campaign_year',
            'event_type',
            'deposit_date',
            'deposit_amount',
            'employment_city',
            'region',
            'business_unit',
            'address_1',
            'city',
            'province',
            'postal_code',
            "charity_selection",
            "description",
            "attachments",
            "employee_name",
        ]);

    }

    public function test_validation_rule_invalid_deposit_date_when_create()
    {
        // create the founcdation data 
        $campaign_year = CampaignYear::create([
            'calendar_year' => today()->year + 1,
            'status' => 'A',
            'start_date' => today(),
            'end_date' => today()->year . '-12-31',
            'number_of_periods' => 26,
            'close_date' => today()->year . '-12-31',
        ]);

        $form_data['deposit_date'] = today()->addDays(2);
       
        $this->actingAs($this->user);
        $response = $this->postJson('/bank_deposit_form', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'deposit_date',
        ]);

    }

    public function test_validation_rule_invalid_deposit_amount_when_create()
    {
        // create the founcdation data 
        $campaign_year = CampaignYear::create([
            'calendar_year' => today()->year + 1,
            'status' => 'A',
            'start_date' => today(),
            'end_date' => today()->year . '-12-31',
            'number_of_periods' => 26,
            'close_date' => today()->year . '-12-31',
        ]);

        $form_data['deposit_amount'] = 99999999;
       
        $this->actingAs($this->user);
        $response = $this->postJson('/bank_deposit_form', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'deposit_amount' => "The deposit amount must be between 0.01 and 999999.99.",
        ]);

    }

    public function test_validation_rule_employee_info_required_if_cash_or_check_when_create()
    {
        // create the founcdation data 
        $organization = Organization::factory()->create([
            'code' => "GOV",
        ]);
        $campaign_year = CampaignYear::factory()->create();

        $form_data['organization_code'] = 'GOV';
        $form_data['event_type'] = 'Cash One-Time Donation';
       
        $this->actingAs($this->user);
        $response = $this->postJson('/bank_deposit_form', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'bc_gov_id' => "An Employee ID is required.",
            'employee_name' => "Employee name required.",
        ]);

    }

    public function test_validation_rule_mailing_address_required_if_cash_or_check_when_create()
    {
        // create the founcdation data 
        $organization = Organization::factory()->create([
            'code' => "GOV",
        ]);
        $campaign_year = CampaignYear::factory()->create();

        $form_data['organization_code'] = 'GOV';
        $form_data['event_type'] = 'Cheque One-Time Donation';
       
        $this->actingAs($this->user);
        $response = $this->postJson('/bank_deposit_form', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            "address_1" =>"The address line 1 field is required unless event type is in Fundraiser, Gaming.",
            "city" => "The city field is required unless event type is in Fundraiser, Gaming.",
            "province" => "The province field is required unless event type is in Fundraiser, Gaming.",
            "postal_code" => "The postal code field is required unless event type is in Fundraiser, Gaming.",
        ]);

    }

    public function test_validation_rule_invalid_employee_id_when_create()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, true);


        $form_data["bc_gov_id"] = 99999991;

        $this->actingAs($this->user);
        $response = $this->postJson('/bank_deposit_form', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $this->delete_file_in_temp_folder($pledge);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'bc_gov_id' =>  "The selected employee ID field is invalid.",
        ]);

    }

    public function test_validation_rule_invalid_pool_option_when_create()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $form_data["charity_selection"] = 'fsp';
        $form_data['regional_pool_id'] = 'X';
       
        $this->actingAs($this->user);
        $response = $this->postJson('/bank_deposit_form', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $this->delete_file_in_temp_folder($pledge);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'regional_pool_id' => "The selected regional pool id is invalid.",
        ]);

    }


    public function test_validation_rule_invalid_percentage_when_create()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $form_data["charity_selection"] = 'dc';
        foreach ($form_data["donation_percent"] as $key => $precentage) {
            $form_data["donation_percent"][$key] = 0;
        }
       
        $this->actingAs($this->user);
        $response = $this->postJson('/bank_deposit_form', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $this->delete_file_in_temp_folder($pledge);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            "donation_percent.1" => "The Donation Percent is required.",
            "donation_percent.2" => "The Donation Percent is required.",
            "donation_percent.3" => "The Donation Percent is required.",
            "donation_percent.4" => "The Donation Percent is required.",
            "donation_percent.5" => "The Donation Percent is required.",
            "donation_percent.6" => "The Donation Percent is required.",
            "donation_percent.7" => "The Donation Percent is required.",
            "donation_percent.8" => "The Donation Percent is required.",
            "donation_percent.9" => "The Donation Percent is required.",
            "donation_percent.10" => "The Donation Percent is required.",
        ]);



    }

    
    public function test_validation_rule_total_percentage_is_not_100_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $form_data["charity_selection"] = 'dc';
        $form_data["donation_percent"][0] = 10.01;
       
        $this->actingAs($this->user);
        $response = $this->postJson('/bank_deposit_form', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $this->delete_file_in_temp_folder($pledge);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            "donation_percent.1" => "The Donation Percent Does not equal 100%.",
            "donation_percent.2" => "The Donation Percent Does not equal 100%.",
            "donation_percent.3" => "The Donation Percent Does not equal 100%.",
            "donation_percent.4" => "The Donation Percent Does not equal 100%.",
            "donation_percent.5" => "The Donation Percent Does not equal 100%.",
            "donation_percent.6" => "The Donation Percent Does not equal 100%.",
            "donation_percent.7" => "The Donation Percent Does not equal 100%.",
            "donation_percent.8" => "The Donation Percent Does not equal 100%.",
            "donation_percent.9" => "The Donation Percent Does not equal 100%.",
            "donation_percent.10" => "The Donation Percent Does not equal 100%.",
        ]);

        


    }

//     public function test_validation_rule_invalid_fund_support_pool_when_create_or_edit()
//     {
//         // create the founcdation data 
//         [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

//         $form_data["step"] = 2;
//         // foreach ( $form_data["percentages"] as $key => $percent) {
//         //     $form_data["percentages"][$key] = $this->faker->randomElement( [0, 101] );
//         // }
//         $form_data['pool_option'] = 'P';
//         $form_data['pool_id'] = 1123;      

//         $this->actingAs($this->user);
//         $response = $this->postJson('/bank_deposit_form', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

//         $response->assertStatus(422);
//         $response->assertJsonValidationErrors([
//             'regional_pool_id' =>  "The selected regional pool id is invalid.",
//         ]);


//     }

    public function test_validation_rule_invalid_charity_when_create()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $form_data["charity_selection"] = 'dc';
        $form_data["id"][0] = 8988;
        $form_data["vendor_id"][0] = 8988;
       
        $this->actingAs($this->user);
        $response = $this->postJson('/bank_deposit_form', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $this->delete_file_in_temp_folder($pledge);

        $response->assertStatus(200);
        // Note: There is no validation on charity when submitted 
        // $response->assertJsonValidationErrors([
        //     'vendor_id.0' => "The invalid charity entered."
        // ]);

    }

   

    /* Private Function */
    private function get_new_record_form_data($bCreate = false, $is_gov = true) 
    {

        $user = User::first();

        $campaign_year = CampaignYear::create([
            'calendar_year' => today()->year + 1,
            'status' => 'A',
            'start_date' => today(),
            'end_date' => today()->year . '-12-31',
            'number_of_periods' => 26,
            'close_date' => today()->year . '-12-31',
        ]);

        $organization = Organization::factory()->create([
                'code' => "GOV",
        ]);
        if (!($is_gov)) {
            $organization = Organization::factory()->create([
                    'code' => "LDB",
                ]);
        }

        $business = BusinessUnit::factory()->create();
        $region = Region::factory()->create();
        $city = City::factory()->create();
        $charities = Charity::factory(10)->create([
            'charity_status' => 'Registered',
        ]);

        $fspool = FSPool::factory()->create([
            'region_id' => $region->id,
            'status' => 'A',
        ]);
        $fspool_charities = FSPoolCharity::factory(5)->create([
                'f_s_pool_id' => $fspool->id,
                'charity_id' => $this->faker->randomElement( $charities->pluck('id')->toArray() ),
                'percentage' => 20,
                'status' => 'A',
        ]);

        $job = EmployeeJob::factory()->create([
            "organization_id" => $organization->id,
            "emplid" => $user->emplid,
            "business_unit" => $business->code,
            "business_unit_id" => $business->id,
            "tgb_reg_district" =>  $region->code,
            "region_id" => $region->id,
            'office_city' => $city->city,
        ]);

        // Test Transaction
        $pledge = new BankDepositForm([
            'organization_code' =>  $organization->code,
            'form_submitter_id' =>  $user->id,
            'campaign_year_id' => $campaign_year->id,
            'event_type' => 'Gaming',
            'sub_type' => '50/50 Draw',
            'deposit_date' => Carbon::yesterday(),
            'deposit_amount' => 989.00,
            'description' => $this->faker->words(3, true),
            'employment_city' => $job->office_city,
            'region_id'	=> $region->id,
            'department_id' => null,
            'regional_pool_id' => null,
            'address_line_1' =>	null,       //substr($this->faker->address(), 0, 60),
            'address_line_2' => null,       //substr($this->faker->address(), 0, 60),
            'address_city' => null,         //$this->faker->city(),
            'address_province' => null,     //$this->faker->regexify('/^[A-Z]{2}$/'),
            'address_postal_code' =>null,   // $this->faker->regexify('/^[A-Z]\d[A-Z]\ {1}\d[A-Z]\d$/'),

            'pecsf_id' => 'G'. substr($campaign_year->calendar_year,2,2) -1 . '001',
            'bc_gov_id'	=> $is_gov ? $user->emplid : null,
            'business_unit'	=> $is_gov ? $job->business_unit_id : $business->id,
            'deptid' => $is_gov ? $job->deptid : null,
            'dept_name' => $is_gov ? $job->dept_name : null,
            'employee_name' => $is_gov ? $user->name :  $this->faker->words(2, true),
        ]);

        foreach ($charities as $key => $charity) {
            $text = $this->faker->words(2, true);

            $item = new BankDepositFormOrganizations([
                        'bank_deposit_form_id' => $pledge->id ?? 1,
                        'organization_name' => $charity->charity_name,
                        'vendor_id' => $charity->id,
                        'donation_percent' => 10,
                        'specific_community_or_initiative' => $text,
                    ]);
            $pledge->charities[$key] = $item; 
        }

        // attachement
        $file = UploadedFile::fake()->image('document.jpg');
        $unique_name = uniqid() . '_' . str_replace(' ', '_', $file->name);

        $attach = new BankDepositFormAttachments([
            'bank_deposit_form_id' => $pledge->id ?? 1,
            'filename' => $unique_name,
            'original_filename' => $file->name,
            'mime' => 'jpg',  
            'local_path' => Storage::path('') . "uploads/bank_deposit_form_attachments/" . $unique_name,
            'file' => base64_encode($file),
        ]);
        $pledge->attachments[0] = $attach;

        $form_data = $this->tranform_to_form_data($pledge);

        if ($bCreate) {
            // create 
            // $pledge->save();
            $pledge->push();  // save your model and all of its associated relationships
        }

        // var_dump($pledge->charities->all());
        // dd('test');

        return [$form_data, $pledge];
    }

    private function tranform_to_form_data($pledge)
    {

        $form_data = [
            "organization_code" => $pledge->organization->code,
            "form_submitter" => $this->user->id,
            "campaign_year" => $pledge->campaign_year_id,
            "description" => $pledge->description,
            
            "event_type" => $pledge->event_type,
            "sub_type" => $pledge->sub_type,
            "deposit_date" => $pledge->deposit_date,
            "deposit_amount" => $pledge->deposit_amount,
            "bc_gov_id" => $pledge->bc_gov_id,
            "employee_name" => $pledge->employee_name,

            "address_1" => $pledge->address_line_1,
            "city" => $pledge->address_city,
            "province" => $pledge->address_province,
            "postal_code" => $pledge->address_postal_code,

            "business_unit" => $pledge->business_unit,
            "region" => $pledge->region_id,
            "employment_city" => $pledge->employment_city,

            "charity_selection" => $pledge->regional_pool_id ? 'fsp' : 'dc',
            "regional_pool_id" => $pledge->regional_pool_id,

            'org_count' => 10, 

            'id' => [],
            'vendor_id' => [],
            'organization_name' => [],
            'donation_percent' => [],
            'additional' => [],

            'attachments' => [],

        ];

        if (!($pledge->regional_pool_id)) {
            for ($i = 0; $i < 10; $i++) {

                $charity_id = $pledge->charities[$i]->vendor_id;
                $charity_name = $pledge->charities[$i]->organization_name;
                $percentage = $pledge->charities[$i]->donation_percent;
                $additional = $pledge->charities[$i]->specific_community_or_initiative;

                array_push($form_data["id"], $charity_id);
                array_push($form_data["vendor_id"], $charity_id);
                array_push($form_data["organization_name"], $charity_name);
                array_push($form_data["donation_percent"], $percentage);
                array_push($form_data["additional"], $additional);

            }

        }

        foreach($pledge->attachments as $attachment) {
            array_push($form_data['attachments'], $attachment->filename);
            // put to the temp folder 
            Storage::put('tmp/' . $attachment->filename, base64_decode($attachment->file));
        }

        // foreach ($pledge->charities() as $key => $pool_charity) {

        //     array_push($form_data["charities"], $pool_charity->charity_id);
        //     array_push($form_data["additional"], $pool_charity->additional);
        //     array_push( $form_data["percentages"], $percentages, $pool_charity->percentage);

        // }

// dd($form_data["charities"]);        
// dd($pledge->charities);        
        return $form_data;

    } 

    private function delete_file_in_temp_folder($pledge) {

        foreach ($pledge->attachments as $attachment) {
            $file_path = Storage::path('tmp/') . $attachment->filename;
            File::delete( $file_path );    
        }

    }

}
