<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 12.10.2018
 * Time: 15:23
 */

namespace App\Materom\SAP;

use App\Materom\RFCData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MasterData
{

    static public function getData($command, $in)
    {
        $globalRFCData = DB::select("select * from global_rfc_config");
        if($globalRFCData) $globalRFCData = $globalRFCData[0]; else return;
        $roleData = DB::select("select * from roles where rfc_role = '" . Auth::user()->role . "'");
        if($roleData) $roleData = $roleData[0]; else return;

        $rfcData = new RFCData($globalRFCData->rfc_router, $globalRFCData->rfc_server,
            $globalRFCData->rfc_sysnr, $globalRFCData->rfc_client,
            $roleData->rfc_user, $roleData->rfc_passwd);
        try {
            $sapconn = new \SAPNWRFC\Connection($rfcData->parameters());
            $sapfm = $sapconn->getFunction('ZSRM_RFC_GET_DATA');
            $result = $sapfm->invoke(['P_CMD' => $command,
                                      'P_IN' => $in]);
            $sapconn->close();
            if (empty($result)) return;
            if (!array_key_exists("P_OUT", $result)) return;
            return $result["P_OUT"];
        } catch (\SAPNWRFC\Exception $e) {
//          Log::error("SAPRFC (GetPOData)):" . $e->getErrorInfo());
            return $e->getErrorInfo();
        }
    }

    static public function getLifnrName($lifnr, $cover_error = 0)
    {
        $lifnr_name = DB::table("sap_lfa1")->where("lifnr", $lifnr)->value("name1");
        if (isset($lifnr_name)) return $lifnr_name;
        $lifnr_name = self::getData("LIFNR_NAME", $lifnr);
        if (!isset($lifnr_name)) {
            if ($cover_error == 0) return;
            $lifnr_name = __("Undefined supplier");
            if ($cover_error == 2)
                DB::insert("insert into sap_lfa1 (lifnr, name1) values ('$lifnr', '$lifnr_name');");
        }
        return $lifnr_name;
    }

    static public function getKunnrName($kunnr, $cover_error = 0)
    {
        $kunnr_name = DB::table("sap_kna1")->where("kunnr", $kunnr)->value("name1");
        if (isset($kunnr_name)) return $kunnr_name;
        $kunnr_name = self::getData("KUNNR_NAME", $kunnr);
        if (!isset($kunnr_name)) {
            if ($cover_error == 0) return;
            $kunnr_name = __("Undefined client");
            if ($cover_error == 2)
                DB::insert("insert into sap_kna1 (kunnr, name1) values ('$kunnr', '$kunnr_name');");
        }
        return $kunnr_name;
    }

    static public function getEkgrpName($ekgrp, $cover_error = 0)
    {
        $ekgrp_name = DB::table("sap_t024")->where("ekgrp", $ekgrp)->value("eknam");
        if (isset($ekgrp_name)) return $ekgrp_name;
        $ekgrp_name = self::getData("EKGRP_NAME", $ekgrp);
        if (!isset($ekgrp_name)) {
            if ($cover_error == 0) return;
            $ekgrp_name = __("Undefined purchase group");
            if ($cover_error == 2)
                DB::insert("insert into sap_t024 (ekgrp, eknam) values ('$ekgrp', '$ekgrp_name');");
        }
        return $ekgrp_name;
    }


}