<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserTerritoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_territories', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('country_id')->nullable();
			$table->string('title', 191);
			$table->string('title_es', 191)->nullable();
			$table->string('title_nl', 191)->nullable();
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
		Schema::drop('user_territories');
	}

}
