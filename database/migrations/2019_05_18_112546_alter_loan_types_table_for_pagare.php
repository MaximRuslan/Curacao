<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLoanTypesTableForPagare extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_types', function (Blueprint $table) {
            $table->longText('pagare')->nullable()->after('loan_agreement_pap');
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
            $table->dropColumn([
                'pagare'
            ]);
        });
    }
}
