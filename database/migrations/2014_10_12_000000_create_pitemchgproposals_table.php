<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePitemchgproposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pitemchg_proposals', function (Blueprint $table) {
            $table->string('type', 1);
            $table->string('ebeln', 10);
            $table->string('ebelp', 5);
            $table->dateTime('cdate')->default(now());
            $table->integer('pos');
            $table->string('lifnr', 10);
            $table->string('idnlf', 10);
            $table->string('matnr', 10)->default('');
            $table->string('mtext', 35)->default('');
            $table->dateTime('lfdat')->default(now());
            $table->string('qty', 10)->default('');
            $table->string('qty_uom', 3)->default('');
            $table->string('purch_price', 15)->default('');
            $table->string('purch_curr', 3)->default('');
            $table->string('sales_price', 15)->default('');
            $table->string('sales_curr', 3)->default('');
            $table->string('infnr', 10)->default('');
            $table->string('source', 1)->default('');
            $table->string('accepted', 1)->default('');
            $table->primary(['type', 'ebeln', 'ebelp', 'cdate', 'pos']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pitemchg_proposals');
    }
}
