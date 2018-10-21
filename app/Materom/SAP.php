<?php
/**
 * Created by PhpStorm.
 * User: Radu Trandafir
 * Date: 01.08.2018
 * Time: 20:47
 */

namespace App\Materom;

use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SAP
{

    static public function alpha_output($input) {
        $output = trim($input);
        if (ctype_digit($output)) {
            $output = ltrim($output, "0");
            if (empty($output)) $output = $input;
        }
        return $output;
    }

    static public function alpha_input($input) {
        $output = trim($input);
        if (ctype_digit($output)) {
            $output = str_pad($input, 10, "0", STR_PAD_LEFT);
        }
        return $output;
    }

    static public function date_output($input) {
        $output = trim($input);
        if (empty($output)) return null;
        if (strtolower($output) == "null") return null;
        if ($output == "00000000") return null;
        return substr($output, 0, 4) . '-' .
               substr($output, 4, 2) . '-' .
               substr($output, 6, 2);
    }

    static public function date_input($input) {
        $output = trim($input);
        if (empty($output)) return "00000000";
        if (strtolower($output) == "null") return "00000000";
        return substr($output, 0, 4) .
            substr($output, 5, 2) .
            substr($output, 8, 2);
    }

    static public function rfcUpdateAPIToken($api_token)
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
            $sapfm = $sapconn->getFunction('ZSRM_RFC_SET_API_TOKEN');
            $returnValue = $sapfm->invoke(['API_TOKEN' => $api_token]);
            $sapconn->close();
            Log::info("SAPRFC (UpdateAPIToken): Successfully updated API token");
        } catch (\SAPNWRFC\Exception $e) {
            Log::error("SAPRFC (UpdateAPIToken):" . $e->getErrorInfo());
        }
    }

    static public function rfcGetPOData($ebeln) {

        $globalRFCData = DB::select("select * from global_rfc_config");
        if($globalRFCData) $globalRFCData = $globalRFCData[0]; else return;
        $roleData = DB::select("select * from roles where rfc_role = '" . Auth::user()->role . "'");
        if($roleData) $roleData = $roleData[0]; else return;

        $rfcData = new RFCData($globalRFCData->rfc_router, $globalRFCData->rfc_server,
            $globalRFCData->rfc_sysnr, $globalRFCData->rfc_client,
            $roleData->rfc_user, $roleData->rfc_passwd);
        try {
//            \SAPNWRFC\Connection::setTraceLevel(3);
//            \SAPNWRFC\Connection::setTraceDir("/home/srm.materom.ro/public/storage/logs");
//            \SAPNWRFC\Connection::setTraceDir("C:/Users/Radu/Apache24/htdocs/matetrand/storage/logs");
            $sapconn = new \SAPNWRFC\Connection($rfcData->parameters());
            $sapfm = $sapconn->getFunction('ZSRM_RFC_GET_PO_DATA2');
            $result = $sapfm->invoke(['P_EBELN' => $ebeln]);
            $sapconn->close();
            return $result;
        } catch (\SAPNWRFC\Exception $e) {
//          Log::error("SAPRFC (GetPOData)):" . $e->getErrorInfo());
            return $e->getErrorInfo();
        }
    }

    static public function acknowledgePOItem($ebeln, $ebelp, $ackflag) {

        $globalRFCData = DB::select("select * from global_rfc_config");
        if($globalRFCData) $globalRFCData = $globalRFCData[0]; else return;
        $roleData = DB::select("select * from roles where rfc_role = '" . Auth::user()->role . "'");
        if($roleData) $roleData = $roleData[0]; else return;

        $rfcData = new RFCData($globalRFCData->rfc_router, $globalRFCData->rfc_server,
            $globalRFCData->rfc_sysnr, $globalRFCData->rfc_client,
            $roleData->rfc_user, $roleData->rfc_passwd);
        try {
            $sapconn = new \SAPNWRFC\Connection($rfcData->parameters());
            $sapfm = $sapconn->getFunction('ZSRM_RFC_PO_GET_SET_ACK_REJ');
            $result = $sapfm->invoke(['I_EBELN' => $ebeln,
                                      'I_EBELP' => $ebelp,
                                      'I_GET_SET_FLAG' => 'B',
                                      'I_INDICATOR' => $ackflag ]);
            $sapconn->close();
            return $result;
        } catch (\SAPNWRFC\Exception $e) {
//          Log::error("SAPRFC (GetPOData)):" . $e->getErrorInfo());
            return $e->getErrorInfo();
        }
    }

    static public function refreshDeliveryStatus($items = null)
    {
        if ($items == null)
            $items = DB::select("select ebeln, ebelp, deldate, delqty, grdate, grqty, gidate from pitems order by ebeln, ebelp");
        else {
            $sql = "where ";
            foreach ($items as $item) $sql .= "(ebeln = '$item->ebeln' and ebelp = '$item->ebelp') or ";
            $sql = substr($sql, 0, -4);
            $items = DB::select("select ebeln, ebelp, deldate, delqty, grdate, grqty, gidate from pitems $sql order by ebeln, ebelp");
        }
        $items = SAP::rfcGetDeliveryData($items);
        DB::beginTransaction();
        foreach ($items as $item) {
            db::update("update pitems set" .
                " deldate = " . ($item->deldate == null ? "null" : "'$item->deldate'") .
                ", delqty = '$item->delqty'" .
                ", grdate = " . ($item->grdate == null ? "null" : "'$item->grdate'") .
                ", grqty = '$item->grqty'" .
                ", gidate = " . ($item->gidate == null ? "null" : "'$item->gidate'") .
                " where ebeln = '$item->ebeln' and ebelp = '$item->ebelp';");
        }
        DB::commit();
        return "OK";
    }

    static public function rfcGetDeliveryData($items) {

        $globalRFCData = DB::select("select * from global_rfc_config");
        if($globalRFCData) $globalRFCData = $globalRFCData[0]; else return;
        $roleData = DB::select("select * from roles where rfc_role = '" . Auth::user()->role . "'");
        if($roleData) $roleData = $roleData[0]; else return;

        $rfcData = new RFCData($globalRFCData->rfc_router, $globalRFCData->rfc_server,
            $globalRFCData->rfc_sysnr, $globalRFCData->rfc_client,
            $roleData->rfc_user, $roleData->rfc_passwd);
        try {
            $sapconn = new \SAPNWRFC\Connection($rfcData->parameters());
            $sapfm = $sapconn->getFunction('ZSRM_RFC_GET_DELIVERY_STATUS');
            foreach ($items as $item) {
                $item->deldate = SAP::date_input($item->deldate);
                $item->grdate = SAP::date_input($item->grdate);
                $item->gidate = SAP::date_input($item->gidate);
            }
            $items = json_decode(($sapfm->invoke(['P_ITEMS' => json_encode($items)]))["P_ITEMS"]);
            $sapconn->close();
            foreach ($items as $item) {
                $item->ebeln = $item->EBELN; unset($item->EBELN);
                $item->ebelp = $item->EBELP; unset($item->EBELP);
                $item->deldate = SAP::date_output($item->DELDATE); unset($item->DELDATE);
                $item->delqty = $item->DELQTY; unset($item->DELQTY);
                $item->grdate = SAP::date_output($item->GRDATE); unset($item->GRDATE);
                $item->grqty = $item->GRQTY; unset($item->GRQTY);
                $item->gidate = SAP::date_output($item->GIDATE); unset($item->GIDATE);
            }
            return $items;
        } catch (\SAPNWRFC\Exception $e) {
//          Log::error("SAPRFC (GetPOData)):" . $e->getErrorInfo());
            return $e->getErrorInfo();
        }
    }

    static public function readInforecords($lifnr, $lifnr_name, $idnlf, $mtext, $matnr)
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
//            \SAPNWRFC\Connection::setTraceLevel(3);
//            \SAPNWRFC\Connection::setTraceDir("/home/srm.materom.ro/public/storage/logs");
//            \SAPNWRFC\Connection::setTraceDir("C:/Users/Radu/Apache24/htdocs/matetrand/storage/logs");
            if ($lifnr == null) $lifnr = "20786";
            if ($lifnr_name == null) $lifnr_name = "";
            if ($idnlf == null) $idnlf = "";
            if ($mtext == null) $mtext = "";
            if ($matnr == null) $matnr = "";
            $sapfm = $sapconn->getFunction('ZSRM_RFC_READ_INFORECORDS');
            // $records = json_decode(($sapfm->invoke(['I_LIFNR' => $lifnr,
            //                                        'I_LIFNR_NAME' => $lifnr_name,
            //                                        'I_IDNLF' => $idnlf,
            //                                        'I_MTEXT' => $mtext,
            //                                        'I_MATNR' => $matnr
            //                                       ]))["INFORECORDS"]);
            $records = ($sapfm->invoke(['I_LIFNR' => $lifnr,
                                                    'I_LIFNR_NAME' => $lifnr_name,
                                                    'I_IDNLF' => $idnlf,
                                                    'I_MTEXT' => $mtext,
                                                    'I_MATNR' => $matnr
                                       ]))["INFORECORDS"];
            $sapconn->close();
            $inforecords = array();
            foreach($records AS $record) {
                $inforecord = new \stdClass();
                $inforecord->lifnr = SAP::alpha_output($record["LIFNR"]);
                $inforecord->lifnr_name = trim($record["NAME1"]);
                $inforecord->idnlf = trim($record["IDNLF"]);
                $inforecord->mtext = trim($record["TXZ01"]);
                $inforecord->matnr = SAP::alpha_output($record["MATNR"]);
                $inforecord->price = $record["NETPR"];
                $inforecord->curr = trim($record["WAERS"]);
                $inforecords[] = $inforecord;
            }
            return $inforecords;
        } catch (\SAPNWRFC\Exception $e) {
//          Log::error("SAPRFC (GetPOData)):" . $e->getErrorInfo());
            return $e->getErrorInfo();
        }

    }


};
