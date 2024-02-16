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

class ReportCRACharityReportTest extends TestCase
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
    public function test_an_anonymous_user_cannot_access_the_cra_charity_report_index_page()
    {
        $response = $this->get('reporting/cra-charities');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_cra_charity_report_create_page()
    {
        $response = $this->get('reporting/cra-charities/create');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_create_the_cra_charity_report()
    {
        $response = $this->post('reporting/cra-charities', []);

        $response->assertStatus(405);
    }
    public function test_an_anonymous_user_cannot_access_the_cra_charity_report_view_page()
    {
        $response = $this->get('reporting/cra-charities/1');
 
        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_cra_charity_report_edit_page()
    {
        $response = $this->get('reporting/cra-charities/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_update_the_cra_charity_report()
    {
        $response = $this->put('reporting/cra-charities/1', [] );

        $response->assertStatus(405);
    }
    public function test_an_anonymous_user_cannot_delete_the_cra_charity_report()
    {
        $response = $this->delete('reporting/cra-charities/1');

        $response->assertStatus(405);
    }
    public function test_an_anonymous_user_cannot_download_cra_charity_reports()
    {     
        $response = $this->get('/reporting/cra-charities/download-export-file/1', []);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    // Authorized User
    public function test_an_authorized_user_cannot_access_the_cra_charity_report_index_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/cra-charities');

        $response->assertStatus(403);
    }
    public function test_an_authorized_user_cannot_access_the_cra_charity_report_create_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/cra-charities/create');

        $response->assertStatus(403);
    }
    public function test_an_authorized_user_cannot_create_the_cra_charity_report()
    {
		$this->actingAs($this->user);
        $response = $this->post('reporting/cra-charities', []);

        $response->assertStatus(405);
    }
    public function test_an_authorized_user_cannot_access_the_cra_charity_report_view_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/cra-charities/1');

        $response->assertStatus(403);
    }
    public function test_an_authorized_user_cannot_access_the_cra_charity_report_edit_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/cra-charities/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_authorized_user_cannot_update_the_cra_charity_report()
    {
		$this->actingAs($this->user);
        $response = $this->put('reporting/cra-charities/1', [] );

        $response->assertStatus(405);
    }
    public function test_an_authorized_user_cannot_delete_the_cra_charity_report()
    {
		$this->actingAs($this->user);
        $response = $this->delete('reporting/cra-charities/1');

        $response->assertStatus(405);
    }
    public function test_an_authorized_user_cannot_download_cra_charity_report()
    {      
        $this->actingAs($this->user);
        $response = $this->get('/reporting/cra-charities/download-export-file/1', []);

        $response->assertStatus(403);
    }
  

    // Administrator User
    public function test_an_administrator_can_access_the_cra_charity_report_index_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/cra-charities');

        $response->assertStatus(200);
    }
    public function test_an_administrator_can_access_the_cra_charity_report_create_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/cra-charities/create');

        $response->assertStatus(200);
    }
    public function test_an_administrator_can_create_the_cra_charity_report()
    {

        $this->actingAs($this->admin);
        $response = $this->post('reporting/cra-charities', []);

        $response->assertStatus(405);
    }
    public function test_an_administrator_cannot_access_the_cra_charity_report_view_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/cra-charities/1');

        $response->assertStatus(200);
    }
    public function test_an_administrator_cannot_access_the_cra_charity_report_edit_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/cra-charities/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_administrator_cannot_update_the_cra_charity_report()
    {
        $this->actingAs($this->admin);
        $response = $this->put('reporting/cra-charities/1', [] );

        $response->assertStatus(405);
    }
    public function test_an_administrator_cannot_delete_the_cra_charity_report()
    {
        $this->actingAs($this->admin);
        $response = $this->delete('reporting/cra-charities/1');

        $response->assertStatus(405);
    }

   
    public function test_an_administrator_can_download_cra_charity_report()
    {      
        // $this->actingAs($this->admin);
        // $response = $this->get('/reporting/cra-charities/download-export-file/1', []);

        $form_data = [
            'year' => 2024,
            'as_of_date' => '2023-10-15',
            'emplid' => null,
            'name' => null,
            'office_city' => null,
            'organization' => null,
            'business_unit' => null,
            'department' => null,
            'tgb_reg_district' => null,
        ];
 
        $this->actingAs($this->admin);
        $response = $this->json('get', '/reporting/cra-charities/export', $form_data,
                                     ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
 
        $response->assertStatus(200);
 
        $context = json_decode($response->getContent());
        $batch_id = $context->batch_id;
        $this->assertDatabaseHas('process_histories', ['id' => $batch_id ] );
 
        $history = ProcessHistory::where('id', $batch_id)->first();
        $filters = json_decode( $history->parameters , true);

        $file = UploadedFile::fake()->create($history->filename)
                            ->storeAs('app/public', $history->filename);


        // $file = UploadedFile::fake()->create( $history->filename)
        //                 ->storeAs('app/public', $history->filename);
 
        // export data
        // Excel::store(new PledgeCharitiesExport($history->id, $filters), 'public/'. $history->filename);  
        $this->actingAs($this->admin);
        $response = $this->get('/reporting/eligible-employees/download-export-file/' . $history->id, ['as_of_date' => '2024-02-15']);
        $response->assertStatus(200);
        $response->assertDownload($history->filename);
 
 //         $download = $response->streamedContent();
 //         $results = Excel::load( $response->streamedContent() );
 // dd($response->streamedContent());
         $this->assertTrue($response->headers->get('content-type') == 'application/csv');
         $this->assertTrue($response->headers->get('content-disposition') == "attachment; filename=" . $history->filename);

    }

}
