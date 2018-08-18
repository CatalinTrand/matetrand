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
        Schema::create('POrders', function (Blueprint $table) {
            $table->string('id');
            $table->boolean('nof');
            $table->dateTime('wtime');
            $table->dateTime('ctime');
            $table->string('vbeln',10);
            $table->string('ebeln',10);
            $table->string('lifnr',10);
            $table->string('lifnr_name',35);
            $table->string('ekgrp',3);
            $table->string('ekgrp_name',35);
            $table->dateTime('erdat');
            $table->string('ernam',30);
            $table->string('curr',3);
            $table->string('fxrate',10);
            $table->string('kunnr',10);
            $table->string('kunnr_name',35);
            $table->string('shipto',10);
            $table->string('shipto_name',35);
            $table->string('ctv');
            $table->string('ctv_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('POrders');
    }
}
