<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 24.02.2019
 * Time: 12:21
 */
$table->string('lifnr', 10)->default('');
$table->string('mfrnr', 10)->default('');
$table->string('wglif', 18)->default('');
$table->string('zzadaos', 10)->default('0.00');
$table->string('zzbr', 10)->default('0.00');
$table->primary(['lifnr', 'mfrnr', 'wglif']);
