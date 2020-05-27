<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLoanApplicationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('loan_applications', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('client_id');
			$table->integer('loan_reason');
			$table->decimal('tax_percentage', 22)->nullable();
			$table->string('tax_name', 191)->nullable();
			$table->decimal('tax', 22)->nullable();
			$table->decimal('loan_component', 22)->nullable();
			$table->boolean('origination_type')->nullable();
			$table->decimal('origination_amount', 22)->nullable();
			$table->decimal('origination_fee', 22)->nullable();
			$table->boolean('renewal_type')->nullable();
			$table->decimal('renewal_amount', 22)->nullable();
			$table->decimal('debt_amount', 22)->nullable();
			$table->boolean('debt_tax_type')->nullable();
			$table->decimal('debt_tax_amount', 22)->nullable();
			$table->integer('period')->nullable();
			$table->decimal('interest', 22)->nullable();
			$table->decimal('interest_amount', 22)->nullable();
			$table->integer('cap_period')->nullable();
			$table->decimal('max_amount', 22)->nullable();
			$table->boolean('debt_type')->nullable();
			$table->integer('salary');
			$table->float('amount');
			$table->integer('other_loan_deduction')->nullable();
			$table->integer('loan_type');
			$table->integer('loan_status');
			$table->integer('loan_decline_reason')->nullable();
			$table->text('decline_description', 65535)->nullable();
			$table->date('start_date')->nullable();
			$table->date('end_date')->nullable();
			$table->date('deadline_date')->nullable();
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
		Schema::drop('loan_applications');
	}

}
