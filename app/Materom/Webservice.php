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
        $find = DB::select("select * from " . System::$table_users_sel . " where id = '$userid' and mfrnr = '$mfrnr'");
        if (count($find) == 0) {
            $mfrnr_name = SAP\MasterData::getLifnrName($mfrnr);
            if (is_array($mfrnr_name) || strlen(trim($mfrnr_name)) == 0) return __('Manufacturer does not exist');
            DB::insert("insert into " . System::$table_users_sel . " (id, mfrnr, mfrnr_name) values ('$userid','$mfrnr', '$mfrnr_name')");
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

        $find = DB::select("select * from " . System::$table_users_agent . " where id = '$userid' and agent = '$agent'");
        if (count($find) == 0) {
            DB::insert("insert into " . System::$table_users_agent . " (id, agent) values ('$userid','$agent')");
            return "";
        } else return __("This agent is already defined for user");
    }

    static public function insertCustomer($userid, $kunnr)
    {
        $kunnr = strtoupper(SAP::alpha_input($kunnr));
        $kunnr_name = MasterData::getKunnrName($kunnr);
        if (empty($kunnr_name)) return "The client is not defined in SAP";

        $find = DB::select("select * from " . System::$table_users_cli . " where id = '$userid' and kunnr = '$kunnr'");
        if (count($find) == 0) {
            DB::insert("insert into " . System::$table_users_cli . " (id, kunnr) values ('$userid','$kunnr')");
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
            $outpitem->mfrnr = ucfirst(strtolower(substr(MasterData::getLifnrName($outpitem->mfrnr), 0, 29)));
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
                if ((Auth::user()->role == 'CTV') && ($pitemchg->internal == 2)) continue;
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
            $porder = DB::select("select * from " . System::$table_porders . " where ebeln = '$ebeln'")[0];
            $lifnr = $porder->lifnr;
            $duser = DB::table("users")->where([["role", "=", "Furnizor"],
                ["lifnr", "=", $lifnr],
                ["sap_system", "=", Auth::user()->sap_system]
            ])->value("id");
        }
        if ($to[0] == 'R') {
            $stage = 'R';
            $porder = DB::select("select * from " . System::$table_porders . " where ebeln = '$ebeln'")[0];
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
            $dusers = DB::select("select id, count(*) as count from " . System::$table_users_agent . " join " . System::$table_user_agent_clients . " using (id) where kunnr = '$kunnr' group by id order by count, id");
            if ($dusers == null || empty($dusers))
                $duser = DB::table(System::$table_user_agent_clients)->where("kunnr", $kunnr)->value("id");
            else $duser = $dusers[0]->id;
            if (Auth::user()->role == "Referent") $internal = 1;
        }

        $cdate = now();
        // DB::update("update ". System::$table_pitemchg ." set acknowledged = '1' where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$cdate' and ctype = 'E'");
        DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, cdate, stage, ctype, reason, cuser, cuser_name, duser) values " .
            "('$ebeln','$ebelp', '$cdate', '$stage', 'E', '$message', '" . Auth::user()->id . "', '" . Auth::user()->username . "', '$duser')");
        Mailservice::sendMessageCopy($duser, Auth::user()->username, $order, $message);
        return "";
    }

    static function readPOItem($order, $item)
    {
        $porder = DB::table(System::$table_porders)->where("ebeln", $order)->first();
        $pitem = DB::table(System::$table_pitems)->where([["ebeln", '=', $order], ["ebelp", '=', $item]])->first();
        $pitem->bukrs = $porder->bukrs;
        $pitem->ekgrp = $porder->ekgrp;
        $pitem->lifnr = $porder->lifnr;
        $pitem->lifnr_name = MasterData::getLifnrName($porder->lifnr);
        $pitem->defmargin = "";
        $pitem->defmargin = MasterData::getSalesMargin($porder->lifnr, $pitem->mfrnr);
        $pitem->curr = $porder->curr;
        $pitem->fxrate = $porder->fxrate;

        $pitem->owner = 0;
        if (Auth::user()->role == 'Furnizor') {
            if ($pitem->stage == 'F') {
                if (Auth::user()->lifnr == $porder->lifnr)
                    $pitem->owner = 1;
            }
        } elseif (Auth::user()->role == 'Referent') {
            if ($pitem->stage == 'R') {
                if (Auth::user()->ekgrp == $porder->ekgrp)
                    $pitem->owner = 1;
            }
        }

        return json_encode($pitem);
    }

    static function sendAck($ebeln, $ebelp, $cdate)
    {
        if ($cdate == null) {
            $lastchanges = DB::select("select cdate from " . System::$table_pitemchg . " where ebeln = '$ebeln' and ebelp = '$ebelp' and (ctype = 'A' or ctype = 'X') order by cdate desc");
            if ($lastchanges == null || empty($lastchanges)) return "No suitable status record found";
            $cdate = $lastchanges[0]->cdate;
        }
        if ($cdate != null) {
            DB::update("update " . System::$table_pitemchg . " set acknowledged = '1' where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$cdate'");
        }

        $item = DB::table(System::$table_pitems)->where([['ebeln', '=', $ebeln], ['ebelp', '=', $ebelp]])->first();
        if (!is_null($mirror_user1 = System::d_ic($item->stage, $item->mirror_ebeln, $item->mirror_ebelp))) {
            if (empty($mirror_user1)) {
                Log::error("Mirroring error (sendAck): no mirror user could be determined for " .
                    Auth::user()->sap_system . "/" . Auth::user()->id . "/" . Auth::user()->role .
                    " order/item " . $item->ebeln . "/" . SAP::alpha_output($item->ebelp));
            } else {
                $currid = Auth::user()->id;
                $sap_system = Auth::user()->sap_system;
                Auth::loginUsingId($mirror_user1);
                System::init_mirror(Auth::user()->sap_system);
                self::sendAck($item->mirror_ebeln, $item->mirror_ebelp, $cdate);
                Auth::loginUsingId($currid);
                System::init($sap_system);
            }
        }

        return "";
    }

    public static function readProposals($type, $ebeln, $ebelp)
    {
        $proposal = DB::table(System::$table_pitemchg)->where([["ebeln", "=", $ebeln], ["ebelp", "=", $ebelp], ["ctype", "=", $type]])->orderBy("cdate", "desc")->first();
        if ($proposal == null) return json_encode(array());
        $proposals = DB::select("select * from " . System::$table_pitemchg_proposals . " where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$proposal->cdate' and type = '$type'");
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
                DB::update("update " . System::$table_pitems . " set stage = 'Z', status = 'A', pstage = '$pstage' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name) values " .
                    "('$ebeln','$ebelp', 'A', 'R', '$cdate', '" . Auth::user()->id . "','" . Auth::user()->username . "')");
            } else {
                if ($item->stage == 'F' || strlen(trim($item->stage)) == 0) {
                    $result = SAP::acknowledgePOItem($ebeln, $ebelp, "X");
                    if (($result != null) && !is_string($result)) $result = json_encode($result);
                    if (($result != null) && strlen(trim($result)) != 0) return $result;
                    DB::update("update " . System::$table_pitems . " set stage = 'R', status = 'T', pstage = '$pstage' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                    DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name) values " .
                        "('$ebeln','$ebelp', 'T', 'R', '$cdate', '" . Auth::user()->id . "','" . Auth::user()->username . "')");
                } else {
                    $reason = "";
                    $new_status = "A";
                    $result = SAP::acknowledgePOItem($ebeln, $ebelp, " ");
                    if (($result != null) && !is_string($result)) $result = json_encode($result);
                    if (($result != null) && strlen(trim($result)) != 0) return $result;
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
                                $result = SAP::createPurchReq($_porder->lifnr, $item->idnlf, $item->mtext, $item->matnr,
                                    $item->qty, $item->qty_uom,
                                    $item->purch_price, $item->purch_curr, $item->lfdat, $_porder->bukrs);
                                if (!empty(trim($result))) {
                                    if (substr($result, 0, 2) == "OK")
                                        $reason = __("New purchase requisition") . " " . SAP::alpha_output(substr($result, 2));
                                    else return $result;
                                }
                            } else {
                                $result = SAP::processSOItem($item->vbeln, $item->posnr,
                                    $item->qty, $item->qty_uom, $_porder->lifnr, SAP::newMatnr($item->matnr, $_porder->bukrs, $_porder->ekgrp),
                                    $item->mtext, $item->idnlf, $item->purch_price, $item->purch_curr,
                                    $item->sales_price, $item->sales_curr, $item->lfdat);
                                if (!empty(trim($result))) {
                                    if (substr($result, 0, 2) == "OK")
                                        $reason = __("New sales order item") . " " . substr(trim($result), 2) . " from item " . ltrim($item->posnr, "0");
                                    else return $result;
                                }
                                $new_status = 'X';
                            }
                        }
                    } else $reason = __("Definitively accepted");
                    $ack = 0;
                    if (Auth::user()->role == "Referent" && $pstage == "R") $ack = 1;
                    DB::update("update " . System::$table_pitems . " set stage = 'Z', status = '$new_status', pstage = '$pstage' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                    DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name, reason, acknowledged) values " .
                        "('$ebeln','$ebelp', 'A', 'Z', '$cdate', '" . Auth::user()->id . "','" . Auth::user()->username . "', '$reason', $ack)");
                }
            }
        } elseif ($item->pstage == 'Z') {
            DB::update("update " . System::$table_pitems . " set stage = 'Z', status = 'A', pstage = '$pstage' where ebeln = '$ebeln' and ebelp = '$ebelp'");
            DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name, acknowledged, oldval) values " .
                "('$ebeln','$ebelp', 'A', 'Z', '$cdate', '" . Auth::user()->id . "','" . Auth::user()->username . "', 1, 'F')");

        }

        if (!is_null($mirror_user1 = System::d_ic($item->stage, $item->mirror_ebeln, $item->mirror_ebelp))) {
            if (empty($mirror_user1)) {
                Log::error("Mirroring error (acceptItemChange): no mirror user could be determined for " .
                    Auth::user()->sap_system . "/" . Auth::user()->id . "/" . Auth::user()->role .
                    " order/item " . $item->ebeln . "/" . SAP::alpha_output($item->ebelp));
            } else {
                $currid = Auth::user()->id;
                $sap_system = Auth::user()->sap_system;
                Auth::loginUsingId($mirror_user1);
                System::init_mirror(Auth::user()->sap_system);
                self::acceptItemChange($item->mirror_ebeln, $item->mirror_ebelp, $type);
                Auth::loginUsingId($currid);
                System::init($sap_system);
            }
        }

        return "";
    }

    public static function acceptItemListChange($ebeln, $itemlist, $type)
    {
        $_porder = Orders::readPOrder($ebeln);
        if ($_porder == null) return "Purchase order $ebeln does not exist";
        $ebelplist = array();
        foreach ($itemlist as $ebelp) {
            if (!isset($_porder->items[$ebelp])) return "Purchase order item $ebeln/$ebelp does not exist";
            if ($_porder->items[$ebelp]->accept != 0 || $type == "F") $ebelplist[] = $ebelp;
        }
        if (empty($ebelplist)) return;
        $allitems = DB::table(System::$table_pitems)->where('ebeln', $ebeln)->get();
        $items = array();
        foreach ($allitems as $item) $items[$item->ebelp] = $item;
        $todoitems1 = array();
        foreach ($ebelplist as $ebelp) {
            $item = $items[$ebelp];
            if ($_porder->items[$ebelp]->accept == 0) {
                if ($type != "F")
                    continue;
            }
            $item_changed = ($item->changed == "1") || ($item->changed == "2");
            $pstage = $item->stage;
            $cdate = now();
            if ($type != "F") {
                if (!$item_changed) {
                    array_push($todoitems1, $ebelp);
                } else {
                    if ($item->stage == 'F') {
                        $result = SAP::acknowledgePOItem($ebeln, $ebelp, "X");
                        if (($result != null) && !is_string($result)) $result = json_encode($result);
                        if (($result != null) && strlen(trim($result)) != 0) return $result;
                        DB::update("update " . System::$table_pitems . " set stage = 'R', status = 'T', pstage = '$pstage' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                        DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name) values " .
                            "('$ebeln','$ebelp', 'T', 'R', '$cdate', '" . Auth::user()->id . "','" . Auth::user()->username . "')");
                    } else {
                        $reason = "";
                        $new_status = "A";
                        $result = SAP::acknowledgePOItem($ebeln, $ebelp, " ");
                        if (($result != null) && !is_string($result)) $result = json_encode($result);
                        if (($result != null) && strlen(trim($result)) != 0) return $result;
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
                                    $result = SAP::createPurchReq($_porder->lifnr, $item->idnlf, $item->mtext, $item->matnr,
                                        $item->qty, $item->qty_uom,
                                        $item->purch_price, $item->purch_curr, $item->lfdat, $_porder->bukrs);
                                    if (!empty(trim($result))) {
                                        if (substr($result, 0, 2) == "OK")
                                            $reason = __("New purchase requisition") . " " . SAP::alpha_output(substr($result, 2));
                                        else return $result;
                                    }
                                } else {
                                    $result = SAP::processSOItem($item->vbeln, $item->posnr,
                                        $item->qty, $item->qty_uom, $_porder->lifnr, SAP::newMatnr($item->matnr, $_porder->bukrs, $_porder->ekgrp),
                                        $item->mtext, $item->idnlf, $item->purch_price, $item->purch_curr,
                                        $item->sales_price, $item->sales_curr, $item->lfdat);
                                    if (!empty(trim($result))) {
                                        if (substr($result, 0, 2) == "OK")
                                            $reason = __("New sales order item") . " " . substr(trim($result), 2) . " from item " . ltrim($item->posnr, "0");
                                        else return $result;
                                    }
                                    $new_status = 'X';
                                }
                            }
                        } else $reason = __("Definitively accepted");
                        DB::update("update " . System::$table_pitems . " set stage = 'Z', status = '$new_status', pstage = '$pstage' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                        DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name, reason) values " .
                            "('$ebeln','$ebelp', 'A', 'Z', '$cdate', '" . Auth::user()->id . "','" . Auth::user()->username . "', '$reason')");
                    }
                }
            } elseif ($item->pstage == 'Z') {
                DB::update("update " . System::$table_pitems . " set stage = 'Z', status = 'A', pstage = '$pstage' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name, acknowledged, oldval) values " .
                    "('$ebeln','$ebelp', 'A', 'Z', '$cdate', '" . Auth::user()->id . "','" . Auth::user()->username . "', 1, 'F')");

            }

            if (!is_null($mirror_user1 = System::d_ic($item->stage, $item->mirror_ebeln, $item->mirror_ebelp))) {
                if (empty($mirror_user1)) {
                    Log::error("Mirroring error (acceptItemChange/List): no mirror user could be determined for " .
                        Auth::user()->sap_system . "/" . Auth::user()->id . "/" . Auth::user()->role .
                        " order/item " . $item->ebeln . "/" . SAP::alpha_output($item->ebelp));
                } else {
                    $currid = Auth::user()->id;
                    $sap_system = Auth::user()->sap_system;
                    Auth::loginUsingId($mirror_user1);
                    System::init_mirror(Auth::user()->sap_system);
                    self::acceptItemChange($item->mirror_ebeln, $item->mirror_ebelp, $type);
                    Auth::loginUsingId($currid);
                    System::init($sap_system);
                }
            }

        }

        if (!empty($todoitems1)) {
            $result = SAP::acknowledgePOItemList($ebeln, $todoitems1, " ");
            if (($result != null) && !is_string($result)) $result = json_encode($result);
            if (($result != null) && strlen(trim($result)) != 0) return $result;
            foreach ($todoitems1 as $ebelp) {
                $item = $items[$ebelp];
                $pstage = $item->stage;
                DB::update("update " . System::$table_pitems . " set stage = 'Z', status = 'A', pstage = '$pstage' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name) values " .
                    "('$ebeln','$ebelp', 'A', 'R', '$cdate', '" . Auth::user()->id . "','" . Auth::user()->username . "')");
                if (!is_null($mirror_user1 = System::d_ic($item->stage, $item->mirror_ebeln, $item->mirror_ebelp))) {
                    if (empty($mirror_user1)) {
                        Log::error("Mirroring error (acceptItemListChange): no mirror user could be determined for " .
                            Auth::user()->sap_system . "/" . Auth::user()->id . "/" . Auth::user()->role .
                            " order/item " . $item->ebeln . "/" . SAP::alpha_output($item->ebelp));
                    } else {
                        $currid = Auth::user()->id;
                        $sap_system = Auth::user()->sap_system;
                        Auth::loginUsingId($mirror_user1);
                        System::init_mirror(Auth::user()->sap_system);
                        DB::update("update " . System::$table_pitems . " set stage = 'Z', status = 'A', pstage = '$pstage' where ebeln = '$item->mirror_ebeln' and ebelp = '$item->mirror_ebelp'");
                        DB::insert("insert into " . System::$table_pitemchg . " ($item->mirror_ebeln, $item->mirror_ebelp, ctype, stage, cdate, cuser, cuser_name) values " .
                            "('$item->mirror_ebeln','$item->mirror_ebelp', 'A', 'R', '$cdate', '" . Auth::user()->id . "','" . Auth::user()->username . "')");
                        Auth::loginUsingId($currid);
                        System::init($sap_system);
                    }
                }
            }

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
                $pmfa_date = now();
                DB::update("update " . System::$table_pitems . " set pmfa = 'C', pmfa_date = '$pmfa_date' where ebeln = '$ebeln' and ebelp = '$item'");
                $result = SAP::rejectSOItem($pitem->vbeln, $pitem->posnr, '09');
                if (($result != null) && !is_string($result)) $result = json_encode($result);
                if (($result != null) && strlen(trim($result)) != 0) return $result;
                $ctvusers = DB::select("select distinct id from " . System::$table_user_agent_clients . " where kunnr = '$pitem->kunnr'");
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
                if ($old_stage != 'R') {
                    $refuser = DB::table("users")->where(["ekgrp" => $_porder->ekgrp, "role" => "Referent",
                        "active" => 1, "sap_system" => Auth::user()->sap_system])->first();
                    if ($refuser != null) {
                        try {
                            Mailservice::sendSalesOrderNotification($refuser->id, $pitem->vbeln, $pitem->posnr);
                        } catch (Exception $e) {
                        }
                    }
                }
            }
        } else SAP::unlockPOItem($ebeln, $item);
        DB::beginTransaction();
        $cdate = now();
        DB::update("update " . System::$table_pitems . " set status = '$new_status', pstage = '$old_stage', stage = '$new_stage' where ebeln = '$ebeln' and ebelp = '$item'");
        $category1 = addcslashes($category, "'");
        $reason1 = addcslashes($reason, "'");
        DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name, oldval, reason) values " .
            "('$ebeln','$item', '$new_status', '$new_stage', '$cdate', '" . Auth::user()->id . "','" . Auth::user()->username . "', '$category1', '$reason1')");
        DB::commit();

        if (!is_null($mirror_user1 = System::d_ic($pitem->stage, $pitem->mirror_ebeln, $pitem->mirror_ebelp))) {
            if (empty($mirror_user1)) {
                Log::error("Mirroring error (cancelItem): no mirror user could be determined for " .
                    Auth::user()->sap_system . "/" . Auth::user()->id . "/" . Auth::user()->role .
                    " order/item " . $pitem->ebeln . "/" . SAP::alpha_output($pitem->ebelp));
            } else {
                $currid = Auth::user()->id;
                $sap_system = Auth::user()->sap_system;
                Auth::loginUsingId($mirror_user1);
                System::init_mirror(Auth::user()->sap_system);
                self::cancelItem($pitem->mirror_ebeln, $pitem->mirror_ebelp, $category, $reason, $new_status, $new_stage);
                Auth::loginUsingId($currid);
                System::init($sap_system);
            }
        } elseif (!is_null($mirror_user1 = System::r_ic($pitem->stage, $pitem->mirror_ebeln, $pitem->mirror_ebelp))) {
            if (empty($mirror_user1)) {
                Log::error("Mirroring error (cancelItem): no mirror user could be determined for " .
                    Auth::user()->sap_system . "/" . Auth::user()->id . "/" . Auth::user()->role .
                    " order/item " . $pitem->ebeln . "/" . SAP::alpha_output($pitem->ebelp));
            } else {
                $currid = Auth::user()->id;
                $sap_system = Auth::user()->sap_system;
                Auth::loginUsingId($mirror_user1);
                System::init_mirror(Auth::user()->sap_system);
                self::cancelItem($pitem->mirror_ebeln, $pitem->mirror_ebelp, $category, $reason, $new_status, $new_stage);
                Auth::loginUsingId($currid);
                System::init($sap_system);
            }
        }

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
            if ($groupByPO != 4)
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
            $result = SAP::UpdateMaterialForInvoiceClosing($ebeln, $ebelp, trim($value));
            if (empty($result)) {
                $cdate = now();
                DB::insert("insert into " . System::$table_pitemchg . "_arch" .
                    " (ebeln,ebelp,ctype,cdate,cuser,cuser_name,reason,oldval,newval) values " .
                    "('$ebeln','$ebelp','M', '$cdate','" . Auth::user()->id . "','" . Auth::user()->username . "','Changed after archiving - for invoice closing','$oldvalue','$value')");
            }
            return;
        }
        $pitem = DB::table(System::$table_pitems)->where([['ebeln', '=', $ebeln], ['ebelp', '=', $ebelp]])->first();

        $dctv = null;
        if (!is_null($pitem->kunnr) || !empty(trim($pitem->kunnr))) {
            $dctvs = DB::select("select id, count(*) as count from " . System::$table_users_agent . " join " . System::$table_user_agent_clients . " using (id) where kunnr = '$pitem->kunnr' group by id order by count, id");
            if ($dctvs == null || empty($dctvs))
                $dctv = DB::table(System::$table_user_agent_clients)->where("kunnr", $pitem->kunnr)->value("id");
            else $dctv = $dctvs[0]->id;
        }

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
        $internal = 0;
        DB::update("update " . System::$table_pitems . " set $column = '$value', changed = '$pitem->changed', status = '$pitem->status', stage = '$new_stage', pstage = '$pitem->stage', backorder = $backorder where ebeln = '$ebeln' and ebelp = '$ebelp'");
        if ($pitem->changed != 0) DB::update("update " . System::$table_porders . " set changed = '1' where ebeln = '$ebeln'");
        if ($column == 'idnlf') $type = 'M';
        if ($column == 'qty') $type = 'Q';
        if ($column == 'lfdat') $type = 'D';
        if ($column == 'etadt') {
            $type = 'J';
            $internal = 1;
            $pmfa_date = now();
            DB::update("update " . System::$table_pitems . " set pmfa = 'D', pmfa_date = '$pmfa_date' where ebeln = '$ebeln' and ebelp = '$ebelp'");
            if (!is_null($dctv) && !empty($dctv)) {
                $etadt = $pitem->etadt;
                if (empty($etadt)) $etadt = $pitem->lfdat;
                $etadt = substr($etadt, 0, 10);
                $message = __("ETA has changed")." ".__("from")." ".$etadt." ".__("to")." ".$value;
                Log::info($message." (".Auth::user()->id." -> $dctv)");
                $order = SAP::alpha_output($pitem->ebeln) . "/" . SAP::alpha_output($pitem->ebelp);
                $sorder = SAP::alpha_output($pitem->vbeln) . "/" . SAP::alpha_output($pitem->posnr);
                Mailservice::sendMessageCopy($dctv, Auth::user()->username, $order, $message, $sorder);
                $ctvuser1 = DB::table(System::$table_roles)->where([["rfc_role", "=", "CTV"]])->value("user1");
                if (($ctvuser1 != null) && !empty($ctvuser1)) {
                    try {
                        Mailservice::sendMessageCopy($ctvuser1->id, $ctvuser1->username, $order, $message, $sorder);
                    } catch (Exception $e) {
                    }
                }

            }
        }
        if ($column == 'purch_price') $type = 'P';
        $newval = trim($value . " " . $valuehlp);
        $cdate = now();
        $cdate->addSeconds($seconds);
        DB::insert("insert into " . System::$table_pitemchg . " (ebeln,ebelp,ctype,stage,cdate,internal,cuser,cuser_name,reason,oebelp,oldval,newval) values ('$ebeln','$ebelp','$type','$new_stage', '$cdate', $internal, '" . Auth::user()->id . "','" . Auth::user()->username . "','','','$oldvalue','$newval')");
        if ($pitem->backorder == 0 && $backorder == 1 && $type == "D") {
            $cdate->addSeconds(1);
            try {
                DB::insert("insert into " . System::$table_pitemchg . " (ebeln,ebelp,ctype,stage,cdate,internal,cuser,cuser_name,reason,oebelp,oldval,newval) values ('$ebeln','$ebelp','B','$new_stage', '$cdate', $internal, '" . Auth::user()->id . "','" . Auth::user()->username . "','','','$pitem->backorder','$backorder')");
            } catch(\Illuminate\Database\QueryException $ex1) {
                $cdate->addSeconds(1);
                try {
                    DB::insert("insert into " . System::$table_pitemchg . " (ebeln,ebelp,ctype,stage,cdate,internal,cuser,cuser_name,reason,oebelp,oldval,newval) values ('$ebeln','$ebelp','B','$new_stage', '$cdate', $internal, '" . Auth::user()->id . "','" . Auth::user()->username . "','','','$pitem->backorder','$backorder')");
                } catch(\Illuminate\Database\QueryException $ex2) {
                    $cdate->addSeconds(1);
                    DB::insert("insert into " . System::$table_pitemchg . " (ebeln,ebelp,ctype,stage,cdate,internal,cuser,cuser_name,reason,oebelp,oldval,newval) values ('$ebeln','$ebelp','B','$new_stage', '$cdate', $internal, '" . Auth::user()->id . "','" . Auth::user()->username . "','','','$pitem->backorder','$backorder')");
                }
            }
        }
        if ($type == "D") {
            DB::update("update " . System::$table_pitems . " set etadt = '$value' where ebeln = '$ebeln' and ebelp = '$ebelp'");
            $cdate->addSeconds(1);
            $oldetadt = substr($pitem->etadt, 0, 10);
            try {
                DB::insert("insert into " . System::$table_pitemchg . " (ebeln,ebelp,ctype,stage,cdate,cuser,cuser_name,reason,oebelp,oldval,newval) values ('$ebeln','$ebelp','J','$new_stage', '$cdate','" . Auth::user()->id . "','" . Auth::user()->username . "','','','$oldetadt','$newval')");
            } catch(\Illuminate\Database\QueryException $ex1) {
                $cdate->addSeconds(1);
                try {
                    DB::insert("insert into " . System::$table_pitemchg . " (ebeln,ebelp,ctype,stage,cdate,cuser,cuser_name,reason,oebelp,oldval,newval) values ('$ebeln','$ebelp','J','$new_stage', '$cdate','" . Auth::user()->id . "','" . Auth::user()->username . "','','','$oldetadt','$newval')");
                } catch(\Illuminate\Database\QueryException $ex2) {
                    $cdate->addSeconds(1);
                    DB::insert("insert into " . System::$table_pitemchg . " (ebeln,ebelp,ctype,stage,cdate,cuser,cuser_name,reason,oebelp,oldval,newval) values ('$ebeln','$ebelp','J','$new_stage', '$cdate','" . Auth::user()->id . "','" . Auth::user()->username . "','','','$oldetadt','$newval')");
                }
            }
        }
        DB::commit();

        if (!is_null($mirror_user1 = System::d_ic($pitem->stage, $pitem->mirror_ebeln, $pitem->mirror_ebelp))) {
            if (empty($mirror_user1)) {
                Log::error("Mirroring error (doChangeItem): no mirror user could be determined for " .
                    Auth::user()->sap_system . "/" . Auth::user()->id . "/" . Auth::user()->role .
                    " order/item " . $pitem->ebeln . "/" . SAP::alpha_output($pitem->ebelp));
            } else {
                $currid = Auth::user()->id;
                $sap_system = Auth::user()->sap_system;
                Auth::loginUsingId($mirror_user1);
                System::init_mirror(Auth::user()->sap_system);
                self::doChangeItem($column, $value, $valuehlp, $oldvalue, $pitem->mirror_ebeln, $pitem->mirror_ebelp, $backorder, $seconds);
                Auth::loginUsingId($currid);
                System::init($sap_system);
            }
        }
        return "";
    }


    public static function doChangeDeliveryDate($value, $oldvalue, $ebeln, $ebelp, $backorder, $delayed_check, $delayed_date)
    {
        $mode = 0;
        $pitem = DB::table(System::$table_pitems)->where([['ebeln', '=', $ebeln], ['ebelp', '=', $ebelp]])->first();
        $changed = 0;
        $pmfa = $pitem->pmfa;

        if ($backorder == "ETADT") {
            $backorder = $pitem->backorder;
            $mode = 1;
        }

        if ($pitem->backorder != 0) {
            if (($pmfa == 'F') && ($pitem->stage == 'Z')) {
                if ($backorder == 0) {
                    $changed = 1;
                    $delayed_check == 0;
                    $pmfa = '';
                }
            }
            else $backorder = 1;
        } else if ($backorder == 1) $changed = 1;
        if (empty($value)) {
            if ($mode == 0) $value = $pitem->lfdat;
            elseif ($mode == 1) $value = $pitem->etadt;
        } else $changed = 1;
        if ($pitem->eta_delayed_check != $delayed_check) $changed = 1;
        if (empty($delayed_date)) {
            $delayed_date = $pitem->eta_delayed_date;
            if ($delayed_check == 1) {
                $backorder = 1;
                $changed = 1;
                $pmfa = "F";
            }
        }
        elseif ($delayed_date != $pitem->eta_delayed_date) $changed = 1;
        if ($delayed_check == 1 && $pitem->eta_delayed_date != $delayed_date) {
            $backorder = 1;
            $changed = 1;
            $pmfa = "F";
        }
        if ($changed == 0) return "";

        $pitem->changed = 1;
        $new_stage = $pitem->stage;
        if (($pitem->stage == 'Z') && ($mode == 0)) {
            $pitem->status = '';
            $new_stage = 'F';
            $pitem->changed = 2;
        }

        DB::beginTransaction();
        if ($mode == 0) {
            DB::update("update " . System::$table_pitems . " set lfdat = '$value', changed = '$pitem->changed', status = '$pitem->status', stage = '$new_stage', pstage = '$pitem->stage', backorder = $backorder,  eta_delayed_check = $delayed_check, eta_delayed_date = '$delayed_date', pmfa = '$pmfa'  where ebeln = '$ebeln' and ebelp = '$ebelp'");
            DB::update("update " . System::$table_porders . " set changed = '1' where ebeln = '$ebeln'");
        } elseif ($mode == 1) {
           self::doChangeItem('etadt', $value, "", $oldvalue, $ebeln, $ebelp, $backorder, -3);
        }

        $internal = 0;
        if ($pitem->eta_delayed_check != $delayed_check) {
            $internal = 2;
        } elseif ($pitem->eta_delayed_check == 1 && $pitem->eta_delayed_date != $delayed_date && !empty($delayed_date)) {
            $internal = 2;
        }

        $cdate = now();
        if ($mode == 0 && $value != $pitem->lfdat) {
            DB::insert("insert into " . System::$table_pitemchg . " (ebeln,ebelp,ctype,internal,stage,cdate,cuser,cuser_name,reason,oebelp,oldval,newval) values ('$ebeln','$ebelp','D',$internal,'$new_stage', '$cdate','" . Auth::user()->id . "','" . Auth::user()->username . "','','','$oldvalue','$value')");
            $cdate->addSeconds(1);
        }
        if ($mode == 0 && $pitem->backorder == 0 && $backorder == 1) {
            DB::insert("insert into " . System::$table_pitemchg . " (ebeln,ebelp,ctype,internal,stage,cdate,cuser,cuser_name,reason,oebelp,oldval,newval) values ('$ebeln','$ebelp','B',$internal,'$new_stage', '$cdate','" . Auth::user()->id . "','" . Auth::user()->username . "','','','$pitem->backorder','$backorder')");
            $cdate->addSeconds(1);
        }
        if ($mode == 0 && $value != $pitem->etadt) {
            DB::update("update " . System::$table_pitems . " set etadt = '$value' where ebeln = '$ebeln' and ebelp = '$ebelp'");
            $oldetadt = substr($pitem->etadt, 0, 10);
            DB::insert("insert into " . System::$table_pitemchg . " (ebeln,ebelp,ctype,internal,stage,cdate,cuser,cuser_name,reason,oebelp,oldval,newval) values ('$ebeln','$ebelp','J',$internal,'$new_stage', '$cdate','" . Auth::user()->id . "','" . Auth::user()->username . "','','','$oldetadt','$value')");
            $cdate->addSeconds(1);
        }
        if ($pitem->eta_delayed_check != $delayed_check) {
            DB::insert("insert into " . System::$table_pitemchg . " (ebeln,ebelp,ctype,internal,stage,cdate,cuser,cuser_name,reason,oebelp,oldval,newval) values ('$ebeln','$ebelp','Y',0,'$new_stage', '$cdate','" . Auth::user()->id . "','" . Auth::user()->username . "','','','$pitem->eta_delayed_check','$delayed_date')");
            $cdate->addSeconds(1);
        } elseif ($delayed_check == 1 && $pitem->eta_delayed_date != $delayed_date && !empty($delayed_date)) {
            DB::insert("insert into " . System::$table_pitemchg . " (ebeln,ebelp,ctype,internal,stage,cdate,cuser,cuser_name,reason,oebelp,oldval,newval) values ('$ebeln','$ebelp','Y',0,'$new_stage', '$cdate','" . Auth::user()->id . "','" . Auth::user()->username . "','','','=','$delayed_date')");
            $cdate->addSeconds(1);
        }

        if (($pitem->eta_delayed_check != $delayed_check) ||
            ($pitem->eta_delayed_date != $delayed_date))
        {
            DB::update("update " . System::$table_pitems . " set backorder = $backorder,  eta_delayed_check = $delayed_check, eta_delayed_date = '$delayed_date', pmfa = '$pmfa'  where ebeln = '$ebeln' and ebelp = '$ebelp'");
        }

        DB::commit();

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
        $users = DB::select("select * from users where role = 'CTV' and sap_system = '" . Auth::user()->sap_system . "'");
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

    static public function sendInquiry($ebeln, $ebelp, $text, $pnad_status, $to)
    {
        $pitem = DB::table(System::$table_pitems)->where(['ebeln' => $ebeln, 'ebelp' => $ebelp])->first();
        $porder = DB::select("select * from " . System::$table_porders . " where ebeln = '$ebeln'")[0];
        $order = SAP::alpha_output($ebeln) . "/" . SAP::alpha_output($ebelp);
        $duser = "";
        $stage = '';
        $newval = "";
        $result = "";
        $internal = 0;
        if ($to[0] == 'F') {
            $stage = 'F';
            $lifnr = $porder->lifnr;
            $duser = DB::table("users")->where([["role", "=", "Furnizor"],
                ["lifnr", "=", $lifnr],
                ["sap_system", "=", Auth::user()->sap_system]
            ])->value("id");
        }
        if ($to[0] == 'R') {
            $stage = 'R';
            $ekgrp = $porder->ekgrp;
            $duser = DB::table("users")->where([["role", "=", "Referent"],
                ["ekgrp", "=", $ekgrp],
                ["sap_system", "=", Auth::user()->sap_system]
            ])->value("id");
            if (Auth::user()->role == "CTV") $internal = 1;
        }
        if ($to[0] == 'C') {
            if ($pitem->vbeln != Orders::stockorder)
                $order = SAP::alpha_output($pitem->vbeln) . "/" . SAP::alpha_output($pitem->posnr);
            $stage = 'C';
            $kunnr = DB::table(System::$table_pitems)->where([["ebeln", "=", $ebeln], ["ebelp", "=", $ebelp]])->value("kunnr");
            $dusers = DB::select("select id, count(*) as count from " . System::$table_users_agent . " join " . System::$table_user_agent_clients . " using (id) where kunnr = '$kunnr' group by id order by count, id");
            if ($dusers == null || empty($dusers))
                $duser = DB::table(System::$table_user_agent_clients)->where("kunnr", $kunnr)->value("id");
            else $duser = $dusers[0]->id;
            if (Auth::user()->role == "Referent") $internal = 1;
        }

        if (strtoupper($to[0]) == 'P' && strtoupper($pnad_status) != 'N') {
            // list($cause, $solution, $details) = explode("//@@//", $text);
            $newval = __("Motiv/Document");
            if ($to[0] == "p") {
                $newval = __("Rezolvat");
            }
            $data = '';
            $pnad_status = trim($pnad_status);
            if (empty($text)) $text = ""; else $text = trim($text);
            $solved = "";
            if ($pnad_status == 'SX' || $pnad_status == 'OX') $solved = "X";
            if ($pnad_status == 'S') {
                // Mark as solved
                $result = SAP::setPnadData($ebeln, $ebelp, 'A', $text, $solved, $data);
            } elseif ($pnad_status == 'SX') {
                // Mark as unsolved
                $result = SAP::setPnadData($ebeln, $ebelp, 'B', $text, $solved, $data);
            } elseif ($pnad_status == 'O' || $pnad_status == 'OX') {
                // Leave solved/unsolved as-is
                if (substr($pitem->qty_diff, 0, 1) == '-') {
                    // Minus
                    $result = SAP::setPnadData($ebeln, $ebelp, 'D', $text, $solved, $data);
                } elseif (trim($pitem->qty_diff) != '') {
                    // Plus
                    $result = SAP::setPnadData($ebeln, $ebelp, 'G', $text, $solved, $data);
                }  elseif (trim($pitem->qty_diff) != '') {
                    // Non-conform
                    $result = SAP::setPnadData($ebeln, $ebelp, 'F', $text, $solved, $data);
                }
            }
            if (trim($result) != "") return $result;
        }

        $uId = Auth::id();
        $uName = Auth::user()->username;
        $cdate = now();
        $pmfa_date = now();
        $text1 = addcslashes($text, "'");
        if (strtoupper($to[0]) == "P") {
            $stage = 'R';
            if (!empty($result))
                \Session::put("alert-danger", $result);
            else {
                DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, cdate, internal, ctype, cuser, cuser_name, duser, newval, stage, reason) VALUES " .
                    "('$ebeln', '$ebelp', '$cdate', $internal, 'E', '$uId', '$uName', '$duser', '$newval', '$stage', '$text1')");
                DB::update("update " . System::$table_pitems . " set pmfa = 'E', pmfa_date = '$pmfa_date' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                \Session::put("alert-success", __("Modificarea exceptiei a fost efectuata cu succes."));
            }
        } else {
            DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, cdate, internal, ctype, cuser, cuser_name, duser, newval, stage, reason) VALUES " .
                "('$ebeln', '$ebelp', '$cdate', $internal, 'E', '$uId', '$uName', '$duser', '$newval', '$stage', '$text1')");
            DB::update("update " . System::$table_pitems . " set pmfa = 'E', pmfa_date = '$pmfa_date' where ebeln = '$ebeln' and ebelp = '$ebelp'");
            Mailservice::sendMessageCopy($duser, $uName, $order, $text);
            \Session::put("alert-success", __("Mesajul a fost trimis cu succes."));
        }

        if (!is_null($mirror_user1 = System::d_ic($pitem->stage, $pitem->mirror_ebeln, $pitem->mirror_ebelp))) {
            if (empty($mirror_user1)) {
                Log::error("Mirroring error (sendInquiry): no mirror user could be determined for " .
                    Auth::user()->sap_system . "/" . Auth::user()->id . "/" . Auth::user()->role .
                    " order/item " . $pitem->ebeln . "/" . SAP::alpha_output($pitem->ebelp));
            } else {
                $currid = Auth::user()->id;
                $sap_system = Auth::user()->sap_system;
                Auth::loginUsingId($mirror_user1);
                System::init_mirror(Auth::user()->sap_system);
                self::sendInquiry($pitem->mirror_ebeln, $pitem->mirror_ebelp, $text, $to);
                Auth::loginUsingId($currid);
                System::init($sap_system);
            }
        } elseif (!is_null($mirror_user1 = System::r_ic($pitem->stage, $pitem->mirror_ebeln, $pitem->mirror_ebelp))) {
            if (empty($mirror_user1)) {
                Log::error("Mirroring error (sendInquiry): no mirror user could be determined for " .
                    Auth::user()->sap_system . "/" . Auth::user()->id . "/" . Auth::user()->role .
                    " order/item " . $pitem->ebeln . "/" . SAP::alpha_output($pitem->ebelp));
            } else {
                $currid = Auth::user()->id;
                $sap_system = Auth::user()->sap_system;
                Auth::loginUsingId($mirror_user1);
                System::init_mirror(Auth::user()->sap_system);
                self::sendInquiry($pitem->mirror_ebeln, $pitem->mirror_ebelp, $text, $to);
                Auth::loginUsingId($currid);
                System::init($sap_system);
            }
        }

        return "";
    }

    static public function processProposal($proposal)
    {
        $cdate = now();
        $stage = $proposal->itemdata->stage;
        $ebeln = $proposal->itemdata->ebeln;
        $ebelp = $proposal->itemdata->ebelp;
        $_porder = DB::table(System::$table_porders)->where("ebeln", $ebeln)->first();
        $bukrs = $_porder->bukrs;
        $ekgrp = $_porder->ekgrp;
        $newstage = 'C';
        if (Auth::user()->role == "Furnizor") $newstage = 'R';
        if (isset($proposal->items)) {
            DB::beginTransaction();
            DB::update("update " . System::$table_pitems . " set stage = '$newstage', pstage = '$stage', status = 'T' " .
                "where ebeln = '$ebeln' and ebelp = '$ebelp'");
            DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, cdate, internal, ctype, stage, cuser, cuser_name) values " .
                "('$ebeln', '$ebelp', '$cdate', 1, '$proposal->type', '$newstage', '" .
                Auth::user()->id . "', '" . Auth::user()->username . "')");
            $counter = 0;
            foreach ($proposal->items as $propitem) {
                $propitem->lifnr = SAP::alpha_input($propitem->lifnr);
                if (strlen($propitem->purch_curr) > 3) $propitem->purch_curr = substr($propitem->purch_curr, 0, 3);
                if (strlen($propitem->sales_curr) > 3) $propitem->sales_curr = substr($propitem->sales_curr, 0, 3);
                $propitem->mtext = str_replace("'", "\'", $propitem->mtext);
                DB::insert("insert into " . System::$table_pitemchg_proposals . " (type, ebeln, ebelp, cdate, pos, lifnr, idnlf, matnr, " .
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
                $ctvusers = DB::select("select distinct id from " . System::$table_user_agent_clients . " where kunnr = '$pitem->kunnr'");
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
                DB::update("update " . System::$table_pitems . " set stage = 'Z', pstage = '$stage', status = 'A', " .
                    "idnlf = '$tmp_idnlf', mtext = '$tmp_mtext', matnr = '$tmp_matnr', lfdat = '$tmp_lfdat', " .
                    "qty = $tmp_qty, qty_uom = '$tmp_qty_unit', " .
                    "purch_price = '$tmp_purch_price', purch_curr = '$tmp_purch_curr' " .
                    "where ebeln = '$ebeln' and ebelp = '$ebelp'");
                DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, cdate, internal, ctype, stage, cuser, cuser_name) values " .
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
                        $proposal->purch_price, $proposal->purch_curr, $proposal->lfdat, $bukrs);
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
                    DB::update("update " . System::$table_pitems . " set stage = 'Z', pstage = '$tmp_stage', status = '$newstatus', " . $set_new_lifnr .
                        "idnlf = '$tmp_idnlf', purch_price = '$tmp_purch_price', " .
                        "qty = '$tmp_qty', lfdat = '$tmp_lfdat', matnr = '$tmp_matnr' " .
                        "where ebeln = '" . $proposal->itemdata->ebeln . "' and ebelp = '" . $proposal->itemdata->ebelp . "'");
                    DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, cdate, internal, ctype, stage, cuser, cuser_name, reason) values " .
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
                        $proposal->quantity, $proposal->quantity_unit, $proposal->lifnr, SAP::newMatnr($proposal->itemdata->matnr, $bukrs, $ekgrp),
                        $proposal->mtext, $proposal->idnlf, $proposal->purch_price, $proposal->purch_curr,
                        $proposal->sales_price, $proposal->sales_curr, $proposal->lfdat);
                    if (!empty(trim($result))) {
                        if (substr($result, 0, 2) == "OK")
                            $soitem = __("New sales order item") . " " . substr($result, 2);
                        else return $result;
                    }
                    DB::beginTransaction();
                    DB::update("update " . System::$table_pitems . " set stage = 'Z', pstage = '$tmp_stage', status = 'X', " . $set_new_lifnr .
                        "idnlf = '$tmp_idnlf', purch_price = '$tmp_purch_price', " .
                        "qty = '$tmp_qty', lfdat = '$tmp_lfdat', matnr = '$tmp_matnr' " .
                        "where ebeln = '" . $proposal->itemdata->ebeln . "' and ebelp = '" . $proposal->itemdata->ebelp . "'");
                    DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, cdate, internal, ctype, stage, oldval, cuser, cuser_name, reason) values " .
                        "('" . $proposal->itemdata->ebeln . "', '" . $proposal->itemdata->ebelp . "', '$cdate', 0, 'X', 'Z', 'C', '" .
                        Auth::user()->id . "', '" . Auth::user()->username . "', '$soitem')");
                    DB::commit();
                    if (Auth::user()->role != "CTV") {
                        $kunnr = $proposal->itemdata->kunnr;
                        $ctvusers = DB::select("select distinct id from " . System::$table_user_agent_clients . " where kunnr = '$kunnr'");
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
            }
        }

        if (!is_null($mirror_user1 = System::d_ic($proposal->itemdata->stage, $proposal->itemdata->mirror_ebeln, $proposal->itemdata->mirror_ebelp))) {
            if (empty($mirror_user1)) {
                Log::error("Mirroring error (processProposal): no mirror user could be determined for " .
                    Auth::user()->sap_system . "/" . Auth::user()->id . "/" . Auth::user()->role .
                    " order/item " . $proposal->itemdata->ebeln . "/" . SAP::alpha_output($proposal->itemdata->ebelp));
            } else {
                $currid = Auth::user()->id;
                $sap_system = Auth::user()->sap_system;
                Auth::loginUsingId($mirror_user1);
                System::init_mirror(Auth::user()->sap_system);
                $proposal->itemdata = DB::table(System::$table_pitems)->where(['ebeln' => $proposal->itemdata->mirror_ebeln,
                    'ebelp' => $proposal->itemdata->mirror_ebelp])->first();
                self::processProposal($proposal);
                Auth::loginUsingId($currid);
                System::init($sap_system);
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

        $now = now();

        $ddays = System::dateOnly($cdate)->diffInWeekDays(System::dateOnly($now));
        if ($ddays > 0) {
            $old_lfdat = substr($proposal->lfdat, 0, 10);
            $new_lfdat = (new Carbon($old_lfdat))->addWeekdays($ddays);
            $proposal->lfdat = substr($new_lfdat, 0, 10);
            $message = "Delivery date adjusted from $old_lfdat to $proposal->lfdat due to delayed approval";
            DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, ctype, stage, cdate, acknowledged, cuser, cuser_name, reason) values " .
                "('$ebeln','$ebelp', 'E', 'C', '$now', 1, '" . Auth::user()->id . "','" . Auth::user()->username . "', '$message')");
            $now->addSecond();
        }

        $result = SAP::acknowledgePOItem($ebeln, $ebelp, " ");
        if (($result != null) && !is_string($result)) $result = json_encode($result);
        if (($result != null) && strlen(trim($result)) != 0) return $result;

        $item = DB::table(System::$table_pitems)->where([["ebeln", "=", $ebeln], ["ebelp", "=", $ebelp]])->first();
        $porder = DB::table(System::$table_porders)->where("ebeln", $ebeln)->first();
        $bukrs = $porder->bukrs;
        $ekgrp = $porder->ekgrp;
        if ((($proposal->lifnr == $porder->lifnr) && (trim($proposal->idnlf) == trim($item->orig_idnlf)))
            || System::$mirroring) {
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
            DB::beginTransaction();
            DB::update("update " . System::$table_pitems . " set stage = 'Z', pstage = '" . $item->stage . "', status = 'A', " .
                "idnlf = '$tmp_idnlf', mtext = '$tmp_mtext', matnr = '$tmp_matnr', lfdat = '$tmp_lfdat', " .
                "qty = $tmp_qty, qty_uom = '$tmp_qty_unit', " .
                "purch_price = '$tmp_purch_price', purch_curr = '$tmp_purch_curr', " .
                "sales_price = '$tmp_sales_price', sales_curr = '$tmp_sales_curr' " .
                "where ebeln = '$ebeln' and ebelp = '$ebelp'");
            DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, cdate, internal, ctype, stage, cuser, cuser_name) values " .
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
            if (!is_null($mirror_user1 = System::r_ic($item->stage, $item->mirror_ebeln, $item->mirror_ebelp))) {
                if (empty($mirror_user1)) {
                    Log::error("Mirroring error (sendAck): no mirror user could be determined for " .
                        Auth::user()->sap_system . "/" . Auth::user()->id . "/" . Auth::user()->role .
                        " order/item " . $item->ebeln . "/" . SAP::alpha_output($item->ebelp));
                } else {
                    $currid = Auth::user()->id;
                    $sap_system = Auth::user()->sap_system;
                    Auth::loginUsingId($mirror_user1);
                    System::init_mirror(Auth::user()->sap_system);
                    self::acceptProposal($item->mirror_ebeln, $item->mirror_ebelp, $cdate, $pos);
                    Auth::loginUsingId($currid);
                    System::init($sap_system);
                }
            }
            return "";
        }

        $result = SAP::rejectPOItem($ebeln, $ebelp);
        if (($result != null) && !is_string($result)) $result = json_encode($result);
        if (($result != null) && strlen(trim($result)) != 0) return $result;
        $result = SAP::processSOItem($item->vbeln, $item->posnr,
            $proposal->qty, $proposal->qty_uom, $proposal->lifnr, SAP::newMatnr($item->matnr, $bukrs, $ekgrp),
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
        DB::update("update " . System::$table_pitems . " set stage = 'Z', pstage = '$item->stage', status = 'X'" . $set_new_lifnr .
            " where ebeln = '$ebeln' and ebelp = '$ebelp'");
        DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, cdate, internal, ctype, stage, oldval, cuser, cuser_name, reason) values " .
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
        } catch (Exception $exception) {
            Log::error($exception);
        }
        $pmfa_date = now();
        if (Auth::user()->role == "CTV") {
            DB::beginTransaction();
            DB::update("update " . System::$table_pitems . " set pmfa = 'A', pmfa_date = '$pmfa_date' where ebeln = '$ebeln' and ebelp = '$ebelp'");
            DB::commit();
        }

        if (!is_null($mirror_user1 = System::r_ic($item->stage, $item->mirror_ebeln, $item->mirror_ebelp))) {
            if (empty($mirror_user1)) {
                Log::error("Mirroring error (acceptProposal): no mirror user could be determined for " .
                    Auth::user()->sap_system . "/" . Auth::user()->id . "/" . Auth::user()->role .
                    " order/item " . $item->ebeln . "/" . SAP::alpha_output($item->ebelp));
            } else {
                $currid = Auth::user()->id;
                $sap_system = Auth::user()->sap_system;
                Auth::loginUsingId($mirror_user1);
                System::init_mirror(Auth::user()->sap_system);
                self::acceptProposal($item->mirror_ebeln, $item->mirror_ebelp, $cdate, $pos);
                Auth::loginUsingId($currid);
                System::init($sap_system);
            }
        }

        return "";
    }

    static public function rejectProposal($ebeln, $ebelp, $cdate)
    {
        $item = DB::table(System::$table_pitems)->where([["ebeln", "=", $ebeln], ["ebelp", "=", $ebelp]])->first();
        $result = SAP::acknowledgePOItem($ebeln, $ebelp, " ");
        if (($result != null) && !is_string($result)) $result = json_encode($result);
        if (($result != null) && strlen(trim($result)) != 0) return $result;
        $result = SAP::rejectPOItem($ebeln, $ebelp);
        if (($result != null) && !is_string($result)) $result = json_encode($result);
        if (($result != null) && strlen(trim($result)) != 0) return $result;
        $result = SAP::rejectSOItem($item->vbeln, $item->posnr, "07");
        if (($result != null) && !is_string($result)) $result = json_encode($result);
        if (($result != null) && strlen(trim($result)) != 0) return $result;
        $soitem = __("Rejected sales order item") . " " . SAP::alpha_output($item->vbeln) . '/' . ltrim($item->posnr, "0");
        DB::beginTransaction();
        DB::update("update " . System::$table_pitems . " set stage = 'Z', pstage = '$item->stage', status = 'X' " .
            "where ebeln = '$ebeln' and ebelp = '$ebelp'");
        $now = now();
        DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, cdate, internal, ctype, stage, oldval, cuser, cuser_name, reason) values " .
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

        $pmfa_date = now();
        if (Auth::user()->role == "CTV") {
            DB::beginTransaction();
            DB::update("update " . System::$table_pitems . " set pmfa = 'B', pmfa_date = '$pmfa_date' where ebeln = '$ebeln' and ebelp = '$ebelp'");
            DB::commit();
        }

        if (!is_null($mirror_user1 = System::r_ic($item->stage, $item->mirror_ebeln, $item->mirror_ebelp))) {
            if (empty($mirror_user1)) {
                Log::error("Mirroring error (rejectProposal): no mirror user could be determined for " .
                    Auth::user()->sap_system . "/" . Auth::user()->id . "/" . Auth::user()->role .
                    " order/item " . $item->ebeln . "/" . SAP::alpha_output($item->ebelp));
            } else {
                $currid = Auth::user()->id;
                $sap_system = Auth::user()->sap_system;
                Auth::loginUsingId($mirror_user1);
                System::init_mirror(Auth::user()->sap_system);
                self::rejectProposal($item->mirror_ebeln, $item->mirror_ebelp, $cdate);
                Auth::loginUsingId($currid);
                System::init($sap_system);
            }
        }

        return "";
    }

    static public function acceptSplit($ebeln, $ebelp, $cdate, $no_ic = false)
    {
        $item = DB::table(System::$table_pitems)->where([["ebeln", "=", $ebeln], ["ebelp", "=", $ebelp]])->first();
        $porder = DB::table(System::$table_porders)->where("ebeln", $ebeln)->first();
        $bukrs = $porder->bukrs;
        $ekgrp = $porder->ekgrp;
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
            foreach ($splititems as $splititem) {
                $result = SAP::createPurchReq($splititem->lifnr, $splititem->idnlf, $splititem->mtext, $item->matnr,
                    $splititem->qty, $splititem->qty_uom,
                    $splititem->purch_price, $splititem->purch_curr, $splititem->lfdat, $bukrs);
                if (!empty(trim($result))) {
                    if (substr($result, 0, 2) == "OK")
                        $text .= SAP::alpha_output(substr($result, 2));
                    else return $result;
                }
            }
            $text = __("New purchase requisition ") . $text;
        } else {
            foreach ($splititems as $splititem) {
                $result = SAP::processSOItem($item->vbeln, $item->posnr,
                    $splititem->qty, $splititem->qty_uom, $splititem->lifnr, SAP::newMatnr($item->matnr, $bukrs, $ekgrp),
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
                $ctvusers = DB::select("select distinct id from " . System::$table_user_agent_clients . " where kunnr = '$item->kunnr'");
                foreach ($ctvusers as $ctvuser) {
                    Mailservice::sendSalesOrderChange($ctvuser->id, $item->vbeln, $item->posnr, $text2);
                }
            }
        }

        DB::beginTransaction();
        DB::update("update " . System::$table_pitems . " set stage = 'Z', pstage = '$item->stage', status = 'X' " .
            "where ebeln = '$ebeln' and ebelp = '$ebelp'");
        DB::delete("delete from " . System::$table_pitemchg_proposals . " where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$cdate'");
        $cdate = now();
        DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, cdate, internal, ctype, stage, oldval, cuser, cuser_name, reason) values " .
            "('$ebeln', '$ebelp', '$cdate', 0, 'A', 'Z', 'U', '" .
            Auth::user()->id . "', '" . Auth::user()->username . "', '$text')");
        DB::commit();


        if (!is_null($mirror_user1 = System::r_ic($item->stage, $item->mirror_ebeln, $item->mirror_ebelp)) && !$no_ic) {
            if (empty($mirror_user1)) {
                Log::error("Mirroring error (acceptSplit): no mirror user could be determined for " .
                    Auth::user()->sap_system . "/" . Auth::user()->id . "/" . Auth::user()->role .
                    " order/item " . $item->ebeln . "/" . SAP::alpha_output($item->ebelp));
            } else {
                $currid = Auth::user()->id;
                $sap_system = Auth::user()->sap_system;
                Auth::loginUsingId($mirror_user1);
                System::init_mirror(Auth::user()->sap_system);
                self::acceptSplit($item->mirror_ebeln, $item->mirror_ebelp, $cdate);
                Auth::loginUsingId($currid);
                System::init($sap_system);
            }
        }

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
        DB::update("update " . System::$table_pitems . " set stage = 'Z', pstage = '$item->stage', status = 'X' " .
            "where ebeln = '$ebeln' and ebelp = '$ebelp'");
        DB::delete("delete from " . System::$table_pitemchg_proposals . " where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$cdate'");
        $now = now();
        DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, cdate, internal, ctype, stage, oldval, cuser, cuser_name, reason) values " .
            "('$ebeln', '$ebelp', '$now', 0, 'X', 'Z', 'W', '" .
            Auth::user()->id . "', '" . Auth::user()->username . "', '$soitem')");
        DB::commit();
        if (Auth::user()->role != 'CTV') {
            $ctvusers = DB::select("select distinct id from " . System::$table_user_agent_clients . " where kunnr = '$item->kunnr'");
            foreach ($ctvusers as $ctvuser) {
                Mailservice::sendSalesOrderNotification($ctvuser->id, $item->vbeln, $item->posnr);
            }
        }

        if (!is_null($mirror_user1 = System::r_ic($item->stage, $item->mirror_ebeln, $item->mirror_ebelp))) {
            if (empty($mirror_user1)) {
                Log::error("Mirroring error (rejectSplit): no mirror user could be determined for " .
                    Auth::user()->sap_system . "/" . Auth::user()->id . "/" . Auth::user()->role .
                    " order/item " . $item->ebeln . "/" . SAP::alpha_output($item->ebelp));
            } else {
                $currid = Auth::user()->id;
                $sap_system = Auth::user()->sap_system;
                Auth::loginUsingId($mirror_user1);
                System::init_mirror(Auth::user()->sap_system);
                self::rejectSplit($item->mirror_ebeln, $item->mirror_ebelp, $cdate);
                Auth::loginUsingId($currid);
                System::init($sap_system);
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
        DB::update("update " . System::$table_pitems . " set stage = 'R', pstage = '$stage', changed = '1' " .
            "where ebeln = '$ebeln' and ebelp = '$ebelp'");
        DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, cdate, internal, ctype, stage, cuser, cuser_name) values " .
            "('$ebeln', '$ebelp', '$cdate', 1, '$proposal->type', 'R', '" .
            Auth::user()->id . "', '" . Auth::user()->username . "')");
        $counter = 0;
        foreach ($proposal->items as $propitem) {
            $propitem->lifnr = SAP::alpha_input($propitem->lifnr);
            if (strlen($propitem->purch_curr) > 3) $propitem->purch_curr = substr($propitem->purch_curr, 0, 3);
            if (strlen($propitem->sales_curr) > 3) $propitem->sales_curr = substr($propitem->sales_curr, 0, 3);
            $propitem->mtext = str_replace("'", "\'", $propitem->mtext);
            DB::insert("insert into " . System::$table_pitemchg_proposals . " (type, ebeln, ebelp, cdate, pos, lifnr, idnlf, matnr, " .
                "mtext, lfdat, qty, qty_uom, purch_price, purch_curr, sales_price, sales_curr, infnr) values ('$proposal->type'," .
                "'$ebeln', '$ebelp', '$cdate', $counter, " .
                "'$propitem->lifnr', '$propitem->idnlf', '$propitem->matnr', '$propitem->mtext', '$propitem->lfdat', " .
                "'$propitem->quantity', '$propitem->quantity_unit', '$propitem->purch_price', '$propitem->purch_curr', " .
                "'$propitem->sales_price', '$propitem->sales_curr', '')");
            $counter++;
        }
        DB::commit();
        if (!is_null($mirror_user1 = System::r_ic($proposal->itemdata->stage, $proposal->itemdata->mirror_ebeln, $proposal->itemdata->mirror_ebelp))) {
            if (empty($mirror_user1)) {
                Log::error("Mirroring error (sendAck): no mirror user could be determined for " .
                    Auth::user()->sap_system . "/" . Auth::user()->id . "/" . Auth::user()->role .
                    " order/item " . $proposal->itemdata->ebeln . "/" . SAP::alpha_output($proposal->itemdata->ebelp));
            } else {
                self::acceptSplit($ebeln, $ebelp, $cdate, true);
                $currid = Auth::user()->id;
                $sap_system = Auth::user()->sap_system;
                Auth::loginUsingId($mirror_user1);
                System::init_mirror(Auth::user()->sap_system);
                $proposal->itemdata = DB::table(System::$table_pitems)->where(['ebeln' => $proposal->itemdata->mirror_ebeln,
                    'ebelp' => $proposal->itemdata->mirror_ebelp])->first();
                $mirror_order = DB::table(System::$table_porders)->where(['ebeln' => $proposal->itemdata->mirror_ebeln])->first();
                foreach ($proposal->items as $propitem) {
                    $propitem->lifnr = $mirror_order->lifnr;
                }
                self::processSplit($proposal);
                Auth::loginUsingId($currid);
                System::init($sap_system);
            }
        } else {
            return self::acceptSplit($ebeln, $ebelp, $cdate);
        }
    }

    static public function archiveItem($ebeln, $ebelp)
    {
        $result = Data::archiveItem($ebeln, $ebelp);
        Log::info("Order item $ebeln/$ebelp was manually archived by " . Auth::user()->id);
        return $result;
    }

    static public function unarchiveItem($ebeln, $ebelp)
    {
        $result = Data::unarchiveItem($ebeln, $ebelp);
        Log::info("Order item $ebeln/$ebelp was manually unarchived by " . Auth::user()->id);
        return $result;
    }

    static public function rollbackItem($ebeln, $ebelp)
    {
        $pitem = DB::table(System::$table_pitems)->where(["ebeln" => $ebeln, "ebelp" => $ebelp])->first();
        if ($pitem != null) {
            $pitemchgs = DB::select("select * from " . System::$table_pitemchg .
                " where ebeln = '$ebeln' and ebelp = '$ebelp' and ctype <> 'E' order by cdate desc");
            $pitemchg = null;
            $prevchg = null;
            if ($pitemchgs != null && count($pitemchgs) > 0) {
                $pitemchg = $pitemchgs[0];
                $prevchgs = DB::select("select * from " . System::$table_pitemchg .
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
                    DB::update("update " . System::$table_pitemchg . " set acknowledged = 2 where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$cdate'");
                    $message = __("Rolled back");
                    if ($pitemchg->ctype == "M") {
                        $message .= " " . __("from material code change");
                        $idnlf = $pitemchg->oldval;
                        DB::update("update " . System::$table_pitems . " set idnlf = '$idnlf' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                    }
                    if ($pitemchg->ctype == "P") {
                        $message .= " " . __("from price change");
                        $purch_price = explode(" ", $pitemchg->oldval)[0];
                        DB::update("update " . System::$table_pitems . " set purch_price = '$purch_price' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                    }
                    if ($pitemchg->ctype == "Q") {
                        $message .= " " . __("from quantity change");
                        $qty = explode(" ", $pitemchg->oldval)[0];
                        DB::update("update " . System::$table_pitems . " set qty = '$qty' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                    }
                    if ($pitemchg->ctype == "D") {
                        $message .= " " . __("from delivery date change");
                        $lfdat = $pitemchg->oldval;
                        DB::update("update " . System::$table_pitems . " set lfdat = '$lfdat' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                    }
                    if ($pitemchg->ctype == "J") {
                        $message .= " " . __("from ETA date change");
                        $etadt = $pitemchg->oldval;
                        DB::update("update " . System::$table_pitems . " set etadt = '$etadt' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                    }
                    $message .= " (previous = " . $pitemchg->oldval . ", current = " . $pitemchg->newval . ")";
                    DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name, reason) values " .
                        "('$ebeln','$ebelp', 'E', 'F', '$now', '" . Auth::user()->id . "','" . Auth::user()->username . "', '$message')");
                    DB::commit();
                    Log::info("Order item $ebeln/$ebelp was manually rolled back (type 0$ctype) by " . Auth::user()->id);
                    if (!is_null($mirror_user1 = System::d_ic($pitem->stage, $pitem->mirror_ebeln, $pitem->mirror_ebelp))) {
                        if (empty($mirror_user1)) {
                            Log::error("Mirroring error (rollbackItem): no mirror user could be determined for " .
                                Auth::user()->sap_system . "/" . Auth::user()->id . "/" . Auth::user()->role .
                                " order/item " . $pitem->ebeln . "/" . SAP::alpha_output($pitem->ebelp));
                        } else {
                            $currid = Auth::user()->id;
                            $sap_system = Auth::user()->sap_system;
                            Auth::loginUsingId($mirror_user1);
                            System::init_mirror(Auth::user()->sap_system);
                            self::rollbackItem($pitem->mirror_ebeln, $pitem->mirror_ebelp);
                            Auth::loginUsingId($currid);
                            System::init($sap_system);
                        }
                    }
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
                    DB::update("update " . System::$table_pitemchg . " set acknowledged = 2 where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$cdate'");
                    DB::update("update " . System::$table_pitems . " set stage = 'F', status = '', pstage = 'R' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                    DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name, reason) values " .
                        "('$ebeln','$ebelp', 'E', 'F', '$now', '" . Auth::user()->id . "','" . Auth::user()->username . "', '$message')");
                    DB::commit();
                    Log::info("Order item $ebeln/$ebelp was manually rolled back (type 1) by " . Auth::user()->id);
                    if (!is_null($mirror_user1 = System::d_ic($pitem->stage, $pitem->mirror_ebeln, $pitem->mirror_ebelp))) {
                        if (empty($mirror_user1)) {
                            Log::error("Mirroring error (rollbackItem): no mirror user could be determined for " .
                                Auth::user()->sap_system . "/" . Auth::user()->id . "/" . Auth::user()->role .
                                " order/item " . $pitem->ebeln . "/" . SAP::alpha_output($pitem->ebelp));
                        } else {
                            $currid = Auth::user()->id;
                            $sap_system = Auth::user()->sap_system;
                            Auth::loginUsingId($mirror_user1);
                            System::init_mirror(Auth::user()->sap_system);
                            self::rollbackItem($pitem->mirror_ebeln, $pitem->mirror_ebelp);
                            Auth::loginUsingId($currid);
                            System::init($sap_system);
                        }
                    }
                    return "OK";
                }

                if ((($pitem->stage == "C") && ($pitemchg->stage == "C")) &&
                    $pitem->pstage == "R" &&
                    (($pitem->status == "T") || ($pitem->status == "A")) &&
                    ($pitemchg->ctype == "O")) {
                    $message = __("Rolled back from proposal on") . " " . $cdate;
                    DB::beginTransaction();
                    DB::update("update " . System::$table_pitemchg . " set acknowledged = 2 where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$cdate'");
                    DB::update("update " . System::$table_pitems . " set stage = 'R', status = '$status', pstage = 'C' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                    DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name, reason) values " .
                        "('$ebeln','$ebelp', 'E', 'R', '$now', '" . Auth::user()->id . "','" . Auth::user()->username . "', '$message')");
                    DB::commit();
                    Log::info("Order item $ebeln/$ebelp was manually rolled back (type 2) by " . Auth::user()->id);
                    if (!is_null($mirror_user1 = System::d_ic($pitem->stage, $pitem->mirror_ebeln, $pitem->mirror_ebelp))) {
                        if (empty($mirror_user1)) {
                            Log::error("Mirroring error (rollbackItem): no mirror user could be determined for " .
                                Auth::user()->sap_system . "/" . Auth::user()->id . "/" . Auth::user()->role .
                                " order/item " . $pitem->ebeln . "/" . SAP::alpha_output($pitem->ebelp));
                        } else {
                            $currid = Auth::user()->id;
                            $sap_system = Auth::user()->sap_system;
                            Auth::loginUsingId($mirror_user1);
                            System::init_mirror(Auth::user()->sap_system);
                            self::rollbackItem($pitem->mirror_ebeln, $pitem->mirror_ebelp);
                            Auth::loginUsingId($currid);
                            System::init($sap_system);
                        }
                    }
                    return "OK";
                }

                if ((($pitem->stage == "Z") && (($pitemchg->stage == "Z") || ($pitemchg->stage == "R"))) &&
                    ($pitem->pstage == "F" || $pitem->pstage == " ") &&
                    ((($pitem->status == "X") && ($pitemchg->ctype == "X")) ||
                        (($pitem->status == "A") && ($pitemchg->ctype == "A"))
                    )
                ) {
                    $message = __("Rolled back");
                    if ($pitem->status == "T") {
                        $message .= " " . __("from supplier acceptance proposal on") . " " . $cdate;
                    } elseif ($pitem->status == "X") {
                        $message .= " " . __("from supplier rejection on") . " " . $cdate;
                    } elseif ($pitem->status == "A") {
                        $message .= " " . __("from supplier acceptance on") . " " . $cdate;
                    }
                    DB::beginTransaction();
                    DB::update("update " . System::$table_pitemchg . " set acknowledged = 2 where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$cdate'");
                    DB::update("update " . System::$table_pitems . " set stage = 'F', status = '', pstage = 'R' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                    DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name, reason) values " .
                        "('$ebeln','$ebelp', 'E', 'F', '$now', '" . Auth::user()->id . "','" . Auth::user()->username . "', '$message')");
                    DB::commit();
                    Log::info("Order item $ebeln/$ebelp was manually rolled back (type 3) by " . Auth::user()->id);
                    if (!is_null($mirror_user1 = System::d_ic($pitem->stage, $pitem->mirror_ebeln, $pitem->mirror_ebelp))) {
                        if (empty($mirror_user1)) {
                            Log::error("Mirroring error (rollbackItem): no mirror user could be determined for " .
                                Auth::user()->sap_system . "/" . Auth::user()->id . "/" . Auth::user()->role .
                                " order/item " . $pitem->ebeln . "/" . SAP::alpha_output($pitem->ebelp));
                        } else {
                            $currid = Auth::user()->id;
                            $sap_system = Auth::user()->sap_system;
                            Auth::loginUsingId($mirror_user1);
                            System::init_mirror(Auth::user()->sap_system);
                            self::rollbackItem($pitem->mirror_ebeln, $pitem->mirror_ebelp);
                            Auth::loginUsingId($currid);
                            System::init($sap_system);
                        }
                    }
                    return "OK";
                }

                if ((($pitem->stage == "Z") && (($pitemchg->stage == "Z") && ($pitemchg->acknowledged == 0))) &&
                    ($pitem->pstage == "R") &&
                    ((($pitem->status == "X") && ($pitemchg->ctype == "X")) ||
                        (($pitem->status == "A") && ($pitemchg->ctype == "A"))
                    )
                ) {
                    $message = __("Rolled back");
                    if ($pitem->status == "X") {
                        $message .= " " . __("from reference rejection on") . " " . $cdate;
                        $prevstatus = 'R';
                    } elseif ($pitem->status == "A") {
                        $message .= " " . __("from reference acceptance on") . " " . $cdate;
                        $prevstatus = 'T';
                    }
                    DB::beginTransaction();
                    DB::update("update " . System::$table_pitemchg . " set acknowledged = 2 where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$cdate'");
                    DB::update("update " . System::$table_pitems . " set stage = 'R', status = '$prevstatus', pstage = 'R' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                    DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name, reason) values " .
                        "('$ebeln','$ebelp', 'E', 'R', '$now', '" . Auth::user()->id . "','" . Auth::user()->username . "', '$message')");
                    DB::commit();
                    Log::info("Order item $ebeln/$ebelp was manually rolled back (type 4) by " . Auth::user()->id);
                    if (!is_null($mirror_user1 = System::d_ic($pitem->stage, $pitem->mirror_ebeln, $pitem->mirror_ebelp))) {
                        if (empty($mirror_user1)) {
                            Log::error("Mirroring error (rollbackItem): no mirror user could be determined for " .
                                Auth::user()->sap_system . "/" . Auth::user()->id . "/" . Auth::user()->role .
                                " order/item " . $pitem->ebeln . "/" . SAP::alpha_output($pitem->ebelp));
                        } else {
                            $currid = Auth::user()->id;
                            $sap_system = Auth::user()->sap_system;
                            Auth::loginUsingId($mirror_user1);
                            System::init_mirror(Auth::user()->sap_system);
                            self::rollbackItem($pitem->mirror_ebeln, $pitem->mirror_ebelp);
                            Auth::loginUsingId($currid);
                            System::init($sap_system);
                        }
                    }
                    return "OK";
                }

            }
        }
        return __("Item could not be rolled back automatically");
    }

    static public function processProposal2($proposal)
    {
        $cdate = now();
        return self::_processProposal2($proposal, $cdate);
    }

    static public function _processProposal2($proposal, $cdate)
    {
        $stage = $proposal->itemdata->stage;
        $ebeln = $proposal->itemdata->ebeln;
        $ebelp = $proposal->itemdata->ebelp;
        $_porder = DB::table(System::$table_porders)->where("ebeln", $ebeln)->first();
        $bukrs = $_porder->bukrs;
        $ekgrp = $_porder->ekgrp;
        $newstage = 'C';
        if (Auth::user()->role == "Furnizor") $newstage = 'R';
        if (isset($proposal->items)) {
            DB::beginTransaction();
            DB::update("update " . System::$table_pitems . " set stage = '$newstage', pstage = '$stage', status = 'T' " .
                "where ebeln = '$ebeln' and ebelp = '$ebelp'");
            DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, cdate, internal, ctype, stage, cuser, cuser_name) values " .
                "('$ebeln', '$ebelp', '$cdate', 1, '$proposal->type', '$newstage', '" .
                Auth::user()->id . "', '" . Auth::user()->username . "')");
            $counter = 0;
            foreach ($proposal->items as $propitem) {
                $propitem->lifnr = SAP::alpha_input($propitem->lifnr);
                if (strlen($propitem->purch_curr) > 3) $propitem->purch_curr = substr($propitem->purch_curr, 0, 3);
                if (strlen($propitem->sales_curr) > 3) $propitem->sales_curr = substr($propitem->sales_curr, 0, 3);
                $propitem->mtext = str_replace("'", "\'", $propitem->mtext);
                DB::insert("insert into " . System::$table_pitemchg_proposals . " (type, ebeln, ebelp, cdate, pos, lifnr, idnlf, matnr, " .
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
                $ctvusers = DB::select("select distinct id from " . System::$table_user_agent_clients . " where kunnr = '$pitem->kunnr'");
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
            if ((($proposal->lifnr == $proposal->itemdata->lifnr) &&
                    (trim($proposal->idnlf) == trim($proposal->itemdata->orig_idnlf)))
                || System::$mirroring) {
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
                DB::update("update " . System::$table_pitems . " set stage = 'Z', pstage = '$stage', status = 'A', " .
                    "idnlf = '$tmp_idnlf', mtext = '$tmp_mtext', matnr = '$tmp_matnr', lfdat = '$tmp_lfdat', " .
                    "qty = $tmp_qty, qty_uom = '$tmp_qty_unit', " .
                    "purch_price = '$tmp_purch_price', purch_curr = '$tmp_purch_curr', " .
                    "sales_price = '$tmp_sales_price', sales_curr = '$tmp_sales_curr' " .
                    "where ebeln = '$ebeln' and ebelp = '$ebelp'");
                DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, cdate, internal, ctype, stage, cuser, cuser_name) values " .
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
                        $proposal->purch_price, $proposal->purch_curr, $proposal->lfdat, $bukrs);
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
                    DB::update("update " . System::$table_pitems . " set stage = 'Z', pstage = '$tmp_stage', status = '$newstatus', " . $set_new_lifnr .
                        "idnlf = '$tmp_idnlf', purch_price = '$tmp_purch_price', " .
                        "qty = '$tmp_qty', lfdat = '$tmp_lfdat', matnr = '$tmp_matnr' " .
                        "where ebeln = '" . $proposal->itemdata->ebeln . "' and ebelp = '" . $proposal->itemdata->ebelp . "'");
                    DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, cdate, internal, ctype, stage, cuser, cuser_name, reason) values " .
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
                        $proposal->quantity, $proposal->quantity_unit, $proposal->lifnr, SAP::newMatnr($proposal->itemdata->matnr, $bukrs, $ekgrp),
                        $proposal->mtext, $proposal->idnlf, $proposal->purch_price, $proposal->purch_curr,
                        $proposal->sales_price, $proposal->sales_curr, $proposal->lfdat);
                    if (!empty(trim($result))) {
                        if (substr($result, 0, 2) == "OK")
                            $soitem = __("New sales order item") . " " . substr($result, 2);
                        else return $result;
                    }
                    DB::beginTransaction();
                    DB::update("update " . System::$table_pitems . " set stage = 'Z', pstage = '$tmp_stage', status = 'X', " . $set_new_lifnr .
                        "idnlf = '$tmp_idnlf', purch_price = '$tmp_purch_price', " .
                        "qty = '$tmp_qty', lfdat = '$tmp_lfdat', matnr = '$tmp_matnr' " .
                        "where ebeln = '" . $proposal->itemdata->ebeln . "' and ebelp = '" . $proposal->itemdata->ebelp . "'");
                    DB::insert("insert into " . System::$table_pitemchg . " (ebeln, ebelp, cdate, internal, ctype, stage, oldval, cuser, cuser_name, reason) values " .
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
                        $ctvusers = DB::select("select distinct id from " . System::$table_user_agent_clients . " where kunnr = '$kunnr'");
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

        if (!is_null($mirror_user1 = System::d_ic($proposal->itemdata->stage, $proposal->itemdata->mirror_ebeln, $proposal->itemdata->mirror_ebelp))) {
            if (empty($mirror_user1)) {
                Log::error("Mirroring error (processProposal2): no mirror user could be determined for " .
                    Auth::user()->sap_system . "/" . Auth::user()->id . "/" . Auth::user()->role .
                    " order/item " . $proposal->itemdata->ebeln . "/" . SAP::alpha_output($proposal->itemdata->ebelp));
            } else {
                $currid = Auth::user()->id;
                $sap_system = Auth::user()->sap_system;
                Auth::loginUsingId($mirror_user1);
                System::init_mirror(Auth::user()->sap_system);
                $mirror_order = DB::table(System::$table_porders)->where(['ebeln' => $proposal->itemdata->mirror_ebeln])->first();
                $proposal->itemdata = DB::table(System::$table_pitems)->where(['ebeln' => $proposal->itemdata->mirror_ebeln,
                    'ebelp' => $proposal->itemdata->mirror_ebelp])->first();
                if (isset($proposal->items) && !empty($proposal->items)) {
                    foreach ($proposal->items as $propitem) {
                        $propitem->lifnr = $mirror_order->lifnr;
                    }
                } else {
                    $proposal->lifnr = $mirror_order->lifnr;
                }
                self::_processProposal2($proposal, $cdate);
                Auth::loginUsingId($currid);
                System::init($sap_system);
            }
        } elseif (!is_null($mirror_user1 = System::r_ic($proposal->itemdata->stage, $proposal->itemdata->mirror_ebeln, $proposal->itemdata->mirror_ebelp))) {
            if (empty($mirror_user1)) {
                Log::error("Mirroring error (processProposal2): no mirror user could be determined for " .
                    Auth::user()->sap_system . "/" . Auth::user()->id . "/" . Auth::user()->role .
                    " order/item " . $proposal->itemdata->ebeln . "/" . SAP::alpha_output($proposal->itemdata->ebelp));
            } else {
                $currid = Auth::user()->id;
                $sap_system = Auth::user()->sap_system;
                Auth::loginUsingId($mirror_user1);
                System::init_mirror(Auth::user()->sap_system);
                $mirror_order = DB::table(System::$table_porders)->where(['ebeln' => $proposal->itemdata->mirror_ebeln])->first();
                $proposal->itemdata = DB::table(System::$table_pitems)->where(['ebeln' => $proposal->itemdata->mirror_ebeln,
                    'ebelp' => $proposal->itemdata->mirror_ebelp])->first();
                if (isset($proposal->items) && !empty($proposal->items)) {
                    foreach ($proposal->items as $propitem) {
                        $propitem->lifnr = $mirror_order->lifnr;
                    }
                } else {
                    $proposal->lifnr = $mirror_order->lifnr;
                }
                self::_processProposal2($proposal, $cdate);
                Auth::loginUsingId($currid);
                System::init($sap_system);
            }
        }
        return "";
    }

    static function acknowledgeByBell($ebeln, $ebelp, $mode)
    {
        $pmfa_date = now();
        $item = DB::table(System::$table_pitems)->where([["ebeln", '=', $ebeln], ["ebelp", '=', $ebelp]])->first();
        if (empty($item)) return;
        switch ($mode) {
            case "A": // proposal accepted by CTV, acknowledged by supplier
                DB::beginTransaction();
                DB::update("update " . System::$table_pitems . " set pmfa = '', pmfa_date = null where ebeln = '$ebeln' and ebelp = '$ebelp'");
                DB::commit();
            case "B": // proposal rejected by CTV, acknowledged by supplier
                DB::beginTransaction();
                DB::update("update " . System::$table_pitems . " set pmfa = '', pmfa_date = null where ebeln = '$ebeln' and ebelp = '$ebelp'");
                DB::commit();
                $result = Data::archiveItem($ebeln, $ebelp);
                Log::info("Archiving LATE $ebeln/$ebelp (" . $item->vbeln . "/" . $item->posnr . "): " . $result);
                break;
            case "C": // item rejected by supplier, acknowledged by CTV
                DB::beginTransaction();
                DB::update("update " . System::$table_pitems . " set pmfa = '', pmfa_date = null where ebeln = '$ebeln' and ebelp = '$ebelp'");
                DB::commit();
            case "D": // ETA modified, acknowledged by CTV
                DB::beginTransaction();
                DB::update("update " . System::$table_pitems . " set pmfa = '', pmfa_date = null where ebeln = '$ebeln' and ebelp = '$ebelp'");
                DB::commit();
            case "E": // PNAD notification, acknowledged by CTV
                DB::beginTransaction();
                DB::update("update " . System::$table_pitems . " set pmfa = '', pmfa_date = null where ebeln = '$ebeln' and ebelp = '$ebelp'");
                DB::commit();
            case "F": // Backorder without delivery date
                if (Auth::user()->role == "CTV") {
                    $item->pmfa_status = $item->pmfa_status | 1;
                } elseif (Auth::user()->role == "Referent") {
                    $item->pmfa_status = $item->pmfa_status | 2;
                } elseif (Auth::user()->role == "Furnizor") {
                    $item->pmfa_status = $item->pmfa_status | 4;
                }
                DB::beginTransaction();
                DB::update("update " . System::$table_pitems . " set pmfa_status = $item->pmfa_status where ebeln = '$ebeln' and ebelp = '$ebelp'");
                DB::commit();
        }
    }
}
