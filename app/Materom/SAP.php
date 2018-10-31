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

    static public function rejectPOItem($ebeln, $ebelp) {

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
                                      'I_GET_SET_FLAG' => 'S',
                                      'I_INDICATOR' => 'L' ]);
            $sapconn->close();
            return $result;
        } catch (\SAPNWRFC\Exception $e) {
//          Log::error("SAPRFC (GetPOData)):" . $e->getErrorInfo());
            return $e->getErrorInfo();
        }
    }

    static public function savePOItemWithChange($data) {

    }

    static public function savePOItem($ebeln, $ebelp) {

        $new_idnlf = "";
        $new_menge = "";
        $new_price = "";
        $new_eindt = "";

        $item = DB::table("pitems")->where([['ebeln', '=', $ebeln], ['ebelp', '=', $ebelp]])->first();
        if ($item->idnlf != $item->orig_idnlf) $new_idnlf = $item->idnlf;
        if ($item->qty != $item->orig_qty) $new_menge = $item->qty;
        if ($item->purch_price != $item->orig_purch_price) $new_price = $item->purch_price;
        if ($item->lfdat != $item->orig_lfdat) $new_eindt = $item->lfdat;

        $globalRFCData = DB::select("select * from global_rfc_config");
        if($globalRFCData) $globalRFCData = $globalRFCData[0]; else return;
        $roleData = DB::select("select * from roles where rfc_role = '" . Auth::user()->role . "'");
        if($roleData) $roleData = $roleData[0]; else return;

        $rfcData = new RFCData($globalRFCData->rfc_router, $globalRFCData->rfc_server,
            $globalRFCData->rfc_sysnr, $globalRFCData->rfc_client,
            $roleData->rfc_user, $roleData->rfc_passwd);
        try {
            $sapconn = new \SAPNWRFC\Connection($rfcData->parameters());
            $sapfm = $sapconn->getFunction('ZSRM_RFC_PO_CHANGE_ITEM');
            $result = $sapfm->invoke(['I_EBELN' => $ebeln,
                                      'I_EBELP' => $ebelp,
                                      'I_IDNLF' => $new_idnlf,
                                      'I_MENGE' => $new_menge,
                                      'I_PRICE' => $new_price,
                                      'I_EINDT' => $new_eindt,
                                      'I_ACKFLAG' => 'N' ]);
            $sapconn->close();
            return $result;
        } catch (\SAPNWRFC\Exception $e) {
//          Log::error("SAPRFC (GetPOData)):" . $e->getErrorInfo());
            return $e->getErrorInfo();
        }
    }

    static public function createPurchReq($lifnr, $idnlf, $mtext, $matnr,
                                          $qty, $unit, $price, $curr, $deldate, $infnr)
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
            $sapfm = $sapconn->getFunction('ZSRM_RFC_PR_CREATE');
            $result = $sapfm->invoke(['I_LIFNR' => $lifnr,
                                      'I_MATNR' => $matnr,
                                      'I_MTEXT' => $mtext,
                                      'I_IDNLF' => $idnlf,
                                      'I_LFDAT' => $lfdat,
                                      'I_PRICE' => $price,
                                      'I_CURR' => $curr,
                                      'I_MENGE' => $qty,
                                      'I_MEINS' => $unit,
                                      'I_DELDATE' => $deldate,
                                      'I_INFNR' => $infnr ]);
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
        if ($items != null) {
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
        }
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
            // check $e->getErrorInfo();
            return null;
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
            if ($lifnr == null) $lifnr = "";
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
                $inforecord->purch_price = $record["NETPR"];
                $inforecord->purch_curr = trim($record["WAERS"]);
                $inforecord->infnr = trim($record["INFNR"]);
                $inforecords[] = $inforecord;
            }
            return $inforecords;
        } catch (\SAPNWRFC\Exception $e) {
//          Log::error("SAPRFC (GetPOData)):" . $e->getErrorInfo());
            return $e->getErrorInfo();
        }

    }

    static public function readZPRETrecords($lifnr, $lifnr_name, $idnlf, $mtext, $matnr)
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
            if ($lifnr == null) $lifnr = "";
            if ($lifnr_name == null) $lifnr_name = "";
            if ($idnlf == null) $idnlf = "";
            if ($mtext == null) $mtext = "";
            if ($matnr == null) $matnr = "";
            $sapfm = $sapconn->getFunction('ZSRM_RFC_READ_ZPRET');
            $records = ($sapfm->invoke(['I_LIFNR' => $lifnr,
                                        'I_LIFNR_NAME' => $lifnr_name,
                                        'I_IDNLF' => $idnlf,
                                        'I_MTEXT' => $mtext,
                                        'I_MATNR' => $matnr
            ]))["ZPRETRECORDS"];
            $sapconn->close();
            $zpretrecords = array();
            foreach($records AS $record) {
                $zpretrecord = new \stdClass();
                $zpretrecord->lifnr = SAP::alpha_output($record["LIFNR"]);
                $zpretrecord->lifnr_name = trim($record["NAME1"]);
                $zpretrecord->idnlf = trim($record["IDNLF"]);
                $zpretrecord->mtext = trim($record["TXZ01"]);
                $zpretrecord->matnr = SAP::alpha_output($record["MATNR"]);
                $zpretrecord->purch_price = $record["PURCH_PRICE"];
                $zpretrecord->purch_curr = trim($record["PURCH_CURR"]);
                $zpretrecord->sales_price = $record["SALES_PRICE"];
                $zpretrecord->sales_curr = trim($record["SALES_CURR"]);
                $zpretrecords[] = $zpretrecord;
            }
            return $zpretrecords;
        } catch (\SAPNWRFC\Exception $e) {
//          Log::error("SAPRFC (GetPOData)):" . $e->getErrorInfo());
            return $e->getErrorInfo();
        }

    }

    static public function getAgentClients($ctvuserid)
    {

        $globalRFCData = DB::select("select * from global_rfc_config");
        if($globalRFCData) $globalRFCData = $globalRFCData[0]; else return;
        $roleData = DB::select("select * from roles where rfc_role = '" . Auth::user()->role . "'");
        if($roleData) $roleData = $roleData[0]; else return;

        $rfcData = new RFCData($globalRFCData->rfc_router, $globalRFCData->rfc_server,
            $globalRFCData->rfc_sysnr, $globalRFCData->rfc_client,
            $roleData->rfc_user, $roleData->rfc_passwd);
        try {
            $clients = array();
            $agents = DB::select("select agent from users_agent where id = '$ctvuserid'");
            if (empty($agents)) return $clients;
            $sapconn = new \SAPNWRFC\Connection($rfcData->parameters());
            $sapfm = $sapconn->getFunction('ZSRM_RFC_READ_CLIENT_HIERARCHY');
            $records = ($sapfm->invoke(['I_AGENTS' => json_encode($agents)]))["E_CLIENTS"];
            $sapconn->close();
            $records = json_decode($records);
            foreach($records AS $record) $clients[] = $record->CLIENT;
            return $clients;
        } catch (\SAPNWRFC\Exception $e) {
//          Log::error("SAPRFC (GetPOData)):" . $e->getErrorInfo());
            return $e->getErrorInfo();
        }
    }
};
