<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $regions = [
            ['code' => '001' ,'effdt' => '1940-01-01', 'name' => 'Alberni-Clayoquot' ,'status' => 'A'],
            ['code' => '002' ,'effdt' => '1940-01-01', 'name' => 'Bulkley-Nechako' ,'status' => 'A'],
            ['code' => '003' ,'effdt' => '1940-01-01', 'name' => 'Cariboo' ,'status' => 'A'],
            ['code' => '004' ,'effdt' => '1940-01-01', 'name' => 'Central Coast' ,'status' => 'A'],
            ['code' => '005' ,'effdt' => '1940-01-01', 'name' => 'Fraser Valley' ,'status' => 'A'],
            ['code' => '006' ,'effdt' => '1940-01-01', 'name' => 'Central Kootenay' ,'status' => 'A'],
            ['code' => '007' ,'effdt' => '1940-01-01', 'name' => 'Central Okanagan' ,'status' => 'A'],
            ['code' => '008' ,'effdt' => '1940-01-01', 'name' => 'Columbia-Shuswap' ,'status' => 'A'],
            ['code' => '009' ,'effdt' => '1940-01-01', 'name' => 'Comox-Strathcona' ,'status' => 'A'],
            ['code' => '010' ,'effdt' => '1940-01-01', 'name' => 'Cowichan Valley' ,'status' => 'A'],
            ['code' => '011' ,'effdt' => '1940-01-01', 'name' => 'East Kootenay' ,'status' => 'A'],
            ['code' => '012' ,'effdt' => '1940-01-01', 'name' => 'Northern Rockies' ,'status' => 'A'],
            ['code' => '013' ,'effdt' => '1940-01-01', 'name' => 'Fraser-Fort George' ,'status' => 'A'],
            ['code' => '014' ,'effdt' => '1940-01-01', 'name' => 'Kitimat-Stikine' ,'status' => 'A'],
            ['code' => '015' ,'effdt' => '1940-01-01', 'name' => 'Kootenay Boundary' ,'status' => 'A'],
            ['code' => '016' ,'effdt' => '1940-01-01', 'name' => 'Mount Waddington' ,'status' => 'A'],
            ['code' => '017' ,'effdt' => '1940-01-01', 'name' => 'Nanaimo' ,'status' => 'A'],
            ['code' => '018' ,'effdt' => '1940-01-01', 'name' => 'North Okanagan' ,'status' => 'A'],
            ['code' => '019' ,'effdt' => '1940-01-01', 'name' => 'Okanagan-Similkameen' ,'status' => 'A'],
            ['code' => '020' ,'effdt' => '1940-01-01', 'name' => 'Peace River' ,'status' => 'A'],
            ['code' => '021' ,'effdt' => '1940-01-01', 'name' => 'Powell River' ,'status' => 'A'],
            ['code' => '022' ,'effdt' => '1940-01-01', 'name' => 'Skeena-Queen Charlotte' ,'status' => 'A'],
            ['code' => '023' ,'effdt' => '1940-01-01', 'name' => 'Squamish-Lillooet' ,'status' => 'A'],
            ['code' => '024' ,'effdt' => '1940-01-01', 'name' => 'Stikine' ,'status' => 'A'],
            ['code' => '025' ,'effdt' => '1940-01-01', 'name' => 'Sunshine Coast' ,'status' => 'A'],
            ['code' => '026' ,'effdt' => '1940-01-01', 'name' => 'Thompson-Nicola' ,'status' => 'A'],
            ['code' => '027' ,'effdt' => '1940-01-01', 'name' => 'Greater Vancouver' ,'status' => 'A'],
            ['code' => '028' ,'effdt' => '1940-01-01', 'name' => 'Capital' ,'status' => 'A'],
            ['code' => '029' ,'effdt' => '1940-01-01', 'name' => 'Ontario' ,'status' => 'I'],
        ];

        foreach ($regions as $region) {
            \App\Models\Region::updateOrCreate([
                'code' => $region['code'],
            ], [
                'effdt' => $region['effdt'],
                'name' => $region['name'],
                'status' => $region['status'],
                'created_by_id' => 1,
                'updated_by_id' => 1,
            ]);

        }



    }
}
