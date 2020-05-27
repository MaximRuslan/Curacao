<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('loan_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->date('date')->nullable();
            $table->date('follow_up')->nullable();
            $table->text('details')->nullable();
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
        Schema::dropIfExists('loan_notes');
    }
}
