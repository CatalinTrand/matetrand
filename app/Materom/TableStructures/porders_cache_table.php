<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 26.01.2019
 * Time: 09:27
 */
$table->string('session', 40);
$table->string('ebeln',10)->default('');
$table->dateTime('cache_date')->default(DB::raw('CURRENT_TIMESTAMP'));

$table->primary(['session', 'ebeln']);
