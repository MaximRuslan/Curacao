<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLoanTypesTableForDebtCollectionTax extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_types', function (Blueprint $table) {
            $table->tinyInteger('debt_collection_tax_type')->nullable()->after('debt_collection_percentage');
            $table->decimal('debt_collection_tax_value', 22, 2)->nullable()->after('debt_collection_tax_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loan_types', function (Blueprint $table) {
            $table->dropColumn([
                'debt_collection_tax_type',
                'debt_collection_tax_value',
            ]);
        });
    }
}
