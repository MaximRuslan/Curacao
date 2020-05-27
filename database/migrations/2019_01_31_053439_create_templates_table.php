<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->nullable();
            $table->string('name')->nullable();
            $table->tinyInteger('type')->nullable();
            $table->string('receivers')->nullable();
            $table->string('params')->nullable();
            $table->string('subject')->nullable();
            $table->string('subject_esp')->nullable();
            $table->string('subject_pap')->nullable();
            $table->longText('content')->nullable();
            $table->longText('content_esp')->nullable();
            $table->longText('content_pap')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('templates');
    }
}
