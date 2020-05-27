<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLoanNotesTableForPriority extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_notes', function (Blueprint $table) {
            $table->boolean('priority')->nullable()->after('details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loan_notes', function (Blueprint $table) {
            $table->dropColumn('priority');
        });
    }
}
