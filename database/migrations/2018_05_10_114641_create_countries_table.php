<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCountriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('countries', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 191)->nullable();
			$table->string('country_code', 10)->nullable();
			$table->float('apr')->nullable();
			$table->integer('phone_length')->nullable();
			$table->string('valuta_name', 191)->nullable();
			$table->string('tax', 191)->nullable();
			$table->decimal('tax_percentage', 22)->nullable();
			$table->timestamps();
			$table->softDeletes();
			$table->string('logo', 64)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('countries');
	}

}
