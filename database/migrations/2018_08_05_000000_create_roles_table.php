<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/roles_table.php';
        });
        Schema::create('roles_300', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/roles_table.php';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
        Schema::dropIfExists('roles_300');
    }
}
