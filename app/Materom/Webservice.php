<?php

namespace App\Materom;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;


class Webservice {

    static public function rfcPing($rfc_router, $rfc_server, $rfc_sysnr,
                                   $rfc_client, $rfc_user, $rfc_password) {
        return (new RFCData($rfc_router, $rfc_server, $rfc_sysnr,
            $rfc_client, $rfc_user, $rfc_password))->ping();
    }

    static public function insertVendorID($userid, $wglif, $mfrnr) {
        $find = DB::select("select * from users_sel where id = '$userid' and wglif = '$wglif' and mfrnr = '$mfrnr'");
        if (count($find) == 0) {
            DB::insert("insert into users_sel (id, wglif, mfrnr) values ('$userid','$wglif','$mfrnr')");
            return "";
        } else return "Vendor already defined for this user";
    }

    static public function changePassword($userid, $newPass) {
        $hash = Hash::make($newPass);
        DB::update("update users set password = '$hash' where id = '$userid'");
        return "OK";
    }

    static public function verifyAPIToken($token){

        $user = DB::select("select * from users where api_token = '$token'");

        if($user)
            return true;

        return false;
    }

    static public function getOrderInfo($order, $type, $item) {
        $str = "";
        if (strcmp($type, 'sales-order') == 0) {
            $links = DB::select("select * from porders where vbeln = '$order' order by ebeln");
            foreach ($links as $link) {
                if (strcmp($str, '') == 0)
                    $str = "$link->ebeln#$link->lifnr#$link->lifnr_name#$link->ekgrp";
                else {
                    $str = "$link->ebeln#$link->lifnr#$link->lifnr_name#$link->ekgrp" . '=' . $str;
                }
            }
        } else if (strcmp($type, 'purch-order') == 0){
            $links = DB::select("select * from pitems where ebeln = '$order' order by ebelp");
            foreach ($links as $link) {
                if (strcmp($str, '') == 0)
                    $str = "$link->ebeln#$link->ebelp#$link->posnr#$link->idnlf";
                else {
                    $str = "$link->ebeln#$link->ebelp#$link->posnr#$link->idnlf" . '=' . $str;
                }
            }
        } else {
            $links = DB::select("select * from pitemchg where ebeln = '$order' and ebelp = '$item' order by cdate desc");
            foreach ($links as $link) {
                if (strcmp($str, '') == 0)
                    $str = "$link->ebeln#$link->ebelp#$link->ctype#$link->oldval#$link->newval#$link->cuser_name";
                else {
                    $str = "$link->ebeln#$link->ebelp#$link->ctype#$link->oldval#$link->newval#$link->cuser_name" . '=' . $str;
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

    static public function sapActivateUser($id) {
        $users = DB::select("select * from users where id ='$id'");
        if (count($users) == 0) return "User does not exist";
        $user = $users[0];
        if ($user->active == 1) return "User is already active";
        DB::update("update users set active = 1 where id ='$id'");
        return "OK";
    }

    static public function sapDeactivateUser($id) {
        $users = DB::select("select * from users where id ='$id'");
        if (count($users) == 0) return "User does not exist";
        $user = $users[0];
        if ($user->active == 0) return "User is not active";
        DB::update("update users set active = 0 where id ='$id'");
        return "OK";
    }

    static public function sapCreateUser($id, $username, $role, $email, $language, $lifnr, $password) {
        $users = DB::select("select * from users where id ='$id'");
        if (count($users) != 0) return "User already exists";
        User::create([
            'id'       => $id,
            'username' => $username,
            'role'     => $role,
            'email'    => $email,
            'lang'     => $language,
            'lifnr'    => $lifnr,
            'password' => Hash::make($password),
            'created_at' => Carbon::now()->getTimestamp()
        ]);
        return "OK";
    }

    static public function sapDeleteUser($id) {
        $users = DB::select("select * from users where id ='$id'");
        if (count($users) == 0) return "User does not exist";
        $user = $users[0];
        DB::delete("delete from users where id ='$id'");
        $users = DB::select("select * from users where id ='$id'");
        if (count($users) != 0) return "User deletion failed";
        return "OK";
    }

    static public function sapGetUserMakers($userid) {
        return "Not yet implemented";
    }

    static public function sapProcessPO($ebeln) {
        $data = SAP::rfcGetPOData($ebeln);
        return $data;
    }

}