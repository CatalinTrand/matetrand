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

};
