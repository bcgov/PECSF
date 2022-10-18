<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDescriptionInSpecialCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('special_campaigns', function (Blueprint $table) {
            //
            $table->dropIndex([ 'name', 'description', 'banner_text']);
            $table->index([ 'name' ]);
        });

        Schema::table('special_campaigns', function (Blueprint $table) {
            //
            $table->text('description')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('special_campaigns', function (Blueprint $table) {
            //
            $table->string('description')->change();
        });

        Schema::table('special_campaigns', function (Blueprint $table) {
            //
            $table->index([ 'name', 'description', 'banner_text']);
            $table->dropIndex([ 'name' ]);
            
        });
    }
}
