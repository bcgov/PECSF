<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimestampsOnOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('organizations', function (Blueprint $table) {
            
            //
            $table->string('code',3)->after('id');
            $table->string('status',1)->after('name');
            $table->date('effdt')->after('status');
            $table->bigInteger('created_by_id')->nullable()->after('effdt');
            $table->bigInteger('updated_by_id')->nullable()->after('created_by_id');
            $table->timestamps();

            $table->unique('code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('organizations', function (Blueprint $table) {
            
            //
            $table->dropUnique(['code']);

            $table->dropColumn('code');
            $table->dropColumn('status');
            $table->dropColumn('effdt');
            $table->dropColumn('created_by_id');
            $table->dropColumn('updated_by_id');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
}
