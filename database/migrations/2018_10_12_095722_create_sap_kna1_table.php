<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSapKna1Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sap_kna1', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/sap_kna1_table.php';
        });
        Schema::create('sap_kna1_300', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/sap_kna1_table.php';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sap_kna1');
        Schema::dropIfExists('sap_kna1_300');
    }
}
