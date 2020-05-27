<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterWalletsTableForHistoryId extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->unsignedInteger('history_id')->nullable()->after('used');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn([
                'history_id',
            ]);
        });
    }

}
