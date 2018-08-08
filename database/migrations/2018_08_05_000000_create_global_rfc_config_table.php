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
            $table->string('rfc_router');
            $table->string('rfc_server');
            $table->string('rfc_sysnr', 2);
            $table->string('rfc_client', 3);
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
    }
}
