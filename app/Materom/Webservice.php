<?php

namespace App\Materom;

use App\Materom\SAP\MasterData;
use App\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;


class Webservice
{

    static public function rfcPing($rfc_router, $rfc_server, $rfc_sysnr,
                                   $rfc_client, $rfc_user, $rfc_password)
    {
        return (new RFCData($rfc_router, $rfc_server, $rfc_sysnr,
            $rfc_client, $rfc_user, $rfc_password))->ping();
    }

    static public function insertManufacturer($userid, $mfrnr)
    {
        if (ctype_digit($mfrnr)) $mfrnr = str_pad($mfrnr, 10, "0", STR_PAD_LEFT);
        $find = DB::select("select * from ". System::$table_users_sel ." where id = '$userid' and mfrnr = '$mfrnr'");
        if (count($find) == 0) {
            $mfrnr_name = SAP\MasterData::getLifnrName($mfrnr);
            if (is_array($mfrnr_name) || strlen(trim($mfrnr_name)) == 0) return __('Manufacturer does not exist');
            DB::insert("insert into ". System::$table_users_sel ." (id, mfrnr, mfrnr_name) values ('$userid','$mfrnr', '$mfrnr_name')");
            return "";
        } else return __("Manufacturer already exists");
    }

    static public function insertReferenceUser($userid, $refid)
    {
        $find = DB::select("select * from users where id = '$refid'");
        if (count($find) == 0) {
            return "No such user!";
        }

        $find = DB::select("select * from users_ref where id = '$userid' and refid = '$refid'");
        if (count($find) == 0) {
            DB::insert("insert into users_ref (id, refid) values ('$userid','$refid')");
            return "";
        } else return __("Reference user already exists");
    }

    static public function insertAgent($userid, $agent)
    {
        $agent = strtoupper(SAP::alpha_input($agent));
        $agent_name = MasterData::getAgentName($agent);
        if (empty($agent_name)) return "The agent is not defined in SAP";

        $find = DB::select("select * from ". System::$table_users_agent ." where id = '$userid' and agent = '$agent'");
        if (count($find) == 0) {
            DB::insert("insert into ". System::$table_users_agent ." (id, agent) values ('$userid','$agent')");
            return "";
        } else return __("This agent is already defined for user");
    }

    static public function insertCustomer($userid, $kunnr)
    {
        $kunnr = strtoupper(SAP::alpha_input($kunnr));
        $kunnr_name = MasterData::getKunnrName($kunnr);
        if (empty($kunnr_name)) return "The client is not defined in SAP";

        $find = DB::select("select * from ". System::$table_users_cli ." where id = '$userid' and kunnr = '$kunnr'");
        if (count($find) == 0) {
            DB::insert("insert into ". System::$table_users_cli ." (id, kunnr) values ('$userid','$kunnr')");
            return "";
        } else return __("This customer is already defined for user");
    }

    static public function changePassword($userid, $newPass)
    {
        $hash = Hash::make($newPass);
        DB::update("update users set password = '$hash' where id = '$userid'");
        return "OK";
    }

    static public function verifyAPIToken($token)
    {

        $user = DB::select("select * from users where api_token = '$token'");

        if ($user)
            return true;

        return false;
    }

    public static function getSubTree($type, $sorder, $porder, $item)
    {
        $data = Orders::loadFromCache($sorder, $porder);
        if ($type == 'S') return self::getSOSubTree($sorder, $data);
        if ($type == 'P') return self::getPOSubTree($data);
        if ($type == 'I') return self::getPOItemSubTree($data, $item);
    }

    public static function getSOSubTree($sorder, $data)
    {
        $porders = array();
        if ($data == null) return json_encode($porders);
        Session::put("autoexplode_SO", $sorder);
        foreach ($data as $porder) {
            $outporder = $porder;
            unset($outporder->items);
            unset($outporder->wtime);
            unset($outporder->ctime);
            unset($outporder->salesorders);
            $porders[] = $outporder;
        }
        return json_encode($porders);
    }

    public static function getPOSubTree($data)
    {
        $pitems = array();
        if ($data == null) return json_encode($pitems);
        $porder = reset($data);
        Session::put("autoexplode_PO", $porder->ebeln);
        foreach ($porder->items as $pitem) {
            $outpitem = $pitem;
            unset($outpitem->qty);
            unset($outpitem->qty_uom);
            unset($outpitem->lfdat);
            unset($outpitem->purch_price);
            unset($outpitem->purch_curr);
            unset($outpitem->purch_prun);
            unset($outpitem->purch_puom);
            unset($outpitem->sales_price);
            unset($outpitem->sales_curr);
            unset($outpitem->sales_prun);
            unset($outpitem->sales_puom);
            $outpitem->mfrnr = ucfirst(strtolower(substr(MasterData::getLifnrName($outpitem->mfrnr), 0, 9)));
            $pitems[] = $outpitem;
        }
        return json_encode($pitems);
    }

    public static function getPOItemSubTree($data, $itemno)
    {
        $pitemchgs = array();
        if ($data != null && isset(reset($data)->items[$itemno])) {
            $pitem = reset($data)->items[$itemno];
            foreach ($pitem->changes as $pitemchg) {
                if ((Auth::user()->role == 'Furnizor') && ($pitemchg->internal == 1)) continue;
                $outpitemchg = $pitemchg;
                unset($outpitemchg->internal);
                $pitemchgs[] = $outpitemchg;
            }
        }
        return json_encode($pitemchgs);
    }

    public static function sortMessages($type)
    {
        Session::put("message-sorting", $type);
        return "";
    }

    public static function replyMessage($ebeln, $ebelp, $message, $to)
    {
        $order = SAP::alpha_output($ebeln) . "/" . SAP::alpha_output($ebelp);
        $duser = "";
        $stage = '';
        if ($to[0] == 'F') {
            $stage = 'F';
            $porder = DB::select("select * from ". System::$table_porders ." where ebeln = '$ebeln'")[0];
            $lifnr = $porder->lifnr;
            $duser = DB::table("users")->where([["role", "=", "Furnizor"],
                ["lifnr", "=", $lifnr],
                ["sap_system", "=", Auth::user()->sap_system]
            ])->value("id");
        }
        if ($to[0] == 'R') {
            $stage = 'R';
            $porder = DB::select("select * from ". System::$table_porders ." where ebeln = '$ebeln'")[0];
            $ekgrp = $porder->ekgrp;
            $duser = DB::table("users")->where([["role", "=", "Referent"],
                ["ekgrp", "=", $ekgrp],
                ["sap_system", "=", Auth::user()->sap_system]
            ])->value("id");
            if (Auth::user()->role == "CTV") $internal = 1;
        }
        if ($to[0] == 'C') {
            $stage = 'C';
            $kunnr = DB::table(System::$table_pitems)->where([["ebeln", "=", $ebeln], ["ebelp", "=", $ebelp]])->value("kunnr");
            $dusers = DB::select("select id, count(*) as count from ". System::$table_users_agent ." join ". System::$table_user_agent_clients ." using (id) where kunnr = '$kunnr' group by id order by count, id");
            if ($dusers == null || empty($dusers))
                $duser = DB::table(System::$table_user_agent_clients)->where("kunnr", $kunnr)->value("id");
            else $duser = $dusers[0]->id;
            if (Auth::user()->role == "Referent") $internal = 1;
        }

        $cdate = now();
        DB::update("update ". System::$table_pitemchg ." set acknowledged = '1' where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$cdate' and ctype = 'E'");
        DB::insert("insert into ". System::$table_pitemchg ." (ebeln, ebelp, cdate, stage, ctype, reason, cuser, cuser_name, duser) values " .
            "('$ebeln','$ebelp', '$cdate', '$stage', 'E', '$message', '" . Auth::user()->id . "', '" . Auth::user()->username . "', '$duser')");
        Mailservice::sendMessageCopy($duser, Auth::user()->username, $order, $message);
        return "";
    }

    static function readPOItem($order, $item)
    {
        $porder = DB::table(System::$table_porders)->where("ebeln", $order)->first();
        $pitem = DB::table(System::$table_pitems)->where([["ebeln", '=', $order], ["ebelp", '=', $item]])->first();
        $pitem->lifnr = $porder->lifnr;
        $pitem->lifnr_name = MasterData::getLifnrName($porder->lifnr);
        $pitem->defmargin = "";
        $pitem->defmargin = MasterData::getSalesMargin($porder->lifnr, $pitem->mfrnr);
        $pitem->curr = $porder->curr;
        $pitem->fxrate = $porder->fxrate;
        return json_encode($pitem);
    }

    static function sendAck($ebeln, $ebelp, $cdate)
    {
        if ($cdate == null) {
            $lastchanges = DB::select("select cdate from ". System::$table_pitemchg . " where ebeln = '$ebeln' and ebelp = '$ebelp' and (ctype = 'A' or ctype = 'X') order by cdate desc");
            if ($lastchanges == null || empty($lastchanges)) return "No suitable status record found";
            $cdate = $lastchanges[0]->cdate;
        }
        if ($cdate != null)
            DB::update("update ". System::$table_pitemchg ." set acknowledged = '1' where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$cdate'");
        return "";
    }

    public static function readProposals($type, $ebeln, $ebelp)
    {
        $proposal = DB::table(System::$table_pitemchg)->where([["ebeln", "=", $ebeln], ["ebelp", "=", $ebelp], ["ctype", "=", $type]])->orderBy("cdate", "desc")->first();
        if ($proposal == null) return json_encode(array());
        $proposals = DB::select("select * from ". System::$table_pitemchg_proposals ." where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$proposal->cdate' and type = '$type'");
        foreach ($proposals as $proposal) {
            $proposal->lifnr_name = MasterData::getLifnrName($proposal->lifnr);
        }
        return json_encode($proposals);
    }

    public static function acceptItemChange($ebeln, $ebelp, $type)
    {
        $item = DB::table(System::$table_pitems)->where([['ebeln', '=', $ebeln], ['ebelp', '=', $ebelp]])->first();
        $_porder = Orders::readPOrder($ebeln);
        if ($_porder == null) return;
        if (!isset($_porder->items[$ebelp])) return;
        if ($_porder->items[$ebelp]->accept == 0) {
            if ($type != "F")
                return;
        }
        $item_changed = ($item->changed == "1") || ($item->changed == "2");
        $pstage = $item->stage;
        $cdate = now();
        if ($type != "F") {
            if (!$item_changed) {
                $result = SAP::acknowledgePOItem($ebeln, $ebelp, " ");
                if (($result != null) && !is_string($result)) $result = json_encode($result);
                if (($result != null) && strlen(trim($result)) != 0) return $result;
                DB::update("update ". System::$table_pitems ." set stage = 'Z', status = 'A', pstage = '$pstage' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                DB::insert("insert into ". System::$table_pitemchg ." (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name) values " .
                    "('$ebeln','$ebelp', 'A', 'R', '$cdate', '" . Auth::user()->id . "','" . Auth::user()->username . "')");
            } else {
                if ($item->stage == 'F') {
                    DB::update("update ". System::$table_pitems ." set stage = 'R', status = 'T', pstage = '$pstage' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                    DB::insert("insert into ". System::$table_pitemchg ." (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name) values " .
                        "('$ebeln','$ebelp', 'T', 'R', '$cdate', '" . Auth::user()->id . "','" . Auth::user()->username . "')");
                } else {
                    $reason = "";
                    $new_status = "A";
                    if ($pstage != 'Z') {
                        if (trim($item->idnlf) == trim($item->orig_idnlf)) {
                            $result = SAP::savePOItem($ebeln, $ebelp);
                            if (($result != null) && !is_string($result)) $result = json_encode($result);
                            if (($result != null) && strlen(trim($result)) != 0) return $result;
                            if ($item->vbeln != Orders::stockorder) {
                                $result = SAP::changeSOItem($item->vbeln, $item->posnr,
                                    $item->qty, $item->qty_uom, $_porder->lifnr, "", "", "",
                                    $item->sales_price, $item->sales_curr, $item->purch_price, $item->purch_curr, $item->lfdat);
                                if (($result != null) && !is_string($result)) $result = json_encode($result);
                                if (($result != null) && strlen(trim($result)) != 0) return $result;
                            }
                        } else {
                            $result = SAP::rejectPOItem($ebeln, $ebelp);
                            if (($result != null) && !is_string($result)) $result = json_encode($result);
                            if (($result != null) && strlen(trim($result)) != 0) return $result;
                            if ($item->vbeln == Orders::stockorder) {
                                $result = SAP::createPurchReq($_porder->lifnr, $item->idnlf, $item->mtext, SAP::newMatnr($item->matnr),
                                    $item->qty, $item->qty_uom,
                                    $item->purch_price, $item->purch_curr, $item->lfdat);
                                if (!empty(trim($result))) {
                                    if (substr($result, 0, 2) == "OK")
                                        $reason = __("New purchase requisition") . " " . SAP::alpha_output(substr($result, 2));
                                    else return $result;
                                }
                            } else {
                                $result = SAP::processSOItem($item->vbeln, $item->posnr,
                                    $item->qty, $item->qty_uom, $_porder->lifnr, SAP::newMatnr($item->matnr),
                                    $item->mtext, $item->idnlf, $item->purch_price, $item->purch_curr,
                                    $item->sales_price, $item->sales_curr, $item->lfdat);
                                if (!empty(trim($result))) {
                                    if (substr($result, 0, 2) == "OK")
                                        $reason = __("New sales order item"). " " . substr(trim($result), 2) . " from item " . ltrim($item->posnr, "0");
                                    else return $result;
                                }
                                $new_status = 'X';
                            }
                        }
                    } else $reason = __("Definitively accepted");
                    DB::update("update ". System::$table_pitems ." set stage = 'Z', status = '$new_status', pstage = '$pstage' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                    DB::insert("insert into ". System::$table_pitemchg ." (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name, reason) values " .
                        "('$ebeln','$ebelp', 'A', 'Z', '$cdate', '" . Auth::user()->id . "','" . Auth::user()->username . "', '$reason')");
                }
            }
        } elseif ($item->pstage == 'Z') {
            DB::update("update ". System::$table_pitems ." set stage = 'Z', status = 'A', pstage = '$pstage' where ebeln = '$ebeln' and ebelp = '$ebelp'");
            DB::insert("insert into ". System::$table_pitemchg ." (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name, acknowledged, oldval) values " .
                "('$ebeln','$ebelp', 'A', 'Z', '$cdate', '" . Auth::user()->id . "','" . Auth::user()->username . "', 1, 'F')");

        }

        return "";
    }

    public static function cancelItem($ebeln, $item, $category, $reason, $new_status, $new_stage)
    {
        $pitem = DB::table(System::$table_pitems)->where([['ebeln', '=', $ebeln], ['ebelp', '=', $item]])->first();
        $_porder = Orders::readPOrder($ebeln);
        if ($_porder == null) return;
        if (!isset($_porder->items[$item])) return;
        if (!(($_porder->items[$item]->reject != 0) ||
              ($_porder->items[$item]->inquired == 4 && $_porder->items[$item]->inq_reply == 1))) return;
        $old_stage = $pitem->stage;
        if ($new_status == 'X') {
            $result = SAP::acknowledgePOItem($ebeln, $item, " ");
            if (($result != null) && !is_string($result)) $result = json_encode($result);
            if (($result != null) && strlen(trim($result)) != 0) return $result;
            $result = SAP::rejectPOItem($ebeln, $item);
            if (($result != null) && !is_string($result)) $result = json_encode($result);
            if (($result != null) && strlen(trim($result)) != 0) return $result;
            if ($pitem->vbeln != Orders::stockorder) {
                $result = SAP::rejectSOItem($pitem->vbeln, $pitem->posnr, '09');
                if (($result != null) && !is_string($result)) $result = json_encode($result);
                if (($result != null) && strlen(trim($result)) != 0) return $result;
                $ctvusers = DB::select("select distinct id from ". System::$table_user_agent_clients ." where kunnr = '$pitem->kunnr'");
                if (($ctvusers == null) || empty($ctvusers)) {
                    $ctvuser1 = DB::table(System::$table_roles)->where([["rfc_role", "=", "CTV"]])->value("user1");
                    if (($ctvuser1 != null) && !empty($ctvuser1)) {
                        try {
                            Mailservice::sendSalesOrderNotification($ctvuser1, $pitem->vbeln, $pitem->posnr);
                        } catch (Exception $e) {
                        }
                    }
                } else {
                    foreach ($ctvusers as $ctvuser) {
                        try {
                            Mailservice::sendSalesOrderNotification($ctvuser->id, $pitem->vbeln, $pitem->posnr);
                        } catch (Exception $e) {
                        }
                    }
                }
            }
            if ($old_stage != 'R') {
                $refuser = DB::table("users")->where(["ekgrp" => $_porder->ekgrp, "role" => "Referent",
                    "active" => 1, "sap_system" => Auth::user()->sap_system])->first();
                if ($refuser != null) {
                    try {
                        Mailservice::sendSalesOrderNotification($refuser->id, $pitem->vbeln, $pitem->posnr);
                    } catch (Exception $e) {}
                }
            }
        }
        DB::beginTransaction();
        $cdate = now();
        DB::update("update ". System::$table_pitems ." set status = '$new_status', pstage = '$old_stage', stage = '$new_stage' where ebeln = '$ebeln' and ebelp = '$item'");
        $category1 = addcslashes($category, "'");
        $reason1 = addcslashes($reason, "'");
        DB::insert("insert into ". System::$table_pitemchg ." (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name, oldval, reason) values " .
            "('$ebeln','$item', '$new_status', '$new_stage', '$cdate', '" . Auth::user()->id . "','" . Auth::user()->username . "', '$category1', '$reason1')");
        DB::commit();
        return "";
    }

    public static function itemsOfOrder($type, $order, $history, $vbeln)
    {
        $items_table = $history != 2 ? System::$table_pitems : System::$table_pitems . "_arch";
        if ($type == "S") {
            return DB::select("select * from $items_table where vbeln = '$order'");
        } else {
            $groupByPO = Session::get('groupOrdersBy');
            if (!isset($groupByPO)) $groupByPO = 1;
            if($groupByPO != 4)
                return DB::select("select * from $items_table where ebeln = '$order'");
            return DB::select("select * from $items_table where ebeln = '$order' and vbeln = '$vbeln'");
        }
    }

    public static function doChangeItem($column, $value, $valuehlp, $oldvalue, $ebeln, $ebelp, $backorder, $seconds = 0)
    {
        $history = Session::get("filter_history");
        if (!isset($history)) $history = 1;
        else $history = intval($history);
        if ($history == 2) {
            // update matnr for invoice closing
            return;
        }
        $pitem = DB::table(System::$table_pitems)->where([['ebeln', '=', $ebeln], ['ebelp', '=', $ebelp]])->first();
        if ($pitem->backorder != 0) $backorder = 1;
        $new_stage = $pitem->stage;
        if ($column != 'etadt') {
            $pitem->changed = 1;
            $new_stage = $pitem->stage;
            if ($pitem->stage == 'Z') {
                $pitem->status = ' ';
                $new_stage = 'F';
                $pitem->changed = 2;
            }
        }
        DB::beginTransaction();
        DB::update("update ". System::$table_pitems ." set $column = '$value', changed = '$pitem->changed', status = '$pitem->status', stage = '$new_stage', pstage = '$pitem->stage', backorder = $backorder where ebeln = '$ebeln' and ebelp = '$ebelp'");
        if ($pitem->changed != 0) DB::update("update ". System::$table_porders ." set changed = '1' where ebeln = '$ebeln'");
        if ($column == 'idnlf') $type = 'M';
        if ($column == 'qty') $type = 'Q';
        if ($column == 'lfdat') $type = 'D';
        if ($column == 'etadt') $type = 'J';
        if ($column == 'purch_price') $type = 'P';
        $newval = trim($value . " " . $valuehlp);
        $cdate = now();
        $cdate->addSeconds($seconds);
        DB::insert("insert into ". System::$table_pitemchg ." (ebeln,ebelp,ctype,stage,cdate,cuser,cuser_name,reason,oebelp,oldval,newval) values ('$ebeln','$ebelp','$type','$new_stage', '$cdate','" . Auth::user()->id . "','" . Auth::user()->username . "','','','$oldvalue','$newval')");
        if ($pitem->backorder == 0 && $backorder == 1 && $type == "D") {
            $cdate->addSeconds(1);
            DB::insert("insert into ". System::$table_pitemchg ." (ebeln,ebelp,ctype,stage,cdate,cuser,cuser_name,reason,oebelp,oldval,newval) values ('$ebeln','$ebelp','B','$new_stage', '$cdate','" . Auth::user()->id . "','" . Auth::user()->username . "','','','$pitem->backorder','$backorder')");
        }
        if ($type == "D") {
            DB::update("update ". System::$table_pitems ." set etadt = '$value' where ebeln = '$ebeln' and ebelp = '$ebelp'");
            $cdate->addSeconds(1);
            $oldetadt = substr($pitem->etadt, 0, 10);
            DB::insert("insert into ". System::$table_pitemchg ." (ebeln,ebelp,ctype,stage,cdate,cuser,cuser_name,reason,oebelp,oldval,newval) values ('$ebeln','$ebelp','J','$new_stage', '$cdate','" . Auth::user()->id . "','" . Auth::user()->username . "','','','$oldetadt','$newval')");
        }
        DB::commit();
        return "";
    }

    public static function createPurchReq($lifnr, $idnlf, $mtext, $matnr, $qty, $unit, $price, $curr, $deldate, $infnr)
    {
        return SAP::createPurchReq($lifnr, $idnlf, $mtext, $matnr, $qty, $unit, $price, $curr, $deldate, $infnr);
    }

    static public function getVendorUsers($lifnr)
    {
        $users = DB::select("select * from users where role = 'Furnizor' and lifnr ='$lifnr' and sap_system ='" . Auth::user()->sap_system . "'");
        $result = '[ ';
        foreach ($users AS $user) {
            if ($user->active == 1) $user_active = 'X'; else $user_active = '';
            $str = '"USERID":"' . $user->id . '", ' .
                '"ACTIVE":"' . $user_active . '", ' .
                '"USERNAME":"' . $user->username . '", ' .
                '"EMAIL":"' . $user->email . '", ' .
                '"LANG":"' . $user->lang . '"';
            if (strlen($result) > 2) $result = $result . ", ";
            $result = $result . "{ " . $str . " }";
        }
        $result = $result . " ]";
        return $result;
    }

    static public function getCTVUsers()
    {
        $users = DB::select("select * from users where role = 'CTV' and sap_system = '" . Auth::user()->sap_system ."'");
        $result = '[ ';
        foreach ($users AS $user) {
            if ($user->active == 1) $user_active = 'X'; else $user_active = '';
            $str = '"SRM_USER":"' . $user->id . '", ' .
                '"SRM_USER_NAME":"' . $user->username . '", ' .
                '"ACTIVE":"' . $user_active . '", ' .
                '"EMAIL":"' . $user->email . '", ' .
                '"LANG":"' . $user->lang . '"';
            if (strlen($result) > 2) $result = $result . ", ";
            $result = $result . "{ " . $str . " }";
        }
        $result = $result . " ]";
        return $result;
    }

    static public function sapActivateUser($id)
    {
        $users = DB::select("select * from users where id ='$id'");
        if (count($users) == 0) return "User does not exist";
        $user = $users[0];
        if ($user->active == 1) return "User is already active";
        DB::update("update users set active = 1 where id ='$id'");
        return "OK";
    }

    static public function sapDeactivateUser($id)
    {
        $users = DB::select("select * from users where id ='$id'");
        if (count($users) == 0) return "User does not exist";
        $user = $users[0];
        if ($user->active == 0) return "User is not active";
        DB::update("update users set active = 0 where id ='$id'");
        return "OK";
    }

    static public function sapCreateUser($id, $username, $role, $email, $language, $lifnr, $password)
    {
        $users = DB::select("select * from users where id ='$id'");
        if (count($users) != 0) return "User already exists";
        User::create([
            'id' => $id,
            'username' => $username,
            'role' => $role,
            'email' => $email,
            'lang' => $language,
            'lifnr' => $lifnr,
            'password' => Hash::make($password),
            'created_at' => Carbon::now()->getTimestamp(),
            'sap_system' => Auth::user()->sap_system,
        ]);
        return "OK";
    }

    static public function sapDeleteUser($id)
    {
        $users = DB::select("select * from users where id ='$id'");
        if (count($users) == 0) return "User does not exist";
        $user = $users[0];
        DB::delete("delete from users where id ='$id'");
        $users = DB::select("select * from users where id ='$id'");
        if (count($users) != 0) return "User deletion failed";
        return "OK";
    }

    static public function sapGetUserMakers($userid)
    {
        return "Not yet implemented";
    }

    static public function sapProcessPO($ebeln)
    {
        $data = SAP::rfcGetPOData($ebeln);
        return Data::processPOdata($ebeln, $data);
    }

    static public function sendInquiry($ebeln, $ebelp, $text, $to)
    {
        $order = SAP::alpha_output($ebeln) . "/" . SAP::alpha_output($ebelp);
        $duser = "";
        $stage = '';
        $internal = 0;
        if ($to[0] == 'F') {
            $stage = 'F';
            $porder = DB::Select("select * from ". System::$table_porders ." where ebeln = '$ebeln'")[0];
            $lifnr = $porder->lifnr;
            $duser = DB::table("users")->where([["role", "=", "Furnizor"],
                ["lifnr", "=", $lifnr],
                ["sap_system", "=", Auth::user()->sap_system]
            ])->value("id");
        }
        if ($to[0] == 'R') {
            $stage = 'R';
            $porder = DB::select("select * from ". System::$table_porders ." where ebeln = '$ebeln'")[0];
            $ekgrp = $porder->ekgrp;
            $duser = DB::table("users")->where([["role", "=", "Referent"],
                ["ekgrp", "=", $ekgrp],
                ["sap_system", "=", Auth::user()->sap_system]
            ])->value("id");
            if (Auth::user()->role == "CTV") $internal = 1;
        }
        if ($to[0] == 'C') {
            $pitem = DB::table(System::$table_pitems)->where(['ebeln' => $ebeln, 'ebelp' => $ebelp])->first();
            if ($pitem->vbeln != Orders::stockorder)
                $order = SAP::alpha_output($pitem->vbeln) . "/" . SAP::alpha_output($pitem->posnr);
            $stage = 'C';
            $kunnr = DB::table(System::$table_pitems)->where([["ebeln", "=", $ebeln], ["ebelp", "=", $ebelp]])->value("kunnr");
            $dusers = DB::select("select id, count(*) as count from ". System::$table_users_agent ." join ". System::$table_user_agent_clients ." using (id) where kunnr = '$kunnr' group by id order by count, id");
            if ($dusers == null || empty($dusers))
                $duser = DB::table(System::$table_user_agent_clients)->where("kunnr", $kunnr)->value("id");
            else $duser = $dusers[0]->id;
            if (Auth::user()->role == "Referent") $internal = 1;
        }

        $uId = Auth::id();
        $uName = Auth::user()->username;
        $cdate = now();
        DB::insert("insert into ". System::$table_pitemchg ." (ebeln, ebelp, cdate, internal, ctype, cuser, cuser_name, duser, stage, reason) VALUES " .
            "('$ebeln', '$ebelp', '$cdate', $internal, 'E', '$uId', '$uName', '$duser', '$stage', '$text')");
        Mailservice::sendMessageCopy($duser, $uName, $order, $text);
        \Session::put("alert-success", __("Mesajul a fost trimis cu succes."));
        return "";
    }

    static public function processProposal($proposal)
    {
        $cdate = now();
        $stage = $proposal->itemdata->stage;
        $ebeln = $proposal->itemdata->ebeln;
        $ebelp = $proposal->itemdata->ebelp;
        $newstage = 'C';
        if (Auth::user()->role == "Furnizor") $newstage ='R';
        if (isset($proposal->items)) {
            DB::beginTransaction();
            DB::update("update ". System::$table_pitems ." set stage = '$newstage', pstage = '$stage', status = 'T' " .
                "where ebeln = '$ebeln' and ebelp = '$ebelp'");
            DB::insert("insert into ". System::$table_pitemchg ." (ebeln, ebelp, cdate, internal, ctype, stage, cuser, cuser_name) values " .
                "('$ebeln', '$ebelp', '$cdate', 1, '$proposal->type', '$newstage', '" .
                Auth::user()->id . "', '" . Auth::user()->username . "')");
            $counter = 0;
            foreach ($proposal->items as $propitem) {
                $propitem->lifnr = SAP::alpha_input($propitem->lifnr);
                if (strlen($propitem->purch_curr) > 3) $propitem->purch_curr = substr($propitem->purch_curr, 0, 3);
                if (strlen($propitem->sales_curr) > 3) $propitem->sales_curr = substr($propitem->sales_curr, 0, 3);
                $propitem->mtext = str_replace("'", "\'", $propitem->mtext);
                DB::insert("insert into ". System::$table_pitemchg_proposals ." (type, ebeln, ebelp, cdate, pos, lifnr, idnlf, matnr, " .
                    "mtext, lfdat, qty, qty_uom, purch_price, purch_curr, sales_price, sales_curr, infnr) values ('$proposal->type'," .
                    "'$ebeln', '$ebelp', '$cdate', $counter, " .
                    "'$propitem->lifnr', '$propitem->idnlf', '$propitem->matnr', '$propitem->mtext', '$propitem->lfdat', " .
                    "'$propitem->quantity', '$propitem->quantity_unit', '$propitem->purch_price', '$propitem->purch_curr', " .
                    "'$propitem->sales_price', '$propitem->sales_curr', '')");
                $counter++;
            }
            DB::commit();
            $pitem = DB::table(System::$table_pitems)->where([["ebeln", "=", $ebeln], ["ebelp", "=", $ebelp]])->first();
            if ($newstage == 'C') {
                $ctvusers = DB::select("select distinct id from ". System::$table_user_agent_clients ." where kunnr = '$pitem->kunnr'");
                if (($ctvusers == null) || empty($ctvusers)) {
                    $ctvuser1 = DB::table(System::$table_roles)->where([["rfc_role", "=", "CTV"]])->value("user1");
                    if (($ctvuser1 != null) && !empty($ctvuser1)) {
                        try {
                            Mailservice::sendSalesOrderProposal($ctvuser1, $pitem->vbeln, $pitem->posnr);
                        } catch (Exception $e) {
                        }
                    }
                } else {
                    foreach ($ctvusers as $ctvuser) {
                        try {
                            Mailservice::sendSalesOrderProposal($ctvuser->id, $pitem->vbeln, $pitem->posnr);
                        } catch (Exception $e) {
                        }
                    }
                }
            }
        } else {
            $proposal->lifnr = SAP::alpha_input($proposal->lifnr);
            if (($proposal->lifnr == $proposal->itemdata->lifnr) &&
                (trim($proposal->idnlf) == trim($proposal->itemdata->orig_idnlf))) {
                // keeping the same supplier for stock orders, just update PO
                $tmp_idnlf = $proposal->idnlf;
                $tmp_mtext = str_replace("'", "\'", $proposal->mtext);
                $tmp_matnr = $proposal->matnr;
                $tmp_lfdat = $proposal->lfdat;
                $tmp_qty = $proposal->quantity;
                $tmp_qty_unit = $proposal->quantity_unit;
                $tmp_purch_price = $proposal->purch_price;
                $tmp_purch_curr = $proposal->purch_curr;
                if (strlen($tmp_purch_curr) > 3) $tmp_purch_curr = substr($tmp_purch_curr, 0, 3);
                DB::beginTransaction();
                DB::update("update ". System::$table_pitems ." set stage = 'Z', pstage = '$stage', status = 'A', " .
                    "idnlf = '$tmp_idnlf', mtext = '$tmp_mtext', matnr = '$tmp_matnr', lfdat = '$tmp_lfdat', " .
                    "qty = $tmp_qty, qty_uom = '$tmp_qty_unit', " .
                    "purch_price = '$tmp_purch_price', purch_curr = '$tmp_purch_curr' " .
                    "where ebeln = '$ebeln' and ebelp = '$ebelp'");
                DB::insert("insert into ". System::$table_pitemchg ." (ebeln, ebelp, cdate, internal, ctype, stage, cuser, cuser_name) values " .
                    "('$ebeln', '$ebelp', '$cdate', 1, 'A', 'Z', '" .
                    Auth::user()->id . "', '" . Auth::user()->username . "')");
                $result = SAP::savePOItem($ebeln, $ebelp);
                if (!empty(trim($result))) {
                    DB::rollBack();
                    return $result;
                }
                DB::commit();
            } else {
                // change supplier/material or change in SO
                $result = SAP::rejectPOItem($proposal->itemdata->ebeln, $proposal->itemdata->ebelp);
                if (($result != null) && !is_string($result)) $result = json_encode($result);
                if (($result != null) && strlen(trim($result)) != 0) return $result;
                $set_new_lifnr = "";
                if ($proposal->lifnr != $proposal->itemdata->lifnr) $set_new_lifnr = " new_lifnr ='" . $proposal->lifnr . "', ";
                if (strlen($proposal->purch_curr) > 3) $proposal->purch_curr = substr($proposal->purch_curr, 0, 3);
                if ($proposal->itemdata->vbeln == Orders::stockorder) {
                    $result = SAP::createPurchReq($proposal->lifnr, $proposal->idnlf, $proposal->mtext, $proposal->matnr,
                        $proposal->quantity, $proposal->quantity_unit,
                        $proposal->purch_price, $proposal->purch_curr, $proposal->lfdat);
                    if (!empty(trim($result))) {
                        if (substr($result, 0, 2) == "OK")
                            $banfn = __("New purchase requisition ") . SAP::alpha_output(substr($result, 2));
                        else return $result;
                    }
                    $tmp_stage = $proposal->itemdata->stage;
                    $tmp_idnlf = trim($proposal->itemdata->orig_idnlf);
                    $tmp_purch_price = $proposal->itemdata->orig_purch_price;
                    $tmp_qty = $proposal->itemdata->orig_qty;
                    $tmp_lfdat = $proposal->itemdata->orig_lfdat;
                    $tmp_matnr = $proposal->itemdata->orig_matnr;
                    $newstatus = 'X';
                    if ($proposal->lifnr == $proposal->itemdata->lifnr) $newstatus = 'A';
                    DB::beginTransaction();
                    DB::update("update ". System::$table_pitems ." set stage = 'Z', pstage = '$tmp_stage', status = '$newstatus', " . $set_new_lifnr .
                        "idnlf = '$tmp_idnlf', purch_price = '$tmp_purch_price', " .
                        "qty = '$tmp_qty', lfdat = '$tmp_lfdat', matnr = '$tmp_matnr' " .
                        "where ebeln = '" . $proposal->itemdata->ebeln . "' and ebelp = '" . $proposal->itemdata->ebelp . "'");
                    DB::insert("insert into ". System::$table_pitemchg ." (ebeln, ebelp, cdate, internal, ctype, stage, cuser, cuser_name, reason) values " .
                        "('" . $proposal->itemdata->ebeln . "', '" . $proposal->itemdata->ebelp . "', '$cdate', 1, '$newstatus', 'Z', '" .
                        Auth::user()->id . "', '" . Auth::user()->username . "', '$banfn')");
                    DB::commit();
                } else {
                    $tmp_stage = $proposal->itemdata->stage;
                    $tmp_idnlf = trim($proposal->itemdata->orig_idnlf);
                    $tmp_purch_price = $proposal->itemdata->orig_purch_price;
                    $tmp_qty = $proposal->itemdata->orig_qty;
                    $tmp_lfdat = $proposal->itemdata->orig_lfdat;
                    $tmp_matnr = $proposal->itemdata->orig_matnr;
                    if (strlen($proposal->purch_curr) > 3) $proposal->purch_curr = substr($proposal->purch_curr, 0, 3);
                    if (strlen($proposal->sales_curr) > 3) $proposal->sales_curr = substr($proposal->sales_curr, 0, 3);
                    $result = SAP::processSOItem($proposal->itemdata->vbeln, $proposal->itemdata->posnr,
                        $proposal->quantity, $proposal->quantity_unit, $proposal->lifnr, SAP::newMatnr($proposal->itemdata->matnr),
                        $proposal->mtext, $proposal->idnlf, $proposal->purch_price, $proposal->purch_curr,
                        $proposal->sales_price, $proposal->sales_curr, $proposal->lfdat);
                    if (!empty(trim($result))) {
                        if (substr($result, 0, 2) == "OK")
                            $soitem = __("New sales order item") . " " . substr($result, 2);
                        else return $result;
                    }
                    DB::beginTransaction();
                    DB::update("update ". System::$table_pitems ." set stage = 'Z', pstage = '$tmp_stage', status = 'X', " . $set_new_lifnr .
                        "idnlf = '$tmp_idnlf', purch_price = '$tmp_purch_price', " .
                        "qty = '$tmp_qty', lfdat = '$tmp_lfdat', matnr = '$tmp_matnr' " .
                        "where ebeln = '" . $proposal->itemdata->ebeln . "' and ebelp = '" . $proposal->itemdata->ebelp . "'");
                    DB::insert("insert into ". System::$table_pitemchg ." (ebeln, ebelp, cdate, internal, ctype, stage, oldval, cuser, cuser_name, reason) values " .
                        "('" . $proposal->itemdata->ebeln . "', '" . $proposal->itemdata->ebelp . "', '$cdate', 0, 'X', 'Z', 'C', '" .
                        Auth::user()->id . "', '" . Auth::user()->username . "', '$soitem')");
                    DB::commit();
                    if (Auth::user()->role != "CTV") {
                        $kunnr = $proposal->itemdata->kunnr;
                        $ctvusers = DB::select("select distinct id from ". System::$table_user_agent_clients ." where kunnr = '$kunnr'");
                        foreach ($ctvusers as $ctvuser) {
                            Mailservice::sendSalesOrderChange($ctvuser->id, $proposal->itemdata->vbeln, $proposal->itemdata->posnr, $result);
                        }
                    }
                    if (Auth::user()->role != "Referent") {
                        $ekgrp = DB::table(System::$table_porders)->where("ebeln", $proposal->itemdata->ebeln)->value("ekgrp");
                        $refuser = DB::table("users")->where(["ekgrp" => $ekgrp, "role" => "Referent", "active" => 1, "sap_system" => Auth::user()->sap_system])->first();
                        if ($refuser != null)
                            Mailservice::sendSalesOrderChange($refuser->id, $proposal->itemdata->vbeln, $proposal->itemdata->posnr, $result);
                    }
                }
                // if (!empty($set_new_lifnr)) Data::archiveItem($proposal->itemdata->ebeln, $proposal->itemdata->ebelp);
            }
        }
        return "";
    }

    static public function acceptProposal($ebeln, $ebelp, $cdate, $pos)
    {
        $proposal = DB::table(System::$table_pitemchg_proposals)->where([
            ["type", "=", "O"],
            ["ebeln", "=", $ebeln],
            ["ebelp", "=", $ebelp],
            ["cdate", "=", $cdate],
            ["pos", "=", $pos]
        ])->first();
        $item = DB::table(System::$table_pitems)->where([["ebeln", "=", $ebeln], ["ebelp", "=", $ebelp]])->first();
        $porder = DB::table(System::$table_porders)->where("ebeln", $ebeln)->first();
        if (($proposal->lifnr == $porder->lifnr) && (trim($proposal->idnlf) == trim($item->orig_idnlf))) {
            // keeping the same supplier for stock orders, just update PO
            $tmp_idnlf = $proposal->idnlf;
            $tmp_mtext = str_replace("'", "\'", $proposal->mtext);
            $tmp_matnr = $proposal->matnr;
            $tmp_lfdat = $proposal->lfdat;
            $tmp_qty = $proposal->qty;
            $tmp_qty_unit = $proposal->qty_uom;
            $tmp_purch_price = $proposal->purch_price;
            $tmp_purch_curr = $proposal->purch_curr;
            if (strlen($tmp_purch_curr) > 3) $tmp_purch_curr = substr($tmp_purch_curr, 0, 3);
            $tmp_sales_price = $proposal->sales_price;
            $tmp_sales_curr = $proposal->sales_curr;
            if (strlen($tmp_sales_curr) > 3) $tmp_sales_curr = substr($tmp_sales_curr, 0, 3);
            $now = now();
            DB::beginTransaction();
            DB::update("update ". System::$table_pitems ." set stage = 'Z', pstage = '" . $item->stage . "', status = 'A', " .
                "idnlf = '$tmp_idnlf', mtext = '$tmp_mtext', matnr = '$tmp_matnr', lfdat = '$tmp_lfdat', " .
                "qty = $tmp_qty, qty_uom = '$tmp_qty_unit', " .
                "purch_price = '$tmp_purch_price', purch_curr = '$tmp_purch_curr', " .
                "sales_price = '$tmp_sales_price', sales_curr = '$tmp_sales_curr' " .
                "where ebeln = '$ebeln' and ebelp = '$ebelp'");
            DB::insert("insert into ". System::$table_pitemchg ." (ebeln, ebelp, cdate, internal, ctype, stage, cuser, cuser_name) values " .
                "('$ebeln', '$ebelp', '$now', 0, 'A', 'Z', '" .
                Auth::user()->id . "', '" . Auth::user()->username . "')");
            $result = SAP::savePOItem($ebeln, $ebelp);
            if (!empty(trim($result))) {
                DB::rollBack();
                return $result;
            }
            $result = SAP::changeSOItem($item->vbeln, $item->posnr,
                $proposal->qty, $proposal->qty_uom, $proposal->lifnr, "", "", "",
                $proposal->sales_price, $proposal->sales_curr, $proposal->purch_price, $proposal->purch_curr, $proposal->lfdat);
            if (!empty(trim($result))) {
                DB::rollBack();
                return $result;
            }
            DB::commit();
            return "";
        }

        $result = SAP::rejectPOItem($ebeln, $ebelp);
        if (($result != null) && !is_string($result)) $result = json_encode($result);
        if (($result != null) && strlen(trim($result)) != 0) return $result;
        $result = SAP::processSOItem($item->vbeln, $item->posnr,
            $proposal->qty, $proposal->qty_uom, $proposal->lifnr, SAP::newMatnr($item->matnr),
            $proposal->mtext, $proposal->idnlf, $proposal->purch_price, $proposal->purch_curr,
            $proposal->sales_price, $proposal->sales_curr, $proposal->lfdat);
        $soitem = "";
        if (!empty(trim($result))) {
            if (substr($result, 0, 2) == "OK")
                $soitem = __("New sales order item") . " " . substr(trim($result), 2) . " from item " . ltrim($item->posnr, "0");
            else return $result;
        }
        $set_new_lifnr = "";
        if ($proposal->lifnr != $porder->lifnr) $set_new_lifnr = ", new_lifnr ='$proposal->lifnr'";
        DB::beginTransaction();
        DB::update("update ". System::$table_pitems ." set stage = 'Z', pstage = '$item->stage', status = 'X'" . $set_new_lifnr .
            " where ebeln = '$ebeln' and ebelp = '$ebelp'");
        $now = now();
        DB::insert("insert into ". System::$table_pitemchg ." (ebeln, ebelp, cdate, internal, ctype, stage, oldval, cuser, cuser_name, reason) values " .
            "('$ebeln', '$ebelp', '$now', 0, 'A', 'Z', 'C', '" .
            Auth::user()->id . "', '" . Auth::user()->username . "', '$soitem')");
        DB::commit();
        try {
            if (Auth::user()->role != "CTV") {
                $ctvusers = DB::select("select distinct id from " . System::$table_user_agent_clients . " where kunnr = '$item->kunnr'");
                foreach ($ctvusers as $ctvuser) {
                    Mailservice::sendSalesOrderChange($ctvuser->id, $item->vbeln, $item->posnr, $result);
                }
            }
        } catch (Exception $exception){
            Log::error($exception);
        }
        if (Auth::user()->role == "CTV") {
            $result = Data::archiveItem($ebeln, $ebelp);
            Log::info("Archiving $ebeln/$ebelp (" . $item->vbeln . "/" . $item->posnr . "): " . $result);
        }
        return "";
    }

    static public function rejectProposal($ebeln, $ebelp, $cdate)
    {
        $item = DB::table(System::$table_pitems)->where([["ebeln", "=", $ebeln], ["ebelp", "=", $ebelp]])->first();
        $result = SAP::rejectPOItem($ebeln, $ebelp);
        if (($result != null) && !is_string($result)) $result = json_encode($result);
        if (($result != null) && strlen(trim($result)) != 0) return $result;
        $result = SAP::rejectSOItem($item->vbeln, $item->posnr, "07");
        if (($result != null) && !is_string($result)) $result = json_encode($result);
        if (($result != null) && strlen(trim($result)) != 0) return $result;
        $soitem = __("Rejected sales order item") . " " . SAP::alpha_output($item->vbeln) . '/' . ltrim($item->posnr, "0");
        DB::beginTransaction();
        DB::update("update ". System::$table_pitems ." set stage = 'Z', pstage = '$item->stage', status = 'X' " .
            "where ebeln = '$ebeln' and ebelp = '$ebelp'");
        $now = now();
        DB::insert("insert into ". System::$table_pitemchg ." (ebeln, ebelp, cdate, internal, ctype, stage, oldval, cuser, cuser_name, reason) values " .
            "('$ebeln', '$ebelp', '$now', 0, 'X', 'Z', 'D', '" .
            Auth::user()->id . "', '" . Auth::user()->username . "', '$soitem')");
        DB::commit();
        try {
            $ctvusers = DB::select("select distinct id from " . System::$table_user_agent_clients . " where kunnr = '$item->kunnr'");
            if (($ctvusers == null) || empty($ctvusers)) {
                $ctvuser1 = DB::table(System::$table_roles)->where([["rfc_role", "=", "CTV"]])->value("user1");
                if (($ctvuser1 != null) && !empty($ctvuser1)) {
                    try {
                        Mailservice::sendSalesOrderNotification($ctvuser1, $item->vbeln, $item->posnr);
                    } catch (Exception $e) {
                    }
                }
            } else {
                foreach ($ctvusers as $ctvuser) {
                    Mailservice::sendSalesOrderNotification($ctvuser->id, $item->vbeln, $item->posnr);
                }
            }
        } catch (Exception $exception) {
            Log::error($exception);
        }
        if (Auth::user()->role == "CTV") {
            $result = Data::archiveItem($ebeln, $ebelp);
            Log::info("Archiving $ebeln/$ebelp (" . $item->vbeln . "/" . $item->posnr . "): " . $result);
        }
        return "";
    }

    static public function acceptSplit($ebeln, $ebelp, $cdate)
    {
        $item = DB::table(System::$table_pitems)->where([["ebeln", "=", $ebeln], ["ebelp", "=", $ebelp]])->first();
        $splititems = DB::table(System::$table_pitemchg_proposals)->where([
            ["type", "=", "S"],
            ["ebeln", "=", $ebeln],
            ["ebelp", "=", $ebelp],
            ["cdate", "=", $cdate]])->get();
        if ($splititems == null || empty($splititems)) return;

        $result = SAP::rejectPOItem($ebeln, $ebelp);
        if (($result != null) && !is_string($result)) $result = json_encode($result);
        if (($result != null) && strlen(trim($result)) != 0) return $result;
        $text = "";
        if ($item->vbeln == Orders::stockorder) {
            foreach($splititems as $splititem) {
                $result = SAP::createPurchReq($splititem->lifnr, $splititem->idnlf, $splititem->mtext, SAP::newMatnr($item->matnr),
                    $splititem->qty, $splititem->qty_uom,
                    $splititem->purch_price, $splititem->purch_curr, $splititem->lfdat);
                if (!empty(trim($result))) {
                    if (substr($result, 0, 2) == "OK")
                        $text .= SAP::alpha_output(substr($result, 2));
                    else return $result;
                }
            }
            $text = __("New purchase requisition ") . $text;
        } else {
            foreach($splititems as $splititem) {
                $result = SAP::processSOItem($item->vbeln, $item->posnr,
                    $splititem->qty, $splititem->qty_uom, $splititem->lifnr, SAP::newMatnr($item->matnr),
                    $splititem->mtext, $splititem->idnlf, $splititem->purch_price, $splititem->purch_curr,
                    $splititem->sales_price, $splititem->sales_curr, $splititem->lfdat);
                if (!empty(trim($result))) {
                    if (substr($result, 0, 2) == "OK")
                        $text .= ',' . SAP::alpha_output(substr($result, 2));
                    else return $result;
                }
            }
            $text2 = substr($text, 1);
            $text = __("New sales order items: ") . $text2;
            if (Auth::user()->role != "CTV") {
                $ctvusers = DB::select("select distinct id from ". System::$table_user_agent_clients ." where kunnr = '$item->kunnr'");
                foreach ($ctvusers as $ctvuser) {
                    Mailservice::sendSalesOrderChange($ctvuser->id, $item->vbeln, $item->posnr, $text2);
                }
            }
        }

        DB::beginTransaction();
        DB::update("update ". System::$table_pitems ." set stage = 'Z', pstage = '$item->stage', status = 'X' " .
            "where ebeln = '$ebeln' and ebelp = '$ebelp'");
        DB::delete("delete from ". System::$table_pitemchg_proposals ." where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$cdate'");
        $cdate = now();
        DB::insert("insert into ". System::$table_pitemchg ." (ebeln, ebelp, cdate, internal, ctype, stage, oldval, cuser, cuser_name, reason) values " .
            "('$ebeln', '$ebelp', '$cdate', 0, 'A', 'Z', 'U', '" .
            Auth::user()->id . "', '" . Auth::user()->username . "', '$text')");
        DB::commit();
        return "";
    }

    static public function rejectSplit($ebeln, $ebelp, $cdate)
    {
        $item = DB::table(System::$table_pitems)->where([["ebeln", "=", $ebeln], ["ebelp", "=", $ebelp]])->first();
        $result = SAP::rejectPOItem($ebeln, $ebelp);
        if (($result != null) && !is_string($result)) $result = json_encode($result);
        if (($result != null) && strlen(trim($result)) != 0) return $result;
        $result = SAP::rejectSOItem($item->vbeln, $item->posnr, "07");
        if (($result != null) && !is_string($result)) $result = json_encode($result);
        if (($result != null) && strlen(trim($result)) != 0) return $result;
        $soitem = __("Rejected split for sales order item ") . SAP::alpha_output($item->vbeln) . '/' . ltrim($item->posnr, "0");
        DB::beginTransaction();
        DB::update("update ". System::$table_pitems ." set stage = 'Z', pstage = '$item->stage', status = 'X' " .
            "where ebeln = '$ebeln' and ebelp = '$ebelp'");
        DB::delete("delete from ". System::$table_pitemchg_proposals ." where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$cdate'");
        $now = now();
        DB::insert("insert into ". System::$table_pitemchg ." (ebeln, ebelp, cdate, internal, ctype, stage, oldval, cuser, cuser_name, reason) values " .
            "('$ebeln', '$ebelp', '$now', 0, 'X', 'Z', 'W', '" .
            Auth::user()->id . "', '" . Auth::user()->username . "', '$soitem')");
        DB::commit();
        if (Auth::user()->role != 'CTV') {
            $ctvusers = DB::select("select distinct id from ". System::$table_user_agent_clients ." where kunnr = '$item->kunnr'");
            foreach ($ctvusers as $ctvuser) {
                Mailservice::sendSalesOrderNotification($ctvuser->id, $item->vbeln, $item->posnr);
            }
        }
        return "";
    }

    static public function processSplit($proposal)
    {
        $result = new \stdClass();
        $cdate = now();
        $stage = $proposal->itemdata->stage;
        $ebeln = $proposal->itemdata->ebeln;
        $ebelp = $proposal->itemdata->ebelp;
        DB::beginTransaction();
        DB::update("update ". System::$table_pitems ." set stage = 'R', pstage = '$stage', changed = '1' " .
            "where ebeln = '$ebeln' and ebelp = '$ebelp'");
        DB::insert("insert into ". System::$table_pitemchg ." (ebeln, ebelp, cdate, internal, ctype, stage, cuser, cuser_name) values " .
            "('$ebeln', '$ebelp', '$cdate', 1, '$proposal->type', 'R', '" .
            Auth::user()->id . "', '" . Auth::user()->username . "')");
        $counter = 0;
        foreach ($proposal->items as $propitem) {
            $propitem->lifnr = SAP::alpha_input($propitem->lifnr);
            if (strlen($propitem->purch_curr) > 3) $propitem->purch_curr = substr($propitem->purch_curr, 0, 3);
            if (strlen($propitem->sales_curr) > 3) $propitem->sales_curr = substr($propitem->sales_curr, 0, 3);
            $propitem->mtext = str_replace("'", "\'", $propitem->mtext);
            DB::insert("insert into ". System::$table_pitemchg_proposals ." (type, ebeln, ebelp, cdate, pos, lifnr, idnlf, matnr, " .
                "mtext, lfdat, qty, qty_uom, purch_price, purch_curr, sales_price, sales_curr, infnr) values ('$proposal->type'," .
                "'$ebeln', '$ebelp', '$cdate', $counter, " .
                "'$propitem->lifnr', '$propitem->idnlf', '$propitem->matnr', '$propitem->mtext', '$propitem->lfdat', " .
                "'$propitem->quantity', '$propitem->quantity_unit', '$propitem->purch_price', '$propitem->purch_curr', " .
                "'$propitem->sales_price', '$propitem->sales_curr', '')");
            $counter++;
        }
        DB::commit();
        return self::acceptSplit($ebeln, $ebelp, $cdate);
    }

    static public function archiveItem($ebeln, $ebelp)
    {
        $result = Data::archiveItem($ebeln, $ebelp);
        Log::info("Order item $ebeln/$ebelp was manually archived by ". Auth::user()->id);
        return $result;
    }

    static public function unarchiveItem($ebeln, $ebelp)
    {
        $result = Data::unarchiveItem($ebeln, $ebelp);
        Log::info("Order item $ebeln/$ebelp was manually unarchived by ". Auth::user()->id);
        return $result;
    }

    static public function rollbackItem($ebeln, $ebelp)
    {
        $pitem = DB::table(System::$table_pitems)->where(["ebeln" => $ebeln, "ebelp" => $ebelp])->first();
        if ($pitem != null) {
            $pitemchgs = DB::select("select * from ". System::$table_pitemchg .
                    " where ebeln = '$ebeln' and ebelp = '$ebelp' and ctype <> 'E' order by cdate desc");
            $pitemchg = null;
            $prevchg = null;
            if ($pitemchgs != null && count($pitemchgs) > 0) {
                $pitemchg = $pitemchgs[0];
                $prevchgs = DB::select("select * from ". System::$table_pitemchg .
                    " where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate < '$pitemchg->cdate' and ctype <> 'E' order by cdate desc");
                if ($prevchgs != null && count($prevchgs) > 0) $prevchg = $prevchgs[0];
                if ($prevchg == null) $prevchg = clone $pitemchg;
            }
            if ($pitemchg != null && ($pitemchg->acknowledged < 2)) {
                $cdate = $pitemchg->cdate;
                $status = $pitem->status;
                $ctype = $pitemchg->ctype;
                $now = now();

                if ((($pitem->stage == "F") || ($pitem->stage == "R"))
                    && ((trim($pitem->pstage) == "") || ($pitem->stage == $pitem->pstage))
                    && (trim($pitem->status) == "") &&
                    ($pitemchg->ctype == "M" || $pitemchg->ctype == "Q" || $pitemchg->ctype == "P" || $pitemchg->ctype == "D" || $pitemchg->ctype == "J")) {
                    DB::beginTransaction();
                    DB::update("update ". System::$table_pitemchg ." set acknowledged = 2 where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$cdate'");
                    $message = __("Rolled back");
                    if ($pitemchg->ctype == "M") {
                        $message .= " " . __("from material code change");
                        $idnlf = $pitemchg->oldval;
                        DB::update("update ". System::$table_pitems ." set idnlf = '$idnlf' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                    }
                    if ($pitemchg->ctype == "Q") {
                        $message .= " " . __("from quantity change");
                        $purch_price = explode(" ", $pitemchg->oldval)[0];
                        DB::update("update ". System::$table_pitems ." set purch_price = '$purch_price' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                    }
                    if ($pitemchg->ctype == "P") {
                        $message .= " " . __("from price change");
                        $qty = explode(" ", $pitemchg->oldval)[0];
                        DB::update("update ". System::$table_pitems ." set qty = '$qty' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                    }
                    if ($pitemchg->ctype == "D") {
                        $message .= " " . __("from delivery date change");
                        $lfdat = $pitemchg->oldval;
                        DB::update("update ". System::$table_pitems ." set lfdat = '$lfdat' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                    }
                    if ($pitemchg->ctype == "J") {
                        $message .= " " . __("from ETA date change");
                        $etadt = $pitemchg->oldval;
                        DB::update("update ". System::$table_pitems ." set etadt = '$etadt' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                    }
                    $message .= " (previous = " . $pitemchg->oldval . ", current = " . $pitemchg->newval . ")";
                    DB::insert("insert into ". System::$table_pitemchg ." (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name, reason) values " .
                        "('$ebeln','$ebelp', 'E', 'F', '$now', '" . Auth::user()->id . "','" . Auth::user()->username . "', '$message')");
                    DB::commit();
                    Log::info("Order item $ebeln/$ebelp was manually rolled back (type 0$ctype) by ". Auth::user()->id);
                    return "OK";
                }

                if ((($pitem->stage == "R") && ($pitemchg->stage == "R")) &&
                    ($pitem->pstage == "F" || $pitem->pstage == " ") &&
                    ((($pitem->status == "T") && ($pitemchg->ctype == "T")) ||
                     (($pitem->status == "A") && ($pitemchg->ctype == "A")))
                   ) {
                    $message = __("Rolled back");
                    if ($pitem->status == "T") {
                        $message .= " " . __("from supplier acceptance proposal on") . " " . $cdate;
                    } elseif ($pitem->status == "A") {
                        $message .= " " . __("from supplier acceptance on") . " " . $cdate;
                    }
                    DB::beginTransaction();
                    DB::update("update ". System::$table_pitemchg ." set acknowledged = 2 where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$cdate'");
                    DB::update("update ". System::$table_pitems ." set stage = 'F', status = '', pstage = 'R' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                    DB::insert("insert into ". System::$table_pitemchg ." (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name, reason) values " .
                        "('$ebeln','$ebelp', 'E', 'F', '$now', '" . Auth::user()->id . "','" . Auth::user()->username . "', '$message')");
                    DB::commit();
                    Log::info("Order item $ebeln/$ebelp was manually rolled back (type 1) by ". Auth::user()->id);
                    return "OK";
                }

                if ((($pitem->stage == "C") && ($pitemchg->stage == "C")) &&
                     $pitem->pstage == "R" &&
                     (($pitem->status == "T") || ($pitem->status == "A")) &&
                     ($pitemchg->ctype == "O"))
                {
                    $message = __("Rolled back from proposal on") . " " . $cdate;
                    DB::beginTransaction();
                    DB::update("update ". System::$table_pitemchg ." set acknowledged = 2 where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$cdate'");
                    DB::update("update ". System::$table_pitems ." set stage = 'R', status = '$status', pstage = 'C' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                    DB::insert("insert into ". System::$table_pitemchg ." (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name, reason) values " .
                        "('$ebeln','$ebelp', 'E', 'R', '$now', '" . Auth::user()->id . "','" . Auth::user()->username . "', '$message')");
                    DB::commit();
                    Log::info("Order item $ebeln/$ebelp was manually rolled back (type 2) by ". Auth::user()->id);
                    return "OK";
                }

                if ((($pitem->stage == "Z") && (($pitemchg->stage == "Z") || ($pitemchg->stage == "R"))) &&
                    ($pitem->pstage == "F" || $pitem->pstage == " ") &&
                    ((($pitem->status == "X") && ($pitemchg->ctype == "X")) ||
                     (($pitem->status == "A") && ($pitemchg->ctype == "A"))
                    )
                   )
                {
                    $message = __("Rolled back");
                    if ($pitem->status == "T") {
                        $message .= " " . __("from supplier acceptance proposal on") . " " . $cdate;
                    } elseif ($pitem->status == "X") {
                        $message .= " " . __("from supplier rejection on") . " " . $cdate;
                    } elseif ($pitem->status == "A") {
                        $message .= " " . __("from supplier acceptance on") . " " . $cdate;
                    }
                    DB::beginTransaction();
                    DB::update("update ". System::$table_pitemchg ." set acknowledged = 2 where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$cdate'");
                    DB::update("update ". System::$table_pitems ." set stage = 'F', status = '', pstage = 'R' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                    DB::insert("insert into ". System::$table_pitemchg ." (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name, reason) values " .
                        "('$ebeln','$ebelp', 'E', 'F', '$now', '" . Auth::user()->id . "','" . Auth::user()->username . "', '$message')");
                    DB::commit();
                    Log::info("Order item $ebeln/$ebelp was manually rolled back (type 3) by ". Auth::user()->id);
                    return "OK";
                }

                if ((($pitem->stage == "Z") && (($pitemchg->stage == "Z") && ($pitemchg->acknowledged == 0))) &&
                    ($pitem->pstage == "R") &&
                    ((($pitem->status == "X") && ($pitemchg->ctype == "X")) ||
                     (($pitem->status == "A") && ($pitemchg->ctype == "A"))
                    )
                   )
                {
                    $message = __("Rolled back");
                    if ($pitem->status == "X") {
                        $message .= " " . __("from reference rejection on") . " " . $cdate;
                        $prevstatus = 'R';
                    } elseif ($pitem->status == "A") {
                        $message .= " " . __("from reference acceptance on") . " " . $cdate;
                        $prevstatus = 'T';
                    }
                    DB::beginTransaction();
                    DB::update("update ". System::$table_pitemchg ." set acknowledged = 2 where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$cdate'");
                    DB::update("update ". System::$table_pitems ." set stage = 'R', status = '$prevstatus', pstage = 'R' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                    DB::insert("insert into ". System::$table_pitemchg ." (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name, reason) values " .
                        "('$ebeln','$ebelp', 'E', 'R', '$now', '" . Auth::user()->id . "','" . Auth::user()->username . "', '$message')");
                    DB::commit();
                    Log::info("Order item $ebeln/$ebelp was manually rolled back (type 4) by ". Auth::user()->id);
                    return "OK";
                }

            }
        }
        return __("Item could not be rolled back automatically");
    }

    static public function processProposal2($proposal)
    {
        $cdate = now();
        $stage = $proposal->itemdata->stage;
        $ebeln = $proposal->itemdata->ebeln;
        $ebelp = $proposal->itemdata->ebelp;
        $newstage = 'C';
        if (Auth::user()->role == "Furnizor") $newstage ='R';
        if (isset($proposal->items)) {
            DB::beginTransaction();
            DB::update("update ". System::$table_pitems ." set stage = '$newstage', pstage = '$stage', status = 'T' " .
                "where ebeln = '$ebeln' and ebelp = '$ebelp'");
            DB::insert("insert into ". System::$table_pitemchg ." (ebeln, ebelp, cdate, internal, ctype, stage, cuser, cuser_name) values " .
                "('$ebeln', '$ebelp', '$cdate', 1, '$proposal->type', '$newstage', '" .
                Auth::user()->id . "', '" . Auth::user()->username . "')");
            $counter = 0;
            foreach ($proposal->items as $propitem) {
                $propitem->lifnr = SAP::alpha_input($propitem->lifnr);
                if (strlen($propitem->purch_curr) > 3) $propitem->purch_curr = substr($propitem->purch_curr, 0, 3);
                if (strlen($propitem->sales_curr) > 3) $propitem->sales_curr = substr($propitem->sales_curr, 0, 3);
                $propitem->mtext = str_replace("'", "\'", $propitem->mtext);
                DB::insert("insert into ". System::$table_pitemchg_proposals ." (type, ebeln, ebelp, cdate, pos, lifnr, idnlf, matnr, " .
                    "mtext, lfdat, qty, qty_uom, purch_price, purch_curr, sales_price, sales_curr, infnr) values ('$proposal->type'," .
                    "'$ebeln', '$ebelp', '$cdate', $counter, " .
                    "'$propitem->lifnr', '$propitem->idnlf', '$propitem->matnr', '$propitem->mtext', '$propitem->lfdat', " .
                    "'$propitem->quantity', '$propitem->quantity_unit', '$propitem->purch_price', '$propitem->purch_curr', " .
                    "'$propitem->sales_price', '$propitem->sales_curr', '')");
                $counter++;
                if ($propitem->sales_save == 1) {
                    SAP::writeZPRET($propitem->lifnr, $propitem->idnlf, $propitem->quantity_unit,
                        $propitem->purch_price, $propitem->purch_curr,
                        $propitem->sales_price, $propitem->sales_curr);
                }
            }
            DB::commit();
            $pitem = DB::table(System::$table_pitems)->where([["ebeln", "=", $ebeln], ["ebelp", "=", $ebelp]])->first();
            if ($newstage == 'C') {
                $ctvusers = DB::select("select distinct id from ". System::$table_user_agent_clients ." where kunnr = '$pitem->kunnr'");
                if (($ctvusers == null) || empty($ctvusers)) {
                    $ctvuser1 = DB::table(System::$table_roles)->where([["rfc_role", "=", "CTV"]])->value("user1");
                    if (($ctvuser1 != null) && !empty($ctvuser1)) {
                        try {
                            Mailservice::sendSalesOrderProposal($ctvuser1, $pitem->vbeln, $pitem->posnr);
                        } catch (Exception $e) {
                        }
                    }
                } else {
                    foreach ($ctvusers as $ctvuser) {
                        try {
                            Mailservice::sendSalesOrderProposal($ctvuser->id, $pitem->vbeln, $pitem->posnr);
                        } catch (Exception $e) {
                        }
                    }
                }
            }
        } else {
            $proposal->lifnr = SAP::alpha_input($proposal->lifnr);
            if (($proposal->lifnr == $proposal->itemdata->lifnr) &&
                (trim($proposal->idnlf) == trim($proposal->itemdata->orig_idnlf))) {
                // keeping the same supplier and material code, just update PO & SO
                $tmp_idnlf = $proposal->idnlf;
                $tmp_mtext = str_replace("'", "\'", $proposal->mtext);
                $tmp_matnr = $proposal->matnr;
                $tmp_lfdat = $proposal->lfdat;
                $tmp_qty = $proposal->quantity;
                $tmp_qty_unit = $proposal->quantity_unit;
                $tmp_purch_price = $proposal->purch_price;
                $tmp_purch_curr = $proposal->purch_curr;
                $tmp_sales_price = $proposal->sales_price;
                $tmp_sales_curr = $proposal->sales_curr;
                if (strlen($tmp_purch_curr) > 3) $tmp_purch_curr = substr($tmp_purch_curr, 0, 3);
                DB::beginTransaction();
                DB::update("update ". System::$table_pitems ." set stage = 'Z', pstage = '$stage', status = 'A', " .
                    "idnlf = '$tmp_idnlf', mtext = '$tmp_mtext', matnr = '$tmp_matnr', lfdat = '$tmp_lfdat', " .
                    "qty = $tmp_qty, qty_uom = '$tmp_qty_unit', " .
                    "purch_price = '$tmp_purch_price', purch_curr = '$tmp_purch_curr', " .
                    "sales_price = '$tmp_sales_price', sales_curr = '$tmp_sales_curr' " .
                    "where ebeln = '$ebeln' and ebelp = '$ebelp'");
                DB::insert("insert into ". System::$table_pitemchg ." (ebeln, ebelp, cdate, internal, ctype, stage, cuser, cuser_name) values " .
                    "('$ebeln', '$ebelp', '$cdate', 1, 'A', 'Z', '" .
                    Auth::user()->id . "', '" . Auth::user()->username . "')");
                $result = SAP::savePOItem($ebeln, $ebelp);
                if (!empty(trim($result))) {
                    DB::rollBack();
                    return $result;
                }
                if (trim($proposal->sales_price) != trim($proposal->itemdata->sales_price)) {
                    $result = SAP::changeSOItem($proposal->itemdata->vbeln, $proposal->itemdata->posnr,
                        "", "", "", "", "", "",
                        $proposal->sales_price, $proposal->sales_curr, $proposal->purch_price, $proposal->purch_curr, "");
                    if (!empty(trim($result))) {
                        DB::rollBack();
                        return $result;
                    }
                    if ($proposal->sales_save == 1) {
                        SAP::writeZPRET($proposal->lifnr, $proposal->idnlf, $proposal->quantity_unit,
                            $proposal->purch_price, $proposal->purch_curr,
                            $proposal->sales_price, $proposal->sales_curr);
                    }
                }
                DB::commit();
            } else {
                // change supplier/material or change in SO
                $result = SAP::rejectPOItem($proposal->itemdata->ebeln, $proposal->itemdata->ebelp);
                if (($result != null) && !is_string($result)) $result = json_encode($result);
                if (($result != null) && strlen(trim($result)) != 0) return $result;
                $set_new_lifnr = "";
                if ($proposal->lifnr != $proposal->itemdata->lifnr) $set_new_lifnr = " new_lifnr ='" . $proposal->lifnr . "', ";
                if (strlen($proposal->purch_curr) > 3) $proposal->purch_curr = substr($proposal->purch_curr, 0, 3);
                if ($proposal->itemdata->vbeln == Orders::stockorder) {
                    $result = SAP::createPurchReq($proposal->lifnr, $proposal->idnlf, $proposal->mtext, $proposal->matnr,
                        $proposal->quantity, $proposal->quantity_unit,
                        $proposal->purch_price, $proposal->purch_curr, $proposal->lfdat);
                    if (!empty(trim($result))) {
                        if (substr($result, 0, 2) == "OK")
                            $banfn = __("New purchase requisition ") . SAP::alpha_output(substr($result, 2));
                        else return $result;
                    }
                    $tmp_stage = $proposal->itemdata->stage;
                    $tmp_idnlf = trim($proposal->itemdata->orig_idnlf);
                    $tmp_purch_price = $proposal->itemdata->orig_purch_price;
                    $tmp_qty = $proposal->itemdata->orig_qty;
                    $tmp_lfdat = $proposal->itemdata->orig_lfdat;
                    $tmp_matnr = $proposal->itemdata->orig_matnr;
                    $newstatus = 'X';
                    if ($proposal->lifnr == $proposal->itemdata->lifnr) $newstatus = 'A';
                    DB::beginTransaction();
                    DB::update("update ". System::$table_pitems ." set stage = 'Z', pstage = '$tmp_stage', status = '$newstatus', " . $set_new_lifnr .
                        "idnlf = '$tmp_idnlf', purch_price = '$tmp_purch_price', " .
                        "qty = '$tmp_qty', lfdat = '$tmp_lfdat', matnr = '$tmp_matnr' " .
                        "where ebeln = '" . $proposal->itemdata->ebeln . "' and ebelp = '" . $proposal->itemdata->ebelp . "'");
                    DB::insert("insert into ". System::$table_pitemchg ." (ebeln, ebelp, cdate, internal, ctype, stage, cuser, cuser_name, reason) values " .
                        "('" . $proposal->itemdata->ebeln . "', '" . $proposal->itemdata->ebelp . "', '$cdate', 1, '$newstatus', 'Z', '" .
                        Auth::user()->id . "', '" . Auth::user()->username . "', '$banfn')");
                    DB::commit();
                    if ($proposal->sales_save == 1) {
                        SAP::writeZPRET($proposal->lifnr, $proposal->idnlf, $proposal->quantity_unit,
                            $proposal->purch_price, $proposal->purch_curr,
                            $proposal->sales_price, $proposal->sales_curr);
                    }
                } else {
                    $tmp_stage = $proposal->itemdata->stage;
                    $tmp_idnlf = trim($proposal->itemdata->orig_idnlf);
                    $tmp_purch_price = $proposal->itemdata->orig_purch_price;
                    $tmp_qty = $proposal->itemdata->orig_qty;
                    $tmp_lfdat = $proposal->itemdata->orig_lfdat;
                    $tmp_matnr = $proposal->itemdata->orig_matnr;
                    if (strlen($proposal->purch_curr) > 3) $proposal->purch_curr = substr($proposal->purch_curr, 0, 3);
                    if (strlen($proposal->sales_curr) > 3) $proposal->sales_curr = substr($proposal->sales_curr, 0, 3);
                    $result = SAP::processSOItem($proposal->itemdata->vbeln, $proposal->itemdata->posnr,
                        $proposal->quantity, $proposal->quantity_unit, $proposal->lifnr, SAP::newMatnr($proposal->itemdata->matnr),
                        $proposal->mtext, $proposal->idnlf, $proposal->purch_price, $proposal->purch_curr,
                        $proposal->sales_price, $proposal->sales_curr, $proposal->lfdat);
                    if (!empty(trim($result))) {
                        if (substr($result, 0, 2) == "OK")
                            $soitem = __("New sales order item") . " " . substr($result, 2);
                        else return $result;
                    }
                    DB::beginTransaction();
                    DB::update("update ". System::$table_pitems ." set stage = 'Z', pstage = '$tmp_stage', status = 'X', " . $set_new_lifnr .
                        "idnlf = '$tmp_idnlf', purch_price = '$tmp_purch_price', " .
                        "qty = '$tmp_qty', lfdat = '$tmp_lfdat', matnr = '$tmp_matnr' " .
                        "where ebeln = '" . $proposal->itemdata->ebeln . "' and ebelp = '" . $proposal->itemdata->ebelp . "'");
                    DB::insert("insert into ". System::$table_pitemchg ." (ebeln, ebelp, cdate, internal, ctype, stage, oldval, cuser, cuser_name, reason) values " .
                        "('" . $proposal->itemdata->ebeln . "', '" . $proposal->itemdata->ebelp . "', '$cdate', 0, 'X', 'Z', 'C', '" .
                        Auth::user()->id . "', '" . Auth::user()->username . "', '$soitem')");
                    DB::commit();
                    if ($proposal->sales_save == 1) {
                        SAP::writeZPRET($proposal->lifnr, $proposal->idnlf, $proposal->quantity_unit,
                            $proposal->purch_price, $proposal->purch_curr,
                            $proposal->sales_price, $proposal->sales_curr);
                    }
                    if (Auth::user()->role != "CTV") {
                        $kunnr = $proposal->itemdata->kunnr;
                        $ctvusers = DB::select("select distinct id from ". System::$table_user_agent_clients ." where kunnr = '$kunnr'");
                        foreach ($ctvusers as $ctvuser) {
                            Mailservice::sendSalesOrderChange($ctvuser->id, $proposal->itemdata->vbeln, $proposal->itemdata->posnr, $result);
                        }
                    }
                    if (Auth::user()->role != "Referent") {
                        $ekgrp = DB::table(System::$table_porders)->where("ebeln", $proposal->itemdata->ebeln)->value("ekgrp");
                        $refuser = DB::table("users")->where(["ekgrp" => $ekgrp, "role" => "Referent", "active" => 1, "sap_system" => Auth::user()->sap_system])->first();
                        if ($refuser != null)
                            Mailservice::sendSalesOrderChange($refuser->id, $proposal->itemdata->vbeln, $proposal->itemdata->posnr, $result);
                    }
                }
                // if (!empty($set_new_lifnr)) Data::archiveItem($proposal->itemdata->ebeln, $proposal->itemdata->ebelp);
            }
        }
        return "";
    }

}