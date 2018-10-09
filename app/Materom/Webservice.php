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
            $mfrnr_name = SAP::rfcGetVendorName($mfrnr);
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

    public static function acceptItemCHG($ebeln, $id, $type)
    {
        DB::update("update pitems set stage = 'A' where ebeln = '$ebeln' and ebelp = '$id'");
        DB::insert("insert into pitemchg (ebeln,ebelp,ctype,cdate,cuser,cuser_name,reason) values ('$ebeln','$id','A',CURRENT_TIMESTAMP,'" . Auth::user()->id . "','" . Auth::user()->username . "','')");
        return "";
    }

    public static function cancelItem($ebeln, $id, $type)
    {
        DB::update("update pitems set stage = 'X' where ebeln = '$ebeln' and ebelp = '$id'");
        DB::insert("insert into pitemchg (ebeln,ebelp,ctype,cdate,cuser,cuser_name,reason) values ('$ebeln','$id','X',CURRENT_TIMESTAMP,'" . Auth::user()->id . "','" . Auth::user()->username . "','')");
        return "";
    }

    public static function getNrOfStatusChildren($id, $status, $type, $history, $vbeln)
    {
        $orders_table = $history == 1 ? "porders" : "porders_arch";
        $items_table = $history == 1 ? "pitems" : "pitems_arch";
        $itemchg_table = $history == 1 ? "pitemchg" : "pitemchg_arch";

        $addedClause = $vbeln === null ? "" : " and vbeln = '$vbeln'";

        if ($status == 0)
            return 1;

        if ($type == 0) {
            //sale-order
            $links = DB::select("select distinct ebeln from ". $items_table ." where vbeln = '$id'");
            foreach ($links as $link) {
                if (self::getNrOfStatusChildren($link->ebeln, $status, 1, $history, $id) > 0)
                    return 1;
            }

            return 0;
        } else {
            $links = DB::select("select * from ". $items_table ." where ebeln = '$id'" . $addedClause);
            foreach ($links as $link) {
                if ($link->stage[0] == 'A' && $status == 2)
                    return 1;
                if ($link->stage[0] == 'X' && $status == 3)
                    return 1;
            }
            return 0;
        }
    }

    static public function getOrderInfo($order, $type, $item, $history, $time_limit,
                                        $filter_vbeln, $filter_ebeln, $filter_matnr, $filter_mtext,
                                        $filter_lifnr, $filter_lifnr_name)
    {
        $orders_table = $history == 1 ? "porders" : "porders_arch";
        $items_table = $history == 1 ? "pitems" : "pitems_arch";
        $itemchg_table = $history == 1 ? "pitemchg" : "pitemchg_arch";

        $str = "";
        $porder = substr($order, 0, 10);
        if (strcmp($type, 'sales-order') == 0) {
            $porders = Data::getSalesOrderFlow($order, $history, $time_limit,
                $filter_vbeln, $filter_ebeln, $filter_matnr, $filter_mtext,
                $filter_lifnr, $filter_lifnr_name);
            foreach ($porders as $porder) {
                $links = DB::select("select * from " . $orders_table . " where ebeln = '$porder->ebeln'");
                foreach ($links as $link) {

                    $gravity = self::getGravity($link, "purch-order", $history);
                    $link->vbeln = $porder->vbeln;
                    $owner = self::getOwner($link, $type, $history);

                    $stage = 0;
                    if (DB::table($itemchg_table)->where('ebeln', $porder->ebeln)->exists())
                        $stage = 10;

                    if (strcmp($str, '') == 0)
                        $str = "$link->ebeln#$link->lifnr#$link->lifnr_name#$link->ekgrp#$order#$link->ekgrp_name#$link->erdat#$link->curr#$link->fxrate#$gravity#$owner#$stage";
                    else {
                        $str = "$link->ebeln#$link->lifnr#$link->lifnr_name#$link->ekgrp#$order#$link->ekgrp_name#$link->erdat#$link->curr#$link->fxrate#$gravity#$owner#$stage" . '=' . $str;
                    }
                }
            }
        } else if (strcmp($type, 'purch-order') == 0) {
            if (is_null($item) || empty($item)) {
                $links = DB::select("select * from " .$items_table . " where ebeln = '$porder' order by ebelp");
                foreach ($links as $link) {
                    $price = $link->purch_price . " " . $link->purch_curr;
                    $quantity = $link->qty . " " . $link->qty_uom;
                    $deldate = $link->lfdat;
                    $owner = self::getOwner($link, $type, $history);
                    $stage = 0;
                    if (DB::table($itemchg_table)->where('ebeln', $porder)->where('ebelp', $link->ebelp)->exists())
                        $stage = 10;
                    if ($link->stage[0] == 'X')
                        $stage = $stage + 2;
                    else if ($link->stage[0] == 'A') {
                        $stage = $stage + 1;
                    }
                    if (strcmp($str, '') == 0)
                        $str = "$link->ebeln#$link->ebelp#$link->posnr#$link->idnlf#$owner#$stage#$quantity#$deldate#$price";
                    else {
                        $str = "$link->ebeln#$link->ebelp#$link->posnr#$link->idnlf#$owner#$stage#$quantity#$deldate#$price" . '=' . $str;
                    }
                }
            } else {
                if ($item == "SALESORDER")
                    $links = DB::select("select * from ". $items_table ." where ebeln = '$porder' and vbeln <> 'REPLENISH' order by ebelp");
                else
                    $links = DB::select("select * from ". $items_table ." where ebeln = '$porder' and vbeln = '$item' order by ebelp");
                foreach ($links as $link) {
                    $owner = self::getOwner($link, $type, $history);
                    $stage = 0;
                    if (DB::table($itemchg_table)->where('ebeln', $porder)->where('ebelp', $link->ebelp)->exists())
                        $stage = 10;
                    if ($link->stage[0] == 'X')
                        $stage = $stage + 2;
                    else if ($link->stage[0] == 'A') {
                        $stage = $stage + 1;
                    }
                    if (strcmp($str, '') == 0)
                        $str = "$link->ebeln#$link->ebelp#$link->posnr#$link->idnlf#$owner#$stage";
                    else {
                        $str = "$link->ebeln#$link->ebelp#$link->posnr#$link->idnlf#$owner#$stage" . '=' . $str;
                    }
                }
            }
        } else {
            $links = DB::select("select * from ".$itemchg_table ." where ebeln = '$porder' and ebelp = '$item' order by cdate desc");
            foreach ($links as $link) {
                $text = "";
                switch ($link->ctype) {
                    case "A":
                        $text = "Acceptare";
                        break;
                    case "T":
                        $text = "Acceptare cu aprobare";
                        break;
                    case "X":
                        $text = "Rejectare";
                        break;
                    case "Q":
                        $text = "Modif. cantitate de la " . $link->oldval . " la " . $link->newval;
                        break;
                    case "P":
                        $text = "Modificare pret de la " . $link->oldval . " la " . $link->newval;
                        break;
                    case "D":
                        $text = "Modif. data livrare de la " . $link->oldval . " la " . $link->newval;
                        break;
                    case "M":
                        $text = "Modif. material de la " . $link->oldval . " la " . $link->newval;
                        break;
                }
                if (strcmp($str, '') == 0)
                    $str = "$link->ebeln#$link->ebelp#$link->cdate#$link->cuser#$link->cuser_name#$text#$link->reason";
                else {
                    $str = "$link->ebeln#$link->ebelp#$link->cdate#$link->cuser#$link->cuser_name#$text#$link->reason" . '=' . $str;
                }
            }
        }
        return $str;
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

    static public function getGravity($order, $type, $history)
    {
        //gravitate : 2 = critical, 1 = warning, 0 = nimic

        $orders_table = $history == 1 ? "porders" : "porders_arch";
        $items_table = $history == 1 ? "pitems" : "pitems_arch";
        $itemchg_table = $history == 1 ? "pitemchg" : "pitemchg_arch";

        if (strcmp($type, "purch-order") == 0) {
            $now = strtotime(date('Y-m-d H:i:s'));
            $wtime = strtotime($order->wtime);
            $ctime = strtotime($order->ctime);
            $interval_wtime = $now - $wtime;
            if ($interval_wtime > 0) {
                $interval_ctime = $now - $ctime;
                if ($interval_ctime > 0) {
                    return 2;
                } else {
                    return 1;
                }
            }
            return 0;
        } else {
            $max = 0;
            $links_items = DB::select("select ebeln from ".$items_table ." where vbeln = '$order->vbeln'");
            $links = array();
            foreach ($links_items as $link_item) {
                $links = array_merge($links, DB::select("select wtime,ctime from ".$orders_table." where ebeln = '$link_item->ebeln'"));
            }
            foreach ($links as $link) {
                $now = strtotime(date('Y-m-d H:i:s'));
                $wtime = strtotime($link->wtime);
                $ctime = strtotime($link->ctime);
                $interval_wtime = $now - $wtime;
                if ($interval_wtime > 0) {
                    $interval_ctime = $now - $ctime;
                    if ($interval_ctime > 0) {
                        $gravitate = 2;
                    } else {
                        $gravitate = 1;
                    }
                } else $gravitate = 0;

                if ($gravitate > $max)
                    $max = $gravitate;

            }
            return $max;
        }
    }

    static public function getOwner($order, $type, $history)
    {
        // 0 = not owned, 1 = yellow arrow, 2 = blue arrow
        $orders_table = $history == 1 ? "porders" : "porders_arch";
        $items_table = $history == 1 ? "pitems" : "pitems_arch";
        $itemchg_table = $history == 1 ? "pitemchg" : "pitemchg_arch";

        if (strcmp($type, "purch-order") == 0) {
            if (strcmp(Auth::user()->role, "Administrator") == 0)
                return 2;
            else {
                $owner = 0;
                $items = DB::select("select stage from ". $items_table ." where ebeln='$order->ebeln'");
                foreach ($items as $item) {
                    $ownerT = 0;
                    if (Auth::user()->role[0] == $item->stage)
                        return 2;
                    if (Auth::user()->role[0] == 'R' && $item->stage == 'F')
                        $ownerT = 1;

                    if ($ownerT > $owner)
                        $owner = $ownerT;
                }
                return $owner;
            }
        } else {
            if (strcmp(Auth::user()->role, "Administrator") == 0)
                return 2;
            else {
                $owner = 0;
                $items = DB::select("select stage from ". $items_table ." where vbeln='$order->vbeln'");
                foreach ($items as $item) {
                    $ownerT = 0;
                    if (Auth::user()->role[0] == $item->stage)
                        return 2;
                    if (Auth::user()->role[0] == 'R' && $item->stage == 'F')
                        if(isMyReference(Auth::user()->id, $order->ebeln, $orders_table))
                            $ownerT = 3;
                        else
                            $ownerT = 1;



                    if ($ownerT > $owner)
                        $owner = $ownerT;
                }
                return $owner;
            }
        }
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