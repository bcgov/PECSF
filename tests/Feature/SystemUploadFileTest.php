<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Setting;
use App\Models\ScheduleJobAudit;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


class SystemUploadFileTest extends TestCase
{
    // protected static $initialized = FALSE;

    private User $admin;
    private User $user;
    private User $sysadmin;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::whereHas("roles", function ($q) {$q->where("name", "admin");})->first();
        $this->user  = User::doesntHave('roles')->first();
        $this->sysadmin = User::where('id', 998)->first();
        $this->sysadmin->source_type  = 'HCM';
        $this->sysadmin->save();
        $this->sysadmin->assignRole('sysadmin');

    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_system_upload_files_index_page()
    {
        $response = $this->get('system/upload-files');

        $response->assertStatus(403);
    }
    public function test_an_anonymous_user_cannot_access_the_system_upload_files_create_page()
    {
        $response = $this->get('system/upload-files/create');

        $response->assertStatus(403);
    }
    public function test_an_anonymous_user_cannot_create_the_system_upload_files_in_db()
    {

        $response = $this->post('system/upload-files', []);

        $response->assertStatus(403);
    }
    public function test_an_anonymous_user_cannot_access_the_system_upload_files_view_page()
    {
        $response = $this->get('system/upload-files/1');

        $response->assertStatus(403);
    }
    public function test_an_anonymous_user_cannot_access_the_system_upload_files_edit_page()
    {
        $response = $this->get('system/upload-files/1/edit');

        $response->assertStatus(404);

    }
    public function test_an_anonymous_user_cannot_update_the_system_settings()
    {

        $response = $this->put('system/upload-files/1', [] );

        $response->assertStatus(405);

    }

    public function test_an_anonymous_user_cannot_delete_the_system_settings()
    {
        $response = $this->delete('system/upload-files/1');

        $response->assertStatus(405);

    }

   

    // Unauthorized user
    public function test_an_unauthorized_user_cannot_access_system_upload_files_index_page()
    {
        $this->actingAs($this->user);

        $response = $this->get('system/upload-files');
        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_system_upload_files_create_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('system/upload-files/create');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_create_the_system_upload_files()
    {
       
        $this->actingAs($this->user);
        $response = $this->post('system/upload-files', []);

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_system_upload_files_view_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('system/upload-files/1');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_system_upload_files_edit_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('system/upload-files/1/edit');

        $response->assertStatus(404);
    }
    public function test_an_unauthorized_user_cannot_update_the_system_upload_files()
    {
        $this->actingAs($this->user);
        $response = $this->put('system/upload-files/1', [] );

        $response->assertStatus(405);
    }
    public function test_an_unauthorized_user_cannot_delete_the_system_settings()
    {
        $this->actingAs($this->user);
        $response = $this->delete('system/upload-files/1');

        $response->assertStatus(405);
    }

    
   

     // Administrator
     public function test_an_administrator_cannot_access_system_upload_files_index_page()
     {

        $this->actingAs($this->admin);
        $response = $this->get('system/upload-files');
  
        $response->assertStatus(403);
     }
     public function test_an_administrator_cannot_access_the_system_upload_files_create_page()
     {
         $this->actingAs($this->admin);
         $response = $this->get('system/upload-files/create');
 
         $response->assertStatus(403);
     }
     public function test_an_administrator_cannot_create_the_system_upload_files()
     {
        $this->actingAs($this->admin);
        $response = $this->post('system/upload-files',[] );

        $response->assertStatus(403);
 
     }
     public function test_an_administrator_cannot_access_the_system_upload_files_view_page()
     {

        $this->actingAs($this->admin);
        $response = $this->json('get', '/system/upload-files/1', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
 
        $response->assertStatus(403);

     }
     public function test_an_administrator_cannot_access_the_system_upload_files_edit_page()
     {
         $this->actingAs($this->admin);
         $response = $this->get('system/upload-files/1/edit');
 
         $response->assertStatus(404);
     }
     public function test_an_administrator_cannot_update_the_system_upload_files()
     {
         $this->actingAs($this->admin);
         $response = $this->put('system/upload-files/1', [] );
 
         $response->assertStatus(405);
     }
     public function test_an_administrator_cannot_delete_the_system_upload_files()
     {

        $this->actingAs($this->admin);
        $response = $this->delete('system/upload-files/1');

        $response->assertStatus(405);
     }


     // System administrtaor
     public function test_an_system_administartor_can_access_system_upload_files_index_page()
     {

        $this->actingAs($this->sysadmin);
        $response = $this->get('system/upload-files');
  
        $response->assertStatus(200);
     }
    //  public function test_an_system_administartor_can_access_the_system_upload_files_create_page()
    //  {
    //      $this->actingAs($this->sysadmin);
    //      $response = $this->get('system/upload-files/create');
 
    //      $response->assertStatus(403);
    //  }
     public function test_an_system_administartor_can_create_the_system_upload_files()
     {

        $file = UploadedFile::fake()->image('document.jpg');

        $this->actingAs($this->sysadmin);
        $response = $this->post('system/upload-files',['uploaded_file' => $file] );

        $response->assertStatus(302);
        $response->assertSessionHas("success",  'File with name "document.jpg" have been updated successfully');

        Storage::disk('uploads')->assertExists('document.jpg');
        Storage::disk('uploads')->delete('document.jpg');
 
     }
    //  public function test_an_system_administartor_can_access_the_system_upload_files_view_page()
    //  {

    //     $this->actingAs($this->sysadmin);
    //     $response = $this->json('get', '/system/upload-files/1', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
 
    //     $response->assertStatus(403);

    //  }
     public function test_an_system_administartor_cannot_access_the_system_upload_files_edit_page()
     {
         $this->actingAs($this->sysadmin);
         $response = $this->get('system/upload-files/1/edit');
 
         $response->assertStatus(404);
     }
     public function test_an_system_administartor_cannot_update_the_system_upload_files()
     {
         $this->actingAs($this->sysadmin);
         $response = $this->put('system/upload-files/1', [] );
 
         $response->assertStatus(405);
     }
     public function test_an_system_administartor_cannot_delete_the_system_upload_files()
     {

        $this->actingAs($this->sysadmin);
        $response = $this->delete('system/upload-files/1');

        $response->assertStatus(405);
     }

   

}
