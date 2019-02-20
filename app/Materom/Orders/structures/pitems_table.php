<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 12.10.2018
 * Time: 16:50
 */
$table->string('ebeln', 10);
$table->string('ebelp', 5);
$table->string('matnr', 18)->default('');
$table->string('vbeln', 10)->default('');
$table->string('posnr', 6)->default('');
$table->string('idnlf', 35)->default('');
$table->string('mtext', 40)->default('');
$table->string('mfrnr', 10)->default('');
$table->string('purch_price', 15)->default('');
$table->string('purch_curr', 3)->default('');
$table->integer('purch_prun')->default(1);
$table->string('purch_puom', 3)->default('');
$table->string('sales_price', 15)->default('');
$table->string('sales_curr', 3)->default('');
$table->integer('sales_prun')->default(1);
$table->string('sales_puom', 3)->default('');
$table->string('qty', 10)->default('');
$table->string('qty_uom', 3)->default('');
$table->string('kunnr', 10)->default('');
$table->string('shipto', 10)->default('');
$table->string('ctv', 20)->default('');
$table->string('ctv_name', 40)->default('');
$table->dateTime('lfdat')->default(now());
$table->dateTime('deldate')->nullable()->default(null);
$table->string('delqty', 20)->default('');
$table->dateTime('grdate')->nullable()->default(null);
$table->string('grqty', 20)->default('');
$table->dateTime('gidate')->nullable()->default(null);
$table->string('stage', 1)->default('F');
$table->string('pstage', 1)->default('');
$table->string('changed', 1)->default('');
$table->string("status", 1)->default('');
$table->string('orig_matnr', 18)->default('');
$table->string('orig_idnlf', 35)->default('');
$table->string('orig_purch_price', 15)->default('');
$table->string('orig_qty', 10)->default('');
$table->dateTime('orig_lfdat')->default(now());
$table->boolean('nof');
$table->string('new_lifnr', 10)->default('');
$table->string('werks', 4)->default('');
$table->string('elikz', 1)->default('');
