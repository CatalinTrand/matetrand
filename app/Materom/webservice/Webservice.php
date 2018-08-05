<?php

namespace App\Materom\webservice;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Webservice{

    static function show($id){
        return User::where('id', '=', $id)->get();
    }

    static function isTokenValid($token){
        $result = DB::select("select value from Tokens where value = '$token'");
        if(count($result) == 1)
            return true;

        return false;
    }

    static function generateToken(){
        $token = substr(base64_encode(md5( mt_rand() )), 0, 99);
        $user = Auth::user()->username;
        DB::insert("insert into Tokens (value, user) values ('$token','$user')");
        return $token;
    }

    static function deleteOldTokens(){
        
    }

}