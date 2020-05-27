<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLoanTransactionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('loan_transactions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('client_id');
			$table->integer('loan_id');
			$table->integer('transaction_type');
			$table->integer('payment_type')->nullable();
			$table->string('notes', 500)->nullable();
			$table->float('amount');
			$table->decimal('cash_back_amount')->nullable();
			$table->boolean('used')->default(0);
			$table->date('payment_date')->nullable();
			$table->date('next_payment_date')->nullable();
			$table->integer('created_by')->nullable();
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
		Schema::drop('loan_transactions');
	}

}
