<?php


use Carbon\Carbon;
use App\Models\User;
use App\Models\FSPool;
use App\Models\Pledge;
use App\Models\Region;
use App\Models\Charity;
use App\Models\Setting;

use Tests\DuskTestCase;

use Laravel\Dusk\Browser;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use App\Models\CampaignYear;
use App\Models\FSPoolCharity;
use App\Models\BankDepositForm;
use App\Models\DonateNowPledge;
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
    CampaignYear::truncate();
    Region::truncate();
    Charity::truncate();
    FSPool::truncate();
    FSPoolCharity::truncate();
   
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






it('redirects anonymous user to the login page when attempting to access the fund supported pool', function () {
    $this->browse(function (Browser $browser) {

        $browser->visit('/settings/fund-supported-pools') 
            ->waitForLocation('/login')
            ->assertPathIs('/login') // Assert it redirects to login
            ->assertSee('Login'); // Assert login prompt is visible

        $browser->visit('/settings/fund-supported-pools/create') 
            ->waitForLocation('/login')
            ->assertPathIs('/login') // Assert it redirects to login
            ->assertSee('Login'); // Assert login prompt is visible

        $response = $this->post('/settings/fund-supported-pools', []);
        $response->assertStatus(302);
        $response->assertRedirect('login');

        $browser->visit('/settings/fund-supported-pools/1') 
            ->waitForLocation('/login')
            ->assertPathIs('/login') // Assert it redirects to login
            ->assertSee('Login'); // Assert login prompt is visible
        
        $browser->visit('/settings/fund-supported-pools/1/edit') 
            ->waitForLocation('/login')
            ->assertPathIs('/login') // Assert it redirects to login
            ->assertSee('Login'); // Assert login prompt is visible
        
        $response = $this->put('/settings/fund-supported-pools/1', []);
        $response->assertStatus(302);
        $response->assertRedirect('login');

        $response = $this->delete('/settings/fund-supported-pools/1');
        $response->assertStatus(302);
            
    });
});



it('denies unauthorized users from accessing protected fund supported pool maintenance routes', function () {
    $this->browse(function (Browser $browser) {

        $browser->loginAs($this->user)
            ->visit('/settings/fund-supported-pools') 
            ->assertSee('USER DOES NOT HAVE THE RIGHT PERMISSIONS.')
            ->assertSee('403'); 

        $browser->loginAs($this->user)
            ->visit('/settings/fund-supported-pools/create') 
            ->assertSee('USER DOES NOT HAVE THE RIGHT PERMISSIONS.')
            ->assertSee('403'); 

        $browser->loginAs($this->user)
            ->visit('/settings/fund-supported-pools/1') 
            ->assertSee('USER DOES NOT HAVE THE RIGHT PERMISSIONS.')
            ->assertSee('403'); 
        
        $browser->loginAs($this->user)
            ->visit('/settings/fund-supported-pools/1/edit') 
            ->assertSee('USER DOES NOT HAVE THE RIGHT PERMISSIONS.')
            ->assertSee('403'); 
            
    });
});



it('shows validation error when required fields are missing when creating a fund supported pool', function () {

        $faker = Faker::create();

        // $campaign_year = CampaignYear::create([
        //     'calendar_year' => 2000 + 1,
        //     'status' => 'A',
        //     'start_date' => 2000,
        //     'end_date' =>  '2000-12-31',
        //     'number_of_periods' => 26,
        //     'close_date' => '2000-12-31',
        // ]);
 

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit('/settings/fund-supported-pools')
                ->clickLink('Add New Fund Supported Pool') 
                ->waitForText('Create a New Fund Supported Pool')
                ->assertSee('Create a New Fund Supported Pool')

                // Submit the form
                ->click('input[type="submit"]')
            
                // assertions
                ->waitFor('#create_pool span.invalid-feedback')
                ->assertSee('The region id field is required.')
                ->assertSee('The date field is incomplete or has an invalid date') 
                ->assertSee('The charity field is required')
                ->assertSee('Please enter a supported program name')
                ->assertSee('Please enter a supported program description')
                ->assertSee('Please enter a percentage')
                ->assertSee('Please enter a contact name')
                ->assertSee('Please enter a valid email format')
                ->assertSee('The image must be a file of type: jpg, jpeg, png, bmp')
                ->logout('admin')
               ;
        });

            
        $this->browse(function (Browser $browser) use($faker) {

            $year = $faker->randomElement( range(2005, today()->year) );

            $browser->loginAs($this->admin)
                ->visit('/settings/fund-supported-pools')
                ->clickLink('Add New Fund Supported Pool') 
                ->waitForText('Create a New Fund Supported Pool')
                ->assertSee('Create a New Fund Supported Pool')

                // Add a new row
                ->click('button#add_row')

                // Fill in the form fields
                // ->select('#calendar_year', $year) 
                ->value('[name="names[]"]', Str::random(80))
                ->value('[name="contact_emails[]"]', Str::random(10) )
                ->value('[name="percentages[]"]',90)
                // ->value('input[name="number_of_periods"]', 'a10')  
                // ->select('select[name="status"]', 'Active')  
                // ->value('input[name="start_date"]', $year . '-11-01')  // Set start date
                // ->value('input[name="end_date"]', $year . '-09-30')  // Set end date
                // ->value('input[name="close_date"]', $year . '-02-28')  // Set second close date (note: could override the first if required)
                // ->value('input[name="volunteer_start_date"]', $year . '-09-01')  // Set volunteer start date
                // ->value('input[name="volunteer_end_date"]', $year . '-06-15')  // Set volunteer end date

                // Submit the form
                ->click('input[type="submit"]')

                // assertions
                // row 0
                ->waitFor('#create_pool span.invalid-feedback')
                ->assertSee('must not be greater than 50 characters')
                ->assertSee('Please enter a valid email format')
                ->assertSee('The sum of percentage is not 100%')

                ->assertSee('The region id field is required.')
                ->assertSee('The date field is incomplete or has an invalid date') 
                ->assertSee('The charity field is required')
                ->assertSee('Please enter a supported program name')
                ->assertSee('Please enter a supported program description')
                ->assertSee('Please enter a percentage')
                ->assertSee('Please enter a contact name')
                ->assertSee('Please enter a valid email format')
                ->assertSee('The image must be a file of type: jpg, jpeg, png, bmp')
             
                // ->assertSee('The effective date field must be in the past if the status is Active')
                // ->assertSee('The linked bu code field is required.')
                ->logout('admin')
               ;
        });


});



it('shows validation error for fields when editing a fund supported pool', function () {

    $faker = Faker::create();

    $region = Region::factory()->create();
    $charities = Charity::factory(5)->create([
        'charity_status' => 'Registered',
    ]);

    $row = FSPool::factory()->create([
                'region_id' => $region->id,
                'start_date' => today()->addDays(10),
            ]);

    $pool_charities = FSPoolCharity::factory()->create([
        'f_s_pool_id' => $row->id,
        'charity_id' => $faker->randomElement( $charities->pluck('id')->toArray() ),
    ]);

    $pool_charities = FSPoolCharity::factory()->create([
        'f_s_pool_id' => $row->id,
        'charity_id' => $faker->randomElement( $charities->pluck('id')->toArray() ),
    ]);


    $this->browse(function (Browser $browser) use ($row, $faker) {

        $browser->loginAs($this->admin)
            ->visit('/settings/fund-supported-pools')
            ->select('#fspool-filter-form select[name="effective_type"]','')
            ->waitForText(  $row->region->name )
            ->pause(1500)
            ->click('form[action$="'.$row->id .'/edit"] input.edit-pool')

            ->waitForText( 'Edit Fund Supported Pool' )
            ->assertSee('Edit Fund Supported Pool')

            // Fill in the form fields
            ->value('[name="names[]"]', Str::random(80))
            ->value('[name="contact_emails[]"]', Str::random(10) )
            ->value('[name="percentages[]"]',90)

            // Submit the form
            ->click('input[type="submit"]')

            // Assertions
            ->waitFor('span.invalid-feedback')
            ->pause(1000)
            ->assertSee('must not be greater than 50 characters')
            ->assertSee('Please enter a valid email format')
            ->assertSee('The sum of percentage is not 100')

            ->logout('admin')
           ;
    });

});


it('administrator can paginate through fund supported pools', function () {

    $faker = Faker::create();

    for($i = 1; $i<=40; $i++) {
        $region = Region::factory()->create();
        $charities = Charity::factory(1)->create([
            'charity_status' => 'Registered',
        ]);
    
        $row = FSPool::factory()->create([
                    'region_id' => $region->id,
                    'start_date' => today()->addDays(10),
                ]);
    
        $pool_charities = FSPoolCharity::factory(1)->create([
            'f_s_pool_id' => $faker->randomElement( $row->pluck('id')->toArray() ),
            'charity_id' => $faker->randomElement( $charities->pluck('id')->toArray() ),
        ]);
    }

    $sorted = FSPool::join('regions','regions.id', 'f_s_pools.id')
                        ->orderBy('regions.code')->get();

    $this->browse(function (Browser $browser) use($sorted) {
        $browser->loginAs($this->admin)
            ->visit('/settings/fund-supported-pools') // API endpoint
            ->select('#fspool-filter-form select[name="effective_type"]','')
            ->waitForText(  'Showing 1 to 10' )
            ->assertSee( $sorted[0]->region->name ) // Check if "name" key exists in JSON
            ->assertSee( $sorted[0]->start_date->format('Y-m-d') ) // Check if "name" key exists in JSON
            ->click('a[data-dt-idx="2"]')
            ->waitForText(  'Showing 11 to 20' )
            ->assertSee( $sorted[10]->region->name ) // Ch
            ->assertSee( $sorted[10]->start_date->format('Y-m-d') ) // Check if "name" key exists in JSON
            ->logout('admin')
            ;

    });

});



  
it('administrator successfully creates a fund supported pool', function () {

    $faker = Faker::create();

    $region = Region::factory()->create();
    $charities = Charity::factory(10)->create([
        'charity_status' => 'Registered',
    ]);

    $item = FSPool::factory()->make([
        'region_id' => $region->id,
        'start_date' => today()->addDays(10),
    ]);

    $pool_charity = FSPoolCharity::factory()->make([
        'charity_id' => $faker->randomElement( $charities->pluck('id')->toArray() ),
    ]);

    $item->charities[0] = $pool_charity;

    $this->browse(function (Browser $browser) use($item, $faker) {

        $browser->loginAs($this->admin)
            ->visit('/settings/fund-supported-pools')
            ->clickLink('Add New Fund Supported Pool') 
            ->waitForText('Create a New Fund Supported Pool')
            ->assertSee('Create a New Fund Supported Pool')

            // Fill in the form fields
            ->select('[name="region_id"]', $item->region_id) 
            ->value('input[name="start_date"]', $item->start_date->format('Y-m-d') )  // Set start date
            ->select('[name="pool_status"]', $item->status) 

            // Open the SELECT2 dropdown
            ->click('#create_pool .select2-container') // Select the container of the SELECT2 field
            ->pause(1000)
            // ->waitFor('.select2-selection__rendered') // Wait for the dropdown to appear
            ->waitFor('.select2-dropdown')
            // Select an option from the dropdown
            ->click('.select2-results__option') // Click the option in the dropdown
            ->waitUntilMissing('.select2-dropdown') // Wait for the dropdown to close

            ->value('[name="names[]"]', $item->charities[0]->name)
            ->click('textarea[name="descriptions[]"]')
            ->type('[name="descriptions[]"]', $item->charities[0]->description)
            ->value('[name="contact_names[]"]', $item->charities[0]->contact_name)
            ->value('[name="contact_titles[]"]', $item->charities[0]->contact_title)
            ->value('[name="contact_emails[]"]', $item->charities[0]->contact_email)
            ->value('[name="percentages[]"]',100)
            ->attach('#create_pool input[name="images[]"]', __DIR__ . '/files/logo-1.png') // Path to the file you want to upload

            // Submit the form
            ->click('input[type="submit"]')
       
            // assertions
            ->waitForLocation('/settings/fund-supported-pools')
            ->assertPathIs('/settings/fund-supported-pools') // Assert it redirects to login
            ->assertSee(' successfully created') // Assert login prompt is visible
            // ->assertSee('The number of periods must be a number.')
            // ->assertSee('More than one active Calendar Year is not allowed')
            // ->assertSee('The start date must be a date before or equal to end date.') 
            // ->assertSee('The end date must be a date after or equal to start date.')
            // ->assertSee('The volunteer start date must be a date before or equal to volunteer end date.')
            // ->assertSee('The volunteer end date must be a date after or equal to volunteer start date.')
        
            // ->assertSee('The effective date field must be in the past if the status is Active')
            // ->assertSee('The linked bu code field is required.')
            ->logout('admin')
        ;

        // Verify the same data in the database
        $this->assertDatabaseHas('f_s_pools', [
            "region_id" => $item->region_id,
            "status" => $item->status,
            "start_date" => $item->start_date,
        ]);

    });

});



it('administrator can read and view the fund supported pool', function () {

    $faker = Faker::create();

    $region = Region::factory()->create();
    $charities = Charity::factory(10)->create([
        'charity_status' => 'Registered',
    ]);


    $item = FSPool::factory()->create([
        'region_id' => $region->id,
        'start_date' => today()->addDays(10),
    ]);

    $pool_charity = FSPoolCharity::factory()->create([
        'f_s_pool_id'  => $item->id,
        'charity_id' => $faker->randomElement( $charities->pluck('id')->toArray() ),
    ]);


    $this->browse(function (Browser $browser) use ($item) {
        $browser->loginAs($this->admin)
            ->visit('/settings/fund-supported-pools')
            ->select('#fspool-filter-form select[name="effective_type"]','')
            ->waitForText('Showing 1 to 1 ')
            // ->waitForText(  $businessUnit->code )
            ->click('form[action$="fund-supported-pools/'.$item->id .'"] input.show-pool')
            ->waitForText( 'Fund Supported Pool' )
            ->assertSee('Fund Supported Pool')
         
            ->assertValue('input[name="region"]', $item->region->name )
            ->assertValue('input[name="start_date"]', $item->start_date->format('Y-m-d') )
            ->assertValue('tr#charity0 input[name="charities[]"]', 
                                    $item->charities[0]->charity->charity_name . ' (' . 
                                    $item->charities[0]->charity->registration_number . ')')
            ->logout('admin');
    });
    
});


/*

it('administrator successfully updates a fund supported pool', function () {

    $faker = Faker::create();

    $item = CampaignYear::factory()->create([
        'calendar_year' => today()->year + 1,
        'status' => 'A',
        'start_date' => today()->year . '-09-01',
        'end_date' => today()->year . '-12-31',
        'number_of_periods' => 26,
        'close_date' => today()->year . '-12-31',
        'volunteer_start_date' => today()->year . '-06-01',
        'volunteer_end_date' => today()->year . '-11-15',
    ]);

    $item->number_of_periods = 27;
    $item->status = 'I';
    $item->start_date = today()->year . '-07-01';
    $item->end_date = today()->year . '-11-30';

    $this->browse(function (Browser $browser) use ($item) {

        $browser->loginAs($this->admin)
            ->visit('/settings/fund-supported-pools')
            ->waitForText('Showing 1 to 1 ')
            // ->waitForText(  $businessUnit->code )
            ->click('a.btn[href$="/settings/fund-supported-pools/'. $item->id .'/edit"]')
            ->waitForText( 'Edit fund supported pool' )
            ->assertSee('Edit fund supported pool')

            // Fill in the form fields
            ->value('input[name="number_of_periods"]', $item->number_of_periods)  
            ->select('select[name="status"]', $item->status)  
            ->value('input[name="start_date"]', $item->start_date->format('Y-m-d') )  // Set start date
            ->value('input[name="end_date"]', $item->end_date->format('Y-m-d') )  // Set end date

            // Submit the form
            ->click('button[type="submit"]')                
 
            // assertions
            ->waitForLocation('/settings/fund-supported-pools')
            ->assertPathIs('/settings/fund-supported-pools') // Assert it redirects to login
            ->assertSee(' updated successfully') // Assert login prompt is visible
            ;

        // Verify the same data in the database
        // Verify the same data in the database
        $this->assertDatabaseHas('campaign_years', Arr::except( $item->attributesToArray(), ['as_of_date', 'created_by_id', 'modified_by_id', 'created_at', 'updated_at']) );
          
    });
});
*/