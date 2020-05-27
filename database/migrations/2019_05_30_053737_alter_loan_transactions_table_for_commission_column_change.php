<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLoanTransactionsTableForCommissionColumnChange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_transactions', function (Blueprint $table) {
            $table->decimal('commission_calculated', 11, 2)->nullable()->change();
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
            $table->boolean('commission_calculated')->default(0)->change();
        });
    }
}
