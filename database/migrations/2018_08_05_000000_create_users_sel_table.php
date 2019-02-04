<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersSelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_sel', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/users_sel_table.php';
        });
        Schema::create('users_sel_300', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/users_sel_table.php';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_sel');
        Schema::dropIfExists('users_sel_300');
    }
}
