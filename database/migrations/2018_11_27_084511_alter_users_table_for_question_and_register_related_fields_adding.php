<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersTableForQuestionAndRegisterRelatedFieldsAdding extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('how_much_loan', 22, 2)->nullable()->after('profile_pic');
            $table->tinyInteger('repay_loan_2_weeks')->nullable()->after('how_much_loan');
            $table->tinyInteger('have_bank_loan')->nullable()->after('repay_loan_2_weeks');
            $table->tinyInteger('have_bank_account')->nullable()->after('have_bank_loan');
            $table->tinyInteger('web_registered')->nullable()->after('have_bank_account');
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
            $table->dropColumn([
                'how_much_loan',
                'repay_loan_2_weeks',
                'have_bank_loan',
                'have_bank_account',
                'web_registered'
            ]);
        });
    }
}
