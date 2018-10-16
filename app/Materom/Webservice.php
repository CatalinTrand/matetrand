<?php

namespace App\Materom;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;


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

    public static function getAllItems($history){
        $items_table = $history == 1 ? "pitems" : "pitems_arch";
        $links = DB::select("select * from ". $items_table);
        $result = "";
        foreach ($links as $link){
            if(strcmp($result,"") != 0)
                $result = "$link->ebeln#$link->ebelp" . "=" . $result;
            else
                $result = "$link->ebeln#$link->ebelp";
        }
        return $result;
    }

    static function new_simple_chg($ebeln,$ebelp,$ctype,$stage,$cuser,$cuser_name,$oldval,$newval,$reason){
        DB::insert("insert into pitemchg (ebeln,ebelp,cdate,internal,ctype,stage,cuser,cuser_name,oldval,newval,reason) values ('$ebeln','$ebelp',NOW(),'0','$ctype','$stage','$cuser','$cuser_name','$oldval','$newval','$reason')");
    }

    static function replyMsg($ebeln,$ebelp,$cdate,$idnlf,$lfdat,$qty,$purch_price,$reason){

        $oldItem = DB::select("select * from pitems where ebeln = '$ebeln' and ebelp = '$ebelp'")[0];

        $uid = Auth::id();
        $uname = Auth::user()->username;
        $stage = (strtoupper($uname))[0];
        if($stage == 'A') $stage = 'R';
        $ctype = "M";

        if(strcmp($oldItem->idnlf,$idnlf) != 0){
            self::new_simple_chg($ebeln,$ebelp,$ctype,$stage,$uid,$uname,$oldItem->idnlf,$idnlf,$reason);
        }

        if(strcmp($oldItem->lfdat,$lfdat) != 0){

            self::new_simple_chg($ebeln,$ebelp,$ctype,$stage,$uid,$uname,$oldItem->lfdat,$lfdat,$reason);
        }

        if(strcmp($oldItem->qty,$qty) != 0){
            self::new_simple_chg($ebeln,$ebelp,$ctype,$stage,$uid,$uname,$oldItem->qty,$qty,$reason);
        }

        if(strcmp($oldItem->purch_price,$purch_price) != 0){
            self::new_simple_chg($ebeln,$ebelp,$ctype,$stage,$uid,$uname,$oldItem->purch_price,$purch_price,$reason);
        }


        DB::update("update pitemchg set acknowledged = '1' where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$cdate'");
        DB::update("update pitems set idnlf = '$idnlf', lfdat = '$lfdat', qty = '$qty', purch_price = '$purch_price' where ebeln = '$ebeln' and ebelp = '$ebelp'");

        return "";
    }

    static function sendAck($ebeln,$ebelp,$cdate){
        DB::update("update pitemchg set acknowledged = '1' where ebeln = '$ebeln' and ebelp = '$ebelp' and cdate = '$cdate'");
        return "";
    }

    static function straightAccept($ebeln,$id){
        $links = DB::select("select * from pitemchg where ebeln = '$ebeln' and ebelp = '$id' order by cdate");

        if(count($links) == 0 || strcmp($links[count($links) - 1]->cuser,Auth::user()->id) != 0)
            return true;

        return false;
    }

    public static function acceptItemCHG($ebeln, $id, $type)
    {
        if(self::straightAccept($ebeln,$id))
            DB::update("update pitems set stage = 'A' where ebeln = '$ebeln' and ebelp = '$id'");
        else
            DB::update("update pitems set stage = 'T' where ebeln = '$ebeln' and ebelp = '$id'");
        DB::insert("insert into pitemchg (ebeln,ebelp,ctype,cdate,cuser,cuser_name,reason) values ('$ebeln','$id','A',CURRENT_TIMESTAMP,'" . Auth::user()->id . "','" . Auth::user()->username . "','')");
        SAP::acknowledgePOItem($ebeln, $id, " ");
        return "";
    }

    public static function cancelItem($ebeln, $id, $type, $category, $reason)
    {
        DB::update("update pitems set stage = 'X' where ebeln = '$ebeln' and ebelp = '$id'");
        DB::insert("insert into pitemchg (ebeln,ebelp,ctype,cdate,cuser,cuser_name,reason,oebelp) values ('$ebeln','$id','X',CURRENT_TIMESTAMP,'" . Auth::user()->id . "','" . Auth::user()->username . "','$reason','$category')");
        return "";
    }

    public static function changeItemStat($column, $value, $valuehlp, $oldvalue, $ebeln, $ebelp)
    {
        DB::update("update pitems set $column = '$value' where ebeln = '$ebeln' and ebelp = '$ebelp'");
        if($column[0]== 'i')
            $type = 'M';
        if($column[0]== 'q')
            $type = 'Q';
        if($column[0]== 'l')
            $type = 'D';
        if($column[0]== 'p')
            $type = 'P';
        $newval = trim($value . " " . $valuehlp);
        DB::insert("insert into pitemchg (ebeln,ebelp,ctype,cdate,cuser,cuser_name,reason,oebelp,oldval,newval) values ('$ebeln','$ebelp','$type',CURRENT_TIMESTAMP,'" . Auth::user()->id . "','" . Auth::user()->username . "','','','$oldvalue','$newval')");
        return "";
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

    static public function isMyReference($id, $ebeln, $orders_table) {
        $lifnr = self::getLIFNROfItem($ebeln, $orders_table);
        $users = DB::select("select distinct id from users where role='Furnizor' and lifnr='$lifnr'");
        if (count($users) == 0) return false;
        foreach ($users as $user) {
            $brands = DB::select("select * from users_sel where id ='$user->id'");
            $xsql = "";
            foreach($brands as $brand) {
                $sel1 = "";
                if (empty(trim($brand->mfrnr))) continue;
                $sel1 = "mfrnr = '$brand->mfrnr'";
                $sel1 = "(". $sel1 . ")";
                if (empty($sql)) $xsql = $sel1;
                else $xsql .= ' or ' . $sel1;
            }
            if (!empty($xsql)) $xsql = " and (" . $xsql . ")";
            $result = DB::select("select * from $orders_table where ebeln='$ebeln' and lifnr='$lifnr' $xsql");
            if(count($result) == 0) continue;
            $result = DB::select("select * from users_ref where id = '$user->id' and refid = '$id'");
            if(count($result) > 0) return true;
        }
        return false;
    }

    static public function getLIFNROfItem($ebeln, $orders_table) {
        return DB::select("select * from $orders_table where ebeln = '$ebeln'")[0]->lifnr;
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