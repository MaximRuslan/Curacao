<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCountriesTableForCompanyNameWebAndTelephone extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->string('telephone', 20)->nullable()->after('mail_smtp');
            $table->string('web')->nullable()->after('telephone');
            $table->string('company_name')->nullable()->after('web');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn([
                'telephone',
                'web',
                'company_name',
            ]);
        });
    }
}
