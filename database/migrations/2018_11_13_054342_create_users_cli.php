<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersCli extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_cli', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/users_cli_table.php';
        });
        Schema::create('users_cli_300', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/users_cli_table.php';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_cli');
        Schema::dropIfExists('users_cli_300');
    }
}
