<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLoanApplicationsTableAndLoanCalculationHistoriesTablesForIndexs extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->index('client_id');
            $table->index('loan_reason');
            $table->index('amount');
            $table->index('loan_type');
            $table->index('loan_status');
            $table->index('loan_decline_reason');
            $table->index('start_date');
            $table->index('employee_id');
            $table->index('created_at');
            $table->index('deleted_by');
            $table->index('deleted_at');
        });
        Schema::table('loan_calculation_histories', function (Blueprint $table) {
            $table->index('loan_id');
            $table->index('total');
            $table->index('created_at');
            $table->index('deleted_at');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->index('id_number');
            $table->index('firstname');
            $table->index('lastname');
            $table->index('web_registered');
            $table->index('country');
        });
        Schema::table('loan_reasons', function (Blueprint $table) {
            $table->index('title');
        });
        Schema::table('loan_types', function (Blueprint $table) {
            $table->index('title');
            $table->index('number_of_days');
        });
        Schema::table('loan_status', function (Blueprint $table) {
            $table->index('title');
        });
        Schema::table('loan_notes', function (Blueprint $table) {
            $table->index('loan_id');
            $table->index('follow_up');
            $table->index('deleted_at');
        });
        Schema::table('loan_transactions', function (Blueprint $table) {
            $table->index('loan_id');
            $table->index('payment_date');
            $table->index('created_at');
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
            $table->dropIndex('loan_applications_client_id_index');
            $table->dropIndex('loan_applications_loan_reason_index');
            $table->dropIndex('loan_applications_amount_index');
            $table->dropIndex('loan_applications_loan_type_index');
            $table->dropIndex('loan_applications_loan_status_index');
            $table->dropIndex('loan_applications_loan_decline_reason_index');
            $table->dropIndex('loan_applications_start_date_index');
            $table->dropIndex('loan_applications_employee_id_index');
            $table->dropIndex('loan_applications_created_at_index');
            $table->dropIndex('loan_applications_deleted_by_index');
            $table->dropIndex('loan_applications_deleted_at_index');
        });
        Schema::table('loan_calculation_histories', function (Blueprint $table) {
            $table->dropIndex('loan_calculation_histories_loan_id_index');
            $table->dropIndex('loan_calculation_histories_total_index');
            $table->dropIndex('loan_calculation_histories_created_at_index');
            $table->dropIndex('loan_calculation_histories_deleted_at_index');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_id_number_index');
            $table->dropIndex('users_firstname_index');
            $table->dropIndex('users_lastname_index');
            $table->dropIndex('users_web_registered_index');
            $table->dropIndex('users_country_index');
        });
        Schema::table('loan_reasons', function (Blueprint $table) {
            $table->dropIndex('loan_reasons_title_index');
        });
        Schema::table('loan_types', function (Blueprint $table) {
            $table->dropIndex('loan_types_title_index');
            $table->dropIndex('loan_types_number_of_days_index');
        });
        Schema::table('loan_status', function (Blueprint $table) {
            $table->dropIndex('loan_status_title_index');
        });
        Schema::table('loan_notes', function (Blueprint $table) {
            $table->dropIndex('loan_notes_loan_id_index');
            $table->dropIndex('loan_notes_follow_up_index');
            $table->dropIndex('loan_notes_deleted_at_index');
        });
        Schema::table('loan_transactions', function (Blueprint $table) {
            $table->dropIndex('loan_transactions_loan_id_index');
            $table->dropIndex('loan_transactions_payment_date_index');
            $table->dropIndex('loan_transactions_created_at_index');
        });
    }

}
