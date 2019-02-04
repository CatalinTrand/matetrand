<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersAgentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_agent', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/users_agent_table.php';
        });
        Schema::create('users_agent_300', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/users_agent_table.php';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_agent');
        Schema::dropIfExists('users_agent_300');
    }
}
