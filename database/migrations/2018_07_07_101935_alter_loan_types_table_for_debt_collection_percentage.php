<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLoanTypesTableForDebtCollectionPercentage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_types', function (Blueprint $table) {
            $table->decimal('debt_collection_percentage', 10, 2)->nullable()->after('debt_amount');
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
                'debt_collection_percentage'
            ]);
        });
    }
}
