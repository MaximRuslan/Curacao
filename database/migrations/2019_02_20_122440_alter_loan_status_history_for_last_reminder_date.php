<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLoanStatusHistoryForLastReminderDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_status_histories', function (Blueprint $table) {
            $table->timestamp('last_reminder')->nullable()->after('note');
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
            $table->dropColumn(['last_reminder']);
        });
    }
}
