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
        Schema::create('pitems', function (Blueprint $table) {
            $table->string('ebeln',10);
            $table->string('ebelp',5);
            $table->string('posnr',6);
            $table->string('idnlf',35)->default('');
            $table->string('mfrnr',10)->default('');
            $table->string('mfrnr_name',35)->default('');
            $table->string('mfrpn',40)->default('');
            $table->string('mfrpn_name',35)->default('');
            $table->string('purch_price',15)->default('');
            $table->string('purch_curr',3)->default('');
            $table->string('sales_price',15)->default('');
            $table->string('sales_curr',3)->default('');
            $table->string('qty',10)->default('');
            $table->string('qty_uom',3)->default('');
            $table->dateTime('lfdat')->default(now());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pitems');
    }
}
