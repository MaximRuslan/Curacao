<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMerchantsTableForReconciliation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->boolean('reconciliation')->default(0)->after('last_activity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropColumn(['reconciliation']);
        });
    }
}
