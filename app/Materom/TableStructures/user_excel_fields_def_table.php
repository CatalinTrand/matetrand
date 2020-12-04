<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 08.11.2020
 * Time: 13:36
 */
$table->string('file', 20)->default('');
$table->string('field', 20)->default('');
$table->integer('pos')->default(0);
$table->string('name', 40)->default('');
$table->string('link', 40)->default('');
$table->string('type', 3)->default('');
$table->primary(['table', 'field']);
