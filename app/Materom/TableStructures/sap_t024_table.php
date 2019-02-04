<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 26.01.2019
 * Time: 09:32
 */
$table->string('ekgrp',3)->default('');
$table->string('eknam',18)->default('');
$table->string('ektel',12)->default('');
$table->string('smtp_addr',241)->default('');
$table->primary("ekgrp");
