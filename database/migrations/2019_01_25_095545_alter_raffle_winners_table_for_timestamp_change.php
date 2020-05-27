<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRaffleWinnersTableForTimestampChange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('raffle_winners', function (Blueprint $table) {
            $table->dateTime('date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('raffle_winners', function (Blueprint $table) {
            $table->date('date')->nullable()->change();
        });
    }
}
