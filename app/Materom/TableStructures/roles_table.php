<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 26.01.2019
 * Time: 09:30
 */
$table->string('rfc_role', 20)->unique();
$table->string('rfc_user', 12);
$table->string('rfc_passwd', 12);
