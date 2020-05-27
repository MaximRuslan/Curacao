<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNlbPaymentTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nlb_payment_types', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('nlb_id')->nullable();
            $table->integer('payment_type')->nullable();
            $table->decimal('amount', 22, 2)->nullable();
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
        Schema::dropIfExists('nlb_payment_types');
    }
}
