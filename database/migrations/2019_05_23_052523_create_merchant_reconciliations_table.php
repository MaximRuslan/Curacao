<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantReconciliationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_reconciliations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('transaction_id', 100)->index();
            $table->integer('merchant_id')->nullable();
            $table->integer('branch_id')->nullable();
            $table->decimal('amount', 11, 2)->nullable();
            $table->boolean('status')->default(0);
            $table->date('date')->nullable();
            $table->string('otp')->nullable()->index();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
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
        Schema::dropIfExists('merchant_reconciliations');
    }
}
