<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class DeleteDailyTurnOverTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('daily_turnovers');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('daily_turnovers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payment_type')->nullable();
            $table->integer('transaction_type')->nullable();
            $table->decimal('amount', 22)->nullable();
            $table->string('notes', 1000)->nullable();
            $table->boolean('direction')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('territory_id')->nullable();
            $table->integer('branch_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
