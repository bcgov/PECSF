<?php

use Laravel\Dusk\Browser;

test('example', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
                ->assertSee('Log in as a System Administrator');
    });
});
