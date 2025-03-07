<?php


use App\Models\User;
use App\Models\Setting;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\Models\CampaignYear;
use Illuminate\Foundation\Testing\DatabaseTruncation;

// Use DuskTestCase for all tests


// uses(DatabaseTruncation::class);

// test('example', function () {
//     $this->browse(function (Browser $browser) {
//         $browser->visit('/')
//                 ->assertSee('Log in as a System Administrator');
//     });
// });


beforeEach(function () {
  
    // Truncate the database and create a fresh user before each test
    Setting::truncate();
    CampaignYear::truncate();
      
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

    $this->user = User::Factory()->create([
            'password' => bcrypt('password123'),
            'source_type' => 'LCL',
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



it('allows a user to log in successfully', function () {
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
