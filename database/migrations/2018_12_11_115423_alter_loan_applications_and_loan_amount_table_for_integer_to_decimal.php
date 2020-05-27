<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLoanApplicationsAndLoanAmountTableForIntegerToDecimal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->decimal('salary', 22, 2)->nullable()->change();
            $table->decimal('other_loan_deduction', 22, 2)->nullable()->change();
        });
        Schema::table('loan_amounts', function (Blueprint $table) {
            $table->decimal('amount', 22, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->integer('salary')->nullable()->change();
            $table->integer('other_loan_deduction')->nullable()->change();
        });
        Schema::table('loan_amounts', function (Blueprint $table) {
            $table->integer('amount')->nullable()->change();
        });
    }
}
