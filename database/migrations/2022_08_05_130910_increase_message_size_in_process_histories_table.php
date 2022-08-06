<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IncreaseMessageSizeInProcessHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('process_histories', function (Blueprint $table) {
            //
            $table->longText('message')->change();
            $table->text('parameters')->nullable()->after('process_name');

            $table->index(['batch_id']);
            $table->index(['process_name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('process_histories', function (Blueprint $table) {
            //
            $table->text('message')->change();
            $table->dropColumn('parameters');

            $table->dropIndex(['batch_id']);
            $table->dropIndex(['process_name']);
        });
    }
}
