<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeleteInScheduleJobAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedule_job_audits', function (Blueprint $table) {
            //
            $table->bigInteger('created_by_id')->nullable()->after('message');
            $table->bigInteger('updated_by_id')->nullable()->after('created_by_id');

            $table->index(['job_name','start_time','end_time', 'status']);
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedule_job_audits', function (Blueprint $table) {
            //
            $table->dropIndex(['job_name','start_time','end_time', 'status']);
            $table->dropColumn('deleted_at');
            $table->dropColumn('created_by_id');
            $table->dropColumn('updated_by_id');

        });
    }
}
