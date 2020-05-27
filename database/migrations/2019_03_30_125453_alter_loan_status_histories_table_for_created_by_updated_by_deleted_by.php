<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLoanStatusHistoriesTableForCreatedByUpdatedByDeletedBy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_status_histories', function (Blueprint $table) {
            $table->integer('created_by')->nullable()->after('note');
            $table->integer('updated_by')->nullable()->after('created_by');
            $table->integer('deleted_by')->nullable()->after('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loan_status_histories', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by', 'deleted_by']);
        });
    }
}
