<?php

namespace App\Materom;

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
        foreach($data as $porder) {
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
        foreach($porder->items as $pitem) {
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
        foreach($pitem->changes as $pitemchg) {
            if ((Auth::user()->role == 'Furnizor') && ($pitemchg->internal == 1)) continue;
            $outpitemchg = $pitemchg;
            unset($outpitemchg->internal);
            $pitemchgs[] = $outpitemchg;
        }
        return json_encode($pitemchgs);
    }

    public static function sortMessages($type){
        Session::put("message-sorting", $type);
        return "";
    }

    public static function replyMessage($ebeln, $ebelp, $cdate, $message)
    {

        $stage = (Auth::user()->role)[0];
        if ($stage == 'F') $stage = 'R';
        if ($stage == 'R') $stage = 'F';
        if ($stage == 'C') $stage = 'R';

        DB::update("update pitemchg set acknowledged = '1' where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$cdate'");
        DB::insert("insert into pitemchg (ebeln, ebelp, stage, ctype, reason, cuser, cuser_name) values " .
            "('$ebeln','$ebelp','$stage','S','$message', '" . Auth::user()->id . "', '" . Auth::user()->username . "')");
        return "";
    }

    static function sendAck($ebeln,$ebelp,$cdate){
        DB::update("update pitemchg set acknowledged = '1' where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$cdate'");
        return "";
    }

    public static function modifyProposals($ebeln,$ebelp,$cdate,$pos,$lifnr,$lifnr_name,$idnlf,$mtext,$matnr,$purch_price,$purch_curr,$sales_price,$sales_curr){
        $maybe = DB::select("select * from pitemchg_proposals where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$cdate' and pos = '$pos'");

        if(count($maybe) > 0){
            //edit existing
            DB::update("update pitemchg_proposals set 
                                    lifnr = '$lifnr',
                                    lifnr_name = '$lifnr_name',  
                                    idnlf = '$idnlf',
                                    mtext = '$mtext',
                                    matnr = '$matnr',
                                    purch_price = '$purch_price',
                                    purch_curr = '$purch_curr',
                                    sales_price = '$sales_price',
                                    sales_curr = '$sales_curr',
                                    where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$cdate' and pos = '$pos'");
        } else {
            //add new
            $lastFind = DB::select("select * from pitemchg_proposals where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$cdate'");
            $pos = count($lastFind) + 1;
            DB::insert("insert into pitemchg_proposals (ebeln,ebelp,cdate,pos,lifnr,lifnr_name,idnlf,mtext,matnr,purch_price,purch_curr,sales_price,sales_curr) values 
                              ('$ebeln','$ebelp','$cdate','$pos','$lifnr','$lifnr_name','$idnlf','$mtext','$matnr','$purch_price','$purch_curr','$sales_price','$sales_curr')");
        }
    }

    public static function acceptItemCHG($ebeln, $ebelp, $type)
    {
        $item_changed = DB::table("pitems")->where([['ebeln', '=', $ebeln], ['ebelp', '=', $ebelp]])->value('changed') == "1";
        if (!$item_changed) {
            DB::update("update pitems set stage = 'Z', status = 'A' where ebeln = '$ebeln' and ebelp = '$ebelp'");
            SAP::acknowledgePOItem($ebeln, $ebelp, " ");
        } else {
            if (Auth::user()->role == "Furnizor") {
                DB::update("update pitems set stage = 'R', status = 'T' where ebeln = '$ebeln' and ebelp = '$ebelp'");
            } else {
                DB::update("update pitems set stage = 'Z', status = 'A' where ebeln = '$ebeln' and ebelp = '$ebelp'");
                SAP::savePOItem($ebeln, $ebelp);
            }
        }

        $stage = (Auth::user()->role)[0];
        if ($stage == 'F') $stage = 'R';
        if ($stage == 'R') $stage = 'R';
        if ($stage == 'C') $stage = 'R';

        DB::insert("insert into pitemchg (ebeln, ebelp, ctype, stage, cdate, cuser, cuser_name) values " .
                          "('$ebeln','$ebelp','A', '$stage', CURRENT_TIMESTAMP, '" . Auth::user()->id . "','" . Auth::user()->username . "')");
        return "";
    }

    public static function cancelItem($ebeln, $item, $category, $reason, $new_status, $new_stage)
    {
        $old_stage = DB::table("pitems")->where([['ebeln', '=', $ebeln], ['ebelp', '=', $item]])->value('stage');
        DB::beginTransaction();
        DB::update("update pitems set status = '$new_status', pstage = '$old_stage', stage = '$new_stage' where ebeln = '$ebeln' and ebelp = '$item'");
        DB::insert("insert into pitemchg (ebeln, ebelp, ctype, cdate, cuser, cuser_name, oldval, reason) values ".
                          "('$ebeln','$item','X', CURRENT_TIMESTAMP,'" . Auth::user()->id . "','" . Auth::user()->username . "', '$category', '$reason')");
        DB::commit();
        if ($new_status = 'X') {
            SAP::acknowledgePOItem($ebeln, $item, " ");
            SAP::rejectPOItem($ebeln, $item);
        }
        return "";
    }

    public static function itemsOfOrder($type,$order,$history){
        $items_table = $history != 2 ? "pitems" : "pitems_arch";
        if($type == "S"){
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

}