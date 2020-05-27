<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLoanApplicationsTableForDebtCollectionType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->tinyInteger('debt_collection_type')->nullable()->after('debt_amount');
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
            $table->dropColumn([
                'debt_collection_type'
            ]);
        });
    }
}
