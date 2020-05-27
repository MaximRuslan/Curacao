<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDebtCalculationHistoriesTableForEmployeeId extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_calculation_histories', function (Blueprint $table) {
            $table->unsignedInteger('employee_id')->nullable()->after('total');
            $table->unsignedDecimal('commission_percent', 10, 2)->nullable()->after('employee_id');
            $table->unsignedDecimal('commission', 22, 2)->nullable()->after('commission_percent');
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
                'employee_id',
                'commission_percent',
                'commission',
            ]);
        });
    }

}
