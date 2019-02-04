<?php

namespace App\Materom;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EditUsers {

    static function editUser($id, $role, $user, $token, $lang, $sapuser, $lifnr, $ekgrp, $active, $email, $sap_system)
    {

        $prevusers = DB::select("select * from users where id ='$id'");
        $prevdata = null; if (count($prevusers) != 0) $prevdata = $prevusers[0];
        $activated_at = $prevdata->activated_at;
        if ($sap_system == null) $sap_system = "";
        $sap_system = trim($sap_system);
        if ("X".$sap_system == "X200") $sap_system = "";

        if(strcmp($active,"Active") == 0) {
            $active = 1;
            if ($prevdata->active == 0) $activated_at = now();
        } else {
            $active = 0;
        }

        if (ctype_digit($lifnr)) $lifnr = str_pad($lifnr, 10, "0", STR_PAD_LEFT);

        if($active == 1)
            DB::update("update users set username = '$user', api_token = '$token', email = '$email', lang = '$lang', sapuser ='$sapuser',  lifnr = '$lifnr'," .
                               " ekgrp = '$ekgrp', active = '$active', deleted_at = null, activated_at = '$activated_at', sap_system = '$sap_system' where id = '$id'");
        else
            DB::update("update users set username = '$user', api_token = '$token', email = '$email', lang = '$lang', sapuser ='$sapuser', ".
            "active = '$active', deleted_at = NOW(), activated_at = '$activated_at', sap_system = '$sap_system' where id = '$id'");

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