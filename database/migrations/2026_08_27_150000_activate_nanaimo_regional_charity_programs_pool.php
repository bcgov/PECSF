<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Reactivates Nanaimo's current-dated Regional Charity Programs (Fund Supported Pool)
     * record so it shows up again in the donation flows and eForm, matching the other
     * currently-active regions. Data-only fix, no schema change.
     */
    public function up()
    {
        $region = DB::table('regions')->where('name', 'Nanaimo')->first();

        if (!$region) {
            return;
        }

        $currentPoolId = DB::table('f_s_pools')
            ->where('region_id', $region->id)
            ->whereNull('deleted_at')
            ->where('start_date', '<=', now())
            ->orderByDesc('start_date')
            ->value('id');

        if ($currentPoolId) {
            DB::table('f_s_pools')
                ->where('id', $currentPoolId)
                ->update(['status' => 'A']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        $region = DB::table('regions')->where('name', 'Nanaimo')->first();

        if (!$region) {
            return;
        }

        $currentPoolId = DB::table('f_s_pools')
            ->where('region_id', $region->id)
            ->whereNull('deleted_at')
            ->where('start_date', '<=', now())
            ->orderByDesc('start_date')
            ->value('id');

        if ($currentPoolId) {
            DB::table('f_s_pools')
                ->where('id', $currentPoolId)
                ->update(['status' => 'I']);
        }
    }
};
