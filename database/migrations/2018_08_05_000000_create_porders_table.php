<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePordersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('porders', function (Blueprint $table) {
            $table->string('ebeln',10)->default('');
            $table->boolean('nof');
            $table->dateTime('wtime');
            $table->dateTime('ctime');
            $table->string('lifnr',10);
            $table->string('lifnr_name',35);
            $table->string('ekgrp',3);
            $table->string('ekgrp_name',35)->default('');
            $table->dateTime('erdat')->default(now());
            $table->string('ernam',30)->default('');
            $table->string('curr',3)->default('');
            $table->string('fxrate',10)->default('');
            $table->primary("ebeln");
            $table->index("lifnr");
            $table->index("ekgrp");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('porders');
    }
}
