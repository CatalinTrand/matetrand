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
            $table->dateTime('cdate');
            $table->string('ctype',1);
            $table->string('cuser')->default('');
            $table->string('cuser_name',35)->default('');
            $table->string('oldval',80)->default('');
            $table->string('newval',80)->default('');
            $table->string('oebeln',16)->default('');
            $table->string('oebelp',5)->default('');
            $table->string('reason',100)->default('');
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
