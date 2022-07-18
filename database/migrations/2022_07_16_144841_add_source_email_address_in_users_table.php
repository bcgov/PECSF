<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSourceEmailAddressInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('source_type',5)->nullable()->after('idir');
            $table->string('idir_email_addr', 100)->nullable()->after('source_type');
            $table->dropColumn('azure_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('source_type');
            $table->dropColumn('idir_email_addr');
            $table->string('azure_id')->nullable()->after('email');
        });
    }
}
