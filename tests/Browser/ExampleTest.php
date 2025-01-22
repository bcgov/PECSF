<?php

use App\Models\Setting;
use Laravel\Dusk\Browser;
use App\Models\CampaignYear;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;

// uses(DatabaseTruncation::class);

beforeEach(function () {
  
    Setting::truncate();
    CampaignYear::truncate();
    User::truncate();

    // Create a fresh test user
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

it('basic example', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
                ->assertSee('Log in as a System Administrator');
    });
});
