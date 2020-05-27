<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDayopensTableForCreatedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dayopens', function (Blueprint $table) {
            $table->dateTime('custom_created_at')->nullable()->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dayopens', function (Blueprint $table) {
            $table->dropColumn(['custom_created_at']);
        });
    }
}
