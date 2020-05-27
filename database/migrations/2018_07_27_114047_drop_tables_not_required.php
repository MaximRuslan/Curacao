<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropTablesNotRequired extends Migration
{
    public function up()
    {
        Schema::dropIfExists('bank_territories');
        Schema::dropIfExists('cms');
        Schema::dropIfExists('loan_type_territories');
        Schema::dropIfExists('proof_types');
        Schema::dropIfExists('taxes');
        Schema::dropIfExists('user_departments');
    }

    public function down()
    {
        Schema::create('bank_territories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bank_id');
            $table->integer('territory_id');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('cms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->longText('value')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('loan_type_territories', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('loan_type_id')->nullable();
            $table->integer('territory_id')->nullable();
            $table->timestamps();
        });
        Schema::create('proof_types', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('title', 191);
            $table->string('title_es', 191)->nullable();
            $table->string('title_nl', 191)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('taxes', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('name', 191)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('user_departments', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('title', 191);
            $table->string('title_es', 191)->nullable();
            $table->string('title_nl', 191)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
