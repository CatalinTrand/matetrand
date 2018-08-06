<?php

namespace App\Materom\Webservice;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Webservice {

    static public function show($id){
        return "Username: " . Auth::user()->username;
        return User::where('id', '=', $id)->get();
    }

    static public function isTokenValid($wstoken){
        return true;
        $result = DB::select("select value from tokens where value = '{$wstoken}'");
        if(count($result) == 1)
            return true;

        return false;
    }

    static public function generateToken(){
//      $token = substr(base64_encode(md5( mt_rand() )), 0, 99);
        $wstoken = csrf_token();
        $user = Auth::user()->username;
        DB::insert("insert into tokens (value, user, created_at) values ('{$wstoken}','{$user}'), '{{ now() }}'");
        return $wstoken;
    }

    static public function deleteOldTokens(){
        $daysOfTokenValidity = -3; //must be negative, ex for 3 days must be -3
        DB::delete("delete from tokens where created_at <= DATE_ADD(NOW(), INTERVAL $daysOfTokenValidity day)");
    }

}