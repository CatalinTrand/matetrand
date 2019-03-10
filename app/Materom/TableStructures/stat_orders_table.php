<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 24.02.2019
 * Time: 12:19
 */
$table->string('lifnr',10)->default('');
$table->dateTime('date')->default(DB::raw('CURRENT_TIMESTAMP'));
$table->integer("cnt_total_orders")->default(0);
$table->integer("cnt_delayed_orders")->default(0);
$table->integer("cnt_total_items")->default(0);
$table->integer("cnt_delayed_items")->default(0);
$table->primary(['lifnr', 'date']);
