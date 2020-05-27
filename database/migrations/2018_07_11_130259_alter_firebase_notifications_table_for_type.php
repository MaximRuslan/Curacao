<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFirebaseNotificationsTableForType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('firebase_notifications', function (Blueprint $table) {
            $table->string('type')->nullable()->after('body');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('firebase_notifications', function (Blueprint $table) {
            $table->dropColumn(['type']);
        });
    }
}
