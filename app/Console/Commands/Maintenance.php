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
    protected $signature = 'materom:maintenance {maintenancecommand}';

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
        $command = $this->argument("maintenancecommand");
        if ($command == null) {
            $this->error(__("Please specify the maintenance command"));
            return;
        }
        if (strtoupper($command) == "UPDATE_CTVS") {
            $this->update_all_ctvs();
            return;
        }
//      $this->info("Archiving ended (" . $okcount . "/". count($pitems) . " item(s) archived)");
    }

    private function update_all_ctvs()
    {
        $globalRFCData = DB::select("select * from global_rfc_config");
        if($globalRFCData) $globalRFCData = $globalRFCData[0]; else return;
        $roleData = DB::select("select * from roles where rfc_role = 'Administrator'");
        if($roleData) $roleData = $roleData[0]; else return;

        $rfcData = new RFCData($globalRFCData->rfc_router, $globalRFCData->rfc_server,
            $globalRFCData->rfc_sysnr, $globalRFCData->rfc_client,
            $roleData->rfc_user, $roleData->rfc_passwd);
        try {
            $sapconn = new \SAPNWRFC\Connection($rfcData->parameters());
            $sapfm = $sapconn->getFunction('ZSRM_RFC_READ_CTV_FOR_CUSTOMER');
        } catch (\SAPNWRFC\Exception $e) {
            $this->error("SAPRFC (UpdateAllCTVs): " . $e);
        }

        $pitems = DB::select("select * from pitems where vbeln != '!REPLENISH'");
        DB::beginTransaction();
        foreach ($pitems as $pitem) {
            $ctv = null;
            try {
                $ctv = $sapfm->invoke(['I_KUNNR' => $pitem->kunnr])["E_CTV"];
            } catch (\SAPNWRFC\Exception $e) {
                $this->error("SAPRFC (UpdateAllCTVs): " . $e);
            }
            if (is_null($ctv)) break;
            $ctv_name = SAP\MasterData::getKunnrName($ctv);
            DB::update("update pitems set ctv='$ctv', ctv_name='$ctv_name' ".
              "where ebeln = '$pitem->ebeln' and ebelp = '$pitem->ebelp'");
            $this->info("Purchase order item " . SAP::alpha_output($pitem->ebeln) . "/" . SAP::alpha_output($pitem->ebelp) .
            ": CTV '" . trim($pitem->ctv) . "' => '" . trim($ctv) . "' (" . trim($ctv_name) . ")");
        }
        DB::commit();

        try {
            $sapconn->close();
        } catch (\SAPNWRFC\Exception $e) {
            $this->error("SAPRFC (UpdateAllCTVs): " . $e);
        }

    }
}