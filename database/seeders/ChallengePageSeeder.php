<?php

namespace Database\Seeders;

use App\Models\BusinessUnit;
use Illuminate\Database\Seeder;

class ChallengePageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BusinessUnit::where("code", "GCPE")
            ->update(['code' => "BC022"]);
        BusinessUnit::where("code", "EMBC")
            ->update(['code' => "BC010"]);

        /*Ministry of Attorney General (105) gets combined with Ministry of Housing (131). */
        BusinessUnit::where("code", "BC131")
            ->update(['code' => "BC105"]);

        /*Ministry of Citizens’ Services (112) get combined with Product Services (067) */
        BusinessUnit::where("code", "BC067")
            ->update(['code' => "BC112"]);

        /*Ministry of Education and Child Care (062) gets combined with Teacher’s Special Account (063). */
        BusinessUnit::where("code", "BC063")
            ->update(['code' => "BC062"]);


    }
}
