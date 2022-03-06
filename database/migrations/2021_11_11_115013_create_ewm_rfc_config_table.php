<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlobalRfcConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ewm_rfc_config', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/ewm_rfc_config_table.php';
        });
        Schema::create('ewm_rfc_config_300', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/ewm_rfc_config_table.php';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ewm_rfc_config');
        Schema::dropIfExists('ewm_rfc_config_300');
    }
}
