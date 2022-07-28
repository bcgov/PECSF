<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUniqueIndexInDonorByDepartments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('donor_by_departments', function (Blueprint $table) {
            //
            $table->dropUnique(['department_id','yearcd','date']);
            $table->index(['department_id','yearcd','date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('donor_by_departments', function (Blueprint $table) {
            //
            $table->dropIndex(['department_id','yearcd','date']);
            $table->unique(['department_id','yearcd','date']);
        });
    }
}
