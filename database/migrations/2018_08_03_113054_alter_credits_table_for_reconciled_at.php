<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCreditsTableForReconciledAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credits', function (Blueprint $table) {
            $table->timestamp('reconciled_at')->nullable()->after('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('credits', function (Blueprint $table) {
            $table->dropColumn('reconciled_at');
        });
    }
}
