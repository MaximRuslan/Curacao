<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLoanTransactionTableForMerchantId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_transactions', function (Blueprint $table) {
            $table->integer('merchant_id')->nullable()->after('next_payment_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loan_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'merchant_id'
            ]);
        });
    }
}
