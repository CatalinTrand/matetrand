<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePitemchgArchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pitemchg_arch', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/Orders/structures/pitemchg_table.php';
            $table->timestamp('archdate')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->primary(['ebeln', 'ebelp', 'cdate']);
        });
        Schema::create('pitemchg_300_arch', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/Orders/structures/pitemchg_table.php';
            $table->timestamp('archdate')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->primary(['ebeln', 'ebelp', 'cdate']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pitemchg_arch');
        Schema::dropIfExists('pitemchg_300_arch');
    }
}
