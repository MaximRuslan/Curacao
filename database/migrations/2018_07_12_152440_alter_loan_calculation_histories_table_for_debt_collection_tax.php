<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLoanCalculationHistoriesTableForDebtCollectionTax extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_calculation_histories', function (Blueprint $table) {
            $table->decimal('debt_collection_tax', 22, 2)->nullable()->after('debt_collection_value');
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
            $table->dropColumn(['debt_collection_tax']);
        });
    }
}
