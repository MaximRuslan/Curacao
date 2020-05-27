<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLoanApplicationTableForApplySignature extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->string('signature')->nullable()->after('deadline_date');
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
            $table->dropColumn('signature');
        });
    }
}
