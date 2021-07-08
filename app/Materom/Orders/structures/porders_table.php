<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 12.10.2018
 * Time: 16:32
 */

$table->string('ebeln',10);
$table->dateTime('wtime');
$table->dateTime('ctime');
$table->string('lifnr', 10);
$table->string('ekgrp', 3);
$table->dateTime('bedat')->default(DB::raw('CURRENT_TIMESTAMP'));
$table->dateTime('erdat')->default(DB::raw('CURRENT_TIMESTAMP'));
$table->string('ernam', 30)->default('');
$table->string('curr', 3)->default('');
$table->string('fxrate', 10)->default('');
$table->string('changed', 1)->default('');
$table->string("status", 1)->default("");
$table->string('qty_ordered', 12)->default("");
$table->string('qty_delivered', 12)->default("");
$table->string('qty_open', 12)->default("");
$table->string('qty_invoiced', 12)->default("");
$table->string('bukrs', 4)->default("MATR");
