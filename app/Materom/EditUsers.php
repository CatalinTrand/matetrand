<?php

namespace App\Materom;
use Illuminate\Support\Facades\DB;

class EditUsers{
    static function editUser($id,$role,$user,$email,$passwd){

        DB::update("update users set role = '$role', username = '$user', email = '$email' where id = '$id'");

        if($passwd){
            $hash = \Illuminate\Support\Facades\Hash::make($passwd);
            DB::update("update users set password = '$hash' where id = '$id'");
        }

        return view('users.editUser');
    }
}