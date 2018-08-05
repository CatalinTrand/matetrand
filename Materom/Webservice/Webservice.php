<?php

namespace Materom\Webservice;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Webservice{

    static function show($id){
        return User::where('id', '=', $id)->get();
    }

    static function isTokenValid($wstoken){
        $result = DB::select("select value from tokens where value = '$wstoken'");
        if(count($result) == 1)
            return true;

        return false;
    }

    static function generateToken(){
        $token = substr(base64_encode(md5( mt_rand() )), 0, 99);
        $user = Auth::user()->username;
        DB::insert("insert into tokens (value, user) values ('$token','$user')");
        return $token;
    }

    static function deleteOldTokens(){
        $daysOfTokenValidity = -3; //must be negative, ex for 3 days must be -3
        DB::delete("delete from tokens where created_at <= DATE_ADD(NOW(), INTERVAL $daysOfTokenValidity day)");
    }

}