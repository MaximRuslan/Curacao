<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserBanksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_banks', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->nullable();
			$table->string('account_number', 100)->nullable();
			$table->integer('bank_id')->nullable();
			$table->string('name_on_account', 191)->nullable();
			$table->string('address_on_account', 1000)->nullable();
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
		Schema::drop('user_banks');
	}

}
