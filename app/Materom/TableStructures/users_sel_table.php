<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 26.01.2019
 * Time: 09:34
 */
$table->string('id');
$table->string('mfrnr',10);
$table->string('mfrnr_name',35)->default('');
$table->primary(['id', 'mfrnr']);
