<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersSelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_sel', function (Blueprint $table) {
            $table->string('id');
            $table->string('mfrnr',10);
            $table->string('mfrnr_name',35)->default('');
            $table->primary(['id', 'mfrnr']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_sel');
    }
}
