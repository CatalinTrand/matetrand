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
$table->boolean('ctv_man')->default(1);
$table->dateTime('lfdat')->default(DB::raw('CURRENT_TIMESTAMP'));
$table->boolean('backorder')->default(0);
$table->dateTime('deldate')->nullable()->default(null);
$table->string('delqty', 20)->default('');
$table->dateTime('grdate')->nullable()->default(null);
$table->string('grqty', 20)->default('');
$table->dateTime('gidate')->nullable()->default(null);
$table->string('qty_received', 10)->default('');
$table->string('qty_invoiced', 10)->default('');
$table->string('qty_diff', 10)->default('');
$table->string('qty_damaged', 10)->default('');
$table->string('qty_details', 120)->default('');
$table->string('qty_solution', 20)->default('');
$table->string('qty_pnad_mblnr', 10)->default('');
$table->string('qty_pnad_mjahr', 4)->default('');
$table->string('pnad_status', 1)->default('');
$table->dateTime('pnad_upd_date')->nullable()->default(null);
$table->string('stage', 1)->default('F');
$table->string('pstage', 1)->default('');
$table->string('changed', 1)->default('');
$table->string("status", 1)->default('');
$table->string('orig_matnr', 18)->default('');
$table->string('orig_idnlf', 35)->default('');
$table->string('orig_purch_price', 15)->default('');
$table->string('orig_qty', 10)->default('');
$table->dateTime('orig_lfdat')->default(DB::raw('CURRENT_TIMESTAMP'));
$table->boolean('nof');
$table->string('new_lifnr', 10)->default('');
$table->string('werks', 4)->default('');
$table->string('elikz', 1)->default('');
$table->dateTime('etadt')->default(DB::raw('CURRENT_TIMESTAMP'));
$table->string('mirror_ebeln', 10)->default('');
$table->string('mirror_ebelp', 5)->default('');
$table->string('pmfa', 4)->default('');
$table->integer('pmfa_status')->default(0);
$table->dateTime('pmfa_date')->nullable()->default(null);
$table->string('inb_dlv', 10)->default('');
$table->string('inb_dlv_posnr', 6)->default('');
$table->string('inb_inv', 10)->default('');
$table->dateTime('inb_inv_date')->nullable()->default(null);
$table->boolean('eta_delayed_check');
$table->dateTime('eta_delayed_date')->default(DB::raw('CURRENT_TIMESTAMP'));
