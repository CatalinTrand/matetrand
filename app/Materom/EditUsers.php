<?php

namespace App\Materom;
use Illuminate\Support\Facades\DB;

class EditUsers {
    static function editUser($id,$role,$user,$email){

        DB::update("update users set role = '$role', username = '$user', email = '$email' where id = '$id'");
        \Session::put("alert-success", "User data was successfully saved");
        return redirect()->back();
    }
}