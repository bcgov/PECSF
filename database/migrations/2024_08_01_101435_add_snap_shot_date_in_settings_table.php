<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            //
            $table->date('ee_snapshot_date_1')->default('2024-09-01')->after('volunteer_language');
            $table->date('ee_snapshot_date_2')->default('2024-10-15')->after('ee_snapshot_date_1');
        });

        DB::statement("ALTER TABLE settings MODIFY COLUMN created_at DATE AFTER ee_snapshot_date_2");
        DB::statement("ALTER TABLE settings MODIFY COLUMN updated_at DATE AFTER created_at");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            //
            $table->dropColumn('ee_snapshot_date_1');
            $table->dropColumn('ee_snapshot_date_2');
        });

        DB::statement("ALTER TABLE settings MODIFY COLUMN created_at DATE AFTER volunteer_end_date");
        DB::statement("ALTER TABLE settings MODIFY COLUMN updated_at DATE AFTER created_at");

    }
};
