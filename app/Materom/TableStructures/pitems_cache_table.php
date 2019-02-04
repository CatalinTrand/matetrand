<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 26.01.2019
 * Time: 09:23
 */

$table->string('session', 40);
$table->string('ebeln', 10);
$table->string('ebelp', 5);
$table->string('vbeln', 10);
$table->dateTime('cache_date')->default(DB::raw('CURRENT_TIMESTAMP'));

$table->primary(['session', 'ebeln', 'ebelp']);
