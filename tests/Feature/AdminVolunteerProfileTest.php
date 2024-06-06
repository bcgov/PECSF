<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\City;

use App\Models\User;
use App\Models\Region;

use App\Models\EmployeeJob;

use Illuminate\Support\Arr;
use App\Models\BusinessUnit;
use App\Models\CampaignYear;
use App\Models\Organization;

use App\Models\VolunteerProfile;

use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;

class AdminVolunteerProfileTest extends TestCase
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

        CampaignYear::truncate();
        Region::truncate();
        BusinessUnit::truncate();
        Organization::truncate();
        City::truncate();

        EmployeeJob::truncate();

        VolunteerProfile::truncate();

    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_admin_volunteer_profile_index_page()
    {
        $response = $this->get('/admin-volunteering/profile');

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }
    public function test_an_anonymous_user_cannot_access_the_admin_volunteer_profile_create_page()
    {
        $response = $this->get('/admin-volunteering/profile/create');

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }
    public function test_an_anonymous_user_cannot_create_the_admin_volunteer_profile()
    {
        $response = $this->post('/admin-volunteering/profile', []);

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }
    public function test_an_anonymous_user_cannot_access_the_admin_volunteer_profile_view_page()
    {
        $response = $this->get('/admin-volunteering/profile/1');

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }
    public function test_an_anonymous_user_cannot_access_the_admin_volunteer_profile_edit_page()
    {
        $response = $this->get('/admin-volunteering/profile/1/edit');

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }
    public function test_an_anonymous_user_cannot_update_the_admin_volunteer_profile()
    {

        $response = $this->put('/admin-volunteering/profile/1', [] );

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }

    public function test_an_anonymous_user_cannot_delete_the_admin_volunteer_profile()
    {
        $response = $this->delete('/admin-volunteering/profile/1');

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }


    public function test_an_unauthorized_user_cannot_access_admin_volunteer_profile_index_page()
    {
        $this->actingAs($this->user);

        $response = $this->get('/admin-volunteering/profile');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_admin_volunteer_profile_create_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/admin-volunteering/profile/create');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_create_the_admin_volunteer_profile()
    {

        $this->actingAs($this->user);
        $response = $this->post('/admin-volunteering/profile', []);

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_admin_volunteer_profile_view_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/admin-volunteering/profile/1');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_admin_volunteer_profile_edit_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/admin-volunteering/profile/1/edit');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_update_the_admin_volunteer_profile()
    {
        $this->actingAs($this->user);
        $response = $this->put('/admin-volunteering/profile/1', [] );

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_delete_the_admin_volunteer_profile()
    {
        $this->actingAs($this->user);
        $response = $this->delete('/admin-volunteering/profile/1');

        $response->assertStatus(403);
    }

    // Administrator 
    public function test_an_administrator_can_access_admin_volunteer_profile_index_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/admin-volunteering/profile');

        $response->assertStatus(200);

    }
    public function test_an_administrator_can_access_the_admin_volunteer_profile_create_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/admin-volunteering/profile/create');

        $response->assertStatus(200);
    }
    public function test_an_administrator_can_create_admin_volunteer_profile_successful_in_db()
    {

        // $row = CampaignYear::factory(1)->create();
        [$form_data, $profile] = $this->get_new_record_form_data(false);
        
        $this->actingAs($this->admin);
        $response = $this->post('/admin-volunteering/profile', $form_data );

        $response->assertStatus(204);
        // $response->assertRedirect( '/admin-volunteering/profile');
        $response->assertSessionHas('success');
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('volunteer_profiles', Arr::except( $profile->attributesToArray(), [
            'preferred_role_name', 'province_name', 'fullname', 'full_address', 'is_renew_profile', 'pecsf_user_bu', 'pecsf_user_city',
        ]) );
           
     }

     public function test_an_administrator_can_access_the_admin_volunteer_profile_view_page_contains_valid_record()
    {
        // $row = CampaignYear::factory(1)->create();
        [$form_data, $profile] = $this->get_new_record_form_data(true);

        $this->actingAs($this->admin);
        // access Index page
        $response = $this->get('/admin-volunteering/profile');

        $response->assertStatus(200);
        $this->actingAs($this->admin);
        $response = $this->get("/admin-volunteering/profile/{$profile->id}");
        $response->assertStatus(200);
        $response->assertViewHas('profile', $profile);
    }
    public function test_an_administrator_can_access_admin_volunteer_profile_edit_page_contains_valid_record()
    {
        [$form_data, $profile] = $this->get_new_record_form_data(true);

        $this->actingAs($this->admin);
        $response = $this->get("/admin-volunteering/profile/{$profile->id}/edit");

        $response->assertStatus(200);
        $response->assertViewHas('profile', $profile) ;

// $data = $response->getOriginalContent()->getData();
// dd($response->viewData('pool_option'));
// echo( $profile->charities );
        // if ($profile->type == 'C') {
        //     $response->assertViewHas('pledges_charities', $profile->charities);
        // }
        // if ($profile->type == 'C') {
            // foreach ($profile->charities as $charity) {
            //     $row = new PledgeCharity([
            //         'charity_id' => $charity->id,
            //         'additional' => $charity->additional,
            //         'percentage' => $charity->percentage,
            //     ]); 
                // $response->assertViewHas('pledges_charities', $profile->charities);
            // }
        // }
    }
    public function test_an_administrator_can_update_admin_volunteer_profile_successful_in_db()
    {
        [$form_data, $profile] = $this->get_new_record_form_data(true);


        $form_data['profile_id'] = $profile->id;
        $form_data['no_of_years'] = 29;

        $profile->no_of_years = $form_data['no_of_years'];

        // $new_form_data = $this->tranform_to_form_data($profile);

        $this->actingAs($this->admin);
        $response = $this->put('/admin-volunteering/profile/' . $profile->id, $form_data );

        $response->assertStatus(204);
        // $response->assertRedirect('/admin-volunteering/profile');
        $response->assertSessionHas('success');
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('volunteer_profiles', Arr::except( $profile->attributesToArray(), [
            'preferred_role_name', 'province_name', 'fullname', 'full_address', 'is_renew_profile', 'pecsf_user_bu', 'pecsf_user_city',
            'updated_at', 'created_at'
        ]) );

    }

    public function test_an_administrator_can_delete_the_admin_volunteer_profile_in_db()
    {
        // $campignyears = CampaignYear::factory(1)->create();
        // $row = $campignyears->first();
        [$form_data, $profile] = $this->get_new_record_form_data(true, false);

        $this->actingAs($this->admin);
        $response = $this->json('delete', '/admin-volunteering/profile/' . $profile->id, [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(204);
        // Assert the file was deleted ...
        $this->assertDatabaseMissing('volunteer_profiles', Arr::except( $profile->attributesToArray(), [
            'preferred_role_name', 'province_name', 'fullname', 'full_address', 'is_renew_profile', 'pecsf_user_bu', 'pecsf_user_city',
            'updated_at', 'created_at'
        ]) );
        
    }



    // /** Pagination */
    public function test_pagination_on_admin_volunteer_profiles_table_via_ajax_call()
    {
        // Seed data for pagination testing
        // $this->seed(\Database\Seeders\CampaignYearSeeder::class);
        [$form_data, $profile] = $this->get_new_record_form_data(true, true);

        $this->actingAs($this->admin);
        $response = $this->getJson('/admin-volunteering/profile', ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $profile->id,
            'emplid' => $profile->emplid,
            'pecsf_id' => $profile->pecsf_id,
        ]);
    }

    /** Form Validation */
    public function test_validation_rule_fields_are_required_when_create_or_edit()
    {
        [$form_data, $profile] = $this->get_new_record_form_data(true, true);
        
        foreach ([0,1] as $i) { 
            $this->actingAs($this->admin);
            if ($i == 0) {
                $response = $this->postJson('/admin-volunteering/profile', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            } else {
                $response = $this->json('put', '/admin-volunteering/profile/' . $profile->id, [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            }

            $response->assertStatus(422);
            $response->assertJsonValidationErrors([
                "campaign_year" => "The campaign year field is required.",
                "organization_id" => "The organization id field is required.",
                "pecsf_id" =>  "The pecsf id field is required.",
                "pecsf_first_name" => "The pecsf first name field is required.",
                "pecsf_last_name" => "The pecsf last name field is required.",
                "business_unit_code" => "The business unit field is required",
                "no_of_years" => "The number of years field is required",
                "preferred_role" => "The preferred volunteer role field is required",
                "address_type" => "The address type field is required.",
            ]);
        }
        
    }
   

    public function test_validation_rule_invalid_no_of_years_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $profile] = $this->get_new_record_form_data(true, false);

        $form_data['profile_id'] = $profile->id;
        $form_data["no_of_years"] = 90;

        foreach ([0,1] as $i) { 
            $this->actingAs($this->admin);
            if ($i == 0) {
                $response = $this->postJson('/admin-volunteering/profile', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            } else {
                $response = $this->json('put', '/admin-volunteering/profile/' . $profile->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            }

            $response->assertStatus(422);
            $response->assertJsonValidationErrors([
                "no_of_years" => "The number of years must be between 1 and 50"
            ]);
        }

    }

    public function test_validation_rule_invalid_preferred_role_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $profile] = $this->get_new_record_form_data(true, false);

        $form_data['profile_id'] = $profile->id;
        $form_data['preferred_role'] = 'XX';

        foreach ([0,1] as $i) { 
            $this->actingAs($this->admin);
            if ($i == 0) {
                $response = $this->postJson('/admin-volunteering/profile', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            } else {
                $response = $this->json('put', '/admin-volunteering/profile/' . $profile->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            }

            $response->assertStatus(422);
            $response->assertJsonValidationErrors([
                'preferred_role' => "The selected preferred volunteer role is invalid",
            ]);
        }

    }

    public function test_validation_rule_invalid_business_unit_code_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $profile] = $this->get_new_record_form_data(true, false);

        $form_data['profile_id'] = $profile->id;
        $form_data['business_unit_code'] = 'TT999';

        foreach ([0,1] as $i) { 
            $this->actingAs($this->admin);
            if ($i == 0) {
                $response = $this->postJson('/admin-volunteering/profile', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            } else {
                $response = $this->json('put', '/admin-volunteering/profile/' . $profile->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            }

            $response->assertStatus(422);
            $response->assertJsonValidationErrors([
                "business_unit_code" =>  "The selected business unit is invalid",
            ]);
        }
        
    }

    public function test_validation_rule_invalid_employee_id_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $profile] = $this->get_new_record_form_data(true, true);

        $form_data['profile_id'] = $profile->id;
        $form_data["emplid"] = 999991;
      
        foreach ([0,1] as $i) { 
            $this->actingAs($this->admin);
            if ($i == 0) {
                $response = $this->postJson('/admin-volunteering/profile', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            } else {
                $response = $this->json('put', '/admin-volunteering/profile/' . $profile->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            }

            $response->assertStatus(422);
            $response->assertJsonValidationErrors([
                'emplid', 
            ]);
        }

    }

    public function test_validation_rule_invalid_pecsf_id_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $profile] = $this->get_new_record_form_data(true, false);

        $form_data['profile_id'] = $profile->id;
        $form_data["pecsf_id"] = 'aaghsd';
    
        foreach ([0,1] as $i) {  
            $this->actingAs($this->admin);
            if ($i == 0) {
                $response = $this->postJson('/admin-volunteering/profile', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            } else {
                $response = $this->json('put', '/admin-volunteering/profile/' . $profile->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            }

            $response->assertStatus(422);
            $response->assertJsonValidationErrors([
                'pecsf_id' => "The pecsf id must be 6 digits.",
            ]);
        }

    }


    

    public function test_validation_rule_name_fields_are_required_when_create_or_edit()
    {
        // create the founcdation data 
        [$form_data, $profile] = $this->get_new_record_form_data(true, false);

        $form_data['profile_id'] = $profile->id;
        $form_data["pecsf_first_name"] = null;
        $form_data["pecsf_last_name"] = null;
        $form_data["pecsf_city"] = null;

        foreach ([0,1] as $i) {    
            $this->actingAs($this->admin);
            if ($i == 0) {
                $response = $this->postJson('/admin-volunteering/profile', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            } else {
                $response = $this->json('put', '/admin-volunteering/profile/' . $profile->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            }
        
            $response->assertStatus(422);
            $response->assertJsonValidationErrors([
                'pecsf_first_name' => "The pecsf first name field is required.",
                "pecsf_last_name" => "The pecsf last name field is required.",
                "pecsf_city" => "The pecsf city field is required.",
            ]);
        }
    }

   

    /* Private Function */
    // private function get_new_record_form_data($bCreate = false, $is_gov = true) 
    // {

    //     $user = User::first();


    //     $organization = Organization::factory()->create([
    //                 'code' => "GOV",
    //     ]);
    //     if (!($is_gov)) {
    //         $organization = Organization::factory()->create([
    //                 'code' => "LDB",
    //             ]);
    //     }

    //     $business = BusinessUnit::factory()->create();
    //     $region = Region::factory()->create();
    //     $charities = Charity::factory(10)->create([
    //         'charity_status' => 'Registered',
    //     ]);

    //     $job = EmployeeJob::factory()->create([
    //         "organization_id" => $organization->id,
    //         "emplid" => $user->emplid,
    //         "business_unit" => $business->code,
    //         "business_unit_id" => $business->id,
    //         "tgb_reg_district" =>  $region->code,
    //         "region_id" => $region->id,
    //     ]);

    //     $charity = Charity::factory()->create();
    //     $file = UploadedFile::fake()->image('avatar.jpg');
             
    //     $specialCampaign = SpecialCampaign::factory()->create([
    //         'charity_id' => $charity->id,
    //         'start_date' => '1990-01-01',
    //         'end_date' => today(),
    //         "image" => $file->name,
    //     ]);

    //     $current = PayCalendar::whereRaw(" ( date(SYSDATE()) between pay_begin_dt and pay_end_dt) ")->first();
    //     $period = PayCalendar::where('check_dt', '>=', $current->check_dt )->skip(2)->take(1)->orderBy('check_dt')->first();

    //     // Test Transaction
    //     $profile = new SpecialCampaignPledge([
    //         'organization_id' =>  $organization->id,
    //         'emplid' => $is_gov ? $user->emplid : null,
    //         'user_id' => $is_gov ? $user->id : null, 
    //         'pecsf_id' => $is_gov ? null : '123456', 
    //         'yearcd' => today()->year,
    //         'seqno' => 1,
    //         'special_campaign_id' => $specialCampaign->id,
    //         'one_time_amount' => 50.0,  
    //         'deduct_pay_from' => $period->check_dt,
    //         'first_name' => $is_gov ? null : $this->faker->word(),
    //         'last_name' => $is_gov ? null : $this->faker->word(),
    //         'city' => $is_gov ? null : $this->faker->word(),
    //     ]);

    //     $form_data = $this->tranform_to_form_data($profile);

    //     if ($bCreate) {
    //         // create 
    //         $profile->save();
    //     }

    //     // var_dump($profile->charities->all());
    //     // dd('test');

    //     return [$form_data, $profile];
    // }

    // private function tranform_to_form_data($profile)
    // {

    //     $form_data = [
    //         "yearcd" => $profile->yearcd,
    //         "organization_id" => $profile->organization_id,
    //         "user_id" => $profile->user_id,
    //         "pecsf_id" => $profile->pecsf_id,
    //         "pecsf_first_name" => $profile->first_name,
    //         "pecsf_last_name" => $profile->last_name,
    //         "pecsf_city" => $profile->city,
    //         "special_campaign_id" => $profile->special_campaign_id,
    //         "one_time_amount" => $profile->one_time_amount,

    //     ];

    //     return $form_data;

    // } 

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
        $business = BusinessUnit::factory()->create(['status' => 'A']);
        $business2 = BusinessUnit::factory()->create(['status' => 'A']);
        
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

        if ($organization->code <> 'GOV') {
            $address_type = 'S';
        } else {    
            $address_type = $this->faker->randomElement( ['G', 'S'] );
        }

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
            'address' => $address_type == 'G' ? null : substr($this->faker->address(), 0, 60),
            'city' => $address_type == 'G' ? null : $city->city,
            'province' => $address_type == 'G' ? null : $this->faker->randomElement( array_keys($province_list) ),
            'postal_code' => $address_type == 'G' ? null : $this->faker->regexify('/^[A-Z]\d[A-Z]\ {1}\d[A-Z]\d$/'),

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

        $org = Organization::where('code', $profile->organization_code)->first();
        $city = City::where('city', $profile->city)->first();

        $form_data = [
            "campaign_year" => $profile->campaign_year,
            'organization_id' => $org->id,

            "emplid" => $profile->emplid,
            "pecsf_id" => $profile->pecsf_id,
            
            "pecsf_first_name" => $profile->first_name,
            "pecsf_last_name" => $profile->last_name,
            "pecsf_city" => $profile->employee_city_name,
            
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
