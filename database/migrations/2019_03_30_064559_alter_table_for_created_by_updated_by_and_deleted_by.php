<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableForCreatedByUpdatedByAndDeletedBy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('created_by')->nullable()->after('remember_token');
            $table->integer('updated_by')->nullable()->after('created_by');
            $table->integer('deleted_by')->nullable()->after('updated_by');
        });
        Schema::table('user_works', function (Blueprint $table) {
            $table->integer('created_by')->nullable()->after('payment_frequency');
            $table->integer('updated_by')->nullable()->after('created_by');
            $table->integer('deleted_by')->nullable()->after('updated_by');
        });
        Schema::table('user_references', function (Blueprint $table) {
            $table->integer('created_by')->nullable()->after('address');
            $table->integer('updated_by')->nullable()->after('created_by');
            $table->integer('deleted_by')->nullable()->after('updated_by');
        });
        Schema::table('user_banks', function (Blueprint $table) {
            $table->integer('created_by')->nullable()->after('address_on_account');
            $table->integer('updated_by')->nullable()->after('created_by');
            $table->integer('deleted_by')->nullable()->after('updated_by');
        });
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->integer('created_by')->nullable()->after('signature_pdf');
            $table->integer('updated_by')->nullable()->after('created_by');
            $table->integer('deleted_by')->nullable()->after('updated_by');
        });
        Schema::table('templates', function (Blueprint $table) {
            $table->integer('created_by')->nullable()->after('content_pap');
            $table->integer('updated_by')->nullable()->after('created_by');
            $table->integer('deleted_by')->nullable()->after('updated_by');
        });
        Schema::table('referral_categories', function (Blueprint $table) {
            $table->integer('created_by')->nullable()->after('status');
            $table->integer('updated_by')->nullable()->after('created_by');
            $table->integer('deleted_by')->nullable()->after('updated_by');
        });
        Schema::table('nlbs', function (Blueprint $table) {
            $table->integer('created_by')->nullable()->after('desc');
            $table->integer('updated_by')->nullable()->after('created_by');
            $table->integer('deleted_by')->nullable()->after('updated_by');
        });
        Schema::table('countries', function (Blueprint $table) {
            $table->integer('created_by')->nullable()->after('company_name');
            $table->integer('updated_by')->nullable()->after('created_by');
            $table->integer('deleted_by')->nullable()->after('updated_by');
        });
        Schema::table('loan_types', function (Blueprint $table) {
            $table->integer('created_by')->nullable()->after('loan_agreement_pap');
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by', 'deleted_by']);
        });
        Schema::table('user_works', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by', 'deleted_by']);
        });
        Schema::table('user_references', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by', 'deleted_by']);
        });
        Schema::table('user_banks', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by', 'deleted_by']);
        });
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by', 'deleted_by']);
        });
        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by', 'deleted_by']);
        });
        Schema::table('referral_categories', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by', 'deleted_by']);
        });
        Schema::table('nlbs', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by', 'deleted_by']);
        });
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by', 'deleted_by']);
        });
        Schema::table('loan_types', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by', 'deleted_by']);
        });
    }
}
