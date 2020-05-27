<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLoanApplicationsTableForEmployeeId extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->unsignedInteger('employee_id')->nullable()->after('deadline_date');
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
            $table->dropColumn(['employee_id']);
        });
    }

}
