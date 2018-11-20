<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePitemsCacheTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pitems_cache', function (Blueprint $table) {

            $table->string('session', 40);
            $table->string('ebeln', 10);
            $table->string('ebelp', 5);
            $table->string('vbeln', 10);
            $table->dateTime('cache_date')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->primary(['session', 'ebeln', 'ebelp']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pitems_cache');
    }
}
