<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionsJSONSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $path = database_path('seeds/regions.json');
        $json = file_get_contents($path);

        $in_regions = json_decode( $json );

        foreach ($in_regions as $region) {

            Region::updateOrCreate([
                'code' => $region->code,
            ],[
                'name' => $region->name,
                'effdt' => $region->effdt,
                'status' => $region->status,
                "notes" => $region->notes,
                'created_by_id' => $region->created_by_id,
                'updated_by_id' => $region->updated_by_id,

            ]);
        }    
    }
}
