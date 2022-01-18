<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 26.01.2019
 * Time: 09:31
 */
$table->string('kunnr',10)->default('');
$table->string('name1',35)->default('');
$table->string('klabc',2)->default('');
$table->primary("kunnr");
