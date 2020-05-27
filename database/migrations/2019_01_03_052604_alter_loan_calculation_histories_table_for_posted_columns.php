<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLoanCalculationHistoriesTableForPostedColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_calculation_histories', function (Blueprint $table) {
            $table->decimal('principal_posted', 11, 2)->nullable()->after('principal');
            $table->decimal('origination_posted', 11, 2)->nullable()->after('origination');
            $table->decimal('interest_posted', 11, 2)->nullable()->after('interest');
            $table->decimal('renewal_posted', 11, 2)->nullable()->after('renewal');
            $table->decimal('tax_for_origination_posted', 11, 2)->nullable()->after('tax_for_origination');
            $table->decimal('tax_for_renewal_posted', 11, 2)->nullable()->after('tax_for_renewal');
            $table->decimal('tax_for_interest_posted', 11, 2)->nullable()->after('tax_for_interest');
            $table->decimal('debt_posted', 11, 2)->nullable()->after('debt');
            $table->decimal('debt_tax_posted', 11, 2)->nullable()->after('debt_tax');
            $table->decimal('debt_collection_value_posted', 11, 2)->nullable()->after('debt_collection_value');
            $table->decimal('debt_collection_tax_posted', 11, 2)->nullable()->after('debt_collection_tax');
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
            $table->dropColumn([
                'principal_posted',
                'origination_posted',
                'interest_posted',
                'renewal_posted',
                'tax_for_origination_posted',
                'tax_for_renewal_posted',
                'tax_for_interest_posted',
                'debt_posted',
                'debt_tax_posted',
                'debt_collection_value_posted',
                'debt_collection_tax_posted',
            ]);
        });
    }
}
