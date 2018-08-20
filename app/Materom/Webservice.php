<?php

namespace App\Materom;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Webservice {

    static public function rfcPing($rfc_router, $rfc_server, $rfc_sysnr,
                                   $rfc_client, $rfc_user, $rfc_password) {
        return (new RFCData())->ping($rfc_router, $rfc_server, $rfc_sysnr,
                                     $rfc_client, $rfc_user, $rfc_password);
    }

    static public function insertVendorID($userid, $wglif, $mfrnr) {
        $find = DB::select("select * from users_sel where id = '$userid' and wglif = '$wglif' and mfrnr = '$mfrnr'");
        if (count($find) == 0){
            DB::insert("insert into users_sel (id, wglif, mfrnr) values ('$userid','$wglif','$mfrnr')");
            return "";
        } else return "Vendor already defined for this user";
    }

    static public function changePassword($userid, $newPass){
        $hash = Hash::make($newPass);
        DB::update("update users set password = '$hash' where id = '$userid'");
        return "";
    }

    static public function getOrderInfo($order,$type){
        $str = "";
        if(strcmp($type,'sales-order') == 0){
            $links = DB::select("select * from porders where vbeln = '$order'");
            foreach ($links as $link){
                if(strcmp($str,'') == 0)
                    $str = $link->ebeln;
                else {
                    $str = $link->ebeln . '=' . $str;
                }
            }
        } else {
            $links = DB::select("select * from pitems where ebeln = '$order'");
            foreach ($links as $link){
                if(strcmp($str,'') == 0)
                    $str = $link->ebelp;
                else {
                    $str = $link->ebelp . "=" . $str;
                }
            }
        }
        return $str;
    }
}