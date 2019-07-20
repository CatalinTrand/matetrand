<?php

namespace App\Materom;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EditUsers {

    static function editUser($id, $role, $user, $token, $lang, $lifnr, $ekgrp, $active, $email, $sap_system,
                             $readonly, $none, $mirror_user1, $ctvadmin, $rgroup)
    {

        $prevusers = DB::select("select * from users where id ='$id'");
        $prevdata = null; if (count($prevusers) != 0) $prevdata = $prevusers[0];
        $activated_at = $prevdata->activated_at;
        if ($sap_system == null) $sap_system = "";
        $sap_system = trim($sap_system);
        if ("X".$sap_system == "X200") $sap_system = "";
        if (strtoupper($readonly) == "ON") $readonly = 1; else $readonly = 0;
        if (strtoupper($none) == "ON") $none = 1; else $none = 0;
        if (strtoupper($ctvadmin) == "ON") $ctvadmin = 1; else $ctvadmin = 0;
        if (Auth::user()->role == 'CTV' && Auth::user()->ctvadmin == 1 && Auth::user()->id == $id) $ctvadmin = 1;
        if ($mirror_user1 == null) $mirror_user1 = ""; else $mirror_user1 = trim($mirror_user1);
        if ($rgroup == null) $rgroup = ""; else $rgroup = trim($rgroup);

        if(strcmp($active,"Active") == 0) {
            $active = 1;
            if ($prevdata->active == 0) $activated_at = now();
        } else {
            $active = 0;
        }

        if (ctype_digit($lifnr)) $lifnr = str_pad($lifnr, 10, "0", STR_PAD_LEFT);

        if($active == 1)
            DB::update("update users set username = '$user', api_token = '$token', email = '$email', lang = '$lang', lifnr = '$lifnr', " .
                              "ekgrp = '$ekgrp', active = '$active', deleted_at = null, activated_at = '$activated_at', sap_system = '$sap_system', readonly = '$readonly', ".
                              "none = '$none', mirror_user1 = '$mirror_user1', ctvadmin = '$ctvadmin', rgroup = '$rgroup' where id = '$id'");
        else
            DB::update("update users set username = '$user', api_token = '$token', email = '$email', lang = '$lang', ".
            "active = '$active', deleted_at = NOW(), activated_at = '$activated_at', sap_system = '$sap_system', readonly = '$readonly', ".
            "none = '$none', mirror_user1 = '$mirror_user1', ctvadmin = '$ctvadmin', rgroup = '$rgroup' where id = '$id'");

        \Session::put("alert-success", "User data was successfully saved");
        if ($prevdata->role == "Administrator" && $prevdata->api_token != $token && !empty($token)) {
            SAP::rfcUpdateAPIToken($token);
            DB::update("update users set api_token = '' where id <> '$id' and sap_system = '" . Auth::user()->sap_system . "'");
        }

        return redirect()->route("users");

    }

    static function delSel($id,$sel){
        DB::delete("delete from ". \App\Materom\System::$table_users_sel ." where id = '$id' and mfrnr = '$sel'");
    }

    static function refDel($id,$refID){
        DB::delete("delete from users_ref where id = '$id' and refid = '$refID'");
    }

    static function agentDel($id,$agentDEL){
        DB::delete("delete from ". \App\Materom\System::$table_users_agent ." where id = '$id' and agent = '$agentDEL'");
    }

    static function kunnrDel($id, $kunnr)
    {
        DB::delete("delete from ". \App\Materom\System::$table_users_cli ." where id = '$id' and kunnr = '$kunnr'");
    }

    static function getSel($id){
        return DB::select("select * from ". \App\Materom\System::$table_users_sel ." where id='$id'");
    }

    static function getRefs($id){
        return DB::select("select * from users_ref where id='$id'");
    }

    static function getAgents($id)
    {
        return DB::select("select * from ". \App\Materom\System::$table_users_agent ." where id='$id'");
    }

    static function getCustomers($id)
    {
        return DB::select("select * from ". \App\Materom\System::$table_users_cli ." where id='$id'");
    }

}