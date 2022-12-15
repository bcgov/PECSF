<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExportAuditLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('export_audit_logs', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->string('schedule_job_name');
            $table->string('schedule_job_id');
            $table->string('to_application');
            $table->string('table_name');
            $table->string('row_id');
            $table->text('row_values')->nullable();
            $table->timestamps();
    
            $table->index(['schedule_job_name', 'schedule_job_id']);
            $table->index(['to_application', 'table_name', 'row_id']);
            $table->index(['created_at']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('export_audit_logs');
    }
}
