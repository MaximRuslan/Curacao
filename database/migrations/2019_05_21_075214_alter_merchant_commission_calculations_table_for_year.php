<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMerchantCommissionCalculationsTableForYear extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchant_commission_calculations', function (Blueprint $table) {
            $table->integer('year')->nullable()->after('month');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merchant_commission_calculations', function (Blueprint $table) {
            $table->dropColumn([
                'year'
            ]);
        });
    }
}
