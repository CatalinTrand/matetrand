<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSapT024Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sap_t024', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/sap_t024_table.php';
        });
        Schema::create('sap_t024_300', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/sap_t024_table.php';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sap_t024');
        Schema::dropIfExists('sap_t024_300');
    }
}
