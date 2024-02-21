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
use App\Models\Announcement;
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

class AnnouncementTest extends TestCase
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

        }

        $this->admin = User::whereHas("roles", function ($q) {$q->where("name", "admin");})->first();
        $this->user  = User::doesntHave('roles')->first();

        Announcement::truncate(); 

    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_admin_announcement_index_page()
    {
        $response = $this->get('/system/announcement');

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }
    public function test_an_anonymous_user_cannot_access_the_admin_announcement_create_page()
    {
        $response = $this->get('/system/announcement/create');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_create_the_admin_announcement()
    {
        $response = $this->post('/system/announcement', []);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_admin_announcement_view_page()
    {
        $response = $this->get('/system/announcement/1');

        $response->assertStatus(404);
    }
    public function test_an_anonymous_user_cannot_access_the_admin_announcement_edit_page()
    {
        $response = $this->get('/system/announcement/1/edit');

        $response->assertStatus(404);
        
    }
    public function test_an_anonymous_user_cannot_update_the_admin_announcement()
    {

        $response = $this->post('/system/announcement/update', [] );

        $response->assertStatus(404);
    }

    public function test_an_anonymous_user_cannot_delete_the_admin_announcement()
    {
        $response = $this->get('/system/announcement/1/detele/filename');

        $response->assertStatus(404);

    }

    public function test_an_anonymous_user_cannot_upload_media_via_ajax_call()
    {
        
        $response = $this->call('post', '/system/image-upload',[]);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    //
    // Test Authorized User
    //
    public function test_an_authorized_user_cannot_access_admin_announcement_index_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('/system/announcement');

        $response->assertStatus(403);

    }
    public function test_an_authorized_user_cannot_access_the_admin_announcement_create_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('/system/announcement/create');

        $response->assertStatus(404);
        
    }
    public function test_an_authorized_user_cannot_create_the_admin_announcement()
    {
		$this->actingAs($this->user);
        $response = $this->post('/system/announcement', []);

        $response->assertStatus(403);

    }
    public function test_an_authorized_user_cannot_access_the_admin_announcement_view_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('/system/announcement/1');

        $response->assertStatus(404);
    }
    public function test_an_authorized_user_cannot_access_the_admin_announcement_edit_page()
    {
		$this->actingAs($this->user);
        $response = $this->get('/system/announcement/1/edit');

        $response->assertStatus(404);
        
    }
    public function test_an_authorized_user_cannot_update_the_admin_announcement()
    {
		$this->actingAs($this->user);
        $response = $this->post('/system/announcement/update', [] );

        $response->assertStatus(404);
    }

    public function test_an_authorized_user_cannot_delete_the_admin_announcement()
    {
		$this->actingAs($this->user);
        $response = $this->get('/system/announcement/1/detele/filename');

        $response->assertStatus(404);

    }

    public function test_an_authorized_user_cannot_upload_media_via_ajax_call()
    {
        $this->actingAs($this->user);
        $response = $this->call('post', '/system/image-upload',[]);

        $response->assertStatus(403);


    }

    //
    // Test Administrator
    //
    public function test_an_administrator_can_access_admin_announcement_index_page()
    {

        // $campaign_year = CampaignYear::create([
        //     'calendar_year' => today()->year + 1,
        //     'status' => 'A',
        //     'start_date' => today(),
        //     'end_date' => today()->year . '-12-31',
        //     'number_of_periods' => 26,
        //     'close_date' => today()->year . '-12-31',
        // ]);

        $this->actingAs($this->admin);
        $response = $this->get('/system/announcement');

        $response->assertStatus(200);
        $response->assertSeeText("Announcement");
        $response->assertSeeText("Title");
        $response->assertSeeText("Status");

    }

    public function test_an_administrator_can_access_the_admin_announcement_create_page()
    {

        $this->actingAs($this->admin);
        $response = $this->get('/system/announcement/create');

        $response->assertStatus(404);
        
    }

    public function test_an_administrator_can_create_announcement_successful_in_db()
    {

        [$form_data, $pledge] = $this->get_new_record_form_data(false);
        
        $this->actingAs($this->admin);
        $response = $this->json('post', '/system/announcement', $form_data, 
                                    ['HTTP_X-Requested-With' => 'XMLHttpRequest', ]);

        $response->assertStatus(302);
        $response->assertSessionHas("success",  "The announcement was successfully saved.");
        $this->assertDatabaseHas('announcements', Arr::except( $pledge->attributesToArray(), []) );

        $this->delete_file_in_temp_folder($pledge);
     }

    // public function test_an_administrator_can_access_the_admin_announcement_view_page()
    // {
    //     [$form_data, $pledge] = $this->get_new_record_form_data(true);

    //     $this->actingAs($this->admin);
    //     $response = $this->call('get', '/system/announcement/' . $pledge->id ,[], [], [],
    //     [
    //         'HTTP_X-Requested-With'=> 'XMLHttpRequest',
    //         'Accept' => 'text/html',
    //     ]);
    //     $this->delete_file_in_temp_folder($pledge);

    //     $response->assertStatus(200);
    //     $response->assertSee( $pledge->description);
    // }

    // public function test_an_administrator_cannot_access_the_admin_announcement_edit_page()
    // {
    //     $this->actingAs($this->admin);
    //     $response = $this->get('/system/announcement/1/edit');

    //     $response->assertStatus(404);
 
    // }

    // public function test_an_administrator_cannot_update_the_admin_announcement_in_db()
    // {

    //     $this->actingAs($this->admin);
    //     $response = $this->put('/system/announcement/1', []);

    //     $response->assertStatus(405);
    // }

    // public function test_an_administrator_cannot_delete_the_admin_announcement_in_db()
    // {
    //     $this->actingAs($this->admin);
    //     $response = $this->delete('/system/announcement/1');

    //     $response->assertStatus(405);

    // }
   

    /* Private Function */
    private function get_new_record_form_data($bCreate = false, $is_gov = true) 
    {

        

        // Test Transaction
        $record = new Announcement([
            'title' =>  "This is a title",
            'body' =>  "This is a body",
            'status' => "A",
            'start_date' => '2024-01-01',
            'end_date' => today(),
            
        ]);

        $form_data = $this->tranform_to_form_data($record);

        if ($bCreate) {
            // create 
            // $pledge->save();
            $record->save();  // save your model and all of its associated relationships
        }

        // var_dump($pledge->charities->all());
        // dd('test');

        return [$form_data, $record];
    }

    private function tranform_to_form_data($rec)
    {

        $form_data = [
            'title' =>  $rec->title, 
            'body' =>  $rec->body, 
            'status' => $rec->status, 
            'start_date' => $rec->start_date,
            'end_date' => $rec->end_date,
            
        ];

        return $form_data;

    } 

    private function delete_file_in_temp_folder($rec) {

       

    }

}
