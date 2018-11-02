<?php

namespace App\Materom;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EditUsers {

    static function editUser($id, $role, $user, $token, $lang, $sapuser, $lifnr, $ekgrp, $active, $email){

        $prevusers = DB::select("select * from users where id ='$id'");
        $prevdata = null; if (count($prevusers) != 0) $prevdata = $prevusers[0];

        if(strcmp($active,"Active") == 0)
            $active = 1;
        else
            $active = 0;

        if (ctype_digit($lifnr)) $lifnr = str_pad($lifnr, 10, "0", STR_PAD_LEFT);

        if($active == 1)
            DB::update("update users set username = '$user', api_token = '$token', email = '$email', lang = '$lang', sapuser ='$sapuser',  lifnr = '$lifnr', ekgrp = '$ekgrp', active = '$active', deleted_at = null where id = '$id'");
        else
            DB::update("update users set username = '$user', api_token = '$token', email = '$email', lang = '$lang', sapuser ='$sapuser', active = '$active', deleted_at = NOW() where id = '$id'");

        \Session::put("alert-success", "User data was successfully saved");
        if ($prevdata->role == "Administrator" && $prevdata->api_token != $token && !empty($token)) {
            SAP::rfcUpdateAPIToken($token);
            DB::update("update users set api_token = '' where id <> '$id'");
        }

        return redirect()->route("users");

    }

    static function delSel($id,$sel){
        DB::delete("delete from users_sel where id = '$id' and mfrnr = '$sel'");
    }

    static function refDel($id,$refID){
        DB::delete("delete from users_ref where id = '$id' and refid = '$refID'");
    }

    static function agentDel($id,$agentDEL){
        DB::delete("delete from users_agent where id = '$id' and agent = '$agentDEL'");
    }

    static function getSel($id){
        return DB::select("select * from users_sel where id='$id'");
    }

    static function getRefs($id){
        return DB::select("select * from users_ref where id='$id'");
    }

    static function getAgents($id){
        return DB::select("select * from users_agent where id='$id'");
    }
}