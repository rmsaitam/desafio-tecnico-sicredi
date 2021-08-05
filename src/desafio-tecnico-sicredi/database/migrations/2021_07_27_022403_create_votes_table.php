<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('option', ['Y', 'N']);
            $table->unsignedBigInteger('associate_id');
            $table->unsignedBigInteger('schedule_session_id');
            $table->foreign('associate_id')
                ->references('id')
                ->on('associates');
            $table->foreign('schedule_session_id')
                ->references('id')
                ->on('schedule_sessions');
            $table->unique(['associate_id', 'schedule_session_id']);
            $table->softDeletes();
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
        Schema::dropIfExists('votes');
    }
}
