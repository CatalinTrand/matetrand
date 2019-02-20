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
            $table->string('lifnr',10)->default('');
            $table->dateTime('date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer("cnt_total_orders");
            $table->integer("cnt_delayed_orders");
            $table->integer("cnt_total_items");
            $table->integer("cnt_delayed_items");
            $table->primary(['lifnr', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('stat_orders');
    }

}