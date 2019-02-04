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
        // 200
        DB::delete("delete from global_rfc_config");
        DB::insert("insert into global_rfc_config (rfc_router, rfc_server, rfc_sysnr, rfc_client)" .
            " values ('','192.168.3.42','00','200')");
        // 300
        DB::delete("delete from global_rfc_config_300");
        DB::insert("insert into global_rfc_config_300 (rfc_router, rfc_server, rfc_sysnr, rfc_client)" .
            " values ('','192.168.3.44','00','300')");

    }
}
