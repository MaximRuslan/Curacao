<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLoanTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('loan_types', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title', 191);
			$table->string('title_es', 191)->nullable();
			$table->string('title_nl', 191)->nullable();
			$table->decimal('minimum_loan')->nullable();
			$table->decimal('maximum_loan')->nullable();
			$table->integer('unit')->nullable();
			$table->decimal('loan_component', 11)->nullable();
			$table->boolean('origination_type')->nullable();
			$table->decimal('origination_amount', 22)->nullable();
			$table->boolean('renewal_type')->nullable();
			$table->decimal('renewal_amount', 22)->nullable();
			$table->boolean('debt_type')->nullable();
			$table->decimal('debt_amount', 22)->nullable();
			$table->boolean('debt_tax_type')->nullable();
			$table->decimal('debt_tax_amount', 22)->nullable();
			$table->integer('number_of_days')->nullable();
			$table->decimal('interest')->nullable();
			$table->integer('cap_period')->nullable();
			$table->integer('country_id')->nullable();
			$table->boolean('status')->nullable()->default(0);
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
		Schema::drop('loan_types');
	}

}
