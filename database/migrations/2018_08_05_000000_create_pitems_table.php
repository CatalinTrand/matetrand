<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pitems', function (Blueprint $table) {

            require __DIR__.'/../../app/Materom/Orders/structures/pitems_table.php';

            $table->primary(['ebeln', 'ebelp']);
            $table->index('vbeln');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pitems');
    }
}
