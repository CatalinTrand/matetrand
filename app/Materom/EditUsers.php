<?php

namespace App\Materom;
use Illuminate\Support\Facades\DB;

class EditUsers{
    static function editUser($id,$role,$user,$lang,$active,$email){

        if(strcmp($active,"Active") == 0)
            $active = 1;
        else
            $active = 0;

        if($active == 1)
            DB::update("update users set role = '$role', username = '$user', email = '$email', lang = '$lang', active = '$active', deleted_at = null where id = '$id'");
        else
            DB::update("update users set role = '$role', username = '$user', email = '$email', lang = '$lang', active = '$active', deleted_at = NOW() where id = '$id'");

        \Session::put("alert-success", "User data was successfully saved");
        return redirect()->back();

    }
}