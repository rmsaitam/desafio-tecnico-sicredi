<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSessionOpenedColumnInTableSchedule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('session_opened')->nullable()->after('description');
            $table->foreign('session_opened')
                ->references('id')
                ->on('schedule_sessions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            if (env('DB_CONNECTION') !== 'sqlite') {
                $table->dropForeign('schedules_session_opened_foreign');
                $table->dropIndex('schedules_session_opened_foreign');
            }
            $table->removeColumn('session_opened');
        });
    }
}
