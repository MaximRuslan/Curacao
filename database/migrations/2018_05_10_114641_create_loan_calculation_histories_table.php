<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLoanCalculationHistoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('loan_calculation_histories', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('loan_id');
			$table->date('date');
			$table->integer('week_iterations')->nullable();
			$table->decimal('payment_amount', 22)->nullable();
			$table->string('transaction_name', 191);
			$table->decimal('principal', 22);
			$table->decimal('origination', 22);
			$table->decimal('interest', 22);
			$table->decimal('renewal', 22);
			$table->decimal('tax', 22);
			$table->decimal('tax_for_origination', 22)->nullable();
			$table->decimal('tax_for_renewal', 22)->nullable();
			$table->decimal('tax_for_interest', 22)->nullable();
			$table->decimal('debt', 22);
			$table->decimal('debt_tax', 22);
			$table->decimal('total_e_tax', 22);
			$table->decimal('total', 22);
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
		Schema::drop('loan_calculation_histories');
	}

}
