<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserInfosTableForCreatedByUpdatedByDeletedBy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_infos', function (Blueprint $table) {
            $table->integer('created_by')->nullable()->after('primary');
            $table->integer('updated_by')->nullable()->after('created_by');
            $table->integer('deleted_by')->nullable()->after('updated_by');
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
            $table->dropColumn(['created_by', 'updated_by', 'deleted_by']);
        });
    }
}
