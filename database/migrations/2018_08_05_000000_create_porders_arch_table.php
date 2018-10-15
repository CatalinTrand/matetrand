<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePordersArchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('porders_arch', function (Blueprint $table) {

            require __DIR__.'/../../app/Materom/Orders/structures/porders_table.php';
            $table->timestamp('archdate')->default(DB::raw('CURRENT_TIMESTAMP'));

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
        Schema::dropIfExists('porders_arch');
    }
}
