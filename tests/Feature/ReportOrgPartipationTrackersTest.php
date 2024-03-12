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
use Illuminate\Support\Facades\Storage;
use App\Exports\EligibleEmployeesExport;
use App\Exports\DonationDataReportExport;
use App\Models\BankDepositFormAttachments;
use App\Models\BankDepositFormOrganizations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReportOrgPartipationTrackersTest extends TestCase
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

        // $this->artisan('db:seed', ['--class' => 'PayCalendarSeeder']);

        // CampaignYear::truncate();
        // Region::truncate();
        // BusinessUnit::truncate();
        // Organization::truncate();
        // Charity::truncate();
        // FSPool::truncate();
        // FSPoolCharity::truncate();
        // EmployeeJob::truncate();

        // PledgeCharity::truncate();
        // Pledge::truncate();

        // BankDepositForm::truncate();
        // BankDepositFormOrganizations::truncate();
        // BankDepositFormAttachments::truncate();

        // PledgeStaging::truncate();
        // PledgeCharityStaging::truncate();

        ProcessHistory::truncate();

        $this->artisan('queue:prune-batches');
        $this->artisan('queue:clear');

        // clean up test files
        $filesInFolder = File::files( Storage::disk('public')->path('') );     
        foreach($filesInFolder as $file) { 
            if (str_contains( $file->getFilename(), 'OrgPartipationTracker_')) {
                // echo $file->getFilename() . PHP_EOL;
                File::delete( $file->getRealPath() );
            }
        } 

    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_the_org_partipation_tracker_report_index_page()
    {
        $response = $this->get('reporting/org-partipation-tracker');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_org_partipation_tracker_report_create_page()
    {
        $response = $this->get('reporting/org-partipation-tracker/create');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_create_the_org_partipation_tracker_report()
    {

        $response = $this->post('reporting/org-partipation-tracker', []);

        $response->assertStatus(405);
    }
    public function test_an_anonymous_user_cannot_access_the_org_partipation_tracker_report_view_page()
    {
        $response = $this->get('reporting/org-partipation-tracker/1');
 
        $response->assertStatus(302);
        $response->assertRedirect('login');

    }
    public function test_an_anonymous_user_cannot_access_the_org_partipation_tracker_report_edit_page()
    {
        $response = $this->get('reporting/org-partipation-tracker/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_update_the_org_partipation_tracker_report()
    {

        $response = $this->put('reporting/org-partipation-tracker/1', [] );

        $response->assertStatus(405);
    }
    public function test_an_anonymous_user_cannot_delete_the_org_partipation_tracker_report()
    {
        $response = $this->delete('reporting/org-partipation-tracker/1');

        $response->assertStatus(405);
    }

    public function test_an_anonymous_user_cannot_export_the_org_partipation_tracker_report()
    {
    
        $response = $this->get('/reporting/org-partipation-tracker/export', []);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function test_an_anonymous_user_cannot_request_export_progress()
    {
      
        $response = $this->get('/reporting/org-partipation-tracker/export-progress', []);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function test_an_anonymous_user_cannot_request_the_org_partipation_tracker_filtered_ids()
    {
      
        $response = $this->get('/reporting/org-partipation-tracker/filter-ids', []);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function test_an_anonymous_user_cannot_download_the_org_partipation_tracker_report()
    {
      
        $response = $this->get('/reporting/org-partipation-tracker/download-export-file/1', []);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }


    public function test_an_anonymous_user_cannot_download_the_org_partipation_tracker_report_as_zip()
    {
      
        $response = $this->get('/reporting/org-partipation-tracker/download-export-files-in-zip', []);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    // Authorized User
    public function test_an_authorized_user_cannot_access_the_org_partipation_tracker_report_index_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/org-partipation-tracker');

        $response->assertStatus(403);
    }
    public function test_an_authorized_user_cannot_access_the_org_partipation_tracker_report_create_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/org-partipation-tracker/create');

        $response->assertStatus(403);
    }
    public function test_an_authorized_user_cannot_create_the_org_partipation_tracker_report()
    {

		$this->actingAs($this->user);
        $response = $this->post('reporting/org-partipation-tracker', []);

        $response->assertStatus(405);
    }
    public function test_an_authorized_user_cannot_access_the_org_partipation_tracker_report_view_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/org-partipation-tracker/1');

        $response->assertStatus(403);
    }
    public function test_an_authorized_user_cannot_access_the_org_partipation_tracker_report_edit_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/org-partipation-tracker/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_authorized_user_cannot_update_the_org_partipation_tracker_report()
    {
		$this->actingAs($this->user);
        $response = $this->put('reporting/org-partipation-tracker/1', [] );

        $response->assertStatus(405);
    }
    public function test_an_authorized_user_cannot_delete_the_org_partipation_tracker_report()
    {
		$this->actingAs($this->user);
        $response = $this->delete('reporting/org-partipation-tracker/1');

        $response->assertStatus(405);
    }
    public function test_an_authorized_user_cannot_export_the_org_partipation_tracker_report()
    {
        $this->actingAs($this->user);
        $response = $this->get('/reporting/org-partipation-tracker/export', []);

        $response->assertStatus(403);

    }

    public function test_an_authorized_user_cannot_request_export_progress()
    {
		$this->actingAs($this->user);      
        $response = $this->get('/reporting/org-partipation-tracker/export-progress', []);

        $response->assertStatus(403);
    }

    public function test_an_authorized_user_cannot_request_the_org_partipation_tracker_filtered_ids()
    {
		$this->actingAs($this->user);      
        $response = $this->get('/reporting/org-partipation-tracker/filter-ids', []);

        $response->assertStatus(403);

    }

    public function test_an_authorized_user_cannot_download_the_org_partipation_tracker_report()
    {

        $this->actingAs($this->user);
        $response = $this->get('/reporting/org-partipation-tracker/download-export-file/1', []);

        $response->assertStatus(403);
    }

    public function test_an_authorized_user_cannot_download_the_org_partipation_tracker_report_as_zip()
    {
      
        $this->actingAs($this->user);
        $response = $this->get('/reporting/org-partipation-tracker/download-export-files-in-zip', []);

        $response->assertStatus(403);

    }



    // Administrator User
    public function test_an_administrator_can_access_the_org_partipation_tracker_report_index_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/org-partipation-tracker');

        $response->assertStatus(200);
    }
    public function test_an_administrator_can_access_the_org_partipation_tracker_report_create_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/org-partipation-tracker/create');

        $response->assertStatus(200);
    }
    public function test_an_administrator_can_create_the_org_partipation_tracker_report()
    {

        $this->actingAs($this->admin);
        $response = $this->post('reporting/org-partipation-tracker', []);

        $response->assertStatus(405);
    }
    public function test_an_administrator_cannot_access_the_org_partipation_tracker_report_view_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/org-partipation-tracker/1');

        $response->assertStatus(200);
    }
    public function test_an_administrator_cannot_access_the_org_partipation_tracker_report_edit_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/org-partipation-tracker/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_administrator_cannot_update_the_org_partipation_tracker_report()
    {
        $this->actingAs($this->admin);
        $response = $this->put('reporting/org-partipation-tracker/1', [] );

        $response->assertStatus(405);
    }
    public function test_an_administrator_cannot_delete_the_org_partipation_tracker_report()
    {
        $this->actingAs($this->admin);
        $response = $this->delete('reporting/org-partipation-tracker/1');

        $response->assertStatus(405);
    }
    public function test_an_administrator_can_export_the_org_partipation_tracker_report()
    {
    
        $this->actingAs($this->admin);

        $response = $this->get('/reporting/org-partipation-tracker/export', []);

        $response->assertStatus(200);

    }

    public function test_an_administrator_can_request_export_progress()
    {
      
        $this->actingAs($this->admin);
        $response = $this->get('/reporting/org-partipation-tracker/export-progress', []);

        $response->assertStatus(200);

    }

    public function test_an_administrator_can_request_the_org_partipation_tracker_filtered_ids()
    {
      
        $this->actingAs($this->admin);
        $response = $this->get('/reporting/org-partipation-tracker/filter-ids', []);

        $response->assertStatus(200);

    }

    public function test_an_administrator_can_download_the_org_partipation_tracker_report()
    {

        $history = $this->create_new_record_form_data();
        
        $this->actingAs($this->admin);
        $response = $this->get('/reporting/org-partipation-tracker/download-export-file/' . $history->id, []);

        $response->assertStatus(200);

    }


    public function test_an_administrator_can_download_the_org_partipation_tracker_report_as_zip()
    {
        // prepare 
        $history = $this->create_new_record_form_data();
        $ids = json_encode( [$history->id] );

        $this->actingAs($this->admin);
        $response = $this->get('/reporting/org-partipation-tracker/download-export-files-in-zip?ids='. $ids);

        $response->assertStatus(200);

    }
   

    // submit a process to generate the excel file
//     public function test_an_administrator_can_download_eligible_employees_report_success()
//     {
  
//         $form_data = [
//             'year' => 2024,
//             'as_of_date' => '2023-10-15',
//             'emplid' => null,
//             'name' => null,
//             'office_city' => null,
//             'organization' => null,
//             'business_unit' => null,
//             'department' => null,
//             'tgb_reg_district' => null,
//         ];
 
//         $this->actingAs($this->admin);
//         $response = $this->json('get', '/reporting/org-partipation-tracker/export', $form_data,
//                                      ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
 
//         $response->assertStatus(200);
 
//         $context = json_decode($response->getContent());
//         $batch_id = $context->batch_id;
//         $this->assertDatabaseHas('process_histories', ['id' => $batch_id ] );
 
//         $history = ProcessHistory::where('id', $batch_id)->first();
//         $filters = json_decode( $history->parameters , true);

//         // $file = UploadedFile::fake()->create( $history->filename)
//         //                 ->storeAs('app/public', $history->filename);
 
//         // export data
//         // Excel::store(new PledgeCharitiesExport($history->id, $filters), 'public/'. $history->filename);  
//         $this->actingAs($this->admin);
//         $response = $this->get('/reporting/org-partipation-tracker/download-export-file/' . $history->id, []);
//         $response->assertStatus(200);
//         $response->assertDownload($history->filename);
 
//  //         $download = $response->streamedContent();
//  //         $results = Excel::load( $response->streamedContent() );
//  // dd($response->streamedContent());
//          $this->assertTrue($response->headers->get('content-type') == 'application/csv');
//          $this->assertTrue($response->headers->get('content-disposition') == "attachment; filename=" . $history->filename);
 
//      }

    /* Private Function */
    private function create_new_record_form_data() 
    {

        $filename = 'OrgPartipationTracker_' . today()->format('Y') .'_BGCPE_' . today()->format('Y-m-d') . '.xlsx';
        Storage::disk('public')->put($filename, 'dummy data');

        $history = ProcessHistory::create([
            'process_name' => 'OrgPartipationTractor',
            'status' => 'Completed',
            'submitted_at' => now(),
            'start_at' => now(),
            'end_at' => now(),
            'original_filename' => $filename,
            'filename' => $filename,
        ]);


        // $organization = Organization::factory()->create([
        //             'code' => "GOV",
        // ]);
        // $business = BusinessUnit::factory()->create();
        // $region = Region::factory()->create();

        // $job = EmployeeJob::factory()->create([
        //     "organization_id" => $organization->id,
        //     // "emplid" => $user->emplid,
        //     "business_unit" => $business->code,
        //     "business_unit_id" => $business->id,
        //     "tgb_reg_district" =>  $region->code,
        //     "region_id" => $region->id,
        // ]);


        // var_dump($pledge->charities->all());
        // dd('test');
        return $history;

    }

}
