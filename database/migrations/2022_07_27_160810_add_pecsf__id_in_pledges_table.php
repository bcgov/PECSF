<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPecsfIdInPledgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('pledges', function (Blueprint $table) {
            //
            $table->string('pecsf_id')->nullable()->after('user_id');
            $table->string('first_name')->nullable()->after('pecsf_id');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('city')->nullable()->after('last_name');

            $table->dropColumn('bi_export_status');
            $table->dropColumn('bi_export_at');
            
            $table->dropForeign(['user_id']);
            $table->dropIndex('pledges_user_id_foreign');

        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::disableForeignKeyConstraints();

        Schema::table('pledges', function (Blueprint $table) {
            //
            $table->foreign('user_id')->references('id')->on('users');

            $table->char('bi_export_status',1)->after('ods_export_at')->nullable();
            $table->timestamp('bi_export_at')->after('bi_export_status')->nullable();
            
            $table->dropColumn('pecsf_id');
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('city');
            
        });

        Schema::enableForeignKeyConstraints();

    }
}
