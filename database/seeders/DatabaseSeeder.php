<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->call(RolePermissionTableSeeder::class);
        // \App\Models\User::factory(10)->create();
        $this->call(UserTableSeeder::class);
        // To switch Charity Seed from XL to Limited 20 Records, toggle below comments
        //$this->call(CharitySeeder::class);
        $this->call(CharitySeeder20Records::class);
        // Campaign Year 
        $this->call(CampaignYearSeeder::class);
    }
}
