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
        Schema::create('ewm_roles', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/ewm_roles_table.php';
        });
        Schema::create('ewm_roles_300', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/ewm_roles_table.php';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ewm_roles');
        Schema::dropIfExists('ewm_roles_300');
    }
}
