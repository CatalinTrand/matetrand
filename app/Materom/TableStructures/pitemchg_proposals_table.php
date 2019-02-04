<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 23.01.2019
 * Time: 15:12
 */

$table->string('type', 1)->default('');;
$table->string('ebeln', 10);
$table->string('ebelp', 5);
$table->dateTime('cdate')->default(DB::raw('CURRENT_TIMESTAMP'));
$table->integer('pos');
$table->string('lifnr', 10);
$table->string('idnlf', 35);
$table->string('matnr', 18)->default('');
$table->string('mtext', 40)->default('');
$table->dateTime('lfdat')->default(DB::raw('CURRENT_TIMESTAMP'));
$table->string('qty', 10)->default('');
$table->string('qty_uom', 3)->default('');
$table->string('purch_price', 15)->default('');
$table->string('purch_curr', 3)->default('');
$table->string('sales_price', 15)->default('');
$table->string('sales_curr', 3)->default('');
$table->string('infnr', 10)->default('');
$table->string('source', 1)->default('');
$table->string('accepted', 1)->default('');
$table->primary(['type', 'ebeln', 'ebelp', 'cdate', 'pos']);
