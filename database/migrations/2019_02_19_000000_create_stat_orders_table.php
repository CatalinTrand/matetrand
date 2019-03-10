<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 19.02.2019
 * Time: 07:45
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('stat_orders', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/stat_orders_table.php';
        });
        Schema::create('stat_orders_300', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/stat_orders_table.php';
        });
    }

    public function down()
    {
        Schema::dropIfExists('stat_orders');
        Schema::dropIfExists('stat_orders_300');
    }

}