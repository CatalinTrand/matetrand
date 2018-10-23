<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 12.10.2018
 * Time: 17:00
 */
$table->string('ebeln', 10);
$table->string('ebelp', 5);
$table->dateTime('cdate')->default(now());
$table->integer('internal')->default(0);
$table->string('ctype', 1)->default("");
$table->string('stage', 1)->default("");
$table->string('cuser')->default('');
$table->string('cuser_name', 35)->default('');
$table->string('oldval', 80)->default('');
$table->string('newval', 80)->default('');
$table->string('oebeln', 10)->default('');
$table->string('oebelp', 5)->default('');
$table->string('reason', 120)->default('');
$table->integer('acknowledged')->default(0);
