<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSapLfa1Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sap_lfa1', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/sap_lfa1_table.php';
        });
        Schema::create('sap_lfa1_300', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/sap_lfa1_table.php';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sap_lfa1');
        Schema::dropIfExists('sap_lfa1_300');
    }
}
