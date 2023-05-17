<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBUInHistorialChallengePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('historical_challenge_pages', function (Blueprint $table) {
            //

            $table->string('business_unit_code')->nullable()->after('id');

            $table->index(['year', 'business_unit_code']);
            $table->index(['year', 'organization_name']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('historical_challenge_pages', function (Blueprint $table) {
            //
            $table->dropIndex(['year', 'business_unit_code']);
            $table->dropIndex(['year', 'organization_name']);
            
            $table->dropColumn('business_unit_code')->nullable()->after('id');

            
        });
    }
}
