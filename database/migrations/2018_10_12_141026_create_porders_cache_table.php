<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePordersCacheTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('porders_cache', function (Blueprint $table) {

            $table->string('session', 40);
            $table->string('ebeln',10)->default('');
            $table->dateTime('cache_date')->default(now());

            $table->primary(['session', 'ebeln']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('porders_cache');
    }
}
