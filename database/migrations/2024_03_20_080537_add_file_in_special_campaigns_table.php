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
        Schema::table('special_campaigns', function (Blueprint $table) {
            //
            $table->string('mime')->nullable()->after('image');
            
        });

        DB::statement("ALTER TABLE special_campaigns ADD COLUMN image_data MEDIUMBLOB AFTER `mime` ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('special_campaigns', function (Blueprint $table) {
            //
            $table->dropColumn('mime');
            $table->dropColumn('image_data');
        });
    }
};