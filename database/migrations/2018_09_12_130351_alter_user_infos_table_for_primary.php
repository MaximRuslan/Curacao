<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserInfosTableForPrimary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_infos', function (Blueprint $table) {
            $table->boolean('primary')->default(0)->after('sent_mail');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_infos', function (Blueprint $table) {
            $table->dropColumn(['primary']);
        });
    }
}
