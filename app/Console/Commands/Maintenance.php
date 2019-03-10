<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 18.11.2018
 * Time: 19:51
 */

namespace App\Console\Commands;

use App\Materom\Data;
use App\Materom\RFCData;
use App\Materom\SAP;
use App\Materom\System;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Maintenance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'materom:maintenance {maintenancesystem} {maintenancecommand}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Performs maintenance commands on DB';

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
        $system = $this->argument("maintenancesystem");
        if ($system == null) {
            $this->error(__("Please specify the system to be maintained"));
            return;
        }
        if (("X".$system != "X200") && ("X".$system != "X300")) {
            $this->error(__("Wrong system specified"));
            return;
        }
        System::init($system);

        $command = $this->argument("maintenancecommand");
        if ($command == null) {
            $this->error(__("Please specify the maintenance command"));
            return;
        }
        if (strtoupper($command) == "UPDATE_CTVS") {
            $this->update_all_ctvs();
            return;
        }
        if (strtoupper($command) == "STATISTICS") {
            Data::gatherStatistics();
            return;
        }
    }

    private function update_all_ctvs()
    {
        $pitems = DB::select("select * from ". System::$table_pitems
                         ." where vbeln != '!REPLENISH' and kunnr <> ''"
                         ." and not exists (select * from ". System::deftable_sap_client_agents ." where ".
                         System::$table_pitems .".kunnr = ". System::deftable_sap_client_agents .".kunnr and ".
                         System::$table_pitems .".ctv = ". System::deftable_sap_client_agents .".agent and ".
                         System::$table_pitems .".ctv_name = ". System::deftable_sap_client_agents .".name1) ".
                         " order by ebeln, ebelp");
        foreach ($pitems as $pitem) {
            $ctv = SAP\MasterData::getAgentForClient($pitem->kunnr);
            if (empty($ctv->agent) && empty($pitem->ctv) && empty($pitem->ctv_name)) continue;
            $count = DB::update("update ". System::$table_pitems ." set ctv='$ctv->agent', ctv_name='$ctv->agent_name' ".
              "where ebeln = '$pitem->ebeln' and ebelp = '$pitem->ebelp'");
            $this->info("Purchase order item " . SAP::alpha_output($pitem->ebeln) . "/" . SAP::alpha_output($pitem->ebelp) .
                ": CTV '" . trim($pitem->ctv) . "' => '" . trim($ctv->agent) . "' (" . trim($ctv->agent_name) . ")");
        }
    }
}