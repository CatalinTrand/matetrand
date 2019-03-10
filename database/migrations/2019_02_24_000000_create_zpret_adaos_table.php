<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 24.02.2019
 * Time: 12:27
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZpretAdaosTable extends Migration
{
    public function up()
    {
        Schema::create('sap_zpret_adaos', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/sap_zpret_adaos.php';
        });
        Schema::create('sap_zpret_adaos_300', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/sap_zpret_adaos.php';
        });
    }

    public function down()
    {
        Schema::dropIfExists('sap_zpret_adaos');
        Schema::dropIfExists('sap_zpret_adaos_300');
    }

}