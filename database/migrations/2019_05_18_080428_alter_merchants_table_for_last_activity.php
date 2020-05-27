<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMerchantsTableForLastActivity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->timestamp('last_activity')->nullable()->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropColumn([
                'last_activity'
            ]);
        });
    }
}
