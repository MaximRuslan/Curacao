<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserReferencesTableForConvertingItToInteger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_references', function (Blueprint $table) {
            $table->dropColumn(['relationship']);
        });
        Schema::table('user_references', function (Blueprint $table) {
            $table->integer('relationship')->nullable()->after('last_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_references', function (Blueprint $table) {
            $table->dropColumn(['relationship']);
        });
        Schema::table('user_references', function (Blueprint $table) {
            $table->string('relationship')->nullable()->after('last_name');
        });
    }
}
