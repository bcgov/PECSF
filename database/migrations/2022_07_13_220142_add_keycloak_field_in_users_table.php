<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKeycloakFieldInUsersTable extends Migration
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
            $table->string('identity_provider')->nullable()->after('azure_id');
            $table->string('keycloak_id')->nullable()->after('identity_provider');
            $table->string('idir')->nullable()->after('guid');
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
            $table->dropColumn('identity_provider');
            $table->dropColumn('keycloak_id');
            $table->dropColumn('idir');
        });
    }
}
