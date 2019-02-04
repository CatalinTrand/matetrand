<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 200
        DB::delete("delete from roles");
        DB::insert("insert into roles (rfc_role, rfc_user, rfc_passwd)" .
            " values ('Administrator','trandafir','PusulaC76')");
        DB::insert("insert into roles (rfc_role, rfc_user, rfc_passwd)" .
            " values ('Furnizor','trandafir','PusulaC76')");
        DB::insert("insert into roles (rfc_role, rfc_user, rfc_passwd)" .
            " values ('Referent','trandafir','PusulaC76')");
        DB::insert("insert into roles (rfc_role, rfc_user, rfc_passwd)" .
            " values ('CTV','trandafir','PusulaC76')");
        // 300
        DB::delete("delete from roles_300");
        DB::insert("insert into roles_300 (rfc_role, rfc_user, rfc_passwd)" .
            " values ('Administrator','trandafir','PusulaC77')");
        DB::insert("insert into roles_300 (rfc_role, rfc_user, rfc_passwd)" .
            " values ('Furnizor','trandafir','PusulaC77')");
        DB::insert("insert into roles_300 (rfc_role, rfc_user, rfc_passwd)" .
            " values ('Referent','trandafir','PusulaC77')");
        DB::insert("insert into roles_300 (rfc_role, rfc_user, rfc_passwd)" .
            " values ('CTV','trandafir','PusulaC77')");
    }
}
