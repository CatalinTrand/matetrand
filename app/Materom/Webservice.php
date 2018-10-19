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

    static function new_simple_chg($ebeln,$ebelp,$ctype,$stage,$cuser,$cuser_name,$oldval,$newval,$reason)
    {
        DB::insert("insert into pitemchg (ebeln,ebelp,cdate,internal,ctype,stage,cuser,cuser_name,oldval,newval,reason) values ('$ebeln','$ebelp',NOW(),'0','$ctype','$stage','$cuser','$cuser_name','$oldval','$newval','$reason')");
    }

    public static function replyToMessage($ebeln, $ebelp, $cdate, $idnlf, $lfdat, $qty, $purch_price, $reason)
    {
        //TODO - cand mai mult de una difera, nu mai merge, individual merg toate

        $oldItem = DB::select("select * from pitems where ebeln = '$ebeln' and ebelp = '$ebelp'")[0];

        $uid = Auth::id();
        $uname = Auth::user()->username;
        $stage = (strtoupper($uname))[0];
        if($stage == 'A') $stage = 'R';


        if(strcmp($oldItem->idnlf,$idnlf) != 0 && strcmp("",$idnlf) != 0){
            $ctype = "M";
            self::new_simple_chg($ebeln,$ebelp,$ctype,$stage,$uid,$uname,$oldItem->idnlf,$idnlf,$reason);
        } else {
            $idnlf = $oldItem->idnlf;
        }

        if(strcmp($oldItem->lfdat,$lfdat ) != 0 && strcmp("",$lfdat) != 0){
            $ctype = "D";
            self::new_simple_chg($ebeln,$ebelp,$ctype,$stage,$uid,$uname,$oldItem->lfdat,$lfdat,$reason);
        } else {
            $lfdat = $oldItem->lfdat;
        }

        if(strcmp($oldItem->qty,$qty) != 0 && strcmp("",$qty) != 0){
            $ctype = "Q";
            self::new_simple_chg($ebeln,$ebelp,$ctype,$stage,$uid,$uname,$oldItem->qty,$qty,$reason);
        } else {
            $qty = $oldItem->qty;
        }

        if(strcmp($oldItem->purch_price,$purch_price) != 0 && strcmp("",$purch_price) != 0){
            $ctype = "P";
            self::new_simple_chg($ebeln,$ebelp,$ctype,$stage,$uid,$uname,$oldItem->purch_price,$purch_price,$reason);
        } else {
            $purch_price = $oldItem->purch_price;
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