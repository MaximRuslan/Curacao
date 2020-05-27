<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRaffleWinnersTableForCountryId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('raffle_winners', function (Blueprint $table) {
            $table->integer('country_id')->nullable()->after('user_id');
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
            $table->dropColumn([
                'country_id'
            ]);
        });
    }
}
