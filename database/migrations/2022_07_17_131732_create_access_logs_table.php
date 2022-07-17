<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->datetime('login_at')->nullable();
            $table->datetime('logout_at')->nullable();
            $table->string('login_method', 20)->nullable();
            $table->string('identity_provider', 20)->nullable();
            $table->string('login_ip', 50)->nullable();
            $table->timestamps();

            $table->index(['user_id','login_at']); 

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('access_logs');
    }
}
