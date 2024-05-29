<?php

namespace Tests\Feature;

use File;
use finfo;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\City;


use App\Models\User;

use App\Models\Region;


use App\Models\Donation;
use App\Models\EmployeeJob;
use App\Models\BusinessUnit;
use App\Models\CampaignYear;
use App\Models\Organization;

use App\Models\ProcessHistory;
use App\Models\VolunteerProfile;
use Illuminate\Http\UploadedFile;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Artisan;
use App\Exports\VolunteerProfilesExport;
use App\Exports\DonationDataReportExport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReportVolunteerProfileReportTest extends TestCase
{

    use WithFaker;

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

        CampaignYear::truncate();
        Region::truncate();
        BusinessUnit::truncate();
        Organization::truncate();
        
        EmployeeJob::truncate();

        VolunteerProfile::truncate();


        ProcessHistory::truncate();

        $this->artisan('queue:prune-batches');
        $this->artisan('queue:clear');

    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_the_volunteer_profile_report_index_page()
    {
        $response = $this->get('reporting/volunteer-profiles');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_volunteer_profile_report_create_page()
    {
        $response = $this->get('reporting/volunteer-profiles/create');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_create_the_volunteer_profile_report()
    {

        $response = $this->post('reporting/volunteer-profiles', []);

        $response->assertStatus(405);
    }
    public function test_an_anonymous_user_cannot_access_the_volunteer_profile_report_view_page()
    {
        $response = $this->get('reporting/volunteer-profiles/1');
 
        $response->assertStatus(302);
        $response->assertRedirect('login');

    }
    public function test_an_anonymous_user_cannot_access_the_volunteer_profile_report_edit_page()
    {
        $response = $this->get('reporting/volunteer-profiles/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_update_the_volunteer_profile_report()
    {

        $response = $this->put('reporting/volunteer-profiles/1', [] );

        $response->assertStatus(405);
    }
    public function test_an_anonymous_user_cannot_delete_the_volunteer_profile_report()
    {
        $response = $this->delete('reporting/volunteer-profiles/1');

        $response->assertStatus(405);
    }
    public function test_an_anonymous_user_cannot_download_the_volunteer_profile_report()
    {
      
        $response = $this->get('/reporting/volunteer-profiles/download-export-file/1', []);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
 
    // Authorized User
    public function test_an_authorized_user_cannot_access_the_volunteer_profile_report_index_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/volunteer-profiles');

        $response->assertStatus(403);
    }
    public function test_an_authorized_user_cannot_access_the_volunteer_profile_report_create_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/volunteer-profiles/create');

        $response->assertStatus(403);
    }
    public function test_an_authorized_user_cannot_create_the_volunteer_profile_report()
    {

		$this->actingAs($this->user);
        $response = $this->post('reporting/volunteer-profiles', []);

        $response->assertStatus(405);
    }
    public function test_an_authorized_user_cannot_access_the_volunteer_profile_report_view_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/volunteer-profiles/1');

        $response->assertStatus(403);
    }
    public function test_an_authorized_user_cannot_access_the_volunteer_profile_report_edit_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/volunteer-profiles/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_authorized_user_cannot_update_the_volunteer_profile_report()
    {
		$this->actingAs($this->user);
        $response = $this->put('reporting/volunteer-profiles/1', [] );

        $response->assertStatus(405);
    }
    public function test_an_authorized_user_cannot_delete_the_volunteer_profile_report()
    {
		$this->actingAs($this->user);
        $response = $this->delete('reporting/volunteer-profiles/1');

        $response->assertStatus(405);
    }
    public function test_an_authozied_user_cannot_download_the_volunteer_profile_report()
    {
        $this->actingAs($this->user);
        $response = $this->get('/reporting/volunteer-profiles/download-export-file/1', []);

        $response->assertStatus(403);
    }
  

    // Administrator User
    public function test_an_administrator_can_access_the_volunteer_profile_report_index_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/volunteer-profiles');

        $response->assertStatus(200);
    }
    public function test_an_administrator_can_access_the_volunteer_profile_report_create_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/volunteer-profiles/create');

        $response->assertStatus(200);
    }
    public function test_an_administrator_can_create_the_volunteer_profile_report()
    {

        $this->actingAs($this->admin);
        $response = $this->post('reporting/volunteer-profiles', []);

        $response->assertStatus(405);
    }
    public function test_an_administrator_cannot_access_the_volunteer_profile_report_view_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/volunteer-profiles/1');

        $response->assertStatus(200);
    }
    public function test_an_administrator_cannot_access_the_volunteer_profile_report_edit_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/volunteer-profiles/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_administrator_cannot_update_the_volunteer_profile_report()
    {
        $this->actingAs($this->admin);
        $response = $this->put('reporting/volunteer-profiles/1', [] );

        $response->assertStatus(405);
    }
    public function test_an_administrator_cannot_delete_the_volunteer_profile_report()
    {
        $this->actingAs($this->admin);
        $response = $this->delete('reporting/volunteer-profiles/1');

        $response->assertStatus(405);
    }

    // submit a process to generate the excel file
    public function test_an_administrator_can_download_the_volunteer_profile_report_success()
    {
 
        $profile = $this->create_new_record_form_data(true, true);
        $year = $profile->campaign_year;

        $this->actingAs($this->admin);
        $response = $this->json('get', '/reporting/volunteer-profiles/export', ['year' => $year],
                                    ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);

       $context = json_decode($response->getContent());
       $batch_id = $context->batch_id;
       $this->assertDatabaseHas('process_histories', ['id' => $batch_id ] );

       $history = ProcessHistory::where('id', $batch_id)->first();
       $filters = json_decode( $history->parameters , true);

       // export data
       Excel::store(new VolunteerProfilesExport($history->id, $filters), 'public/'. $history->filename);  

        $response = $this->get('/reporting/volunteer-profiles/download-export-file/' . $history->id, []);
        $response->assertStatus(200);
        $response->assertDownload($history->filename);

//         $download = $response->streamedContent();
//         $results = Excel::load( $response->streamedContent() );
// dd($response->streamedContent());
        $this->assertTrue($response->headers->get('content-type') == 'application/csv');
        $this->assertTrue($response->headers->get('content-disposition') == "attachment; filename=" . $history->filename);

    }

    /* Private Function */
    private function create_new_record_form_data($bCreate = false, $is_gov = true) 
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

        if ($bCreate) {
            // create 
            $profile->save();
        }

        // var_dump($profile->charities->all());
        // dd('test');

        return $profile;

    }

}
