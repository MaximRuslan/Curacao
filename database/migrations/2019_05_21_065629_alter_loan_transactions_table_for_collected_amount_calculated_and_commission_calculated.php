<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLoanTransactionsTableForCollectedAmountCalculatedAndCommissionCalculated extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_transactions', function (Blueprint $table) {
            $table->boolean('calculated_amount')->default(0)->after('reconciled_at');
            $table->boolean('commission_calculated')->default(0)->after('calculated_amount');
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
                'calculated_amount',
                'commission_calculated',
            ]);
        });
    }
}
