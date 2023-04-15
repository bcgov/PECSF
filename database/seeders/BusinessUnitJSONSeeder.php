<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BusinessUnitJSONSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $path = database_path('seeds/business_units.json');
        $json = file_get_contents($path);

        $in_business_units = json_decode( $json );

        foreach ($in_business_units as $business_unit) {
            \App\Models\BusinessUnit::updateOrCreate([
                'code' => $business_unit->code,
            ], [
                'effdt' => $business_unit->effdt,
                'status' => $business_unit->status,
                'name' => $business_unit->name,
                'notes' => $business_unit->notes,
                'created_by_id' => $business_unit->created_by_id,
                'updated_by_id' => $business_unit->updated_by_id,
            ]);

        }

    }
}
