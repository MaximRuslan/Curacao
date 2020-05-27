<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserWorksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_works', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->nullable();
			$table->string('employer', 191)->nullable();
			$table->string('address', 1000)->nullable();
			$table->string('telephone_code', 191)->nullable();
			$table->string('telephone', 30)->nullable();
			$table->string('extension', 191)->nullable();
			$table->string('position', 191)->nullable();
			$table->date('employed_since')->nullable();
			$table->boolean('employment_type')->nullable();
			$table->date('contract_expires')->nullable();
			$table->string('department', 191)->nullable();
			$table->string('supervisor_name', 191)->nullable();
			$table->string('supervisor_telephone_code', 191)->nullable();
			$table->string('supervisor_telephone', 30)->nullable();
			$table->decimal('salary', 22)->nullable();
			$table->boolean('payment_frequency')->nullable();
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
		Schema::drop('user_works');
	}

}
