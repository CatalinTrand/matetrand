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
        DB::delete("delete from roles");
        DB::insert("insert into roles (rfc_role, rfc_user, rfc_passwd)" .
            " values ('Administrator','cont-test','PusulaC')");
    }
}
