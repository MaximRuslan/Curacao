<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('date')->nullable();
            $table->decimal('bonus_payout', 11, 2)->nullable();
            $table->integer('client_id')->nullable();
            $table->integer('loan_id')->nullable();
            $table->boolean('status')->default(0);
            $table->integer('referred_client')->nullable();
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
        Schema::dropIfExists('referral_histories');
    }
}
