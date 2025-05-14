<?php


use Carbon\Carbon;
use App\Models\PayCalendar;
use App\Models\User;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

use Faker\Factory as Faker;
use Illuminate\Support\Str;
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
    PayCalendar::truncate();
      
    $this->admin = User::whereHas("roles", function ($q) {$q->where("name", "admin");})->first();
    $this->user  = User::doesntHave('roles')->orderBy('id')->first();

});

afterEach(function () {
    // Cleanup admin user
    // $this->admin->delete();
    
    // Refresh the browser after each test
    session()->flush();
});



it('redirects anonymous user to the login page when attempting to access the pay calendar', function () {
    $this->browse(function (Browser $browser) {

        $browser->visit('/settings/pay-calendars') 
            ->waitForLocation('/login')
            ->assertPathIs('/login') // Assert it redirects to login
            ->assertSee('Login'); // Assert login prompt is visible

        $response = $this->get('/settings/pay-calendars/create');
        $response->assertStatus(404);

        $response = $this->post('/settings/pay-calendars', []);
        $response->assertStatus(405);

        $response = $this->get('/settings/pay-calendars/1');
        $response->assertStatus(404);
        
        $response = $this->get('/settings/pay-calendars/1/edit');
        $response->assertStatus(404);
        
        $response = $this->put('/settings/pay-calendars/1', []);
        $response->assertStatus(404);

        $response = $this->delete('/settings/pay-calendars/1');
        $response->assertStatus(404);
            
    });
});


it('denies unauthorized users from accessing protected pay calendar maintenance routes', function () {
    $this->browse(function (Browser $browser) {

        $browser->loginAs($this->user)
            ->visit('/settings/pay-calendars') 
            ->assertSee('USER DOES NOT HAVE THE RIGHT PERMISSIONS.')
            ->assertSee('403'); 

        $this->actingAs($this->user);
        $response = $this->get('/settings/pay-calendars/create');
        $response->assertStatus(404);

        $this->actingAs($this->user);
        $response = $this->post('/settings/pay-calendars', []);
        $response->assertStatus(405);

        $this->actingAs($this->user);
        $response = $this->get('/settings/pay-calendars/1');
        $response->assertStatus(404);

        $this->actingAs($this->user);
        $response = $this->get('/settings/pay-calendars/1/edit');
        $response->assertStatus(404);

        $this->actingAs($this->user);
        $response = $this->put('/settings/pay-calendars/1', [] );
        $response->assertStatus(404);

        $this->actingAs($this->user);
        $response = $this->delete('/settings/pay-calendars/1');
        $response->assertStatus(404);
            
    });
});


it('administrator cannot access unavailable pay calendar maintenance routes', function () {
    $this->browse(function (Browser $browser) {

        $this->actingAs($this->admin);
        $response = $this->get('/settings/pay-calendars/create');
        $response->assertStatus(404);

        $this->actingAs($this->admin);
        $response = $this->post('/settings/pay-calendars', []);
        $response->assertStatus(405);

        $this->actingAs($this->admin);
        $response = $this->get('/settings/pay-calendars/1');
        $response->assertStatus(404);

        $this->actingAs($this->admin);
        $response = $this->get('/settings/pay-calendars/1/edit');
        $response->assertStatus(404);

        $this->actingAs($this->admin);
        $response = $this->put('/settings/pay-calendars/1', [] );
        $response->assertStatus(404);

        $this->actingAs($this->admin);
        $response = $this->delete('/settings/pay-calendars/1');
        $response->assertStatus(404);
            
    });
});

it('administrator can paginate through pay calendars', function () {

    PayCalendar::factory(100)->create();
    $sorted = PayCalendar::orderBy('id', 'desc')->get();

    $this->browse(function (Browser $browser) use($sorted) {
        $browser->loginAs($this->admin)
            ->visit('/settings/pay-calendars') // API endpoint
            // ->screenshot('debug-pagation-page') 

            ->waitForText(  'Showing 1 to 50' )
            ->assertSee( $sorted[0]->pay_end_dt  ) // Check if "name" key exists in JSON
            ->assertSee( $sorted[0]->check_dt ) // Check if "name" key exists in JSON
            ->click('a[data-dt-idx="2"]')
            ->waitForText(  'Showing 51 to 100' )
            ->assertSee( $sorted[50]->check_dt ) // Ch

            ->logout('admin')
            ;

    });

});
