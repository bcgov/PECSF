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
        Schema::table('volunteer_profiles', function (Blueprint $table) {
            //
            $table->dropColumn('pecsf_city');

            $table->string('employee_city_name')->nullable()->after('last_name');
            $table->string('employee_bu_code')->nullable()->after('pecsf_city');
            $table->string('employee_region_code')->nullable()->after('employee_bu_code');
                        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('volunteer_profiles', function (Blueprint $table) {
            //
            $table->string('pecsf_city')->nullable()->after('last_name');

            $table->dropColumn('employee_city_name');
            $table->dropColumn('employee_bu_code');
            $table->dropColumn('employee_region_code');
        });
    }
};
