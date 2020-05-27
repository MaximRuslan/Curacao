<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBanksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('banks', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 191)->nullable();
			$table->string('contact_person', 191)->nullable();
			$table->string('email', 191)->nullable();
			$table->string('phone', 30)->nullable();
			$table->integer('country_id')->nullable();
			$table->boolean('transaction_fee_type')->nullable();
			$table->decimal('transaction_fee', 22)->nullable();
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
		Schema::drop('banks');
	}

}
