<?php


use Carbon\Carbon;
use App\Models\User;
use App\Models\FSPool;
use App\Models\Region;
use App\Models\Setting;
use Tests\DuskTestCase;

use Laravel\Dusk\Browser;
use Faker\Factory as Faker;

use Illuminate\Support\Str;
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
    Region::truncate();
    FSPool::truncate();
      
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



it('redirects anonymous user to the login page when attempting to access the region', function () {
    $this->browse(function (Browser $browser) {

        $browser->visit('/settings/regions') 
            ->waitForLocation('/login')
            ->assertPathIs('/login') // Assert it redirects to login
            ->assertSee('Login'); // Assert login prompt is visible

        $browser->visit('/settings/regions/create') 
            ->waitForLocation('/login')
            ->assertPathIs('/login') // Assert it redirects to login
            ->assertSee('Login'); // Assert login prompt is visible

        $response = $this->post('/settings/regions', []);
        $response->assertStatus(302);
        $response->assertRedirect('login');

        $browser->visit('/settings/regions/1') 
            ->waitForLocation('/login')
            ->assertPathIs('/login') // Assert it redirects to login
            ->assertSee('Login'); // Assert login prompt is visible
        
        $browser->visit('/settings/regions/1/edit') 
            ->waitForLocation('/login')
            ->assertPathIs('/login') // Assert it redirects to login
            ->assertSee('Login'); // Assert login prompt is visible
        
        $response = $this->put('/settings/regions/1', []);
        $response->assertStatus(302);
        $response->assertRedirect('login');

        $response = $this->delete('/settings/regions/1');
        $response->assertStatus(302);
        $response->assertRedirect('login');
            
    });
});


it('denies unauthorized users from accessing protected region maintenance routes', function () {
    $this->browse(function (Browser $browser) {

        $browser->loginAs($this->user)
            ->visit('/settings/regions') 
            ->assertSee('USER DOES NOT HAVE THE RIGHT PERMISSIONS.')
            ->assertSee('403'); 

        $browser->loginAs($this->user)
            ->visit('/settings/regions/create') 
            ->assertSee('USER DOES NOT HAVE THE RIGHT PERMISSIONS.')
            ->assertSee('403'); 

        $browser->loginAs($this->user)
            ->visit('/settings/regions/1') 
            ->assertSee('USER DOES NOT HAVE THE RIGHT PERMISSIONS.')
            ->assertSee('403'); 
        
        $browser->loginAs($this->user)
            ->visit('/settings/regions/1/edit') 
            ->assertSee('USER DOES NOT HAVE THE RIGHT PERMISSIONS.')
            ->assertSee('403'); 
            
    });
});

it('shows validation error when required fields are missing when creating a region', function () {

        $faker = Faker::create();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit('/settings/regions')
                ->press('[data-target="#region-create-modal"]')
                ->waitForText('Add a new region')
                ->assertSee('Add a new region')
                ->press('#create-confirm-btn')
                ->acceptDialog()
            
                ->waitFor('#region-create-model-form span.text-danger')
                ->assertSee('The code field is required.')
                ->assertSee('The name field is required.') 
                ->assertSee('The effective date field is required')

                ->logout('admin')
               ;
        });

        $this->browse(function (Browser $browser) use($faker) {
            $browser->loginAs($this->admin)
                ->visit('/settings/regions')
                ->press('[data-target="#region-create-modal"]')
                ->waitForText('Add a new region')
                ->assertSee('Add a new region')
                // ->pause(1000)
                ->value('#region-create-model-form [name="code"]', 'BCC4' )
                ->value('#region-create-model-form [name="name"]', Str::random(40) )
                ->value('#region-create-model-form [name="effdt"]', Carbon::tomorrow()->format('Y-m-d') )
                ->value('#region-create-model-form [name="notes"]', $faker->sentence() )
          
                ->press('#create-confirm-btn')
                ->acceptDialog()
            
                ->waitFor('#region-create-model-form span.text-danger')
                ->assertSee('The code must be a number.')
                ->assertSee('The code format is invalid, 3 characters long and all digits.')
                ->assertSee('The name must not be greater than 30 characters.') 

                ->logout('admin')
               ;
        });

});


it('shows validation error for fields when editing a region', function () {

    $faker = Faker::create();

    $item = Region::factory()->create([]);

    $this->browse(function (Browser $browser) use ($item, $faker) {
        $browser->loginAs($this->admin)
            ->visit('/settings/regions')
            ->waitForText(  $item->code )
            ->click('a.edit-region[data-id="'. $item->id .'"]')

            ->waitForText( 'Edit an existing region' )
            ->assertSee('Edit an existing region')
            // ->pause(1000)
            ->value('#region-edit-model-form [name="name"]', Str::random(40) )
            ->press('#save-confirm-btn')
            ->acceptDialog()
            ->waitFor('#region-edit-model-form span.text-danger')
   
            ->assertSee('The name must not be greater than 30 characters.') 

            ->logout('admin')
           ;
    });

});

//



it('administrator can paginate through regions', function () {

    Region::factory(25)->create();
    $sorted = Region::orderBy('code')->get();

    $this->browse(function (Browser $browser) use($sorted) {
        $browser->loginAs($this->admin)
            ->visit('/settings/regions') // API endpoint
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



  
it('administrator successfully creates a region', function () {

    $yesterday = Carbon::yesterday(); 

    $item = Region::factory()->make([
        'effdt' => $yesterday->format('Y-m-d'),
    ]);

    $this->browse(function (Browser $browser) use($yesterday, $item) {
    
        $browser->loginAs($this->admin)
            ->visit('/settings/regions')
            ->waitForText('Showing 0 to ')
            ->press('[data-target="#region-create-modal"]')
            ->waitForText('Add a new region')
            ->assertSee('Add a new region')
            ->value('#region-create-model-form [name="code"]', $item->code)
            ->value('#region-create-model-form [name="name"]', $item->name)
            ->value('#region-create-model-form [name="effdt"]', $yesterday->format('Y-m-d'))
            ->value('#region-create-model-form [name="status"]', $item->status)
            ->value('#region-create-model-form [name="notes"]', $item->notes)
            ->press('#create-confirm-btn')
            ->acceptDialog()
            ->waitForText( $item->code . ' has been successfully created.')
            ->assertSee(  $item->code . ' has been successfully created.')
            ->logout('admin');
            ;

        // Verify the same data in the database
        $this->assertDatabaseHas('regions', Arr::except( $item->attributesToArray(), ['created_by_id', 'updated_by_id', 'hasFSPool']) );
            
    });
});


it('administrator can read and view the region', function () {

    $item = Region::factory()->create([]);

    $this->browse(function (Browser $browser) use ($item) {
        $browser->loginAs($this->admin)
            ->visit('/settings/regions')
            ->waitForText('Showing 1 to 1 ')
            // ->waitForText(  $businessUnit->code )
            ->click('a.show-region[data-id="'. $item->id .'"]')
            
            ->waitForText( 'Existing region details' )
            ->assertSee('Existing region details')
            ->assertSee( $item->code )
            ->assertSee( $item->name )
            ->assertSee( $item->notes )

            ->logout('admin');
            ;
          
    });
});


it('administrator successfully updates a region', function () {

    $faker = Faker::create();

    $item = Region::factory()->create([]);

    $item->name = $faker->words(3, true);

    $this->browse(function (Browser $browser) use ($item) {
        $browser->loginAs($this->admin)
            ->visit('/settings/regions')
            ->waitForText('Showing 1 to 1 ')
            // ->waitForText(  $item->code )
            ->click('a.edit-region[data-id="'. $item->id .'"]')
            
            ->waitForText( 'Edit an existing region' )
            ->assertSee('Edit an existing region')
            ->type('#region-edit-modal [name="name"]', $item->name)
            ->press('#save-confirm-btn')
            ->acceptDialog()
            ->waitForLocation('/settings/regions')
            ->waitForText( $item->code . ' has been successfully updated.')
            ->assertSee( $item->code . ' has been successfully updated.')
            ->logout('admin');
            ;

        // Verify the same data in the database
        $this->assertDatabaseHas('regions', Arr::except( $item->attributesToArray(), 
                    ['updated_by_id', 'created_at', 'updated_at', 'hasFSPool']) );
          
    });
});
    
it('administrator successfully deletes a region', function () {

    $item = Region::factory()->create([]);

    $this->browse(function (Browser $browser) use ($item) {
        $browser->loginAs($this->admin)
            ->visit('/settings/regions')
            ->waitForText('Showing 1 to 1 ')
            // ->waitForText(  $item->code )
            ->click('a.delete-region[data-id="'. $item->id .'"]')
            // ->pause(10000)
            ->waitForText( 'Are you sure you want to delete region code "' . $item->code  . '" ?' )
            ->press('button.swal2-confirm')
            // ->press('button.swal2-cancel')
            ->waitForLocation('/settings/regions')
            ->waitForText($item->code . ' has been successfully deleted.')
            ->waitForText('No data available in table')
            ->assertDontSee( $item->name )
            ->logout('admin');
            ;

        $this->assertSoftDeleted('regions',  Arr::except( $item->attributesToArray(), 
                    ['created_by_id', 'updated_by_id', 'created_at', 'updated_at', 'hasFSPool']) );
        
    });
});

