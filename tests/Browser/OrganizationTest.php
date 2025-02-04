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

use App\Models\Organization;
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
    Organization::truncate();
      
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



it('redirects anonymous user to the login page', function () {
    $this->browse(function (Browser $browser) {

        $browser->visit('/settings/organizations') 
            ->waitForLocation('/login')
            ->assertPathIs('/login') // Assert it redirects to login
            ->assertSee('Login'); // Assert login prompt is visible

        $browser->visit('/settings/organizations/create') 
            ->waitForLocation('/login')
            ->assertPathIs('/login') // Assert it redirects to login
            ->assertSee('Login'); // Assert login prompt is visible

        $response = $this->post('/settings/organizations', []);
        $response->assertStatus(302);
        $response->assertRedirect('login');

        $browser->visit('/settings/organizations/1') 
            ->waitForLocation('/login')
            ->assertPathIs('/login') // Assert it redirects to login
            ->assertSee('Login'); // Assert login prompt is visible
        
        $browser->visit('/settings/organizations/1/edit') 
            ->waitForLocation('/login')
            ->assertPathIs('/login') // Assert it redirects to login
            ->assertSee('Login'); // Assert login prompt is visible
        
        $response = $this->put('/settings/organizations/1', []);
        $response->assertStatus(302);
        $response->assertRedirect('login');

        $response = $this->delete('/settings/organizations/1');
        $response->assertStatus(302);
        $response->assertRedirect('login');
            
    });
});


it('denies access to protected organizations maintenance routes for unauthorized user', function () {
    $this->browse(function (Browser $browser) {

        $browser->loginAs($this->user)
            ->visit('/settings/organizations') 
            ->assertSee('USER DOES NOT HAVE THE RIGHT PERMISSIONS.')
            ->assertSee('403'); 

        $browser->loginAs($this->user)
            ->visit('/settings/organizations/create') 
            ->assertSee('USER DOES NOT HAVE THE RIGHT PERMISSIONS.')
            ->assertSee('403'); 

        $browser->loginAs($this->user)
            ->visit('/settings/organizations/1') 
            ->assertSee('USER DOES NOT HAVE THE RIGHT PERMISSIONS.')
            ->assertSee('403'); 
        
        $browser->loginAs($this->user)
            ->visit('/settings/organizations/1/edit') 
            ->assertSee('USER DOES NOT HAVE THE RIGHT PERMISSIONS.')
            ->assertSee('403'); 
            
    });
});

it('validateion error on create', function () {

        $faker = Faker::create();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit('/settings/organizations')
                ->press('[data-target="#organization-create-modal"]')
                ->waitForText('Add a new organization')
                ->assertSee('Add a new organization')
                ->press('#create-confirm-btn')
                ->acceptDialog()

                ->waitFor('#organization-create-model-form span.text-danger')
                ->assertSee('The code field is required.')
                ->assertSee('The name field is required.') 
                ->assertSee('The effective date field is required.')
                ->assertSee('The business unit field is required.')
                ->logout('admin')
               ;
        });

        $this->browse(function (Browser $browser) use($faker) {
            $browser->loginAs($this->admin)
                ->visit('/settings/organizations')
                ->press('[data-target="#organization-create-modal"]')
                ->waitForText('Add a new organization')
                ->assertSee('Add a new organization')
                ->pause(1000)

                ->type('#organization-create-model-form [name="code"]', Str::random(8)  )
                ->type('#organization-create-model-form [name="name"]', Str::random(80) )
                ->select('#organization-create-model-form [name="status"]', 'A' )
                ->type('#organization-create-model-form [name="effdt"]', Carbon::tomorrow()->format('m-d-Y') )
                ->select('#organization-create-model-form [name="bu_code"]', '' )
                ->press('#create-confirm-btn')
                ->acceptDialog()
              
                ->waitFor('#organization-create-model-form span.text-danger')
                ->assertSee('The code format is invalid, max 3 characters long and all uppercase, no space.')
                ->assertSee('The name must not be greater than 50 characters.') 
                // ->assertSee('The effective date field must be in the past if the status is Active')
                ->assertSee('The business unit field is required.')
             
                // ->assertSee('The effective date field must be in the past if the status is Active')
                // ->assertSee('The linked bu code field is required.')
                ->logout('admin')
               ;
        });

});


it('Government Org validation error on edit', function () {

    $faker = Faker::create();

    $yesterday = Carbon::yesterday(); 

    $item = Organization::factory()->create([
        'code' => 'GOV',
        'effdt' => $yesterday->format('Y-m-d'),
    ]);

    $this->browse(function (Browser $browser) use ($item, $faker) {
      
        $browser->loginAs($this->admin)
            ->visit('/settings/organizations')
            ->waitForText('Showing 1 to ')
            ->click('a.edit-organization[data-id="'. $item->id .'"]')

            ->waitForText( 'Edit an existing organization' )
            ->assertSee('Edit an existing organization')
            // ->pause(1000)
            ->type('#organization-edit-model-form [name="name"]', ' ' )
            ->type('#organization-edit-model-form [name="effdt"]', ' ' )
            ->press('#save-confirm-btn')
            ->acceptDialog()
            ->waitFor('#organization-edit-model-form span.text-danger')

            ->assertSee('The name field is required.') 
            ->assertSee('The effective date field is required.')

            ->logout('admin')
        ;
    });
});

it('Non-GOV organization validation error on edit', function () {

    $faker = Faker::create();

    $yesterday = Carbon::yesterday(); 

    $bu = BusinessUnit::factory()->create();

    $item = Organization::factory()->create([
        
        'effdt' => $yesterday->format('Y-m-d'),
        'bu_code' => $bu->code,
    ]);

    $this->browse(function (Browser $browser) use ($item, $faker) {
        $browser->loginAs($this->admin)
            ->visit('/settings/organizations')
            ->waitForText('Showing 1 to ')
            ->click('a.edit-organization[data-id="'. $item->id .'"]')

            ->waitForText( 'Edit an existing organization' )
            ->assertSee('Edit an existing organization')
            // ->pause(1000)
            ->type('#organization-edit-model-form [name="name"]', ' ' )
            ->type('#organization-edit-model-form [name="effdt"]', ' ' )
            ->select('#organization-edit-model-form [name="bu_code"]', '' )
            ->press('#save-confirm-btn')
            ->acceptDialog()
            ->waitFor('#organization-edit-model-form span.text-danger')

            ->assertSee('The name field is required.') 
            ->assertSee('The effective date field is required.')
            ->assertSee('The business unit field is required.')

            ->logout('admin')
        ;
    });

});

//



it('pagination on organizations table via ajax call', function () {

    $faker = Faker::create();

    $bus = BusinessUnit::factory(25)->create();

    Organization::factory(25)->create([
        'bu_code' => $faker->randomElement(  $bus->pluck('code')->toArray() ),
    ]);
    
    $sorted = Organization::orderBy('code')->get();

    $this->browse(function (Browser $browser) use($sorted) {
        $browser->loginAs($this->admin)
            ->visit('/settings/organizations') // API endpoint
            ->screenshot('debug-pagation-page') 

            ->waitForText(  'Showing 1 to 10' )
            ->assertSee( $sorted[0]->code ) 
            ->assertSee( $sorted[0]->name ) 

            ->click('a[data-dt-idx="2"]')
            ->waitForText(  'Showing 11 to 20' )
            ->assertSee( $sorted[10]->code ) 
            ->assertSee( $sorted[10]->name ) 

            ->logout('admin')
            ;

    });

});



  
it('administrator creates a new organization', function () {

    $yesterday = Carbon::yesterday(); 

    $bu = BusinessUnit::factory()->create();

    $item = Organization::factory()->make([
        'effdt' => $yesterday->format('Y-m-d'),
        'bu_code' => $bu->code,
    ]);

    $this->browse(function (Browser $browser) use($item, $yesterday) {
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
            ->visit('/settings/organizations')
            ->press('[data-target="#organization-create-modal"]')
            // ->waitForText('Add a new organization')
            ->waitForText('Add a new organization')
            ->assertSee('Add a new organization')
            ->pause(1000)
            ->type('#organization-create-model-form [name="code"]', $item->code)
            ->type('#organization-create-model-form [name="name"]', $item->name)
            ->type('#organization-create-model-form [name="effdt"]', $yesterday->format('mdY'))
            ->select('#organization-create-model-form [name="status"]', $item->status)
            ->select('#organization-create-model-form [name="bu_code"]', $item->business_unit->code)
           
            ->press('#create-confirm-btn')
            ->acceptDialog()
            ->waitForText('Organization code ' . $item->code . ' has been successfully created.')
            ->assertSee('Organization code ' . $item->code . ' has been successfully created.')
            ->logout('admin');
            ;

        // Verify the same data in the database
        $this->assertDatabaseHas('organizations', Arr::except( $item->attributesToArray(), ['created_by_id', 'updated_by_id']) );
            
    });
});


it('show a organization', function () {

    $yesterday = Carbon::yesterday(); 

    $bu = BusinessUnit::factory()->create();

    $item = Organization::factory()->create([
        'effdt' => $yesterday->format('Y-m-d'),
        'bu_code' => $bu->code,
    ]);

    $this->browse(function (Browser $browser) use ($item, $yesterday) {
        $browser->loginAs($this->admin)
            ->visit('/settings/organizations')
            ->waitForText('Showing 1 to ')
            ->click('a.show-organization[data-id="'. $item->id .'"]')
            
            ->waitForText( 'Existing organization details' )
            ->assertSee('Existing organization details')
            ->assertSee( $item->code )
            ->assertSee( $item->name )
            ->assertSee($yesterday->format('Y-m-d'))
            ->logout('admin');
            ;
          
    });
});


it('update an organization', function () {

    $faker = Faker::create();

    $yesterday = Carbon::yesterday(); 

    $bu = BusinessUnit::factory()->create();

    $item = Organization::factory()->create([
        'effdt' => $yesterday->format('Y-m-d'),
        'bu_code' => $bu->code,
    ]);

    $item->name = $faker->words(3, true);

    $this->browse(function (Browser $browser) use ($item) {
        $browser->loginAs($this->admin)
            ->visit('/settings/organizations')
            ->waitForText('Showing 1 to ')
            ->click('a.edit-organization[data-id="'. $item->id .'"]')
            
            ->waitForText( 'Edit an existing organization' )
            ->assertSee('Edit an existing organization')
            ->type('#organization-edit-modal [name="name"]', $item->name)
            ->press('#save-confirm-btn')
            ->acceptDialog()
            // ->waitForLocation('/settings/organizations')
            ->waitForText('Organization code ' . $item->code . ' has been successfully updated.')
            ->assertSee('Organization code ' . $item->code . ' has been successfully updated.')
            ->logout('admin');
            ;

        // Verify the same data in the database
        $this->assertDatabaseHas('organizations', Arr::except( $item->attributesToArray(), 
                    ['updated_by_id', 'created_at', 'updated_at']) );
          
    });
});
    
it('deletes a organization', function () {

    $item = Organization::factory()->create([]);

    $this->browse(function (Browser $browser) use ($item) {
        $browser->loginAs($this->admin)
            ->visit('/settings/organizations')
            ->waitForText('Showing 1 to ')
            ->click('a.delete-organization[data-id="'. $item->id .'"]')
            // ->pause(10000)
            ->waitForText( 'Are you sure you want to delete organization code "' . $item->code  . '" ?' )
            ->press('button.swal2-confirm')
            // ->press('button.swal2-cancel')
            ->waitForLocation('/settings/organizations')
            ->waitForText('Organization code ' . $item->code . ' has been successfully deleted.')
            ->waitForText('No data available in table')
            ->assertDontSee( $item->name )
            ->logout('admin');
            ;

        $this->assertSoftDeleted('organizations',  Arr::except( $item->attributesToArray(), 
                    ['created_by_id', 'updated_by_id', 'created_at', 'updated_at']) );
        
    });
});

