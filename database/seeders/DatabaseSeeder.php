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
        
        // Core tables
        //$this->call(BusinessunitSeeder::class);   // Based on TEST export on Sep 13, 2022
        $this->call(BusinessUnitJSONSeeder::class);   // Based on TEST export on Sep 13, 2022
        $this->call(CampaignYearSeeder::class);
        // $this->call(RegionSqlFileSeeder::class);        // Based on TEST export on Sep 17, 2022
        $this->call(OrganizationJSONSeeder::class);  // Based on TEST export on Sep 17, 2022
        $this->call(UserTableSeeder::class);
        $this->call(SettingsSeeder::class);           // Challenege page setting 

        // $this->call(FSPoolsJSONSeeder::class);   // have to run after import charities
        
        // To switch Charity Seed from XL to Limited 20 Records, toggle below comments
        //$this->call(CharitySeeder::class);
        // $this->call(CharitySeeder20Records::class);
        
    }
}
