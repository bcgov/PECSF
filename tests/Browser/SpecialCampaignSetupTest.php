<?php


use Carbon\Carbon;
use App\Models\User;
use App\Models\Charity;
use App\Models\Setting;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

use Faker\Factory as Faker;

use Illuminate\Support\Str;
use App\Models\CampaignYear;
use App\Models\SpecialCampaign;
use Spatie\Permission\Models\Role;
use App\Models\SpecialCampaignPledge;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\DatabaseTruncation;


// uses(Tests\DuskTestCase::class);
// uses(Tests\DuskTestCase::class)->in('Unit', 'Feature');


// uses(DatabaseTruncation::class);

// test('example', function () {
//     $this->browse(function (Browser $browser) {
//         $browser->visit('/')
//                 ->assertSee('Log in as a System Administrator');
//     });
// });



beforeAll(function () {

    // artisan('db:seed');
    // artisan::call('db:seed', ['--class' => 'UserTableSeeder']);


    // // Truncate tables and seed required data
 

    // // Create a shared test user
    // $this->user = User::Factory()->create([
    //     'password' => bcrypt('password123'),
    //     'source_type' => 'LCL',
    // ]);

    // $this->user->assignRole('admin');

});


beforeEach(function () {
  
    // Truncate the database and create a fresh user before each test
    Setting::truncate();
    Charity::truncate();
    SpecialCampaign::truncate();
    SpecialCampaignPledge::truncate();
   
    $this->admin = User::whereHas("roles", function ($q) {$q->where("name", "admin");})->first();
    $this->user  = User::doesntHave('roles')->orderBy('id')->first();

    $this->setting = Setting::create([
        ]);

});

afterEach(function () {
    // Cleanup admin user
    // $this->admin->delete();
    
    // Refresh the browser after each test
    session()->flush();
});






it('redirects anonymous user to the login page when attempting to access the special campaign', function () {
    $this->browse(function (Browser $browser) {

        $browser->visit('/settings/special-campaigns') 
            ->waitForLocation('/login')
            ->assertPathIs('/login') // Assert it redirects to login
            ->assertSee('Login'); // Assert login prompt is visible

        $browser->visit('/settings/special-campaigns/create') 
            ->waitForLocation('/login')
            ->assertPathIs('/login') // Assert it redirects to login
            ->assertSee('Login'); // Assert login prompt is visible

        $response = $this->post('/settings/special-campaigns', []);
        $response->assertStatus(302);
        $response->assertRedirect('login');

        $browser->visit('/settings/special-campaigns/1') 
            ->waitForLocation('/login')
            ->assertPathIs('/login') // Assert it redirects to login
            ->assertSee('Login'); // Assert login prompt is visible
        
        $browser->visit('/settings/special-campaigns/1/edit') 
            ->waitForLocation('/login')
            ->assertPathIs('/login') // Assert it redirects to login
            ->assertSee('Login'); // Assert login prompt is visible
        
        $response = $this->put('/settings/special-campaigns/1', []);
        $response->assertStatus(302);
        $response->assertRedirect('login');

        $response = $this->delete('/settings/special-campaigns/1');
        $response->assertStatus(302);
        $response->assertRedirect('login');
            
    });
});



it('denies unauthorized users from accessing protected special campaign maintenance routes', function () {
    $this->browse(function (Browser $browser) {

        $browser->loginAs($this->user)
            ->visit('/settings/special-campaigns') 
            ->assertSee('USER DOES NOT HAVE THE RIGHT PERMISSIONS.')
            ->assertSee('403'); 

        $browser->loginAs($this->user)
            ->visit('/settings/special-campaigns/create') 
            ->assertSee('USER DOES NOT HAVE THE RIGHT PERMISSIONS.')
            ->assertSee('403'); 

        $browser->loginAs($this->user)
            ->visit('/settings/special-campaigns/1') 
            ->assertSee('USER DOES NOT HAVE THE RIGHT PERMISSIONS.')
            ->assertSee('403'); 
        
        $browser->loginAs($this->user)
            ->visit('/settings/special-campaigns/1/edit') 
            ->assertSee('USER DOES NOT HAVE THE RIGHT PERMISSIONS.')
            ->assertSee('403'); 
            
    });
});


it('shows validation error when required fields are missing when creating a special campaign', function () {

    $faker = Faker::create();

    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/settings/special-campaigns')
            ->press('Add a New Value')
            ->waitForText('Add a new special campaign')
            ->assertSee('Add a new special campaign')

            // Fill in the form fields

            // Submit the form
            ->press('#create-confirm-btn')
            ->pause(500)
            ->press('button.swal2-confirm')

            // assertions
            ->waitFor('#bu-create-model-form span.text-danger')
            ->assertSee('The name field is required') 
            ->assertSee('The charity id field is required')
            ->assertSee('The start date field is required')
            ->assertSee('The end date field is required')
            ->assertSee('The description field is required')
            ->assertSee('The logo image file is required')
            ->assertSee('The banner text field is required')

            ->logout('admin')
           ;
    });

    $this->browse(function (Browser $browser) use($faker) {
        $browser->loginAs($this->admin)
            ->visit('/settings/special-campaigns')
            ->press('Add a New Value')
            ->waitForText('Add a new special campaign')
            ->assertSee('Add a new special campaign')

            // Fill in the form fields
            ->value('#bu-create-model-form [name="name"]', Str::random(80) )
            ->value('#bu-create-model-form [name="start_date"]', Carbon::tomorrow()->format('Y-m-d') )
            ->value('#bu-create-model-form [name="end_date"]', Carbon::yesterday()->format('Y-m-d') )
            ->value('#bu-create-model-form [name="banner_text"]', Str::random(160) )
            ->attach('input[name="logo_image_file"]', __DIR__ . '/files/logo-1.png') // Path to the file you want to upload

            // Submit the form
            ->press('#create-confirm-btn')
            ->pause(500)
            ->press('button.swal2-confirm')
        
            // assertions
            ->waitFor('#bu-create-model-form span.text-danger')
            ->assertSee('The name must not be greater than 50 characters')
            ->assertSee('The start date must be a date before or equal to end date') 
            ->assertSee('The end date must be a date after or equal to start date')
            ->assertSee('The banner text must not be greater than 150 characters')
         
            // ->assertSee('The effective date field must be in the past if the status is Active')
            // ->assertSee('The linked bu code field is required.')
            ->logout('admin')
           ;
    });

});


it('shows validation error for fields when editing a special campaign', function () {

    $faker = Faker::create();

    $special_campaign = SpecialCampaign::factory()->create([]);

    $this->browse(function (Browser $browser) use ($special_campaign, $faker) {
        $browser->loginAs($this->admin)
            ->visit('/settings/special-campaigns')
            ->waitForText(  $special_campaign->name )
            ->click('button.edit-bu[data-id="'. $special_campaign->id .'"]')

            ->waitForText( 'Edit an existing special campaign' )
            ->assertSee('Edit an existing special campaign')

            // Fill in the form fields
            ->pause(500)
            ->value('#bu-edit-model-form input[name="name"]', '')
            ->value('#bu-edit-model-form textarea[name="description"]', '')
            ->attach('input[name="logo_image_file"]', __DIR__ . '/files/logo-2.png') // Path to the file you want to upload

            // Submit the form
            ->press('#save-confirm-btn')
            ->pause(500)
            ->press('button.swal2-confirm')

            // assertions
            ->pause(500)
            ->assertSee('The name field is required') 
            ->assertSee('The description field is required')

            ->logout('admin')
           ;
    });

});


it('administrator can paginate through special campaigns', function () {

    SpecialCampaign::factory(25)->create();
    $sorted = SpecialCampaign::orderBy('name')->get();

    $this->browse(function (Browser $browser) use($sorted) {
        $browser->loginAs($this->admin)
            ->visit('/settings/special-campaigns') // API endpoint
            ->waitForText(  'Showing 1 to 10' )
            ->assertSee( $sorted[0]->name ) // Check if "name" key exists in JSON
            ->assertSee( $sorted[0]->start_date->format('Y-m-d') ) // Check if "name" key exists in JSON
            ->click('a[data-dt-idx="2"]')
            ->waitForText(  'Showing 11 to 20' )
            ->assertSee( $sorted[10]->name ) // Ch
            ->assertSee( $sorted[10]->start_date->format('Y-m-d') ) // Check if "name" key exists in JSON
            ->logout('admin')
            ;

    });

});



  
it('administrator successfully creates a special campaign', function () {

    // $charity = Charity::factory()->create();

    $item = SpecialCampaign::factory()->make([
        "start_date" => today()->format('Y-m-d'),
        "end_date" => today()->addDays(30)->format('Y-m-d'),
    ]);

    $this->browse(function (Browser $browser) use($item) {

        $browser->loginAs($this->admin)
            ->visit('/settings/special-campaigns')
            ->press('Add a New Value')
            ->waitForText('Add a new special campaign')
            ->assertSee('Add a new special campaign')

            // Fill in the form fields
            ->click('#bu-create-model-form input[name="name"]')
            ->value('#bu-create-model-form input[name="name"]', $item->name)  

            // Open the SELECT2 dropdown
            ->click('#bu-create-modal .select2-container') // Select the container of the SELECT2 field
            ->pause(1000)
            // ->waitFor('.select2-selection__rendered') // Wait for the dropdown to appear
            ->waitFor('#bu-create-modal .select2-dropdown')
          
             // Select an option from the dropdown
            ->click('#bu-create-modal .select2-results__option') // Click the option in the dropdown
            ->waitUntilMissing('#bu-create-modal .select2-dropdown') // Wait for the dropdown to close

            ->value('#bu-create-model-form [name="description"]', $item->description)  
            ->value('#bu-create-model-form input[name="start_date"]', $item->start_date->format('Y-m-d') )  // Set start date
            ->value('#bu-create-model-form input[name="end_date"]', $item->end_date->format('Y-m-d') )  // Set end date
            ->value('#bu-create-model-form input[name="banner_text"]', $item->banner_text )            
            ->attach('#bu-create-model-form input[name="logo_image_file"]', __DIR__ . '/files/logo-1.png') // Path to the file you want to upload



            // Submit the form
            ->press('#create-confirm-btn')
            ->pause(500)
            ->press('button.swal2-confirm')          

            // 
            ->waitForText('Special Campaign "' . $item->name . '" has been successfully created.')
            ->assertSee('Special Campaign "' . $item->name . '" has been successfully created.')
            ->logout('admin')
        ;

        // Verify the same data in the database
        $this->assertDatabaseHas('special_campaigns', Arr::except( $item->attributesToArray(), ['image', 'status', 'created_by_id', 'modified_by_id']) );

    });

});




it('administrator can read and view the special campaign', function () {

    $item = SpecialCampaign::factory()->create([
        "start_date" => today()->format('Y-m-d'),
        "end_date" => today()->addDays(30)->format('Y-m-d'),
    ]);

    $this->browse(function (Browser $browser) use ($item) {
        $browser->loginAs($this->admin)
            ->visit('/settings/special-campaigns')
            ->waitForText('Showing 1 to 1 ')
            ->waitForText(  $item->name )

            // select item from the listing
            ->click('button.show-bu[data-id="'. $item->id .'"]')
            ->waitForText( 'Existing special campaign details' )
            ->assertSee('Existing special campaign details')

            // assertion
            ->assertValue('#bu-show-model-form input[name="name"]', $item->name )
            ->assertValue('#bu-show-model-form input[name="start_date"]', $item->start_date->format('Y-m-d') )
            ->assertValue('#bu-show-model-form input[name="end_date"]', $item->end_date->format('Y-m-d') )
            ->assertValue('#bu-show-model-form input[name="banner_text"]', $item->banner_text )
            ->assertValue('#bu-show-model-form input[name="charity_name"]', $item->charity->charity_name  )
            ->assertValue('#bu-show-model-form input[name="registration_number"]', $item->charity->registration_number  )

            ->logout('admin');
         
    });
});




it('administrator successfully updates a special campaign', function () {

    $faker = Faker::create();

    $item = SpecialCampaign::factory()->create([
        "start_date" => today()->format('Y-m-d'),
        "end_date" => today()->addDays(30)->format('Y-m-d'),
    ]);

    $item->name = 'This is a new one';
    $item->start_date = today()->year . '-07-01';
    $item->end_date = today()->year . '-11-30';

    $this->browse(function (Browser $browser) use ($item) {

        $browser->loginAs($this->admin)
            ->visit('/settings/special-campaigns')
            ->waitForText('Showing 1 to 1 ')

            // select item from the listing
            ->click('button.edit-bu[data-id="'. $item->id .'"]')
            ->waitForText( 'Edit an existing special campaign' )
            ->assertSee('Edit an existing special campaign')

            // Fill in the form fields
            ->value('#bu-edit-model-form [name="name"]', $item->name)  
            ->value('#bu-edit-model-form [name="start_date"]', $item->start_date->format('Y-m-d') )  // Set start date
            ->value('#bu-edit-model-form [name="end_date"]', $item->end_date->format('Y-m-d') )  // Set end date

            // Submit the form
            ->press('#save-confirm-btn')
            ->pause(500)
            ->press('button.swal2-confirm')
 
            // assertions
            ->waitForText('Special Campaign "' . $item->name . '" has been successfully updated.')
            ->assertSee('Special Campaign "' . $item->name . '" has been successfully updated.')

            ->logout('admin');
            ;

        // Verify the same data in the database
        // Verify the same data in the database
        $this->assertDatabaseHas('special_campaigns', Arr::except( $item->attributesToArray(), ['status', 'created_by_id', 'modified_by_id', 'created_at', 'updated_at']) );
          
    });
});

it('administrator successfully deletes a special campaign', function () {

    $item = SpecialCampaign::factory()->create([
        "start_date" => today()->format('Y-m-d'),
        "end_date" => today()->addDays(30)->format('Y-m-d'),
    ]);

    $this->browse(function (Browser $browser) use ($item) {
        $browser->loginAs($this->admin)
            ->visit('/settings/special-campaigns')
            ->waitForText('Showing 1 to 1 ')
            ->click('button.delete-bu[data-id="'. $item->id .'"]')

            ->waitForText( 'Are you sure you want to delete special campaign "' . $item->name  . '" ?' )
            ->press('button.swal2-confirm')
            // ->press('button.swal2-cancel')
            // ->waitForLocation('/settings/special')
            ->waitForText('Special Campaign "' . $item->name . '" has been successfully deleted.')
            ->waitForText('No data available in table')
            ->pause(1000)
            ->logout('admin');
            ;

        $this->assertSoftDeleted('special_campaigns', [
            'id' => $item->id,
            'name' => $item->name,
        ]); 
        
    });
});

