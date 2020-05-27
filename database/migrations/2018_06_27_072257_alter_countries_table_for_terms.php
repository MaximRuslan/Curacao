<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCountriesTableForTerms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->longText('terms_eng')->nullable()->after('map_link');
            $table->longText('terms_esp')->nullable()->after('terms_eng');
            $table->longText('terms_pap')->nullable()->after('terms_esp');
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
                'terms_eng',
                'terms_esp',
                'terms_pap',
            ]);
        });
    }
}
