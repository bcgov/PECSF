<?php


use Carbon\Carbon;
use App\Models\User;
use App\Models\Pledge;
use App\Models\Setting;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Faker\Factory as Faker;

use Illuminate\Support\Str;

use App\Models\CampaignYear;
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






it('redirects anonymous user to the login page when attempting to access the campaign year', function () {
    $this->browse(function (Browser $browser) {

        $browser->visit('/settings/campaignyears') 
            ->waitForLocation('/login')
            ->assertPathIs('/login') // Assert it redirects to login
            ->assertSee('Login'); // Assert login prompt is visible

        $browser->visit('/settings/campaignyears/create') 
            ->waitForLocation('/login')
            ->assertPathIs('/login') // Assert it redirects to login
            ->assertSee('Login'); // Assert login prompt is visible

        $response = $this->post('/settings/campaignyears', []);
        $response->assertStatus(302);
        $response->assertRedirect('login');

        $browser->visit('/settings/campaignyears/1') 
            ->waitForLocation('/login')
            ->assertPathIs('/login') // Assert it redirects to login
            ->assertSee('Login'); // Assert login prompt is visible
        
        $browser->visit('/settings/campaignyears/1/edit') 
            ->waitForLocation('/login')
            ->assertPathIs('/login') // Assert it redirects to login
            ->assertSee('Login'); // Assert login prompt is visible
        
        $response = $this->put('/settings/campaignyears/1', []);
        $response->assertStatus(302);
        $response->assertRedirect('login');

        $response = $this->delete('/settings/campaignyears/1');
        $response->assertStatus(405);
            
    });
});



it('denies unauthorized users from accessing protected campaign year maintenance routes', function () {
    $this->browse(function (Browser $browser) {

        $browser->loginAs($this->user)
            ->visit('/settings/campaignyears') 
            ->assertSee('USER DOES NOT HAVE THE RIGHT PERMISSIONS.')
            ->assertSee('403'); 

        $browser->loginAs($this->user)
            ->visit('/settings/campaignyears/create') 
            ->assertSee('USER DOES NOT HAVE THE RIGHT PERMISSIONS.')
            ->assertSee('403'); 

        $browser->loginAs($this->user)
            ->visit('/settings/campaignyears/1') 
            ->assertSee('USER DOES NOT HAVE THE RIGHT PERMISSIONS.')
            ->assertSee('403'); 
        
        $browser->loginAs($this->user)
            ->visit('/settings/campaignyears/1/edit') 
            ->assertSee('USER DOES NOT HAVE THE RIGHT PERMISSIONS.')
            ->assertSee('403'); 
            
    });
});



it('shows validation error when required fields are missing when creating a campaign year', function () {

        $faker = Faker::create();

        $campaign_year = CampaignYear::create([
            'calendar_year' => 2000 + 1,
            'status' => 'A',
            'start_date' => 2000,
            'end_date' =>  '2000-12-31',
            'number_of_periods' => 26,
            'close_date' => '2000-12-31',
        ]);
 

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit('/settings/campaignyears')
                ->clickLink('Create New Campaign Year') 
                ->waitForText('Create Campaign Year')
                ->assertSee('Create Campaign Year')

                // Submit the form
                ->click('button[type="submit"]')
            
                // assertions
                ->waitFor('form[action*="campaignyears"] span.invalid-feedback')
                ->assertSee('The number of periods field is required.')
                ->assertSee('More than one active Calendar Year is not allowed') 
                ->assertSee('The start date field is required.')
                ->assertSee('The end date field is required.')
                ->assertSee('The volunteer start date field is required.')
                ->assertSee('The volunteer end date field is required.')
                ->logout('admin')
               ;
        });

            
        $this->browse(function (Browser $browser) use($faker) {

            $year = $faker->randomElement( range(2005, today()->year) );

            $browser->loginAs($this->admin)
                ->visit('/settings/campaignyears')
                ->clickLink('Create New Campaign Year') 
                ->waitForText('Create Campaign Year')
                ->assertSee('Create Campaign Year')

                // Fill in the form fields
                ->select('#calendar_year', $year) 
                ->value('input[name="number_of_periods"]', 'a10')  
                ->select('select[name="status"]', 'Active')  
                ->value('input[name="start_date"]', $year . '-11-01')  // Set start date
                ->value('input[name="end_date"]', $year . '-09-30')  // Set end date
                ->value('input[name="close_date"]', $year . '-02-28')  // Set second close date (note: could override the first if required)
                ->value('input[name="volunteer_start_date"]', $year . '-09-01')  // Set volunteer start date
                ->value('input[name="volunteer_end_date"]', $year . '-06-15')  // Set volunteer end date

                // Submit the form
                ->click('button[type="submit"]')                

                // assertions
                ->waitFor('form[action*="campaignyears"] span.invalid-feedback')
                ->assertSee('The number of periods must be a number.')
                ->assertSee('More than one active Calendar Year is not allowed')
                ->assertSee('The start date must be a date before or equal to end date.') 
                ->assertSee('The end date must be a date after or equal to start date.')
                ->assertSee('The volunteer start date must be a date before or equal to volunteer end date.')
                ->assertSee('The volunteer end date must be a date after or equal to volunteer start date.')
             
                // ->assertSee('The effective date field must be in the past if the status is Active')
                // ->assertSee('The linked bu code field is required.')
                ->logout('admin')
               ;
        });


});



it('shows validation error for fields when editing a campaign year', function () {

    $faker = Faker::create();

    $campaign_year = CampaignYear::create([
        'calendar_year' => 2000 + 1,
        'status' => 'A',
        'start_date' => 2000,
        'end_date' =>  '2000-12-31',
        'number_of_periods' => 26,
        'close_date' => '2000-12-31',
    ]);

    $row = CampaignYear::factory()->create([
        'calendar_year' => today()->year + 1,
        'status' => 'A',
        'start_date' => today()->year . '-09-01',
        'end_date' => today()->year . '-12-31',
        'number_of_periods' => 26,
        'close_date' => today()->year . '-12-31',
        'volunteer_start_date' => today()->year . '-06-01',
        'volunteer_end_date' => today()->year . '-11-15',
    ]);

    $this->browse(function (Browser $browser) use ($row, $faker) {

        $year = $faker->randomElement( range(2005, today()->year) );
        
        $browser->loginAs($this->admin)
            ->visit('/settings/campaignyears')
            ->waitForText(  $row->calendar_year )
            ->click('a.btn[href*="/settings/campaignyears/'. $row->id .'/edit"]')

            ->waitForText( 'Edit Campaign Year' )
            ->assertSee('Edit Campaign Year')

            // Fill in the form fields

            ->value('input[name="number_of_periods"]', 'a10')  
            ->value('input[name="start_date"]', $year . '-11-01')  // Set start date
            ->value('input[name="end_date"]', $year . '-09-30')  // Set end date
            ->value('input[name="close_date"]', $year . '-02-28')  // Set second close date (note: could override the first if required)
            ->value('input[name="volunteer_start_date"]', $year . '-09-01')  // Set volunteer start date
            ->value('input[name="volunteer_end_date"]', $year . '-06-15')  // Set volunteer end date

            // Submit the form
            ->click('button[type="submit"]')                

            // Assertions
            ->waitFor('form[action*="campaignyears"] span.invalid-feedback')
            ->assertSee('The number of periods must be a number.')
            ->assertSee('More than one active Calendar Year is not allowed')
            ->assertSee('The start date must be a date before or equal to end date.') 
            ->assertSee('The end date must be a date after or equal to start date.')
            ->assertSee('The volunteer start date must be a date before or equal to volunteer end date.')
            ->assertSee('The volunteer end date must be a date after or equal to volunteer start date.')

            ->logout('admin')
           ;
    });

});



it('administrator can paginate through campaign years', function () {

    CampaignYear::factory(25)->create();
    $sorted = CampaignYear::orderBy('calendar_year','desc')->get();

    $this->browse(function (Browser $browser) use($sorted) {
        $browser->loginAs($this->admin)
            ->visit('/settings/campaignyears') // API endpoint
            ->waitForText(  'Showing 1 to 10' )
            ->assertSee( $sorted[0]->calendar_year ) // Check if "name" key exists in JSON
            ->assertSee( $sorted[0]->start_date->format('Y-m-d') ) // Check if "name" key exists in JSON
            ->click('a[data-dt-idx="2"]')
            ->waitForText(  'Showing 11 to 20' )
            ->assertSee( $sorted[10]->calendar_year ) // Ch
            ->assertSee( $sorted[10]->start_date->format('Y-m-d') ) // Check if "name" key exists in JSON
            ->logout('admin')
            ;

    });

});



  
it('administrator successfully creates a campaign year', function () {

    $item = CampaignYear::factory()->make([
        'calendar_year' => today()->year + 1,
        'status' => 'A',
        'start_date' => today()->year . '-09-01',
        'end_date' => today()->year . '-12-31',
        'number_of_periods' => 26,
        'close_date' => today()->year . '-12-31',
        'volunteer_start_date' => today()->year . '-06-01',
        'volunteer_end_date' => today()->year . '-11-15',
    ]);

    $this->browse(function (Browser $browser) use($item) {

        $browser->loginAs($this->admin)
            ->visit('/settings/campaignyears')
            ->clickLink('Create New Campaign Year') 
            ->waitForText('Create Campaign Year')
            ->assertSee('Create Campaign Year')

            // Fill in the form fields
            ->select('#calendar_year', $item->calendar_year) 
            ->value('input[name="number_of_periods"]', $item->number_of_periods)  
            ->select('select[name="status"]', $item->status)  
            ->value('input[name="start_date"]', $item->start_date->format('Y-m-d') )  // Set start date
            ->value('input[name="end_date"]', $item->end_date->format('Y-m-d') )  // Set end date
            ->value('input[name="close_date"]', $item->close_date->format('Y-m-d') )  // Set second close date (note: could override the first if required)
            ->value('input[name="volunteer_start_date"]', $item->volunteer_start_date->format('Y-m-d'))  // Set volunteer start date
            ->value('input[name="volunteer_end_date"]', $item->volunteer_end_date->format('Y-m-d'))  // Set volunteer end date
            // Submit the form
            ->click('button[type="submit"]')                

            // assertions
            ->waitForLocation('/settings/campaignyears')
            ->assertPathIs('/settings/campaignyears') // Assert it redirects to login
            ->assertSee(' created successfully') // Assert login prompt is visible
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
        $this->assertDatabaseHas('campaign_years', Arr::except( $item->attributesToArray(), ['as_of_date', 'created_by_id', 'modified_by_id']) );

    });

});



it('administrator can read and view the campaign year', function () {

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

    $this->browse(function (Browser $browser) use ($item) {
        $browser->loginAs($this->admin)
            ->visit('/settings/campaignyears')
            ->waitForText('Showing 1 to 1 ')
            // ->waitForText(  $businessUnit->code )
            ->click('a.btn[href$="/settings/campaignyears/'. $item->id .'"]')
            ->waitForText( 'Campaign Years' )
            ->assertSee('Campaign Years')
            
            ->assertValue('input[name="start_date"]', $item->start_date->format('Y-m-d') )
            ->assertValue('input[name="end_date"]', $item->end_date->format('Y-m-d') )
            ->assertValue('input[name="close_date"]', $item->close_date->format('Y-m-d') )

            ->logout('admin');
         
    });
});




it('administrator successfully updates a campaign year', function () {

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
            ->visit('/settings/campaignyears')
            ->waitForText('Showing 1 to 1 ')
            // ->waitForText(  $businessUnit->code )
            ->click('a.btn[href$="/settings/campaignyears/'. $item->id .'/edit"]')
            ->waitForText( 'Edit Campaign Year' )
            ->assertSee('Edit Campaign Year')

            // Fill in the form fields
            ->value('input[name="number_of_periods"]', $item->number_of_periods)  
            ->select('select[name="status"]', $item->status)  
            ->value('input[name="start_date"]', $item->start_date->format('Y-m-d') )  // Set start date
            ->value('input[name="end_date"]', $item->end_date->format('Y-m-d') )  // Set end date

            // Submit the form
            ->click('button[type="submit"]')                
 
            // assertions
            ->waitForLocation('/settings/campaignyears')
            ->assertPathIs('/settings/campaignyears') // Assert it redirects to login
            ->assertSee(' updated successfully') // Assert login prompt is visible
            ;

        // Verify the same data in the database
        // Verify the same data in the database
        $this->assertDatabaseHas('campaign_years', Arr::except( $item->attributesToArray(), ['as_of_date', 'created_by_id', 'modified_by_id', 'created_at', 'updated_at']) );
          
    });
});
