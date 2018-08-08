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
    static function insertGlobalData($server, $router)
    {
        DB::delete("delete from global_rfc_config");
        DB::insert("insert into global_rfc_config (rfc_router,rfc_server) values ('$router','$server')");
        return view('roles.roles');
    }

    static function insertRoleData($role, $sysnr,$client,$user,$passwd)
    {
        DB::delete("delete from roles where rfc_role = '$role'");
        DB::insert("insert into roles (rfc_role,rfc_sysnr,rfc_client,rfc_user,rfc_passwd) values ('$role','$sysnr','$client','$user','$passwd')");
        return view('roles.roles');
    }
}