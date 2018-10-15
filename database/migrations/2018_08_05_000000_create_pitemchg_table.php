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

            require __DIR__.'/../../app/Materom/Orders/structures/pitemchg_table.php';

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
        Schema::dropIfExists('pitemchg');
    }
}
