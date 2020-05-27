<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCountriesTableForPagare extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn([
                'agreement'
            ]);
        });

        Schema::table('countries', function (Blueprint $table) {
            $table->boolean('pagare')->default(0)->after('terms_pap');
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
                'pagare'
            ]);
        });

        Schema::table('countries', function (Blueprint $table) {
            $table->longText('agreement')->nullable()->after('terms_pap');
        });
    }
}
