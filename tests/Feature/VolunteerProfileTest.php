<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\City;

use App\Models\User;
use App\Models\Region;
use App\Models\Charity;

use App\Models\EmployeeJob;
use Illuminate\Support\Arr;
use App\Models\BusinessUnit;
use App\Models\CampaignYear;
use App\Models\Organization;
use App\Models\VolunteerProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Models\PledgeHistorySummary;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;

class VolunteerProfileTest extends TestCase
{
    use WithFaker;

    // protected static $initialized = FALSE;

    private User $admin;
    private User $user;

    protected static $db_inited = false;

       public function setUp(): void
    {
        parent::setUp();

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

        // $this->artisan('db:seed', ['--class' => 'PayCalendarSeeder']);

        CampaignYear::truncate();
        Region::truncate();
        BusinessUnit::truncate();
        Organization::truncate();
        EmployeeJob::truncate();
        City::truncate();
        
        VolunteerProfile::truncate();

    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_volunteer_profile_index_page()
    {
        $response = $this->get('/volunteering/profile');

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }
    public function test_an_anonymous_user_cannot_access_the_volunteer_profile_create_page()
    {
        $response = $this->get('/volunteering/profile/create');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_create_the_volunteer_profile()
    {
        $response = $this->post('/volunteering/profile', []);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_volunteer_profile_view_page()
    {
        $response = $this->get('/volunteering/profile/1');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_volunteer_profile_edit_page()
    {
        $response = $this->get('/volunteering/profile/1/edit');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_update_the_volunteer_profile()
    {

        $response = $this->put('/volunteering/profile/1', [] );

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function test_an_anonymous_user_cannot_delete_the_volunteer_profile()
    {
        $response = $this->delete('/volunteering/profile/1');

        $response->assertStatus(405);
        // $response->assertRedirect('login');
    }


    //
    // Test Authorized User
    //
    public function test_an_authorized_user_can_access_volunteer_profile_index_page_during_period_open_and_not_yet_registered()
    {
        [$form_data, $profile] = $this->get_new_record_form_data(false);

        // redirect to create page when the campaign_period is open and no yet registered
        $this->actingAs($this->user);
        $response = $this->get('/volunteering/profile');

        $response->assertStatus(302);
        $response->assertRedirectContains("/volunteering/profile/create");
    }

    public function test_an_authorized_user_can_access_volunteer_profile_index_page_during_period_open_and_registered()
    {
        [$form_data, $profile] = $this->get_new_record_form_data(true);

        // redirect to create page when the campaign_period is open and no yet registered
        $this->actingAs($this->user);
        $response = $this->get('/volunteering/profile');

        $response->assertStatus(302);
        $response->assertRedirectContains("/volunteering/profile/1");
    }

    public function test_an_authorized_user_can_access_the_volunteer_profile_create_page()
    {
        [$form_data, $profile] = $this->get_new_record_form_data(false);

        $this->actingAs($this->user);
        $response = $this->get('/volunteering/profile/create');

        $response->assertStatus(200);
        $response->assertSeeText("Register as a Volunteer");
    }

    


    public function test_an_authorized_user_can_create_volunteer_profile_successful_in_db()
    {

        // $row = CampaignYear::factory(1)->create();
        [$form_data, $profile] = $this->get_new_record_form_data(false);
        
        $this->actingAs($this->user);
        $response = $this->post('/volunteering/profile', $form_data );

        $response->assertStatus(302);
        $response->assertRedirect( '/volunteering/profile/thank-you');
        $response->assertSessionHas('success');
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('volunteer_profiles', Arr::except( $profile->attributesToArray(), [
                'preferred_role_name', 'province_name', 'fullname', 'full_address', 'is_renew_profile', 'pecsf_user_bu', 'pecsf_user_city',
            ]) );
           
     }

     public function test_an_authorized_user_can_access_the_volunteer_profile_view_page_contains_valid_record()
    {
        [$form_data, $profile] = $this->get_new_record_form_data(true);

        $this->actingAs($this->user);
        // access Index page
        $response = $this->get('/volunteering/profile');

        $response->assertStatus(302);
        $response->assertRedirect('/volunteering/profile/1');
        
    }

    public function test_an_authorized_user_can_access_volunteer_profile_edit_page_contains_valid_record()
    {
        [$form_data, $profile] = $this->get_new_record_form_data(true);

        $this->actingAs($this->user);
        $response = $this->get("/volunteering/profile/create");

        $province_list = VolunteerProfile::PROVINCE_LIST;
        $role_list = VolunteerProfile::ROLE_LIST;


        $response->assertStatus(200);
        $response->assertViewHasAll([
            'profile' => $profile,
            'role_list' => $role_list,
            
        ]);

    }

    public function test_an_authorized_user_cannot_access_the_volunteer_profile_view_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/volunteering/profile/1');

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }
    public function test_an_authorized_user_cannot_access_the_volunteer_profile_edit_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/volunteering/profile/1/edit');

        $response->assertStatus(403);
        // $response->assertRedirect('login');
    }
    public function test_an_authorized_user_cannot_update_the_volunteer_profile()
    {

        $this->actingAs($this->user);
        $response = $this->put('/volunteering/profile/1', [] );

        $response->assertStatus(404);
        // $response->assertRedirect('login');
    }

    public function test_an_authorized_user_cannot_delete_the_volunteer_profile()
    {
        $this->actingAs($this->user);
        $response = $this->delete('/volunteering/profile/1');

        $response->assertStatus(405);
        // $response->assertRedirect('login');
    }

//     // /** Form Validation */
//     // public function test_validation_rule_fields_are_required_when_create()
//     // {
//     //     // create the founcdation data 
//     //     [$form_data, $profile] = $this->get_new_record_form_data(false, false);

//     //     $form_data =  [
//     //         "step" => "3",
//     //         "campaign_year_id" => $profile->campaign_year_id,
//     //         "organization_id" => $profile->organization_id,
//     //         ];

//     //     $this->actingAs($this->admin);
//     //     // Post empty data to the create page route
//     //     // $response = $this->post('/volunteering/profile', $form_data);
//     //     $this->actingAs($this->admin);
//     //     $response = $this->postJson('/volunteering/profile', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
//     //     // This should cause errors with the
//     //     // title and content fields as they aren't present
//     //     $response->assertStatus(422);
//     //     $response->assertJsonValidationErrors([
//     //         'pay_period_amount_other', 
//     //         'one_time_amount_other',
//     //         'pool_option',
//     //         'pay_period_amount_error',
//     //         'one_time_amount_error',
//     //     ]);
//     // }
//     // public function test_validation_rule_fields_are_required_when_edit()
//     // {
//     //     // create the founcdation data 
//     //     [$form_data, $profile] = $this->get_new_record_form_data(true, true);

//     //     $form_data =  [
//     //         "step" => "3",
//     //         "campaign_year_id" => $profile->campaign_year_id,
//     //         "organization_id" => $profile->organization_id,
//     //         ];


//     //     $this->actingAs($this->admin);
//     //     $response = $this->json('put', '/volunteering/profile/' . $profile->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        
//     //     // This should cause errors with the
//     //     // title and content fields as they aren't present
//     //     $response->assertStatus(422);
//     //     $response->assertJsonValidationErrors([
//     //         'pay_period_amount_other', 
//     //         'one_time_amount_other',
//     //         'pool_option',
//     //         'pay_period_amount_error',
//     //         'one_time_amount_error',
//     //     ]);

//     // }


    public function test_validation_rule_fields_are_required_on_step_1_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $profile] = $this->get_new_record_form_data(true);

        $form_data = [
            "step" => "1",
        ];
       
        $this->actingAs($this->user);

            $response = $this->postJson('/volunteering/profile', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'business_unit_code' =>  "The organization field is required",
            'no_of_years' => "The number of years field is required",
            'preferred_role' => "The preferred volunteer role field is required",
        ]);

    }

    public function test_validation_rule_fields_are_required_on_step_2_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $profile] = $this->get_new_record_form_data(true);

        $form_data = [
            "step" => "2",
            "address_type" => "",
        ];
      
        $this->actingAs($this->user);
        $response = $this->postJson('/volunteering/profile', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            "address_type" => "The address type field is required.",
        ]);

    }

    public function test_validation_rule_fields_are_required_on_step_2_for_non_global_listing_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $profile] = $this->get_new_record_form_data(true);

        $form_data = [
            "step" => "2",
            "address_type" => "S",
        ];
      
        $this->actingAs($this->user);
        $response = $this->postJson('/volunteering/profile', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            
            "address" => "The address field is required",
            "city" => "The city field is required",
            "province" => "The province field is required",
            "postal_code" => "The postal code field is required",
        ]);

    }


    public function test_validation_rule_invalid_no_of_years_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $profile] = $this->get_new_record_form_data(true, false);

        $form_data["step"] = 1;
        $form_data["no_of_years"] = 0;
        
        $this->actingAs($this->user);
        $response = $this->postJson('/volunteering/profile', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            "no_of_years" => "The number of years must be between 1 and 50"
        ]);
        

    }

    public function test_validation_rule_invalid_preferred_role_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $profile] = $this->get_new_record_form_data(true, false);

        $form_data["step"] = 1;
        $form_data['preferred_role'] = 'XX';
       
        $this->actingAs($this->user);
        $response = $this->postJson('/volunteering/profile', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'preferred_role' => "The selected preferred volunteer role is invalid",
        ]);

    }

    public function test_validation_rule_invalid_business_unit_code_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $profile] = $this->get_new_record_form_data(true, false);

        $form_data["step"] = 1;
        // foreach ( $form_data["percentages"] as $key => $percent) {
        //     $form_data["percentages"][$key] = $this->faker->randomElement( [0, 101] );
        // }
        $form_data['business_unit_code'] = 'TT999';

        $this->actingAs($this->user);
        $response = $this->postJson('/volunteering/profile', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            "business_unit_code" => "The selected organization is invalid",
        ]);

    }
 

    /* Private Function */
    private function get_new_record_form_data($bCreate = false, $is_gov = true) 
    {

        $user = User::first();

        $campaign_year = CampaignYear::create([
            'calendar_year' => today()->year + 1,
            'status' => 'I',
            'start_date' => today(),
            'end_date' => today()->year . '-12-31',
            'number_of_periods' => 26,
            'close_date' => today()->year . '-12-31',
            'volunteer_start_date' => today(),
            'volunteer_end_date' => today()->year . '-12-31',
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
        $business2 = BusinessUnit::factory()->create();
        
        $region = Region::factory()->create();

        $city = City::factory()->create([
            'TGB_REG_DISTRICT' => $region->code,
        ]);

        $job = EmployeeJob::factory()->create([
            "organization_id" => $organization->id,
            "emplid" => $user->emplid,
            "business_unit" => $business->code,
            "business_unit_id" => $business->id,
            "tgb_reg_district" =>  $region->code,
            "region_id" => $region->id,
            "office_city" => $city->city
        ]);

        $province_list = \App\Models\VolunteerProfile::PROVINCE_LIST;
        $role_list = \App\Models\VolunteerProfile::ROLE_LIST;
        $address_type = $this->faker->randomElement( ['G', 'S'] );
        $opt_out_recongnition = $this->faker->randomElement( ['Y', 'N'] );

        // Test Transaction
        $profile = VolunteerProfile::factory()->make([
            'campaign_year' => today()->year,
            'organization_code' =>  $organization->code,
            'emplid' => $is_gov ? $user->emplid : null,
            'pecsf_id' => $is_gov ? null : $this->faker->randomNumber(6,true),
            'first_name' => $is_gov ? null : $this->faker->words(1, true),
            'last_name' => $is_gov ? null : $this->faker->words(1, true),
            'employee_city_name' => $is_gov ? $job->office_city : $city->city,
            'employee_bu_code' => $is_gov ? $job->business_unit : $organization->bu_code,
            'employee_region_code' => $is_gov ? $job->tgb_reg_district : $city->tgb_reg_district,
            'business_unit_code' => $business2->code,

            'address_type' =>  $address_type,
            'address' => ($address_type == 'G' || $opt_out_recongnition == 'Y') ? null : substr($this->faker->address(), 0, 60),
            'city' => ($address_type == 'G' || $opt_out_recongnition == 'Y') ? null : $city->city,
            'province' => ($address_type == 'G' || $opt_out_recongnition == 'Y') ? null : $this->faker->randomElement( array_keys($province_list) ),
            'postal_code' => ($address_type == 'G' || $opt_out_recongnition == 'Y') ? null : $this->faker->regexify('/^[A-Z]\d[A-Z]\ {1}\d[A-Z]\d$/'),
            'opt_out_recongnition' => $opt_out_recongnition,

        ]);

        $form_data = $this->tranform_to_form_data($profile);

        if ($bCreate) {
            // create 
            $profile->save();
            
        }

        // var_dump($profile->charities->all());
        // dd('test');

        return [$form_data, $profile];
    }

    private function tranform_to_form_data($profile)
    {

        $city = City::where('city', $profile->city)->first();

        $form_data = [
            "step" => 2,
            "campaign_year" => $profile->campaign_year,
            "business_unit_code" => $profile->business_unit_code,
            "no_of_years" => $profile->no_of_years,
            "preferred_role" => $profile->preferred_role,

            "address_type" => $profile->address_type,
            "address" => $profile->address,
            "city" => $city ? $city->id : null,
            "province" => $profile->province,
            "postal_code" => $profile->postal_code,
            'opt_out_recongnition' => $profile->opt_out_recongnition == 'Y' ? 'Y' : null,

        ];

// dd($form_data["charities"]);        
// dd($profile->charities);        
        return $form_data;

    } 


}
