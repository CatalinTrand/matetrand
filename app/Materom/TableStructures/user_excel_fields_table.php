<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 08.11.2020
 * Time: 13:36
 */
$table->string('id', 20)->default('');
$table->string('file', 20)->default('');
$table->integer('pos')->default(0);
$table->boolean('checked');
$table->string('field', 20)->default('');
$table->primary(['id', 'table', 'pos']);
