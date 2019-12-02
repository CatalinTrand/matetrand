<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 12.11.2019
 * Time: 08:49
 */

namespace App\Console\Commands;

use App\Materom\Data;
use App\Materom\SAP;
use App\Materom\System;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FullArchiving extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'materom:fullarchive {system}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Searches for purchase orders to be archived and performs archiving';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $system = $this->argument("system");
        if ($system == null) {
            $this->error(__("Please specify the system to be searched for archivable orders"));
            return;
        }
        if (("X".$system != "X200") && ("X".$system != "X300")) {
            $this->error(__("Wrong system specified"));
            return;
        }
        System::init($system);
        ini_set('memory_limit', '256M');
        Data::performArchiving();

        $this->info("Full archiving ended");
    }

}