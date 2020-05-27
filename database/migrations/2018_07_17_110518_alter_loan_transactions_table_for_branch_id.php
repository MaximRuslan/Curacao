<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLoanTransactionsTableForBranchId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_transactions', function (Blueprint $table) {
            $table->integer('branch_id')->nullable()->after('created_by');
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
            $table->dropColumn(['branch_id']);
        });
    }
}
