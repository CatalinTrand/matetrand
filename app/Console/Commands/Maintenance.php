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
use App\Materom\SAP\MasterData;
use App\Materom\System;
use Exception;
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
            self::update_all_ctvs($this);
            return;
        }
        if (strtoupper($command) == "STATISTICS") {
            Data::gatherStatistics();
            return;
        }
        if (strtoupper($command) == "UPDATE_SAP_CLIENT_AGENTS") {
            self::update_all_sap_client_agents($this);
            return;
        }
        if (strtoupper($command) == "REFRESH_DELIVERY_STATUS") {
            self::refresh_delivery_status($this);
            return;
        }
        if (strtoupper($command) == "REFRESH_CUSTOMER_CACHE") {
            MasterData::refreshCustomerCache();
            return;
        }
    }

    public static function update_all_sap_client_agents($command = null)
    {
        $customers = DB::select("select distinct kunnr from ". System::$table_pitems
                         . " where vbeln != '!REPLENISH' and kunnr <> ''");
        DB:: beginTransaction();
        $count = 0;
        foreach($customers as $customer) {
            $kunnr = $customer->kunnr;
            DB::delete("delete from " . System::deftable_sap_client_agents . " where kunnr = '$kunnr'");
            $record = SAP::readCTVforCustomer($kunnr);
            if (!empty($record->agent)) {
                $count++;
                try {
                    DB::insert("insert into " . System::deftable_sap_client_agents . " (kunnr, agent, name1) " .
                        "values ('$kunnr', '$record->agent', '$record->agent_name')");
                } catch (Exception $e) {
                    Log::error($e);
                }

            }
        }
        DB::commit();
        Log::info("Table sap_client_agents refreshed: " . count($customers) . " customers checked, $count customers updated.");
    }

    public static function update_all_ctvs($command = null)
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
            if ($command != null)
                $command->info("Purchase order item " . SAP::alpha_output($pitem->ebeln) . "/" . SAP::alpha_output($pitem->ebelp) .
                    ": CTV '" . trim($pitem->ctv) . "' => '" . trim($ctv->agent) . "' (" . trim($ctv->agent_name) . ")");
        }
        Log::info("Agents of table items refreshed: " . count($pitems) . " items updated.");
    }

    public static function refresh_delivery_status($command = null)
    {
        SAP::refreshDeliveryStatus(2);
        if ($command != null) $command->info("Delivery status successfully refreshed.");
        else Log::info("Delivery status successfully refreshed.");
    }
}