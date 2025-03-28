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
        Schema::table('visits_monitoring', function (Blueprint $table) {
            //
            $table->string('page',512)->change();
            $table->index(['created_at']);
            $table->index(['created_at', 'page']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visits_monitoring', function (Blueprint $table) {
            //
            $table->dropIndex(['created_at']);
            $table->dropIndex(['created_at', 'page']);
            $table->text('page')->change();
        });
    }
};
