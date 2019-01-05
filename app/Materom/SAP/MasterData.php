<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 12.10.2018
 * Time: 15:23
 */

namespace App\Materom\SAP;

use App\Materom\RFCData;
use App\Materom\SAP;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasterData
{

    static public function getData($command, $in)
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
        if (empty(trim($lifnr))) return "";
        if (Auth::user()->role == "CTV") return "Furnizor " . SAP::alpha_output($lifnr);
        $lifnr_name = DB::table("sap_lfa1")->where("lifnr", $lifnr)->value("name1");
        if (isset($lifnr_name)) return $lifnr_name;
        $lifnr_name = self::getData("LIFNR_NAME", $lifnr);
        if (!isset($lifnr_name)) {
            $lifnr_name = __("Undefined supplier");
        }
        if ($cover_error == 2) {
            $lifnr_name = str_replace('"', "'", $lifnr_name);
            DB::insert('insert into sap_lfa1 (lifnr, name1) values ("'.$lifnr.'", "'.$lifnr_name.'");');
        }
        return $lifnr_name;
    }

    static public function getKunnrName($kunnr, $cover_error = 0)
    {
        if (empty(trim($kunnr))) return "";
        $kunnr_name = DB::table("sap_kna1")->where("kunnr", $kunnr)->value("name1");
        if (isset($kunnr_name)) return $kunnr_name;
        $kunnr_name = self::getData("KUNNR_NAME", $kunnr);
        if (!isset($kunnr_name)) {
            $kunnr_name = __("Undefined client");
        }
        if ($cover_error == 2) {
            $kunnr_name = str_replace('"', "'", $kunnr_name);
            DB::insert('insert into sap_kna1 (kunnr, name1) values ("'.$kunnr.'", "'.$kunnr_name.'");');
        }
        return $kunnr_name;
    }

    static public function getEkgrpName($ekgrp, $cover_error = 0)
    {
        if (empty(trim($ekgrp))) return "";
        $ekgrp_name = DB::table("sap_t024")->where("ekgrp", $ekgrp)->value("eknam");
        if (isset($ekgrp_name)) return $ekgrp_name;
        $ekgrp_name = self::getData("EKGRP_NAME", $ekgrp);
        if (!isset($ekgrp_name) || $ekgrp_name == null) {
            $ekgrp_name = __("Undefined purchase group");
        }
        if ($cover_error == 2) {
            $ekgrp_name = str_replace('"', "'", $ekgrp_name);
            DB::insert('insert into sap_t024 (ekgrp, eknam) values ("'.$ekgrp.'", "'.$ekgrp_name.'");');
        }
        return $ekgrp_name;
    }

    static public function refreshCustomerCache()
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
            $sapfm = $sapconn->getFunction('ZSRM_RFC_GET_CUSTOMERS');
            $kunnr_from = "";
            $customers = array();
            while (true) {
                $result = json_decode($sapfm->invoke(['P_KUNNR_FROM' => $kunnr_from])["E_DATA"]);
                if (empty($result)) break;
                foreach($result as $customer) {
                    $customer->kunnr = $customer->KUNNR; unset($customer->KUNNR);
                    $customer->name1 = $customer->NAME1; unset($customer->NAME1);
                    $customers[] = $customer;
                }
                $kunnr_from = $customer->kunnr;
            }
            $sapconn->close();
        } catch (\SAPNWRFC\Exception $e) {
//          Log::error("SAPRFC (GetPOData)):" . $e->getErrorInfo());
            return $e->getErrorInfo();
        }
        DB::beginTransaction();
        DB::delete("delete from sap_kna1");
        foreach ($customers as $customer) {
            $customer->name1 = str_replace('"', "'", $customer->name1);
            DB::insert('insert into sap_kna1 (kunnr, name1) values ("'. $customer->kunnr .
                '", "'. $customer->name1 . '")');
        }
        DB::commit();
        Log::info("Customers cache refreshed (" . count($customers) . " records)");
    }

    static public function refreshVendorCache()
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
            $sapfm = $sapconn->getFunction('ZSRM_RFC_GET_VENDORS');
            $lifnr_from = "";
            $vendors = array();
            while (true) {
                $result = json_decode($sapfm->invoke(['P_LIFNR_FROM' => $lifnr_from])["E_DATA"]);
                if (empty($result)) break;
                foreach($result as $vendor) {
                    $vendor->lifnr = $vendor->LIFNR; unset($vendor->LIFNR);
                    $vendor->name1 = $vendor->NAME1; unset($vendor->NAME1);
                    $vendors[] = $vendor;
                }
                $lifnr_from = $vendor->lifnr;
            }
            $sapconn->close();
        } catch (\SAPNWRFC\Exception $e) {
//          Log::error("SAPRFC (GetPOData)):" . $e->getErrorInfo());
            return $e->getErrorInfo();
        }
        DB::beginTransaction();
        DB::delete("delete from sap_lfa1");
        foreach ($vendors as $vendor) {
            $vendor->name1 = str_replace('"', "'", $vendor->name1);
            DB::insert('insert into sap_lfa1 (lifnr, name1) values ("'. $vendor->lifnr .
                '", "'. $vendor->name1 . '")');
        }
        DB::commit();
        Log::info("Vendors cache refreshed (" . count($vendors) . " records)");
    }

    static public function refreshPurchGroupsCache()
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
            $sapfm = $sapconn->getFunction('ZSRM_RFC_GET_PURCH_GROUPS');
            $result = json_decode($sapfm->invoke([])["E_DATA"]);
            $pgroups = array();
            if (empty($result)) return;
            foreach($result as $pgroup) {
                $pgroup->ekgrp = $pgroup->EKGRP; unset($pgroup->EKGRP);
                $pgroup->eknam = $pgroup->EKNAM; unset($pgroup->EKNAM);
                $pgroup->ektel = $pgroup->EKTEL; unset($pgroup->EKTEL);
                $pgroup->smtp_addr = $pgroup->SMTP_ADDR; unset($pgroup->SMTP_ADDR);
                $pgroups[] = $pgroup;
            }
            $sapconn->close();
        } catch (\SAPNWRFC\Exception $e) {
//          Log::error("SAPRFC (GetPOData)):" . $e->getErrorInfo());
            return $e->getErrorInfo();
        }
        DB::beginTransaction();
        DB::delete("delete from sap_t024");
        foreach ($pgroups as $pgroup) {
            $pgroup->eknam = str_replace('"', "'", $pgroup->eknam);
            $pgroup->ektel = str_replace('"', "'", $pgroup->ektel);
            DB::insert('insert into sap_t024 (ekgrp, eknam, ektel, smtp_addr) values ("'.
                $pgroup->ekgrp . '", "'. $pgroup->eknam . '",  "'. $pgroup->ektel . '", "'. $pgroup->smtp_addr . '")');
        }
        DB::commit();
        Log::info("Purchase groups cache refreshed (" . count($pgroups) . " records)");
    }

}