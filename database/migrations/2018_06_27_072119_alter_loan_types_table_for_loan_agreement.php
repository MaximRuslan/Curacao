<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLoanTypesTableForLoanAgreement extends Migration
{
    public function up()
    {
        Schema::table('loan_types', function (Blueprint $table) {
            $table->longText('loan_agreement_eng')->nullable()->after('status');
            $table->longText('loan_agreement_esp')->nullable()->after('loan_agreement_eng');
            $table->longText('loan_agreement_pap')->nullable()->after('loan_agreement_esp');
        });
    }

    public function down()
    {
        Schema::table('loan_types', function (Blueprint $table) {
            $table->dropColumn([
                'loan_agreement_eng',
                'loan_agreement_esp',
                'loan_agreement_pap',
            ]);
        });
    }
}
