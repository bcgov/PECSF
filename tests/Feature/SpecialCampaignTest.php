<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Region;
use App\Models\Charity;
use App\Models\EmployeeJob;
use App\Models\PayCalendar;
use Illuminate\Support\Arr;
use App\Models\BusinessUnit;
use App\Models\Organization;
use App\Models\SpecialCampaign;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Models\SpecialCampaignPledge;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;

class SpecialCampaignTest extends TestCase
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
        $this->user  = User::doesntHave('roles')->orderBy('id')->first();

        // $this->artisan('db:seed', ['--class' => 'PayCalendarSeeder']);


        Region::truncate();
        BusinessUnit::truncate();
        Organization::truncate();
        Charity::truncate();
        EmployeeJob::truncate();

        SpecialCampaign::truncate();
        SpecialCampaignPledge::truncate();

    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_special_campaign_pledge_index_page()
    {
        $response = $this->get('/special-campaign');

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }
    public function test_an_anonymous_user_cannot_access_the_special_campaign_pledge_create_page()
    {
        $response = $this->get('/special-campaign/create');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_create_the_special_campaign_pledge()
    {
        $response = $this->post('/special-campaign', []);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_special_campaign_pledge_view_page()
    {
        $response = $this->get('/special-campaign/1');

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_special_campaign_pledge_edit_page()
    {
        $response = $this->get('/special-campaign/1/edit');

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_update_the_special_campaign_pledge()
    {

        $response = $this->put('/special-campaign/1', [] );

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }

    public function test_an_anonymous_user_cannot_delete_the_special_campaign_pledge()
    {
        $response = $this->delete('/special-campaign/1');

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }


    //
    // Test Authorized User
    //
    public function test_an_authorized_user_can_access_special_campaign_pledge_index_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/special-campaign');

        $response->assertStatus(302);
        $response->assertRedirectContains("special-campaign/create");
    }

    public function test_an_authorized_user_can_access_the_special_campaign_pledge_create_page()
    {
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $this->actingAs($this->user);
        $response = $this->get('/special-campaign/create');

        $response->assertStatus(200);
        $response->assertSeeText("Donate to a special campaign");
    }

    public function test_an_authorized_user_can_create_special_campaign_pledge_successful_in_db()
    {

        // $row = CampaignYear::factory(1)->create();
        [$form_data, $pledge] = $this->get_new_record_form_data(false);
        
        $this->actingAs($this->user);
        $response = $this->post('/special-campaign', $form_data );

        $response->assertStatus(302);
        $response->assertRedirect( '/special-campaign/thank-you');
        $response->assertSessionHas('success');
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('special_campaign_pledges', Arr::except( $pledge->attributesToArray(), ['in_support_of']) );
           
    }


    public function test_an_authorized_user_cannot_access_the_special_campaign_pledge_view_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/special-campaign/1');

        $response->assertStatus(404);

    }
    public function test_an_authorized_user_cannot_access_the_special_campaign_pledge_edit_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/special-campaign/1/edit');

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }
    public function test_an_authorized_user_cannot_update_special_campaign_pledge_successful_in_db()
    {
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $this->actingAs($this->user);
        $response = $this->put('/special-campaign/' . $pledge->id, [] );

        $response->assertStatus(404);

    }

    public function test_an_authorized_user_cannot_delete_the_special_campaign_pledge()
    {
        $this->actingAs($this->user);
        $response = $this->delete('/special-campaign/1');

        $response->assertStatus(404);

    }


// //     // /** Form Validation */
// //     // public function test_validation_rule_fields_are_required_when_create()
// //     // {
// //     //     // create the founcdation data 
// //     //     [$form_data, $pledge] = $this->get_new_record_form_data(false, false);

// //     //     $form_data =  [
// //     //         "step" => "3",
// //     //         "campaign_year_id" => $pledge->campaign_year_id,
// //     //         "organization_id" => $pledge->organization_id,
// //     //         ];

// //     //     $this->actingAs($this->admin);
// //     //     // Post empty data to the create page route
// //     //     // $response = $this->post('/special-campaign', $form_data);
// //     //     $this->actingAs($this->admin);
// //     //     $response = $this->postJson('/special-campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
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
// //     //     $response = $this->json('put', '/special-campaign/' . $pledge->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        
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


    public function test_validation_rule_fields_are_required_on_step_1_when_create()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $form_data = [
            "step" => "1",
        ];
       
        $this->actingAs($this->user);

            $response = $this->postJson('/special-campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'special_campaign_id', 
        ]);

    }

    public function test_validation_rule_fields_are_required_on_step_2_when_create()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true);

        $form_data = [
            "step" => "2",
        ];
       
        $this->actingAs($this->user);
        $response = $this->postJson('/special-campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'one_time_amount_custom', 
        ]);

    }

    public function test_validation_rule_invalid_fund_support_pool_when_create()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $form_data["step"] = 1;
        $form_data['special_campaign_id'] = 122121;

        $this->actingAs($this->user);
        $response = $this->postJson('/special-campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'special_campaign_id' =>  "The selected special campaign id is invalid.",
        ]);


    }

    public function test_validation_rule_invalid_min_amount_when_create()
    {
        // create the founcdation data 
        [$form_data, $pledge] = $this->get_new_record_form_data(true, false);

        $form_data["step"] = 2;
        $form_data["one_time_amount"] = '';
        $form_data["one_time_amount_custom"] = 0.01;
        
        $this->actingAs($this->user);
        $response = $this->postJson('/special-campaign', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'one_time_amount_custom' => "The minimum One-time custom amount is $1",
        ]);
        

    }


    /* Private Function */
    private function get_new_record_form_data($bCreate = false, $is_gov = true) 
    {

        $user = User::first();


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
        $charities = Charity::factory(10)->create([
            'charity_status' => 'Registered',
        ]);

        $job = EmployeeJob::factory()->create([
            "organization_id" => $organization->id,
            "emplid" => $user->emplid,
            "business_unit" => $business->code,
            "business_unit_id" => $business->id,
            "tgb_reg_district" =>  $region->code,
            "region_id" => $region->id,
        ]);

        $charity = Charity::factory()->create();
        $file = UploadedFile::fake()->image('avatar.jpg');
             
        $specialCampaign = SpecialCampaign::factory()->create([
            'charity_id' => $charity->id,
            'start_date' => '1990-01-01',
            'end_date' => today(),
            "image" => $file->name,
        ]);

        $current = PayCalendar::whereRaw(" ( date(SYSDATE()) between pay_begin_dt and pay_end_dt) ")->first();
        $period = PayCalendar::where('check_dt', '>=', $current->check_dt )->skip(2)->take(1)->orderBy('check_dt')->first();

        // Test Transaction
        $pledge = SpecialCampaignPledge::factory()->make([
            'organization_id' =>  $organization->id,
            'emplid' => $is_gov ? $user->emplid : null,
            'user_id' => $is_gov ? $user->id : 0, 
            'yearcd' => today()->year,
            'seqno' => 1,
            'special_campaign_id' => $specialCampaign->id,
            'one_time_amount' => 50.0,  
            'deduct_pay_from' => $period->check_dt,
            'first_name' => null,
            'last_name' => null,
            'city' => null,
        ]);

        $form_data = $this->tranform_to_form_data($pledge);

        if ($bCreate) {
            // create 
            $pledge->save();
        }

        // var_dump($pledge->charities->all());
        // dd('test');

        return [$form_data, $pledge];
    }

    private function tranform_to_form_data($pledge)
    {

        $form_data = [
            "step"   => "2",
            "yearcd" => $pledge->yearcd,
            "special_campaign_id" => $pledge->special_campaign_id,
            "one_time_amount" => in_array($pledge->one_time_amount, [0,6,12,20,50]) ?  $pledge->one_time_amount : '',
            "one_time_amount_custom" =>  in_array($pledge->one_time_amount, [0,6,12,20,50]) ? 0 : $pledge->one_time_amount,

        ];

// dd($form_data["charities"]);        
// dd($pledge->charities);        
        return $form_data;

    } 

}
