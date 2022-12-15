<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationJSONSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $path = database_path('seeds/organizations.json');
        $json = file_get_contents($path);

        $in_organizations = json_decode( $json );

        foreach ($in_organizations as $organization) {

            Organization::updateOrCreate([
                'code' => $organization->code,
            ],[
                'name' => $organization->name,
                'effdt' => $organization->effdt,
                'status' => $organization->status,
                'created_by_id' => $organization->created_by_id,
                'updated_by_id' => $organization->updated_by_id,
            ]);
        }    
    }
}
