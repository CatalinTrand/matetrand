<?php

namespace App\Materom;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Webservice {

    static public function rfcPing($rfc_router, $rfc_server, $rfc_sysnr,
                                   $rfc_client, $rfc_user, $rfc_password) {
        return (new RFCData())->ping($rfc_router, $rfc_server, $rfc_sysnr,
                                     $rfc_client, $rfc_user, $rfc_password);
    }

    static public function insertFollowupID($userid, $followupid) {
        $result = DB::select("select * from users where id = '" . $followupid . "'");
        if ($result){
            $find = DB::select("select * from users_wf where id = '" . $userid ."' and follow_up_id = '" . $followupid . "'");
            if (count($find) == 0){
                DB::insert("insert into users_wf (id, follow_up_id) values ('" . $userid . "','" . $followupid . "')");
                return "";
            } else return "Follower already defined for this user";
        } else return "This user ID does not exist";
    }

    static public function insertRefferalID($userid, $refferalid) {
        $result = DB::select("select * from users where id = '" . $refferalid . "'");
        if ($result){
            $find = DB::select("select * from users_ref where id = '" . $userid ."' and refferal_id = '" . $refferalid . "'");
            if (count($find) == 0){
                DB::insert("insert into users_ref (id, refferal_id) values ('" . $userid . "','" . $refferalid . "')");
                return "";
            } else return "Refferal already defined for this user";
        } else return "This user ID does not exist";
    }
}