<?php

namespace Tests\Feature;

use File;
use finfo;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;

use App\Models\FSPool;

use App\Models\Pledge;

use App\Models\Region;

use App\Models\Charity;
use App\Models\Donation;
use App\Models\EmployeeJob;
use App\Models\BusinessUnit;
use App\Models\CampaignYear;
use App\Models\Organization;
use App\Models\FSPoolCharity;
use App\Models\PledgeCharity;
use App\Models\PledgeStaging;
use App\Models\ProcessHistory;
use App\Models\BankDepositForm;
use Illuminate\Http\UploadedFile;
use App\Models\DailyCampaignSummary;
use App\Models\PledgeCharityStaging;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PledgeCharitiesExport;
use Illuminate\Support\Facades\Artisan;
use App\Exports\DonationDataReportExport;
use App\Models\BankDepositFormAttachments;
use App\Models\BankDepositFormOrganizations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReportChallengePageDataReportTest extends TestCase
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

        $this->artisan('db:seed', ['--class' => 'PayCalendarSeeder']);

        CampaignYear::truncate();
        Region::truncate();
        BusinessUnit::truncate();
        Organization::truncate();
        Charity::truncate();
        FSPool::truncate();
        FSPoolCharity::truncate();
        EmployeeJob::truncate();

        PledgeCharity::truncate();
        Pledge::truncate();

        BankDepositForm::truncate();
        BankDepositFormOrganizations::truncate();
        BankDepositFormAttachments::truncate();

        PledgeStaging::truncate();
        PledgeCharityStaging::truncate();

        ProcessHistory::truncate();

        $this->artisan('queue:prune-batches');
        $this->artisan('queue:clear');

    }
    

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_the_challenge_page_data_report_index_page()
    {
        $response = $this->get('reporting/challenge-page-data');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_challenge_page_data_report_create_page()
    {
        $response = $this->get('reporting/challenge-page-data/create');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_create_the_challenge_page_data_report()
    {

        $response = $this->post('reporting/challenge-page-data', []);

        $response->assertStatus(405);
    }
    public function test_an_anonymous_user_cannot_access_the_challenge_page_data_report_view_page()
    {
        $response = $this->get('reporting/challenge-page-data/1');
 
        $response->assertStatus(404);

    }
    public function test_an_anonymous_user_cannot_access_the_challenge_page_data_report_edit_page()
    {
        $response = $this->get('reporting/challenge-page-data/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_update_the_challenge_page_data_report()
    {

        $response = $this->put('reporting/challenge-page-data/1', [] );

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_delete_the_challenge_page_data_report()
    {
        $response = $this->delete('reporting/challenge-page-data/1');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_access_date_options()
    {

        $response = $this->json('get', "reporting/challenge-page-data/date-options", ['campaign_year' => 2024], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
  
        $response->assertStatus(401);
    }

    // Authorized User
    public function test_an_authorized_user_cannot_access_the_challenge_page_data_report_index_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/challenge-page-data');

        $response->assertStatus(403);
    }
    public function test_an_authorized_user_cannot_access_the_challenge_page_data_report_create_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/challenge-page-data/create');

        $response->assertStatus(404);
    }
    public function test_an_authorized_user_cannot_create_the_challenge_page_data_report()
    {

		$this->actingAs($this->user);
        $response = $this->post('reporting/challenge-page-data', []);

        $response->assertStatus(405);
    }
    public function test_an_authorized_user_cannot_access_the_challenge_page_data_report_view_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/challenge-page-data/1');

        $response->assertStatus(404);
    }
    public function test_an_authorized_user_cannot_access_the_challenge_page_data_report_edit_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/challenge-page-data/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_authorized_user_cannot_update_the_challenge_page_data_report()
    {
		$this->actingAs($this->user);
        $response = $this->put('reporting/challenge-page-data/1', [] );

        $response->assertStatus(404);
    }
    public function test_an_authorized_user_cannot_delete_the_challenge_page_data_report()
    {
		$this->actingAs($this->user);
        $response = $this->delete('reporting/challenge-page-data/1');

        $response->assertStatus(404);
    }
    public function test_an_authorized_user_cannot_access_date_options()
    {
        $this->actingAs($this->user);
        $response = $this->json('get', "reporting/challenge-page-data/date-options", ['campaign_year' => 2024], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
  
        $response->assertStatus(403);
    }
  

    // Administrator User
    public function test_an_administrator_can_access_the_challenge_page_data_report_index_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/challenge-page-data');

        $response->assertStatus(200);
    }
    public function test_an_administrator_can_access_the_challenge_page_data_report_create_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/challenge-page-data/create');

        $response->assertStatus(404);
    }
    public function test_an_administrator_can_create_the_challenge_page_data_report()
    {

        $this->actingAs($this->admin);
        $response = $this->post('reporting/challenge-page-data', []);

        $response->assertStatus(405);
    }
    public function test_an_administrator_cannot_access_the_challenge_page_data_report_view_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/challenge-page-data/1');

        $response->assertStatus(404);
    }
    public function test_an_administrator_cannot_access_the_challenge_page_data_report_edit_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/challenge-page-data/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_administrator_cannot_update_the_challenge_page_data_report()
    {
        $this->actingAs($this->admin);
        $response = $this->put('reporting/challenge-page-data/1', [] );

        $response->assertStatus(404);
    }
    public function test_an_administrator_cannot_delete_the_challenge_page_data_report()
    {
        $this->actingAs($this->admin);
        $response = $this->delete('reporting/challenge-page-data/1');

        $response->assertStatus(404);
    }
    public function test_an_administrator_can_access_date_options()
    {
        $this->actingAs($this->admin);
        $response = $this->json('get', "reporting/challenge-page-data/date-options", ['campaign_year' => 2024], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
  
        $response->assertStatus(200);
    }
   

}
