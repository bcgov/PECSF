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
        Schema::table('eligible_employee_by_bus', function (Blueprint $table) {
            //
            $table->text('notes')->nullable()->after('ee_count');
            $table->bigInteger('created_by_id')->nullable()->after('notes');
            $table->bigInteger('updated_by_id')->nullable()->after('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eligible_employee_by_bus', function (Blueprint $table) {
            //
            $table->dropColumn('notes');
            $table->dropColumn('created_by_id');
            $table->dropColumn('updated_by_id');
        });
    }
};
