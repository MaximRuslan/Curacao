<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanStatusHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_status_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('loan_id')->nullable();
            $table->integer('status_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('note', 1000)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_status_histories');
    }
}
