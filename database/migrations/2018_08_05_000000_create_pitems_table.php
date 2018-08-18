<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('PItems', function (Blueprint $table) {
            $table->string('ebeln',10);
            $table->string('ebelp',5);
            $table->string('posnr',6);
            $table->string('idnlf',35);
            $table->string('mfrnr',10);
            $table->string('mfrnr_name',35);
            $table->string('mfrpn',40);
            $table->string('mfrpn_name',35);
            $table->string('purch_price',15);
            $table->string('purch_curr',3);
            $table->string('sales_price',15);
            $table->string('sales_curr',3);
            $table->string('qty',10);
            $table->string('qty_uom',3);
            $table->dateTime('lfdat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('PItems');
    }
}
