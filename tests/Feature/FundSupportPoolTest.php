<?php

namespace Tests\Feature;

use File;
use Tests\TestCase;
use App\Models\User;
use App\Models\FSPool;
use App\Models\Region;
use App\Models\Charity;
use Illuminate\Support\Arr;
use App\Models\FSPoolCharity;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;

class FundSupportPoolTest extends TestCase
{
    use WithFaker;

    // protected static $initialized = FALSE;

    private User $admin;
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::whereHas("roles", function ($q) {$q->where("name", "admin");})->first();
        $this->user  = User::doesntHave('roles')->first();

        FSPool::truncate();
        FSPoolCharity::truncate();
    }

    /** Test Authenication */
    public function test_an_anonymous_user_cannot_access_fund_supported_pool_index_page()
    {
        $response = $this->get('/settings/fund-supported-pools');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_fund_supported_pool_create_page()
    {
        $response = $this->get('/settings/fund-supported-pools/create');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_create_the_fund_supported_pool()
    {
        [$form_data, $data, $pool] = $this->get_new_record_form_data();

        $response = $this->post('/settings/fund-supported-pools', $form_data);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_fund_supported_pool_view_page()
    {
        $response = $this->get('/settings/fund-supported-pools/1');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_access_the_fund_supported_pool_edit_page()
    {
        $response = $this->get('/settings/fund-supported-pools/1/edit');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_an_anonymous_user_cannot_update_the_fund_supported_pool()
    {

        $response = $this->put('/settings/fund-supported-pools/1', [] );

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function test_an_anonymous_user_cannot_delete_the_fund_supported_pool()
    {
        $response = $this->delete('/settings/fund-supported-pools/1');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }


    public function test_an_unauthorized_user_cannot_access_fund_supported_pool_index_page()
    {
        $this->actingAs($this->user);

        $response = $this->get('/settings/fund-supported-pools');
        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_fund_supported_pool_create_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/fund-supported-pools/create');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_create_the_fund_supported_pool()
    {
        [$form_data, $data, $pool] = $this->get_new_record_form_data();

        $this->actingAs($this->user);
        $response = $this->post('/settings/fund-supported-pools', $form_data);

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_fund_supported_pool_view_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/fund-supported-pools/1');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_access_the_fund_supported_pool_edit_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/settings/fund-supported-pools/1/edit');

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_update_the_fund_supported_pool()
    {
        $this->actingAs($this->user);
        $response = $this->put('/settings/fund-supported-pools/1', [] );

        $response->assertStatus(403);
    }
    public function test_an_unauthorized_user_cannot_delete_the_fund_supported_pool()
    {
        $this->actingAs($this->user);
        $response = $this->delete('/settings/fund-supported-pools/1');

        $response->assertStatus(403);
    }

    public function test_an_administrator_can_access_fund_supported_pool_index_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/settings/fund-supported-pools');

        $response->assertStatus(200);

    }
    public function test_an_administrator_can_access_the_fund_supported_pool_create_page()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/settings/fund-supported-pools/create');

        $response->assertStatus(200);
    }
    public function test_an_administrator_can_create_fund_supported_pool_successful_in_db()
    {
        // $cy = FSPool::factory(1)->create();
        [$form_data, $data, $pool] = $this->get_new_record_form_data();
 
        $this->actingAs($this->admin);
        $response = $this->post('/settings/fund-supported-pools',  $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        // Assert the file was stored...
        $this->assertDatabaseHas('f_s_pools', Arr::except($data, ['charities']) );
        foreach ($data['charities'] as $child) {
            $this->assertDatabaseHas('f_s_pool_charities', Arr::except($child, ['image', 'image_data']));
        }
        // Assert a file does exist 
        $items = FSPoolCharity::where('f_s_pool_id', $data['id'])->get('image');
        foreach($items as $item) {
            $file_path = public_path("img/uploads/fspools/") . $item->image;
            $this->assertTrue( File::exists( $file_path ));
            File::delete( $file_path );    
        }
        
     }

    public function test_an_administrator_can_create_duplicate_fund_supported_pool_successful_in_db()
    {
        // $cy = FSPool::factory(1)->create();
        [$form_data, $data, $pool] = $this->get_new_record_form_data(true);

        // create the one first
        $new_start_date = '2040-01-01';
        $data['start_date'] = $new_start_date;
 
        $this->actingAs($this->admin);
        $response = $this->json('post', '/settings/fund-supported-pools/duplicate/' . $pool->id, ['start_date' => $new_start_date],
                                    ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(204);
        // Assert the file was stored...
        $this->assertDatabaseHas('f_s_pools', Arr::except($data, ['id', 'charities'] ) );
        foreach ($data['charities'] as $pool_charity) {
            $this->assertDatabaseHas('f_s_pool_charities', Arr::except($pool_charity, ['id', 'image', 'image_data']));
        }
        // Assert a file does exist 
        $items = FSPoolCharity::where('f_s_pool_id', $data['id'])->get('image');
        foreach($items as $item) {
            $file_path = public_path("img/uploads/fspools/") . $item->image;
            $this->assertTrue( File::exists( $file_path ));
            File::delete( $file_path );    
        }
        
     }

     public function test_an_administrator_can_access_the_fund_supported_pool_view_page_contains_valid_record()
    {
        [$form_data, $data, $pool] = $this->get_new_record_form_data(true);

        $this->actingAs($this->admin);
        // $response = $this->getJson("/settings/fund-supported-pools/{$pool->id}", ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $response = $this->get("/settings/fund-supported-pools/{$pool->id}");

        $response->assertStatus(200);
        // $response->assertJsonFragment( $data );
        $response->assertViewHas('pool', $pool );
        $items = FSPoolCharity::get();
        
        foreach($pool->charities as $pool_charity) {
            $response->assertViewHas('charities', function($charities) use($pool_charity) {
                return $charities->contains($pool_charity);
            });

            // clean up image files in folder 
            $original_filename = public_path("img/uploads/fspools/") . $pool_charity->image;
            File::delete( $original_filename);
        }
        
    }
    public function test_an_administrator_can_access_fund_supported_pool_edit_page_contains_valid_record()
    {
        [$form_data, $data, $pool] = $this->get_new_record_form_data(true);

        $this->actingAs($this->admin);
        $response = $this->getJson("/settings/fund-supported-pools/{$pool->id}/edit", ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
 
        $response->assertViewHas('pool', $pool );
        $items = FSPoolCharity::get();

        foreach($pool->charities as $pool_charity) {
            $response->assertViewHas('charities', function($charities) use($pool_charity) {
                return $charities->contains($pool_charity);
            });

            // clean up image files in folder 
            $original_filename = public_path("img/uploads/fspools/") . $pool_charity->image;
            File::delete( $original_filename);
        }

    }
    public function test_an_administrator_can_update_fund_supported_pool_successful_in_db()
    {
        // $campignyears = FSPool::factory(1)->create();
        [$form_data, $data, $pool] = $this->get_new_record_form_data(true);
        // $pool = FSPool::create( $parent );
        // $pool_charities = collect();
        // foreach ($children as $child) {
        //     $rec = FSPoolCharity::create($child);
        //     $pool_charities->push($rec);
        //     // create file in the folder
        //     $original_filename = public_path("img/uploads/fspools/") . $child['image'];
        //     file_put_contents($original_filename, $child['image_data']);
        // }
       
        // modify values
        // $parent['start_date'] = today()->addDay(30)->format('Y-m-d');
        $data['charities'][0]['contact_name'] = 'Modifled Contact Name';
        // $form_data['start_date'] = $data['start_date'];
        $form_data['contact_names'][0] = $data['charities'][0]['contact_name'];

        $form_data['_method'] = 'PUT';

        $this->actingAs($this->admin);
        // $response = $this->json('put', '/settings/fund-supported-pools/' .  $row->id, $record, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $response = $this->post('/settings/fund-supported-pools/' . $pool->id,  $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(302);
        // $response->assertJsonFragment( Arr::except($record, ['image', 'logo_image_file', '_method']) );
        // Assert the file was stored...
        $this->assertDatabaseHas('f_s_pools',  Arr::except($data, ['charities'])  );
        foreach ($data['charities'] as $child) {
            $this->assertDatabaseHas('f_s_pool_charities', Arr::except($child, ['id', 'image', 'image_data']));
        }
        // Assert a file does exist 
        $items = FSPoolCharity::where('f_s_pool_id', $data['id'])->get('image');
        foreach($items as $item) {
            $file_path = public_path("img/uploads/fspools/") . $item->image;
            $this->assertTrue( File::exists( $file_path ));

            // clean up 
            File::delete( $file_path );    
        }

    }
    public function test_an_administrator_can_delete_the_fund_supported_pool_successful_in_db()
    {
        // $campignyears = FSPool::factory(1)->create();
        [$form_data, $data, $pool] = $this->get_new_record_form_data(true);
        // $pool = FSPool::create( $parent );
        // $pool_charities = collect();
        // foreach ($children as $child) {
        //     $rec = FSPoolCharity::create($child);
        //     $pool_charities->push($rec);
        //     // create file in the folder
        //     $original_filename = public_path("img/uploads/fspools/") . $child['image'];
        //     file_put_contents($original_filename, $child['image_data']);
        // }

        $this->actingAs($this->admin);
        $response = $this->json('delete', '/settings/fund-supported-pools/' . $pool->id, [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        // $response = $this->delete('/settings/fund-supported-pools/' . $cy->id);

        $response->assertStatus(204);
        // Assert the file was deleted ...
        $this->assertSoftDeleted('f_s_pools',  Arr::except($data, ['charities'])  );
         // Assert a file does not exist...
        foreach ($data['charities'] as $child) {
            $original_filepath = public_path("img/uploads/fspools/") . $child['image'];
            $this->assertFalse( File::exists( $original_filepath ));
        }

    }

    /** Pagination */
    public function test_pagination_on_campaign_years_table_via_ajax_call()
    {
        // Seed data for pagination testing
        // $this->seed(\Database\Seeders\Fund-supported-poolseeder::class);
        $region = Region::factory()->create();
        $charity = Charity::factory()->create([
            'charity_status' => 'Registered',
        ]);

        $pool = FSPool::factory()->create([
                    'region_id' => $region->id,
                ]);

        $pool_charities = FSPoolCharity::factory(20)->create([
            'f_s_pool_id' => $this->faker->randomElement( $pool->pluck('id')->toArray() ),
            'charity_id' => $this->faker->randomElement( $charity->pluck('id')->toArray() ),
        ]);

        $this->actingAs($this->admin);
        $response = $this->getJson('/settings/fund-supported-pools', ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment( $pool->toArray() );
        foreach($pool_charities as $pool_charity) {
            $response->assertJsonFragment(   Arr::except( $pool_charity->toArray(),  ['percentage'])  );
        }

    }

    /** Form Validation */
    public function test_validation_rule_fields_are_required_when_create()
    {

        $this->actingAs($this->admin);
        $response = $this->postJson('/settings/fund-supported-pools', [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        // $response = $this->post('/settings/fund-supported-pools');
        // This should cause errors with the
        // title and content fields as they aren't present

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'region_id',
            'start_date',
            // 'pool_status',
        ]);

    }

    public function test_validation_rule_fields_are_required_when_edit()
    {
        $record = $this->get_new_record_form_data();
        $bu = FSPool::create( $record );

        $this->actingAs($this->admin);
        $response = $this->json('put', '/settings/fund-supported-pools/' . $bu->id, [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        // $response = $this->post('/settings/fund-supported-pools');
        // This should cause errors with the
        // title and content fields as they aren't present
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'region_id',
            'start_date',
            'pool_status',
        ]);
    }
    public function test_validation_name_is_over_max_50()
    {
        [$form_data, $parent, $children] = $this->get_new_record_form_data();
        foreach ($form_data['names'] as $key => $name) {
            $form_data['names'][$key] = "very long name, is over 50 chars,Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem ";
        }

        $this->actingAs($this->admin);
        $response = $this->json('post', '/settings/fund-supported-pools', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'names.0',
            'names.1',
            'names.2',
            'names.3',
            
        ]);
    }
    public function test_validation_100_percentage_is_valid()
    {
        [$form_data, $parent, $children] = $this->get_new_record_form_data();

        $form_data['percentages'][0] = 10;
        $form_data['percentages'][1] = 20;
        $form_data['percentages'][2] = 40;
        $form_data['percentages'][3] = 30;

        $this->actingAs($this->admin);
        $response = $this->json('post', '/settings/fund-supported-pools/', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonMissingValidationErrors([
            'percentages.0',
            'percentages.1',
            'percentages.2',
            'percentages.3',
        ]);
    }
    public function test_validation_percentage_is_invalid()
    {
        [$form_data, $parent, $children] = $this->get_new_record_form_data();

        $form_data['percentages'][0] = 60;
        $form_data['percentages'][1] = 0;
        $form_data['percentages'][2] = 20;
        $form_data['percentages'][3] = 20;

        $this->actingAs($this->admin);
        $response = $this->json('post', '/settings/fund-supported-pools/', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'percentages.1',
        ]);
    }
    public function test_validation_charity_id_is_invalid()
    {
        [$form_data, $parent, $children] = $this->get_new_record_form_data();

        $form_data['charities'][0] = 99999;
        $form_data['names'][1] = "very long name, is over 50 chars,Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem ";

        $this->actingAs($this->admin);
        $response = $this->json('post', '/settings/fund-supported-pools/', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'charities.0',
            'names.1',
        ]);
    }
    public function test_validation_upload_file_is_invalid()
    {
        [$form_data, $parent, $children] = $this->get_new_record_form_data();

        $file = UploadedFile::fake()->create('invoice.xlsx');
        $form_data["images"][0] = $file;

        $this->actingAs($this->admin);
        $response = $this->json('post', '/settings/fund-supported-pools/', $form_data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'images.0',
        ]);
    }

  
    /* Private Function */
    private function get_new_record_form_data($bCreate = false) 
    {
        // Dependence
        $regions = Region::factory(1)->create();
        $charities = Charity::factory(4)->create([
            'charity_status' => 'Registered',
        ]);

        $data = [
            "id" => 1,
            "region_id" => $regions[0]->id,
            "start_date" => today()->addDay(1)->format('Y-m-d'),
            "status" => "A",
            "charities" => [],
        ];

        foreach ($charities as $key => $charity) {
            $file = UploadedFile::fake()->image('avatar' . $key . '.jpg');
            $pool_charity = [
                'f_s_pool_id' => 1,
                'charity_id' => $charity->id,
                'percentage' => 25, 
                'status' => 'A',
                "name" => $this->faker->words(2, true),
                "description" => $this->faker->sentence(),
                "contact_title" => $this->faker->words(2, true), 
                "contact_name" => $this->faker->words(1, true),
                "contact_email" => $this->faker->email(),
                "notes" => $this->faker->sentences(2, true),
                "image" => $file->name,

                "image_data" => $file,
            ];
            
            array_push($data['charities'], $pool_charity);
        }

        $pool = null;
        if ($bCreate) {
            // create parent 
            $pool = FSPool::create( $data );
            $data['id'] = $pool->id;

            // create child 
            foreach ($data['charities'] as $key => $child) {
                
                $pool->charities()->create( $child );
                $data['charities'][$key]['id'] = $pool->id;

                // $rec = FSPoolCharity::create($child);
                // $pool_charities->push($rec);
                // create file in the folder
                $original_filename = public_path("img/uploads/fspools/") . $child['image'];
                file_put_contents($original_filename, $child['image_data']);
            }
    
        }

        $ids = [];
        $status = [];
        $names = [];
        $descriptions = [];
        $percentages = [];
        $contact_names = [];
        $contact_titles = [];
        $contact_emails = [];
        $notes = [];
        $images = [];
        foreach ($data['charities'] as $key => $charity) {
            array_push($ids, $charity['charity_id']);
            array_push($status, $charity['status']);
            array_push($names, $charity['name'] );
            array_push($descriptions, $charity['description'] );
            array_push($percentages, $charity['percentage']);
            array_push($contact_names, $charity['contact_name'] );
            array_push($contact_titles, $charity['contact_title'] );
            array_push($contact_emails, $charity['contact_email'] );
            array_push($notes, $charity['notes'] );

            array_push($images, $charity['image_data'] );
        }

        $form_data = [
            "region_id" => $data['region_id'],
            "pool_status" => $data['status'],
            "start_date" => $data['start_date'],

            "charities" => $ids,
            "status" => $status,
            "names" => $names,
            "descriptions" => $descriptions,
            "percentages" => $percentages,
            "contact_names" => $contact_names,
            "contact_titles" => $contact_titles,
            "contact_emails" => $contact_emails,
            "notes" => $notes,
            "images" => $images,
        ];

        return [$form_data, $data, $pool];
    }

}


