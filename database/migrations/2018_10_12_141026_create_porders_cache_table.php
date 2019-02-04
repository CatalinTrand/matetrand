<?php

use Illuminate\Support\Facades\DB;
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
            require __DIR__.'/../../app/Materom/TableStructures/porders_cache_table.php';
        });
        Schema::create('porders_cache_300', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/porders_cache_table.php';
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
        Schema::dropIfExists('porders_cache_300');
    }
}
