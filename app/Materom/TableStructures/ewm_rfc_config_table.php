<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 23.01.2019
 * Time: 15:03
 */

$table->string('rfc_router')->default('');
$table->string('rfc_server');
$table->string('rfc_sysnr', 2);
$table->string('rfc_client', 3);
