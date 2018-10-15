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

            require __DIR__.'/../../app/Materom/Orders/structures/porders_table.php';

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
