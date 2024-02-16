<?php

namespace Tests\Feature;

use File;
use finfo;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;

use App\Models\Donation;

use App\Models\Organization;

use App\Models\ProcessHistory;

use Illuminate\Http\UploadedFile;
use App\Models\DailyCampaignSummary;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Artisan;
use App\Exports\DonationDataReportExport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReportDonationReportTest extends TestCase
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

        Organization::truncate();
        Donation::truncate();

        ProcessHistory::truncate();

        $this->artisan('queue:prune-batches');
        $this->artisan('queue:clear');

    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_the_donation_report_index_page()
    {
        $response = $this->get('reporting/donation-report');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_donation_report_create_page()
    {
        $response = $this->get('reporting/donation-report/create');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_create_the_donation_report()
    {

        $response = $this->post('reporting/donation-report', []);

        $response->assertStatus(405);
    }
    public function test_an_anonymous_user_cannot_access_the_donation_report_view_page()
    {
        $response = $this->get('reporting/donation-report/1');
 
        $response->assertStatus(302);
        $response->assertRedirect('login');

    }
    public function test_an_anonymous_user_cannot_access_the_donation_report_edit_page()
    {
        $response = $this->get('reporting/donation-report/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_update_the_donation_report()
    {

        $response = $this->put('reporting/donation-report/1', [] );

        $response->assertStatus(405);
    }
    public function test_an_anonymous_user_cannot_delete_the_donation_report()
    {
        $response = $this->delete('reporting/donation-report/1');

        $response->assertStatus(405);
    }
    public function test_an_anonymous_user_cannot_download_donations_report()
    {
      
        $response = $this->get('/reporting/donation-report/download-export-file/1', []);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
 

    // Authorized User
    public function test_an_authorized_user_cannot_access_the_donation_report_index_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/donation-report');

        $response->assertStatus(403);
    }
    public function test_an_authorized_user_cannot_access_the_donation_report_create_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/donation-report/create');

        $response->assertStatus(403);
    }
    public function test_an_authorized_user_cannot_create_the_donation_report()
    {

		$this->actingAs($this->user);
        $response = $this->post('reporting/donation-report', []);

        $response->assertStatus(405);
    }
    public function test_an_authorized_user_cannot_access_the_donation_report_view_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/donation-report/1');

        $response->assertStatus(403);
    }
    public function test_an_authorized_user_cannot_access_the_donation_report_edit_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/donation-report/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_authorized_user_cannot_update_the_donation_report()
    {
		$this->actingAs($this->user);
        $response = $this->put('reporting/donation-report/1', [] );

        $response->assertStatus(405);
    }
    public function test_an_authorized_user_cannot_delete_the_donation_report()
    {
		$this->actingAs($this->user);
        $response = $this->delete('reporting/donation-report/1');

        $response->assertStatus(405);
    }
    public function test_an_authorized_user_cannot_download_donations_report()
    {
        $this->actingAs($this->user);
        $response = $this->get('/reporting/donation-report/download-export-file/1', []);

        $response->assertStatus(403);
    }
  

    // Administrator User
    public function test_an_administrator_can_access_the_donation_report_index_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/donation-report');

        $response->assertStatus(200);
    }
    public function test_an_administrator_can_access_the_donation_report_create_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/donation-report/create');

        $response->assertStatus(200);
    }
    public function test_an_administrator_can_create_the_donation_report()
    {

        $this->actingAs($this->admin);
        $response = $this->post('reporting/donation-report', []);

        $response->assertStatus(405);
    }
    public function test_an_administrator_cannot_access_the_donation_report_view_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/donation-report/1');

        $response->assertStatus(200);
    }
    public function test_an_administrator_cannot_access_the_donation_report_edit_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/donation-report/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_administrator_cannot_update_the_donation_report()
    {
        $this->actingAs($this->admin);
        $response = $this->put('reporting/donation-report/1', [] );

        $response->assertStatus(405);
    }
    public function test_an_administrator_cannot_delete_the_donation_report()
    {
        $this->actingAs($this->admin);
        $response = $this->delete('reporting/donation-report/1');

        $response->assertStatus(405);
    }

    // submit a process to generate the excel file
    public function test_an_administrator_can_download_donation_report_success()
    {
 
        Organization::factory()->create(['code' => 'BCS', ]);
        Organization::factory()->create(['code' => 'LA', ]);
        Organization::factory()->create(['code' => 'LDB', ]);

        
        $this->actingAs($this->admin);
        $response = $this->json('get', '/reporting/donation-report/export', ['year' => 2024],
                                    ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);

       $context = json_decode($response->getContent());
       $batch_id = $context->batch_id;
       $this->assertDatabaseHas('process_histories', ['id' => $batch_id ] );

       $history = ProcessHistory::where('id', $batch_id)->first();
       $filters = json_decode( $history->parameters , true);


        Donation::factory(50)->create([
            'yearcd' => 2024,
            'process_history_id' => $history->id, 
        ]);

       // export data
       Excel::store(new DonationDataReportExport($history->id, $filters), 'public/'. $history->filename);  

        $response = $this->get('/reporting/donation-report/download-export-file/' . $history->id, []);
        $response->assertStatus(200);
        $response->assertDownload($history->filename);

//         $download = $response->streamedContent();
//         $results = Excel::load( $response->streamedContent() );
// dd($response->streamedContent());
        $this->assertTrue($response->headers->get('content-type') == 'application/csv');
        $this->assertTrue($response->headers->get('content-disposition') == "attachment; filename=" . $history->filename);

    }



}
