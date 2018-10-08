<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePitemsArchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pitems_arch', function (Blueprint $table) {
            $table->string('ebeln',10);
            $table->string('ebelp',5);
            $table->string('vbeln',10)->default('');
            $table->string('posnr',6)->default('');
            $table->string('idnlf',35)->default('');
            $table->string('mtext',35)->default('');
            $table->string('mfrnr',10)->default('');
            $table->string('mfrnr_name',35)->default('');
            $table->string('purch_price',15)->default('');
            $table->string('purch_curr',3)->default('');
            $table->integer('purch_prun', false)->default(1);
            $table->string('purch_puom',3)->default('');
            $table->string('sales_price',15)->default('');
            $table->string('sales_curr',3)->default('');
            $table->integer('sales_prun', false)->default(1);
            $table->string('sales_puom',3)->default('');
            $table->string('qty',10)->default('');
            $table->string('qty_uom',3)->default('');
            $table->string('kunnr',10)->default('');
            $table->string('kunnr_name',35)->default('');
            $table->string('shipto',10)->default('');
            $table->string('shipto_name',35)->default('');
            $table->string('ctv', 20)->default('');
            $table->string('ctv_name')->default('');
            $table->dateTime('lfdat')->default(now());
            $table->boolean('changed')->default(false);
            $table->string('stage',1)->default('F');
            $table->primary(['ebeln', 'ebelp']);
            $table->index('vbeln');
            $table->timestamp('creation')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pitems_arch');
    }
}
