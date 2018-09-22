<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GlobalRFCDataTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::delete("delete from global_rfc_config");
//        DB::insert("insert into global_rfc_config (rfc_router, rfc_server, rfc_sysnr, rfc_client)" .
//            " values ('/H/91.239.173.2/H/','192.168.3.42','00','200')");
        DB::insert("insert into global_rfc_config (rfc_router, rfc_server, rfc_sysnr, rfc_client)" .
            " values ('','192.168.3.42','00','200')");

    }
}
