<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLoanTransactionsTableForUpdatedBy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_transactions', function (Blueprint $table) {
            $table->integer('updated_by')->nullable()->after('created_by');
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
                'updated_by'
            ]);
        });
    }
}
