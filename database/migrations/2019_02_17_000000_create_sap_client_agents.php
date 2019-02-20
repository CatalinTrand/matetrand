<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 17.02.2019
 * Time: 14:04
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSAPClientAgents extends Migration
{

    public function up()
    {

        Schema::create('sap_client_agents', function (Blueprint $table) {
            $table->string('kunnr',10)->default('');
            $table->string('agent',10)->default('');
            $table->string('name1',35)->default('');
            $table->primary("kunnr");
        });
    }

    public function down()
    {
        Schema::dropIfExists('sap_client_agents');
    }

}