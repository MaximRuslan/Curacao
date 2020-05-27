<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->integer('min_referrals')->nullable();
            $table->integer('max_referrals')->nullable();
            $table->decimal('loan_start', 11, 2)->nullable();
            $table->decimal('loan_pif', 11, 2)->nullable();
            $table->boolean('status')->default(0);
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
        Schema::dropIfExists('referral_categories');
    }
}
