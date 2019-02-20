<?php
/**
 * Created by PhpStorm.
 * User: Catalin
 * Date: 08/08/2018
 * Time: 17:27
 */

namespace App\Materom;


use Illuminate\Support\Facades\DB;

class Roles
{
    static function insertGlobalData($rfc_router, $rfc_server, $rfc_sysnr,$rfc_client)
    {
        DB::delete("delete from ". System::$table_global_rfc_config);
        DB::insert("insert into ". System::$table_global_rfc_config ." (rfc_router,rfc_server,rfc_sysnr,rfc_client) values ('$rfc_router','$rfc_server','$rfc_sysnr','$rfc_client')");
        \Session::put("alert-success", "Global RFC data was successfully saved");
        return redirect()->back();
    }

    static function insertRoleData($rfc_role, $rfc_user, $rfc_passwd, $user1)
    {
        if ($user1 == null) $user1 = "";
        $user1 = trim($user1);
        DB::delete("delete from ". System::$table_roles ." where rfc_role = '$rfc_role'");
        DB::insert("insert into ". System::$table_roles ." (rfc_role,rfc_user,rfc_passwd,user1) values ('$rfc_role','$rfc_user','$rfc_passwd','$user1')");
        \Session::put("alert-success", "Role RFC data was successfully saved");
        return redirect()->back();
    }
}