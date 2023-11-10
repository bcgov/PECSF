<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pledges', function (Blueprint $table) {
            //
            $table->string('cancelled',1)->nullable()->after('report_generated_at');
            $table->bigInteger('cancelled_by_id')->nullable()->after('cancelled');
            $table->dateTime('cancelled_at')->nullable()->after('cancelled_by_id');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pledges', function (Blueprint $table) {
            //
            $table->dropColumn('cancelled');
            $table->dropColumn('cancelled_by_id');
            $table->dropColumn('cancelled_at');
        });
    }
};
