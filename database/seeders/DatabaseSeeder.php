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
        
        // Core tables
        //$this->call(BusinessunitSeeder::class);   // Based on TEST export on Sep 13, 2022
        $this->call(BusinessUnitSqlFileSeeder::class);   // Based on TEST export on Sep 13, 2022
        $this->call(CampaignYearSeeder::class);
        $this->call(RegionSqlFileSeeder::class);        // Based on TEST export on Sep 17, 2022
        $this->call(OrganizationSqlFileSeeder::class);  // Based on TEST export on Sep 17, 2022
        
        
        // To switch Charity Seed from XL to Limited 20 Records, toggle below comments
        //$this->call(CharitySeeder::class);
        // $this->call(CharitySeeder20Records::class);
        
    }
}
