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

class ReportPledgeCharityReportTest extends TestCase
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
    public function test_an_anonymous_user_cannot_access_the_pledge_charity_report_index_page()
    {
        $response = $this->get('reporting/pledge-charities');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_pledge_charity_report_create_page()
    {
        $response = $this->get('reporting/pledge-charities/create');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_create_the_pledge_charity_report()
    {

        $response = $this->post('reporting/pledge-charities', []);

        $response->assertStatus(405);
    }
    public function test_an_anonymous_user_cannot_access_the_pledge_charity_report_view_page()
    {
        $response = $this->get('reporting/pledge-charities/1');
 
        $response->assertStatus(302);
        $response->assertRedirect('login');

    }
    public function test_an_anonymous_user_cannot_access_the_pledge_charity_report_edit_page()
    {
        $response = $this->get('reporting/pledge-charities/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_update_the_pledge_charity_report()
    {

        $response = $this->put('reporting/pledge-charities/1', [] );

        $response->assertStatus(405);
    }
    public function test_an_anonymous_user_cannot_delete_the_pledge_charity_report()
    {
        $response = $this->delete('reporting/pledge-charities/1');

        $response->assertStatus(405);
    }
 

    // Authorized User
    public function test_an_authorized_user_cannot_access_the_pledge_charity_report_index_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/pledge-charities');

        $response->assertStatus(403);
    }
    public function test_an_authorized_user_cannot_access_the_pledge_charity_report_create_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/pledge-charities/create');

        $response->assertStatus(403);
    }
    public function test_an_authorized_user_cannot_create_the_pledge_charity_report()
    {

		$this->actingAs($this->user);
        $response = $this->post('reporting/pledge-charities', []);

        $response->assertStatus(405);
    }
    public function test_an_authorized_user_cannot_access_the_pledge_charity_report_view_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/pledge-charities/1');

        $response->assertStatus(403);
    }
    public function test_an_authorized_user_cannot_access_the_pledge_charity_report_edit_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('reporting/pledge-charities/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_authorized_user_cannot_update_the_pledge_charity_report()
    {
		$this->actingAs($this->user);
        $response = $this->put('reporting/pledge-charities/1', [] );

        $response->assertStatus(405);
    }
    public function test_an_authorized_user_cannot_delete_the_pledge_charity_report()
    {
		$this->actingAs($this->user);
        $response = $this->delete('reporting/pledge-charities/1');

        $response->assertStatus(405);
    }
  

    // Administrator User
    public function test_an_administrator_can_access_the_pledge_charity_report_index_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/pledge-charities');

        $response->assertStatus(200);
    }
    public function test_an_administrator_can_access_the_pledge_charity_report_create_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/pledge-charities/create');

        $response->assertStatus(200);
    }
    public function test_an_administrator_can_create_the_pledge_charity_report()
    {

        $this->actingAs($this->admin);
        $response = $this->post('reporting/pledge-charities', []);

        $response->assertStatus(405);
    }
    public function test_an_administrator_cannot_access_the_pledge_charity_report_view_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/pledge-charities/1');

        $response->assertStatus(200);
    }
    public function test_an_administrator_cannot_access_the_pledge_charity_report_edit_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('reporting/pledge-charities/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_administrator_cannot_update_the_pledge_charity_report()
    {
        $this->actingAs($this->admin);
        $response = $this->put('reporting/pledge-charities/1', [] );

        $response->assertStatus(405);
    }
    public function test_an_administrator_cannot_delete_the_pledge_charity_report()
    {
        $this->actingAs($this->admin);
        $response = $this->delete('reporting/pledge-charities/1');

        $response->assertStatus(405);
    }

    // submit a process to generate the excel file
    public function test_an_administrator_can_download_donation_report_success()
    {
 
        $pledge = $this->create_new_record_form_data(true, true);
        $year = $pledge->campaign_year->calendar_year;

        $this->actingAs($this->admin);
        $response = $this->json('get', '/reporting/pledge-charities/export', ['year' => $year],
                                    ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);

       $context = json_decode($response->getContent());
       $batch_id = $context->batch_id;
       $this->assertDatabaseHas('process_histories', ['id' => $batch_id ] );

       $history = ProcessHistory::where('id', $batch_id)->first();
       $filters = json_decode( $history->parameters , true);

       // export data
       Excel::store(new PledgeCharitiesExport($history->id, $filters), 'public/'. $history->filename);  

        $response = $this->get('/reporting/pledge-charities/download-export-file/' . $history->id, []);
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
            'status' => 'A',
            'start_date' => today(),
            'end_date' => today()->year . '-12-31',
            'number_of_periods' => 26,
            'close_date' => today()->year . '-12-31',
        ]);


        $organization = Organization::factory()->create([
                    'code' => "GOV",
                    'status' => 'A',
        ]);
        // if (!($is_gov)) {
            $organization = Organization::factory()->create([
                    'code' => "LDB",
                    'status' => 'A',
                ]);
        // }
        $business = BusinessUnit::factory()->create();
        $region = Region::factory()->create();
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
        ]);

        // Test Transaction
        $pledge = new Pledge([
            'organization_id' =>  $organization->id,
            'emplid' => $is_gov ? $user->emplid : null,
            'user_id' => $is_gov ? $user->id : 0, 
            'pecsf_id' => $is_gov ? null : '882288',
            'business_unit' => $is_gov ? $job->business_unit : $business->code,
            'tgb_reg_district' => $is_gov ? $job->tgb_reg_district : $region->code,
            'deptid' => $is_gov  ? $job->deptid : null,
            'dept_name' => $is_gov  ? $job->dept_name : null,
            'first_name' => null,
            'last_name' => null,
            'city' => $job->office_city,
            'campaign_year_id' => $campaign_year->id,
            'type' => "C",
            'region_id' => null,
            'f_s_pool_id' => 0,
            'one_time_amount' => 50.0,
            'pay_period_amount' => 20.0,
            'goal_amount' => 570.0,
        ]);

        foreach ($charities as $key => $charity) {
            $text = $this->faker->words(2, true);

            $item = new PledgeCharity([
                        'charity_id' => $charity->id,
                        'pledge_id' => $pledge->id ?? 1,
                        'frequency' => 'one-time',
                        'additional' => $text,
                        'percentage' => 10,
                        'amount' => $pledge->one_time_amount * 0.1,
                        'goal_amount' => $pledge->one_time_amount * 0.1,
                    ]);
            $pledge->charities[$key] = $item; 

            $item = new PledgeCharity([
                'charity_id' => $charity->id,
                    'pledge_id' => $pledge->id ?? 1,
                    'frequency' => 'bi-weekly',
                    'additional' => $text,
                    'percentage' => 10,
                    'amount' => $pledge->pay_period_amount * 0.1,
                    'goal_amount' => ($pledge->goal_amount - $pledge->one_time_amount) * 0.1,
                ]);
            $pledge->charities[$key + 10] = $item; 
        }

        // $form_data = $this->tranform_to_form_data($pledge);


        if ($bCreate) {
            // create 
            // $pledge->save();
            $pledge->push();  // save your model and all of its associated relationships
        }

        // var_dump($pledge->charities->all());
        // dd('test');
        return $pledge;

    }

}
