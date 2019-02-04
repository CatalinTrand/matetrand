<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePitemchgproposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pitemchg_proposals', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/pitemchg_proposals_table.php';
        });
        Schema::create('pitemchg_proposals_arch', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/pitemchg_proposals_table.php';
            $table->timestamp('archdate')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
        Schema::create('pitemchg_proposals_300', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/pitemchg_proposals_table.php';
        });
        Schema::create('pitemchg_proposals_300_arch', function (Blueprint $table) {
            require __DIR__.'/../../app/Materom/TableStructures/pitemchg_proposals_table.php';
            $table->timestamp('archdate')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pitemchg_proposals');
        Schema::dropIfExists('pitemchg_proposals_arch');
        Schema::dropIfExists('pitemchg_proposals_300');
        Schema::dropIfExists('pitemchg_proposals_300_arch');
    }
}
