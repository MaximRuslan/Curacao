<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLoanTypesTableForApr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_types', function (Blueprint $table) {
            $table->double('apr','8','2')->nullable()->after('loan_component');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loan_types', function (Blueprint $table) {
            $table->dropColumn('apr');
        });
    }
}
