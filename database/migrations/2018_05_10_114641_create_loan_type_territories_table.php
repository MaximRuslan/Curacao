<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLoanTypeTerritoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('loan_type_territories', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('loan_type_id')->nullable();
			$table->integer('territory_id')->nullable();
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
		Schema::drop('loan_type_territories');
	}

}
