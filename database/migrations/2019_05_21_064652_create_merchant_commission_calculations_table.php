<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantCommissionCalculationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_commission_calculations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('merchant_id')->nullable();
            $table->integer('branch_id')->nullable();
            $table->decimal('collected_amount', 22, 2)->nullable();
            $table->decimal('commission', 22, 2)->nullable();
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
        Schema::dropIfExists('merchant_commission_calculations');
    }
}
