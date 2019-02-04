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
            require __DIR__.'/../../app/Materom/TableStructures/pitems_cache_table.php';
        });
        Schema::create('pitems_cache_300', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/pitems_cache_table.php';
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
        Schema::dropIfExists('pitems_cache_300');
    }
}
