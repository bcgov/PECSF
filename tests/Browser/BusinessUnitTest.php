<?php


use Carbon\Carbon;
use App\Models\User;
use App\Models\Setting;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

use App\Models\BusinessUnit;
use App\Models\CampaignYear;

use Spatie\Permission\Models\Role;
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
    CampaignYear::truncate();
    BusinessUnit::truncate();
      
    $this->admin = User::whereHas("roles", function ($q) {$q->where("name", "admin");})->first();
    $this->user  = User::doesntHave('roles')->orderBy('id')->first();

    $this->setting = Setting::create([
        ]);

    $this->campaign_year = CampaignYear::create([
            'calendar_year' => today()->year + 1,
            'status' => 'A',
            'start_date' => today(),
            'end_date' => today()->year . '-12-31',
            'number_of_periods' => 26,
            'close_date' => today()->year . '-12-31',
        ]);

});

afterEach(function () {
    // Cleanup admin user
    // $this->admin->delete();
    
    // Refresh the browser after each test
    session()->flush();
});


// if (!function_exists('afterTruncatingDatabase')) {
//     function afterTruncatingDatabase(callable $callback)
//     {
//         $callback();
//     }
// }

/*
it('fails to log in with invalid credentials', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/login')
            ->assertSee('Login')
            ->click('a.sysadmin-login')
            ->type('email', 'invaliduser@example.com')
            ->type('password', 'wrongpassword')
            ->press('#admin-login button[type="submit"]')
            ->waitForText('These credentials do not match our records.')
            ->assertSee('These credentials do not match our records.');
    });
});


it('fails to log in with missing required fields', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/login')
            ->assertSee('Login')
            ->click('a.sysadmin-login')
            ->press('#admin-login button[type="submit"]')
            ->waitForText('The email field is required.')
            ->assertSee('The email field is required.')
            ->assertSee('The password field is required.');
    });
});



it('allows a logged in user to log out successfully', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/login')
            ->assertSee('Login')
            ->click('a.sysadmin-login')
            ->type('email', $this->user->email)
            ->type('password', 'password123') // Use the known password
            ->press('#admin-login button[type="submit"]')
            ->waitForLocation('/')
            ->assertPathIs('/')
            ->waitForText('Statistics')
            ->assertSee('Statistics');    
   
        $browser->visit('/')
            ->click('li.user-menu button')
            ->waitForText('Log Out') 
            ->clicklink('Log Out')
            ->waitForLocation('/login')
            ->assertPathIs('/login')
            ->waitForText('Login')
            ->assertSee('Login');
     

    });
});

*/



it('redirects anonymous user to the login page', function () {
    $this->browse(function (Browser $browser) {

        $browser->visit('/settings/business-units') 
            ->waitForLocation('/login')
            ->assertPathIs('/login') // Assert it redirects to login
            ->assertSee('Login'); // Assert login prompt is visible

        $browser->visit('/settings/business-units/create') 
            ->waitForLocation('/login')
            ->assertPathIs('/login') // Assert it redirects to login
            ->assertSee('Login'); // Assert login prompt is visible

        $response = $this->post('/settings/business-units', []);
        $response->assertStatus(302);
        $response->assertRedirect('login');

        $browser->visit('/settings/business-units/1') 
            ->waitForLocation('/login')
            ->assertPathIs('/login') // Assert it redirects to login
            ->assertSee('Login'); // Assert login prompt is visible
        
        $browser->visit('/settings/business-units/1/edit') 
            ->waitForLocation('/login')
            ->assertPathIs('/login') // Assert it redirects to login
            ->assertSee('Login'); // Assert login prompt is visible
        
        $response = $this->put('/settings/business-units/1', []);
        $response->assertStatus(302);
        $response->assertRedirect('login');

        $response = $this->delete('/settings/business-units/1');
        $response->assertStatus(302);
        $response->assertRedirect('login');
            
    });
});


it('denies access to protected business unit maintenance routes for unauthorized user', function () {
    $this->browse(function (Browser $browser) {

        $browser->loginAs($this->user)
            ->visit('/settings/business-units') 
            ->assertSee('USER DOES NOT HAVE THE RIGHT PERMISSIONS.')
            ->assertSee('403'); 

        $browser->loginAs($this->user)
            ->visit('/settings/business-units/create') 
            ->assertSee('USER DOES NOT HAVE THE RIGHT PERMISSIONS.')
            ->assertSee('403'); 

        $browser->loginAs($this->user)
            ->visit('/settings/business-units/1') 
            ->assertSee('USER DOES NOT HAVE THE RIGHT PERMISSIONS.')
            ->assertSee('403'); 
        
        $browser->loginAs($this->user)
            ->visit('/settings/business-units/1/edit') 
            ->assertSee('USER DOES NOT HAVE THE RIGHT PERMISSIONS.')
            ->assertSee('403'); 
            
    });
});

it('validateion error on create', function () {

        $faker = Faker::create();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit('/settings/business-units')
                ->press('[data-target="#bu-create-modal"]')
                ->waitForText('Add a new business unit')
                ->assertSee('Add a new business unit')
                ->press('#create-confirm-btn')
                ->acceptDialog()
            
                ->waitFor('#bu-create-model-form span.text-danger')
                ->assertSee('The code field is required.')
                ->assertSee('The name field is required.') 
                ->assertSee('The acronym field is required.')
                ->assertSee('The effective date field must be in the past if the status is Active')
                ->assertSee('The linked bu code field is required.')
                ->logout('admin')
               ;
        });

        $this->browse(function (Browser $browser) use($faker) {
            $browser->loginAs($this->admin)
                ->visit('/settings/business-units')
                ->press('[data-target="#bu-create-modal"]')
                ->waitForText('Add a new business unit')
                ->assertSee('Add a new business unit')
                // ->pause(1000)
                ->type('#bu-create-model-form [name="code"]', 'BC' . $faker->regexify('[0-9]{9}') )
                ->type('#bu-create-model-form [name="name"]', Str::random(80) )
                ->type('#bu-create-model-form [name="acronym"]',  $faker->regexify('[a-z]{5}') )
                ->type('#bu-create-model-form [name="effdt"]', Carbon::tomorrow()->format('m-d-Y') )
                ->type('#bu-create-model-form [name="linked_bu_code"]', 'BC' . $faker->regexify('[0-9]{3}') )
                ->press('#create-confirm-btn')
                ->acceptDialog()
            
                ->waitFor('#bu-create-model-form span.text-danger')
                ->assertSee('The code must not be greater than 5 characters.')
                ->assertSee('The name must not be greater than 60 characters.') 
                ->assertSee('The acronym field must be uppercase.')
                ->assertSee('The acronym must not be greater than 4 characters.')
                ->assertSee('The effective date field must be in the past if the status is Active')
                ->assertSee('The associated business unit is invalid.')
             
                // ->assertSee('The effective date field must be in the past if the status is Active')
                // ->assertSee('The linked bu code field is required.')
                ->logout('admin')
               ;
        });

});


it('validation error on edit', function () {

    $faker = Faker::create();

    $businessUnit = BusinessUnit::factory()->create([]);

    $this->browse(function (Browser $browser) use ($businessUnit, $faker) {
        $browser->loginAs($this->admin)
            ->visit('/settings/business-units')
            ->waitForText(  $businessUnit->code )
            ->click('a.edit-bu[data-id="'. $businessUnit->id .'"]')

            ->waitForText( 'Edit an existing business unit' )
            ->assertSee('Edit an existing business unit')
            // ->pause(1000)
            ->type('#bu-edit-model-form [name="name"]', '  ' )
            ->type('#bu-edit-model-form [name="linked_bu_code"]', 'BC' . rand(100, 999) )
            ->press('#save-confirm-btn')
            ->acceptDialog()
            ->waitFor('#bu-edit-model-form span.text-danger')
   
            // ->waitFor('The code field is required.')
            ->assertSee('The name field is required.') 
            // ->assertSee('The acronym field is required.')
            ->assertSee('The associated business unit is invalid.')

            ->logout('admin')
           ;
    });

});

//



it('pagination on business units table via ajax call', function () {

    BusinessUnit::factory(25)->create();
    $sorted = BusinessUnit::orderBy('code')->get();

    $this->browse(function (Browser $browser) use($sorted) {
        $browser->loginAs($this->admin)
            ->visit('/settings/business-units') // API endpoint
            // ->screenshot('debug-pagation-page') 

            ->waitForText(  'Showing 1 to 10' )
            ->assertSee( $sorted[0]->code ) // Check if "name" key exists in JSON
            ->assertSee( $sorted[0]->notes ) // Check if "name" key exists in JSON
            ->click('a[data-dt-idx="2"]')
            ->waitForText(  'Showing 11 to 20' )
            ->assertSee( $sorted[10]->code ) // Ch

            ->logout('admin')
            ;

    });

});



  
it('administrator creates a new business unit', function () {

    $yesterday = Carbon::yesterday(); 

    $item = BusinessUnit::factory()->make([
        'effdt' => $yesterday->format('Y-m-d'),
    ]);

    $this->browse(function (Browser $browser) use($yesterday, $item) {
        // $browser->visit('/login')

        //     ->assertSee('Login')
        //     ->click('a.sysadmin-login')
        //     ->type('email', $this->user->email)
        //     ->type('password', env('SYNC_ADMIN_PROFILE_SECRET') ) // Use the known password
        //     ->press('#admin-login button[type="submit"]')
        //     ->waitForLocation('/')
        //     ->assertPathIs('/')
        //     ->waitForText('Statistics')
        //     ->assertSee('Statistics');
    
        $browser->loginAs($this->admin)
            ->visit('/settings/business-units')
            ->waitForText('Showing 0 to ')
            ->press('[data-target="#bu-create-modal"]')
            ->waitForText('Add a new business unit')
            ->assertSee('Add a new business unit')
            ->pause(5000)
            ->type('#bu-create-model-form [name="code"]', $item->code)
            ->type('#bu-create-model-form [name="name"]', $item->name)
            ->type('#bu-create-model-form [name="effdt"]', $yesterday->format('m-d-Y'))
            ->select('#bu-create-model-form [name="status"]', $item->status)
            ->type('#bu-create-model-form [name="acronym"]', $item->acronym)
            ->type('#bu-create-model-form [name="linked_bu_code"]', $item->linked_bu_code)
            ->type('#bu-create-model-form [name="notes"]', $item->notes)
            ->press('#create-confirm-btn')
            ->acceptDialog()
            ->waitForText('Business Unit ' . $item->code . ' has been successfully created.')
            ->assertSee('Business Unit ' . $item->code . ' has been successfully created.')
            ->logout('admin');
            ;

        // Verify the same data in the database
        $this->assertDatabaseHas('business_units', Arr::except( $item->attributesToArray(), ['created_by_id', 'updated_by_id']) );
            
    });
});


it('show a business unit', function () {

    $item = BusinessUnit::factory()->create([]);

    $this->browse(function (Browser $browser) use ($item) {
        $browser->loginAs($this->admin)
            ->visit('/settings/business-units')
            ->waitForText('Showing 1 to 1 ')
            // ->waitForText(  $businessUnit->code )
            ->click('a.show-bu[data-id="'. $item->id .'"]')
            
            ->waitForText( 'Existing business unit details' )
            ->assertSee('Existing business unit details')
            ->assertSee( $item->code )
            ->assertSee( $item->name )
            ->assertSee( $item->notes )

            ->logout('admin');
            ;
          
    });
});


it('update a business unit', function () {

    $faker = Faker::create();

    $item = BusinessUnit::factory()->create([]);

    $item->name = $faker->words(3, true);

    $this->browse(function (Browser $browser) use ($item) {
        $browser->loginAs($this->admin)
            ->visit('/settings/business-units')
            ->waitForText('Showing 1 to 1 ')
            // ->waitForText(  $item->code )
            ->click('a.edit-bu[data-id="'. $item->id .'"]')
            
            ->waitForText( 'Edit an existing business unit' )
            ->assertSee('Edit an existing business unit')
            ->type('#bu-edit-modal [name="name"]', $item->name)
            ->press('#save-confirm-btn')
            ->acceptDialog()
            ->waitForLocation('/settings/business-units')
            ->waitForText('Business Unit ' . $item->code . ' has been successfully updated.')
            ->assertSee('Business Unit ' . $item->code . ' has been successfully updated.')
            ->logout('admin');
            ;

        // Verify the same data in the database
        $this->assertDatabaseHas('business_units', Arr::except( $item->attributesToArray(), 
                    ['updated_by_id', 'created_at', 'updated_at']) );
          
    });
});
    
it('deletes a business unit', function () {

    $item = BusinessUnit::factory()->create([]);

    $this->browse(function (Browser $browser) use ($item) {
        $browser->loginAs($this->admin)
            ->visit('/settings/business-units')
            ->waitForText('Showing 1 to 1 ')
            // ->waitForText(  $item->code )
            ->click('a.delete-bu[data-id="'. $item->id .'"]')
            // ->pause(10000)
            ->waitForText( 'Are you sure you want to delete business unit code "' . $item->code  . '" ?' )
            ->press('button.swal2-confirm')
            // ->press('button.swal2-cancel')
            ->waitForLocation('/settings/business-units')
            ->waitForText('Business Unit ' . $item->code . ' has been successfully deleted.')
            ->waitForText('No data available in table')
            ->assertDontSee( $item->name )
            ->logout('admin');
            ;

        $this->assertSoftDeleted('business_units',  Arr::except( $item->attributesToArray(), 
                    ['created_by_id', 'updated_by_id', 'created_at', 'updated_at']) );
        
    });
});

