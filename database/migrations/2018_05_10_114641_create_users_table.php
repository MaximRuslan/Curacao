<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('role_id');
			$table->string('lang', 191)->nullable();
			$table->string('firstname', 191);
			$table->string('lastname', 191);
			$table->string('email', 191)->unique();
			$table->string('password', 191);
			$table->string('contact_person', 191)->nullable();
			$table->integer('sex')->nullable();
			$table->date('dob')->nullable();
			$table->string('place_of_birth', 191)->nullable();
			$table->string('address', 1000)->nullable();
			$table->integer('country')->nullable();
			$table->string('civil_status', 191)->nullable();
			$table->string('spouse_first_name', 191)->nullable();
			$table->string('spouse_last_name', 191)->nullable();
			$table->date('exp_date')->nullable();
			$table->string('pp_number', 191)->nullable();
			$table->date('pp_exp_date')->nullable();
			$table->string('scan_id', 191)->nullable();
			$table->integer('department')->nullable();
			$table->integer('territory')->nullable();
			$table->integer('branch')->nullable();
			$table->string('id_number', 191)->nullable();
			$table->smallInteger('transaction_type')->nullable();
			$table->decimal('transaction_fee')->nullable();
			$table->smallInteger('commission_type')->nullable();
			$table->decimal('commission_fee')->nullable();
			$table->smallInteger('working_type')->nullable();
			$table->integer('status')->nullable();
			$table->boolean('sent_email')->nullable();
			$table->integer('is_verified')->nullable();
			$table->string('profile_pic', 191)->nullable();
			$table->string('remember_token', 100)->nullable();
			$table->softDeletes();
			$table->timestamps();
			$table->string('address_proof', 191)->nullable();
			$table->string('payslip1', 191)->nullable();
			$table->string('payslip2', 191)->nullable();
			$table->string('other_document', 191)->nullable();
			$table->dateTime('last_activity')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
