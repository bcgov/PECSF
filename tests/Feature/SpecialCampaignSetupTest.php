<?php

namespace Tests\Feature;

use File;
use Tests\TestCase;
use App\Models\User;
use App\Models\Charity;
use Illuminate\Support\Arr;
use App\Models\SpecialCampaign;
use Illuminate\Http\UploadedFile;
use App\Models\SpecialCampaignPledge;
use Illuminate\Testing\Fluent\AssertableJson;

class SpecialCampaignSetupTest extends TestCase
{

    // protected static $initialized = FALSE;

    private User $admin;
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::whereHas("roles", function ($q) {$q->where("name", "admin");})->first();
        $this->user  = User::doesntHave('roles')->first();

        Charity::truncate();
        SpecialCampaign::truncate();
        SpecialCampaignPledge::truncate();
    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_special_campaign_index_page()
    {
        $response = $this->get('/settings/special-campaigns');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_special_campaign_create_page()
    {
        $response = $this->get('/settings/special-campaigns/create');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_create_the_special_campaign()
    {
        $form_data = $this->get_new_record_form_data();

        $response = $this->post('/settings/special-campaigns', $form_data);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_special_campaign_view_page()
    {
        $response = $this->get('/settings/special-campaigns/1');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_special_campaign_edit_page()
    {
        $response = $this->get('/settings/special-campaigns/1/edit');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_update_the_special_campaign()
    {

        $response = $this->put('/settings/special-campaigns/1', [] );

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function test_an_anonymous_user_cannot_delete_the_special_campaign()
    {
        $response = $this->delete('/settings/special-campaigns/1');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }


    public function test_an_unauthorized_user_cannot_access_special_campaign_index_page()
    {
        $this->actingAs($this->user);

        $response = $this->get('/settings/special-campaigns');
        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_special_campaign_create_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/special-campaigns/create');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_create_the_special_campaign()
    {
        $form_data = $this->get_new_record_form_data();

        $this->actingAs($this->user);
        $response = $this->post('/settings/special-campaigns', $form_data);

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_special_campaign_view_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/special-campaigns/1');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_special_campaign_edit_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/special-campaigns/1/edit');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_update_the_special_campaign()
    {
        $this->actingAs($this->user);
        $response = $this->put('/settings/special-campaigns/1', [] );

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_delete_the_special_campaign()
    {
        $this->actingAs($this->user);
        $response = $this->delete('/settings/special-campaigns/1');

        $response->assertStatus(403);
    }

    public function test_an_administrator_can_access_special_campaign_index_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/settings/special-campaigns');

        $response->assertStatus(200);

    }
    // public function test_an_administrator_can_access_the_special_campaign_create_page()
    // {
    //     $this->actingAs($this->admin);
    //     $response = $this->get('/settings/special-campaigns/create');

    //     $response->assertStatus(200);
    // }
    public function test_an_administrator_can_create_special_campaign_successful_in_db()
    {
        // $cy = SpecialCampaign::factory(1)->create();
        $form_data = $this->get_new_record_form_data();
 
        $this->actingAs($this->admin);
        $response = $this->post('/settings/special-campaigns',  $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        // grab the file name since the datetime stamp added on the file name
        $content = json_decode($response->content());
        $form_data['image'] = $content->image;
        $file_path = public_path("img/uploads/special_campaign/") . $content->image;

        $response->assertStatus(200);
        // Assert the file was stored...
        $this->assertDatabaseHas('special_campaigns', Arr::except($form_data, ['logo_image_file']) );
        // Assert a file does not exist...
        $this->assertTrue( File::exists( $file_path ));
        // Storage::disk('avatars')->assertMissing( $content->image );
        
        // Clear up           
        File::delete( $file_path );
     }

     public function test_an_administrator_can_access_the_special_campaign_view_page_contains_valid_record()
    {
        $form_data = $this->get_new_record_form_data();
        $row = SpecialCampaign::create( $form_data);

        $this->actingAs($this->admin);
        // $response = $this->get("/settings/special-campaigns/{$cy->id}");
        $response = $this->getJson("/settings/special-campaigns/{$row->id}", ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment( $form_data );
        // $response->assertViewHas('campaign_year', $cy );
    }
    public function test_an_administrator_can_access_special_campaign_edit_page_contains_valid_record()
    {
        $form_data = $this->get_new_record_form_data();
        $row = SpecialCampaign::create( $form_data ); 

        $this->actingAs($this->admin);
        $response = $this->getJson("/settings/special-campaigns/{$row->id}/edit", ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment( $form_data );
    }
    public function test_an_administrator_can_update_special_campaign_successful_in_db()
    {
        // $campignyears = SpecialCampaign::factory(1)->create();
        $form_data = $this->get_new_record_form_data();
        $row = SpecialCampaign::create($form_data);
       
        $original_filename = public_path("img/uploads/special_campaign/") . $form_data['image'];
        file_put_contents($original_filename, $form_data['logo_image_file']);

        // $file_path = public_path("img/uploads/special_campaign/") . $content->image;

        // modify values
        $form_data['name'] = 'Modified Dummy';
        $form_data['_method'] = 'PUT';
        // unset($form_data['logo_image_file']);

        $this->actingAs($this->admin);
        // $response = $this->json('put', '/settings/special-campaigns/' .  $row->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $response = $this->post('/settings/special-campaigns/' . $row->id,  $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        // grab the file name since the datetime stamp added on the file name
        $content = json_decode($response->content());
        // $form_data = $this->get_new_record_form_data();
        $form_data['image'] = $content->image;
        $file_path = public_path("img/uploads/special_campaign/") . $content->image;
       

        $response->assertStatus(200);
        $response->assertJsonFragment( Arr::except($form_data, ['image', 'logo_image_file', '_method']) );
        // Assert the file was stored...
        $this->assertDatabaseHas('special_campaigns', Arr::except($form_data, ['logo_image_file', '_method'])  );
        // Assert a file does not exist...
        $this->assertTrue( File::exists( $file_path ));
        
        // Clean up           
        File::delete( $file_path );

    }
    public function test_an_administrator_can_delete_the_special_campaign_successful_in_db()
    {
        // $campignyears = SpecialCampaign::factory(1)->create();
        $form_data = $this->get_new_record_form_data();
        $row = SpecialCampaign::create($form_data);

        $original_filename = public_path("img/uploads/special_campaign/") . $form_data['image'];
        file_put_contents($original_filename, $form_data['logo_image_file']);

        $this->actingAs($this->admin);
        $response = $this->json('delete', '/settings/special-campaigns/' . $row->id, [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        // $response = $this->delete('/settings/special-campaigns/' . $cy->id);

        $response->assertStatus(204);
        // Assert the file was deleted ...
        $this->assertSoftDeleted('special_campaigns', Arr::except($form_data, ['logo_image_file'])  );
         // Assert a file does not exist...
        $this->assertFalse( File::exists( $original_filename ));

    }

    /** Pagination */
    public function test_pagination_on_campaign_years_table_via_ajax_call()
    {
        // Seed data for pagination testing
        // $this->seed(\Database\Seeders\Special-campaignseeder::class);
        $form_data = $this->get_new_record_form_data();
        $bu = SpecialCampaign::create( $form_data ); 

        $this->actingAs($this->admin);
        $response = $this->getJson('/settings/special-campaigns', ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment( $form_data );
    }

    /** Form Validation */
    public function test_validation_rule_fields_are_required_when_create()
    {

        $this->actingAs($this->admin);
        $response = $this->postJson('/settings/special-campaigns', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        // $response = $this->post('/settings/special-campaigns');
        // This should cause errors with the
        // title and content fields as they aren't present

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'name',
            'charity_id',
            'start_date',
            'end_date',
            'description',
            'banner_text',
            'logo_image_file',
        ]);

    }

    public function test_validation_rule_fields_are_required_when_edit()
    {
        $form_data = $this->get_new_record_form_data();
        $bu = SpecialCampaign::create( $form_data );

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/special-campaigns/' . $bu->id, [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        // $response = $this->post('/settings/special-campaigns');
        // This should cause errors with the
        // title and content fields as they aren't present
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'name',
            'charity_id',
            'start_date',
            'end_date',
            'description',
            'banner_text',
            'logo_image_file',
        ]);
    }
    public function test_validation_name_is_over_max_50()
    {
        $form_data = $this->get_new_record_form_data();
        $row = SpecialCampaign::create( $form_data );
        //
        $form_data['name'] = "very long name, is over 50 chars,Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, ";

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/special-campaigns/' . $row->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'name',
        ]);
    }
    public function test_validation_start_date_later_than_end_date_is_valid()
    {
        $form_data = $this->get_new_record_form_data();
        $row = SpecialCampaign::create( $form_data );
        //
        $form_data['id'] = $row->id;
        $form_data['start_date'] = "2023-12-12";
        $form_data['end_date'] = "2023-11-10";

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/special-campaigns/' . $row->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'start_date',
            'end_date',
        ]);
    }
    public function test_validation_charity_id_is_invalid()
    {
        $form_data = $this->get_new_record_form_data();
        $row = SpecialCampaign::create( $form_data );
        //
        $form_data['charity_id'] = 99999;

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/special-campaigns/' . $row->id, $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'charity_id',
        ]);
    }

  
    /* Private Function */
    private function get_new_record_form_data() 
    {

        $charity = Charity::factory()->create();

        $file = UploadedFile::fake()->image('avatar.jpg');

        $form_data = [
            "name" => "2022 Red Cross - Hurricane Relief",
            "description" => "This is a description",
            "banner_text" => "Support emergency response efforts in guatamala due to the recent hurricane!",
            "charity_id" => $charity->id,
            "start_date" => "2023-12-01",
            "end_date" => "2024-01-31",
            "image" => $file->name,
            "logo_image_file" => $file,
        ];

        return $form_data;
    }

}
