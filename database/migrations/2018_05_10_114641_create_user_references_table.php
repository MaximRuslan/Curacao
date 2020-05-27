<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserReferencesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_references', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->string('first_name', 191)->nullable();
			$table->string('last_name', 191)->nullable();
			$table->string('relationship', 191)->nullable();
			$table->string('telephone', 191)->nullable();
			$table->string('cellphone', 191)->nullable();
			$table->string('address', 1000)->nullable();
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
		Schema::drop('user_references');
	}

}
