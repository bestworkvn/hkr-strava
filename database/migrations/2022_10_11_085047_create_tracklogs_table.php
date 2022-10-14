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
        Schema::create('tracklogs', function (Blueprint $table) {
            $table->id();
            $table->string('original_id');
            $table->foreignId('user_id');
            $table->string('name');
            $table->float('distance');
            $table->float('moving_time');
            $table->float('elapsed_time');
            $table->dateTime('start_date');
            $table->dateTime('start_date_local');
            $table->string('timezone');
            $table->float('average_speed');
            $table->string('type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tracklogs');
    }
};
