<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OntarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $cities = [
            ['city' => 'Mississauga' ,'country' => 'CAN', 'province' => 'ON' ,'DescrShort' => 'Mississauga'],
        ];

        foreach ($cities as $c) {
            \App\Models\City::Create( [
                'city' => $c['city'],
                'country' => $c['country'],
                'province' => $c['province'],
                'DescrShort' => $c['DescrShort'],
            ]);
        }

    }
}
