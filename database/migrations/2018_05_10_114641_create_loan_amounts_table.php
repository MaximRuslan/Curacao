<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLoanAmountsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('loan_amounts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('loan_id');
			$table->integer('attachment_id')->nullable();
			$table->integer('type')->comment('1: Income, 2: existing loan / expense');
			$table->integer('amount_type');
			$table->integer('amount');
			$table->date('date')->nullable();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('loan_amounts');
	}

}
