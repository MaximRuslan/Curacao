<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCreditsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('credits', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->nullable();
			$table->boolean('payment_type')->nullable();
			$table->decimal('amount', 22)->nullable();
			$table->string('bank_id', 191)->nullable();
			$table->decimal('transaction_charge')->nullable();
			$table->string('notes', 191)->nullable();
			$table->boolean('status')->nullable();
			$table->string('file_name', 124)->nullable();
			$table->string('file_path', 124)->nullable();
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
		Schema::drop('credits');
	}

}
