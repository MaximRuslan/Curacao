<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDayopensTableForAuditReports extends Migration
{
    public function up()
    {
        Schema::table('dayopens', function (Blueprint $table) {
            $table->dropColumn('date');
        });
        Schema::table('dayopens', function (Blueprint $table) {
            $table->timestamp('date')->nullable()->after('branch_id');
            $table->timestamp('completion_date')->nullable()->after('date');
            $table->integer('verified_by')->nullable()->after('completion_date');
        });
    }

    public function down()
    {
        Schema::table('dayopens', function (Blueprint $table) {
            $table->dropColumn(['date', 'completion_date', 'verified_by']);
        });
        Schema::table('dayopens', function (Blueprint $table) {
            $table->timestamp('date')->nullable()->after('branch_id');
        });
    }
}
