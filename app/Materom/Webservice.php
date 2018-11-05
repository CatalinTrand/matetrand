<?php

namespace App\Materom;

use App\Materom\SAP\MasterData;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;


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
        $find = DB::select("select * from users_sel where id = '$userid' and mfrnr = '$mfrnr'");
        if (count($find) == 0) {
            $mfrnr_name = SAP\MasterData::getLifnrName($mfrnr);
            if (is_array($mfrnr_name) || strlen(trim($mfrnr_name)) == 0) return __('Manufacturer does not exist');
            DB::insert("insert into users_sel (id, mfrnr, mfrnr_name) values ('$userid','$mfrnr', '$mfrnr_name')");
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
        $agent_name = MasterData::getKunnrName($agent);
        if (empty($agent_name)) return "No such user!";

        $find = DB::select("select * from users_agent where id = '$userid' and agent = '$agent'");
        if (count($find) == 0) {
            $agent = SAP::alpha_input($agent);
            DB::insert("insert into users_agent (id, agent) values ('$userid','$agent')");
            return "";
        } else return __("This agent is already set for user");
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
        if ($type == 'S') return self::getSOSubTree($data);
        if ($type == 'P') return self::getPOSubTree($data);
        if ($type == 'I') return self::getPOItemSubTree($data, $item);
    }

    public static function getSOSubTree($data)
    {
        $porders = array();
        if ($data == null) return json_encode($porders);
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
            $pitems[] = $outpitem;
        }
        return json_encode($pitems);
    }

    public static function getPOItemSubTree($data, $itemno)
    {
        $pitemchgs = array();
        if ($data == null) return json_encode($pitemchgs);
        $pitem = reset($data)->items[$itemno];
        foreach ($pitem->changes as $pitemchg) {
            if ((Auth::user()->role == 'Furnizor') && ($pitemchg->internal == 1)) continue;
            $outpitemchg = $pitemchg;
            unset($outpitemchg->internal);
            $pitemchgs[] = $outpitemchg;
        }
        return json_encode($pitemchgs);
    }

    public static function sortMessages($type)
    {
        Session::put("message-sorting", $type);
        return "";
    }

    public static function replyMessage($ebeln, $ebelp, $cdate, $message)
    {

        $stage = (Auth::user()->role)[0];
        if ($stage == 'A') $stage = 'F';
        if ($stage == 'F') $stage = 'R';
        if ($stage == 'R') $stage = 'F';
        if ($stage == 'C') $stage = 'R';

        DB::update("update pitemchg set acknowledged = '1' where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$cdate'");
        DB::insert("insert into pitemchg (ebeln, ebelp, stage, ctype, reason, cuser, cuser_name) values " .
            "('$ebeln','$ebelp','$stage','E','$message', '" . Auth::user()->id . "', '" . Auth::user()->username . "')");
        return "";
    }

    static function readPOItem($order, $item)
    {
        $porder = DB::table("porders")->where("ebeln", $order)->first();
        $pitem = DB::table("pitems")->where([["ebeln", '=', $order], ["ebelp", '=', $item]])->first();
        $pitem->lifnr = $porder->lifnr;
        $pitem->lifnr_name = MasterData::getLifnrName($porder->lifnr);
        return json_encode($pitem);
    }

    static function sendAck($ebeln, $ebelp, $cdate)
    {
        DB::update("update pitemchg set acknowledged = '1' where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$cdate'");
        return "";
    }

    public static function readProposals($ebeln, $ebelp)
    {
        $proposal = DB::table("pitemchg")->where([["ebeln", "=", $ebeln], ["ebelp", "=", $ebelp], ["ctype", "=", 'O']])->orderBy("cdate", "desc")->first();
        $proposals = DB::select("select * from pitemchg_proposals where ebeln = '$proposal->ebeln' and ebelp = '$proposal->ebelp' and cdate = '$proposal->cdate' and type = 'O'");
        foreach ($proposals as $proposal) {
            $proposal->lifnr_name = MasterData::getLifnrName($proposal->lifnr);
        }
        return json_encode($proposals);
    }

    public static function acceptItemCHG($ebeln, $ebelp, $type)
    {
        $item = DB::table("pitems")->where([['ebeln', '=', $ebeln], ['ebelp', '=', $ebelp]])->first();
        $item_changed = $item->changed == "1";
        if (!$item_changed) {
            SAP::acknowledgePOItem($ebeln, $ebelp, " ");
            DB::update("update pitems set stage = 'Z', status = 'A' where ebeln = '$ebeln' and ebelp = '$ebelp'");
            DB::insert("insert into pitemchg (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name) values " .
                "('$ebeln','$ebelp', 'A', 'R', CURRENT_TIMESTAMP, '" . Auth::user()->id . "','" . Auth::user()->username . "')");
        } else {
            if ($item->stage == 'F') {
                DB::update("update pitems set stage = 'R', status = 'T' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                DB::insert("insert into pitemchg (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name) values " .
                    "('$ebeln','$ebelp', 'T', 'R', CURRENT_TIMESTAMP, '" . Auth::user()->id . "','" . Auth::user()->username . "')");
            } else {
                SAP::savePOItem($ebeln, $ebelp);
                DB::update("update pitems set stage = 'Z', status = 'A' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                DB::insert("insert into pitemchg (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name) values " .
                    "('$ebeln','$ebelp', 'A', 'R', CURRENT_TIMESTAMP, '" . Auth::user()->id . "','" . Auth::user()->username . "')");
            }
        }

        return "";
    }

    public static function cancelItem($ebeln, $item, $category, $reason, $new_status, $new_stage)
    {
        $old_stage = DB::table("pitems")->where([['ebeln', '=', $ebeln], ['ebelp', '=', $item]])->value('stage');
        DB::beginTransaction();
        DB::update("update pitems set status = '$new_status', pstage = '$old_stage', stage = '$new_stage' where ebeln = '$ebeln' and ebelp = '$item'");
        DB::insert("insert into pitemchg (ebeln, ebelp, ctype, cdate, cuser, cuser_name, oldval, reason) values " .
            "('$ebeln','$item', '$new_status', CURRENT_TIMESTAMP,'" . Auth::user()->id . "','" . Auth::user()->username . "', '$category', '$reason')");
        DB::commit();
        if ($new_status == 'X') {
            SAP::acknowledgePOItem($ebeln, $item, " ");
            SAP::rejectPOItem($ebeln, $item);
        }
        return "";
    }

    public static function itemsOfOrder($type, $order, $history)
    {
        $items_table = $history != 2 ? "pitems" : "pitems_arch";
        if ($type == "S") {
            return DB::select("select * from $items_table where vbeln = '$order'");
        } else {
            return DB::select("select * from $items_table where ebeln = '$order'");
        }
    }

    public static function doChangeItem($column, $value, $valuehlp, $oldvalue, $ebeln, $ebelp)
    {
        DB::beginTransaction();
        DB::update("update pitems set $column = '$value', changed = '1' where ebeln = '$ebeln' and ebelp = '$ebelp'");
        DB::update("update porders set changed = '1' where ebeln = '$ebeln'");
        if ($column == 'idnlf') $type = 'M';
        if ($column == 'qty') $type = 'Q';
        if ($column == 'lfdat') $type = 'D';
        if ($column == 'purch_price') $type = 'P';
        $newval = trim($value . " " . $valuehlp);
        DB::insert("insert into pitemchg (ebeln,ebelp,ctype,cdate,cuser,cuser_name,reason,oebelp,oldval,newval) values ('$ebeln','$ebelp','$type',CURRENT_TIMESTAMP,'" . Auth::user()->id . "','" . Auth::user()->username . "','','','$oldvalue','$newval')");
        DB::commit();
        return "";
    }

    public static function createPurchReq($lifnr, $idnlf, $mtext, $matnr, $qty, $unit, $price, $curr, $deldate, $infnr)
    {
        return SAP::createPurchReq($lifnr, $idnlf, $mtext, $matnr, $qty, $unit, $price, $curr, $deldate, $infnr);
    }

    static public function getVendorUsers($lifnr)
    {
        $users = DB::select("select * from users where role = 'Furnizor' and lifnr ='$lifnr'");
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
        $users = DB::select("select * from users where role = 'CTV'");
        $result = '[ ';
        foreach ($users AS $user) {
            if ($user->active == 1) $user_active = 'X'; else $user_active = '';
            $str = '"SRM_USER":"' . $user->id . '", ' .
                '"SRM_USER_NAME":"' . $user->username . '", ' .
                '"ACTIVE":"' . $user_active . '", ' .
                '"EMAIL":"' . $user->email . '", ' .
                '"SAP_USER":"' . $user->sapuser . '", ' .
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
            'created_at' => Carbon::now()->getTimestamp()
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

    static public function sendInquiry($from,$ebeln,$ebelp,$text){
        $porder = DB::select("select * from porders where ebeln = '$ebeln'")[0];
        if($from[0] != 'V')
            $stage = 'R';
        else
            $stage = 'F';

        $uId = Auth::id();
        $uName = Auth::user()->username;
        DB::insert("insert into pitemchg (ebeln, ebelp,ctype,cuser,cuser_name,stage,reason) VALUES ('$ebeln','$ebelp','E','$uId','$uName','$stage','$text')");
        return "";
    }

    static public function processProposal($proposal)
    {
        $result = new \stdClass();
        $cdate = now();
        $stage = $proposal->itemdata->stage;
        $ebeln = $proposal->itemdata->ebeln;
        $ebelp = $proposal->itemdata->ebelp;
        if (isset($proposal->items)) {
            DB::beginTransaction();
            DB::update("update pitems set stage = 'C', pstage = '$stage' " .
                "where ebeln = '$ebeln' and ebelp = '$ebelp'");
            DB::insert("insert into pitemchg (ebeln, ebelp, cdate, internal, ctype, stage, cuser, cuser_name) values " .
                "('$ebeln', '$ebelp', '$cdate', 1, 'O', 'C', '" .
                Auth::user()->id . "', '" . Auth::user()->username . "')");
            $counter = 0;
            foreach ($proposal->items as $propitem) {
                $propitem->lifnr = SAP::alpha_input($propitem->lifnr);
                DB::insert("insert into pitemchg_proposals (type, ebeln, ebelp, cdate, pos, lifnr, idnlf, matnr, " .
                    "mtext, lfdat, qty, qty_uom, purch_price, purch_curr, sales_price, sales_curr, infnr) values ('$proposal->type'," .
                    "'$ebeln', '$ebelp', '$cdate', $counter, " .
                    "'$propitem->lifnr', '$propitem->idnlf', '$propitem->matnr', '$propitem->mtext', '$propitem->lfdat', " .
                    "'$propitem->quantity', '$propitem->quantity_unit', '$propitem->purch_price', '$propitem->purch_curr', " .
                    "'$propitem->sales_price', '$propitem->sales_curr', '')");
                $counter++;
            }
            DB::commit();
        } else {
            $proposal->lifnr = SAP::alpha_input($proposal->lifnr);
            if ($proposal->lifnr == $proposal->itemdata->lifnr) {
                // keeping the same supplier for stock orders, just update PO
                $tmp_idnlf = $proposal->idnlf;
                $tmp_mtext = $proposal->mtext;
                $tmp_matnr = $proposal->matnr;
                $tmp_lfdat = $proposal->lfdat;
                $tmp_qty = $proposal->quantity;
                $tmp_qty_unit = $proposal->quantity_unit;
                $tmp_purch_price = $proposal->purch_price;
                $tmp_purch_curr = $proposal->purch_curr;
                DB::beginTransaction();
                DB::update("update pitems set stage = 'Z', pstage = '$stage', status = 'A', " .
                    "idnlf = '$tmp_idnlf', mtext = '$tmp_mtext', matnr = '$tmp_matnr', lfdat = '$tmp_lfdat', " .
                    "qty = $tmp_qty, qty_uom = '$tmp_qty_unit', " .
                    "purch_price = '$tmp_purch_price', purch_curr = '$tmp_purch_curr' " .
                    "where ebeln = '$ebeln' and ebelp = '$ebelp'");
                DB::insert("insert into pitemchg (ebeln, ebelp, cdate, internal, ctype, stage, cuser, cuser_name) values " .
                    "('$ebeln', '$ebelp', '$cdate', 1, 'A', 'Z', '" .
                    Auth::user()->id . "', '" . Auth::user()->username . "')");
                $result  = SAP::savePOItem($ebeln, $ebelp);
                if (!empty(trim($result))) {
                    DB::rollBack();
                    return $result;
                }
                DB::commit();
            } else {
                // change supplier or change in SO
                SAP::rejectPOItem($proposal->itemdata->ebeln, $proposal->itemdata->ebelp);
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
                    $tmp_idnlf = $proposal->itemdata->orig_idnlf;
                    $tmp_purch_price = $proposal->itemdata->orig_purch_price;
                    $tmp_qty = $proposal->itemdata->orig_qty;
                    $tmp_lfdat = $proposal->itemdata->orig_lfdat;
                    $tmp_matnr = $proposal->itemdata->orig_matnr;
                        DB::beginTransaction();
                    DB::update("update pitems set stage = 'Z', pstage = '$tmp_stage', status = 'X', " .
                        "idnlf = '$tmp_idnlf', purch_price = '$tmp_purch_price', " .
                        "qty = '$tmp_qty', lfdat = '$tmp_lfdat', matnr = '$tmp_matnr' " .
                        "where ebeln = '" . $proposal->itemdata->ebeln . "' and ebelp = '" . $proposal->itemdata->ebelp . "'");
                    DB::insert("insert into pitemchg (ebeln, ebelp, cdate, internal, ctype, stage, cuser, cuser_name, reason) values " .
                        "('" . $proposal->itemdata->ebeln . "', '" . $proposal->itemdata->ebelp . "', '$cdate', 1, 'X', 'Z', '" .
                        Auth::user()->id . "', '" . Auth::user()->username . "', '$banfn')");
                    DB::commit();
                } else {
                    $tmp_stage = $proposal->itemdata->stage;
                    $tmp_idnlf = $proposal->itemdata->orig_idnlf;
                    $tmp_purch_price = $proposal->itemdata->orig_purch_price;
                    $tmp_qty = $proposal->itemdata->orig_qty;
                    $tmp_lfdat = $proposal->itemdata->orig_lfdat;
                    $tmp_matnr = $proposal->itemdata->orig_matnr;
                    $result = SAP::processSOItem($proposal->itemdata->vbeln, $proposal->itemdata->posnr,
                        $proposal->quantity, $proposal->quantity_unit, $proposal->lifnr, $proposal->matnr,
                        $proposal->mtext, $proposal->idnlf, $proposal->purch_price, $proposal->purch_curr,
                        $proposal->sales_price, $proposal->sales_curr, $proposal->lfdat);
                    if (!empty(trim($result))) {
                        if (substr($result, 0, 2) == "OK")
                            $soitem = __("New sales order item ") . substr($result, 2);
                        else return $result;
                    }
                    DB::beginTransaction();
                    DB::update("update pitems set stage = 'Z', pstage = '$tmp_stage', status = 'X', " .
                        "idnlf = '$tmp_idnlf', purch_price = '$tmp_purch_price', " .
                        "qty = '$tmp_qty', lfdat = '$tmp_lfdat', matnr = '$tmp_matnr' " .
                        "where ebeln = '" . $proposal->itemdata->ebeln . "' and ebelp = '" . $proposal->itemdata->ebelp . "'");
                    DB::insert("insert into pitemchg (ebeln, ebelp, cdate, internal, ctype, stage, oldval, cuser, cuser_name, reason) values " .
                        "('" . $proposal->itemdata->ebeln . "', '" . $proposal->itemdata->ebelp . "', '$cdate', 0, 'X', 'Z', 'C', '" .
                        Auth::user()->id . "', '" . Auth::user()->username . "', '$soitem')");
                    DB::commit();
               }
            }
        }
        return "";
    }

    static public function acceptProposal($ebeln, $ebelp, $cdate, $pos)
    {
        $proposal = DB::table("pitemchg_proposals")->where([
                                                        ["type", "=", "O"],
                                                        ["ebeln", "=", $ebeln],
                                                        ["ebelp", "=", $ebelp],
                                                        ["cdate", "=", $cdate],
                                                        ["pos", "=", $pos]
                                                    ])->first();
        $item = DB::table("pitems")->where([["ebeln", "=", $ebeln],["ebelp", "=", $ebelp]])->first();
        $porder = DB::table("porders")->where("ebeln", $ebeln)->first();
        if ($proposal->lifnr == $porder->lifnr) {
            // keeping the same supplier for stock orders, just update PO
            $tmp_idnlf = $proposal->idnlf;
            $tmp_mtext = $proposal->mtext;
            $tmp_matnr = $proposal->matnr;
            $tmp_lfdat = $proposal->lfdat;
            $tmp_qty = $proposal->qty;
            $tmp_qty_unit = $proposal->qty_uom;
            $tmp_purch_price = $proposal->purch_price;
            $tmp_purch_curr = $proposal->purch_curr;
            $now = now();
            DB::beginTransaction();
            DB::update("update pitems set stage = 'Z', pstage = '" . $item->stage . "', status = 'A', " .
                "idnlf = '$tmp_idnlf', mtext = '$tmp_mtext', matnr = '$tmp_matnr', lfdat = '$tmp_lfdat', " .
                "qty = $tmp_qty, qty_uom = '$tmp_qty_unit', " .
                "purch_price = '$tmp_purch_price', purch_curr = '$tmp_purch_curr' " .
                "where ebeln = '$ebeln' and ebelp = '$ebelp'");
            DB::insert("insert into pitemchg (ebeln, ebelp, cdate, internal, ctype, stage, cuser, cuser_name) values " .
                "('$ebeln', '$ebelp', '$now', 1, 'A', 'Z', '" .
                Auth::user()->id . "', '" . Auth::user()->username . "')");
            $result  = SAP::savePOItem($ebeln, $ebelp);
            if (!empty(trim($result))) {
                DB::rollBack();
                return $result;
            }
            DB::commit();
            return "";
        }

        SAP::rejectPOItem($ebeln, $ebelp);
        $result = SAP::processSOItem($item->vbeln, $item->posnr,
            $proposal->qty, $proposal->qty_uom, $proposal->lifnr, $proposal->matnr,
            $proposal->mtext, $proposal->idnlf, $proposal->purch_price, $proposal->purch_curr,
            $proposal->sales_price, $proposal->sales_curr, $proposal->lfdat);
        $soitem = "";
        if (!empty(trim($result))) {
            if (substr($result, 0, 2) == "OK")
                $soitem = __("New sales order item ") . substr(trim($result), 2). " from item " . ltrim($item->posnr, "0");
            else return $result;
        }
        DB::beginTransaction();
        DB::update("update pitems set stage = 'Z', pstage = '$item->stage', status = 'A' " .
            "where ebeln = '$ebeln' and ebelp = '$ebelp'");
        $now = now();
        DB::insert("insert into pitemchg (ebeln, ebelp, cdate, internal, ctype, stage, oldval, cuser, cuser_name, reason) values " .
            "('$ebeln', '$ebelp', '$now', 1, 'A', 'Z', 'C', '" .
            Auth::user()->id . "', '" . Auth::user()->username . "', '$soitem')");
        DB::commit();
        return "";
    }

    static public function rejectProposal($ebeln, $ebelp, $cdate)
    {
        $item = DB::table("pitems")->where([["ebeln", "=", $ebeln],["ebelp", "=", $ebelp]])->first();
        SAP::rejectPOItem($ebeln, $ebelp);
        $result = SAP::rejectSOItem($item->vbeln, $item->posnr);
        $soitem = __("Rejected sales order item "). SAP::alpha_output($item->vbeln) . '/' . ltrim($item->posnr, "0");
        if (!empty(trim($result))) return;
        DB::beginTransaction();
        DB::update("update pitems set stage = 'Z', pstage = '$item->stage', status = 'X' " .
            "where ebeln = '$ebeln' and ebelp = '$ebelp'");
        $now = now();
        DB::insert("insert into pitemchg (ebeln, ebelp, cdate, internal, ctype, stage, oldval, cuser, cuser_name, reason) values " .
            "('$ebeln', '$ebelp', '$now', 1, 'X', 'Z', 'C', '" .
            Auth::user()->id . "', '" . Auth::user()->username . "', '$soitem')");
        DB::commit();
        return "";
    }

}