<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLoanCalculationHistoriesForTotalNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_calculation_histories', function (Blueprint $table) {
            $table->decimal('total_e_tax',22,2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loan_calculation_histories', function (Blueprint $table) {
            $table->decimal('total_e_tax',22,2)->change();
        });
    }
}
