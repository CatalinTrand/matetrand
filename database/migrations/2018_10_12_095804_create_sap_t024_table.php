<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSapT024Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sap_t024', function (Blueprint $table) {
            $table->string('ekgrp',3)->default('');
            $table->string('eknam',18)->default('');
            $table->string('ektel',12)->default('');
            $table->string('smtp_addr',241)->default('');
            $table->primary("ekgrp");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sap_t024');
    }
}
