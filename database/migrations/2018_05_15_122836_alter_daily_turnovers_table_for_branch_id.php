<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDailyTurnoversTableForBranchId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_turnovers', function (Blueprint $table) {
            $table->integer('branch_id')->after('territory_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_turnovers', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
    }
}
