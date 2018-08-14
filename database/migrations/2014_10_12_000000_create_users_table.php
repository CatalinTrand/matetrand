<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('id', 20)->unique();
            $table->string('username', 50);
            $table->string('role', 20);
            $table->string('email', 100);
            $table->string('password');
            $table->string('lifnr', 10)->default('');
            $table->string('ekgrp', 3)->default('');;
            $table->string('lang',2)->default('en');
            $table->integer('active')->default('1');
            $table->rememberToken();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
