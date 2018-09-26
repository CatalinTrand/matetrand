<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePitemchgTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pitemchg', function (Blueprint $table) {
            $table->string('ebeln',10);
            $table->string('ebelp',5);
            $table->string('ctype',1);
            $table->dateTime('cdate');
            $table->string('cuser');
            $table->string('cuser_name',35);
            $table->string('text',80);
            $table->string('oebeln',16);
            $table->string('oebelp',5);
            $table->string('reason',100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pitemchg');
    }
}
