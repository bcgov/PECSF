<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Follow-up to 2026_09_18_100000_update_settings_for_2026_campaign_year: rolls
     * forward the remaining stale fields on the settings row for the 2026 cycle -
     * the unused-but-stale volunteer_start_date/end_date, the
     * challenge_processed_final_date marker, and the row's created_at/updated_at
     * timestamps. Split into its own migration since the 2026_09_18_100000 migration
     * was already run against the server before these fields were identified.
     *
     * Intentionally left untouched:
     * - system_lockdown_start/end: an unrelated login-lockout window, not part of the
     *   campaign cycle; the stale past date is inert and safe to leave as-is.
     * - volunteer_language: free-text content shown on /settings/volunteering, not a
     *   date field.
     */
    public function up()
    {
        DB::table('settings')->update([
            // Legacy/unused volunteer window on this row (real window lives on campaign_years)
            'volunteer_start_date' => '2026-09-01',
            'volunteer_end_date'   => '2027-01-15',

            // Marker consumed by the auto-finalize job; any value != challenge_final_date is valid
            'challenge_processed_final_date' => '2027-02-28',

            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Reverting to last cycle's dates would cause data inconsistency, so no down action
    }
};
