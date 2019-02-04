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
        Schema::create('global_rfc_config', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/global_rfc_config_table.php';
        });
        Schema::create('global_rfc_config_300', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/global_rfc_config_table.php';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('global_rfc_config');
        Schema::dropIfExists('global_rfc_config_300');
    }
}
