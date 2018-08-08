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
        DB::delete("delete from global_rfc_config");
        DB::insert("insert into global_rfc_config (rfc_router,rfc_server,rfc_sysnr,rfc_client) values ('$rfc_router','$rfc_server','$rfc_sysnr','$rfc_client')");
        return view('roles.roles');
    }

    static function insertRoleData($rfc_role, $rfc_user, $rfc_passwd)
    {
        DB::delete("delete from roles where rfc_role = '$rfc_role'");
        DB::insert("insert into roles (rfc_role,rfc_user,rfc_passwd) values ('$rfc_role','$rfc_user','$rfc_passwd')");
        return view('roles.roles');
    }
}