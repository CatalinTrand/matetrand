<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAgentClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_agent_clients', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/user_agent_clients_table.php';
        });
        Schema::create('user_agent_clients_300', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/user_agent_clients_table.php';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_agent_clients');
        Schema::dropIfExists('user_agent_clients_300');
    }
}
