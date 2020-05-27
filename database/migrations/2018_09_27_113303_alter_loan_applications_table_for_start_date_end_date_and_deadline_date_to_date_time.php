<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLoanApplicationsTableForStartDateEndDateAndDeadlineDateToDateTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dateTime('start_date')->change()->nullable();
            $table->dateTime('end_date')->change()->nullable();
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
            $table->date('start_date')->change()->nullable();
            $table->date('end_date')->change()->nullable();
        });
    }
}
