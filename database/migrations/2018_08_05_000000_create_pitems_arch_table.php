<?php

use Illuminate\Support\Facades\DB;
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
            require __DIR__.'/../../app/Materom/Orders/structures/pitems_table.php';
            $table->primary(['ebeln', 'ebelp']);
            $table->index('vbeln');
            $table->timestamp('archdate')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
        Schema::create('pitems_300_arch', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/Orders/structures/pitems_table.php';
            $table->primary(['ebeln', 'ebelp']);
            $table->index('vbeln');
            $table->timestamp('archdate')->default(DB::raw('CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('pitems_300_arch');
    }
}
