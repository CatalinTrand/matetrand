<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::delete("delete from pitemchg");
        DB::delete("delete from pitems");
        DB::delete("delete from porders");
        DB::insert("insert into porders (nof, wtime, ctime, vbeln, ebeln, ekgrp, lifnr, lifnr_name)" .
            " values (FALSE, '2018-09-01 18:00:00', '2018-09-02 00:00:00', " .
            "         'S000000001', 'P000000001', 'A01', '0000020786', 'Furnizor 20786')");
        DB::insert("insert into porders (nof, wtime, ctime, vbeln, ebeln, ekgrp, lifnr, lifnr_name)" .
            " values (FALSE, '2018-09-01 18:00:00', '2018-09-02 00:00:00', " .
            "         'S000000001', 'P000000002', 'A01', '0000020786', 'Furnizor 20786')");
        DB::insert("insert into porders (nof, wtime, ctime, vbeln, ebeln, ekgrp, lifnr, lifnr_name)" .
            " values (FALSE, '2018-09-01 18:00:00', '2018-09-02 00:00:00', " .
            "         'S000000002', 'P000000003', 'A02', '0000020786', 'Furnizor 20786')");

        DB::insert("insert into pitems (ebeln, ebelp, posnr, idnlf)" .
            " values ('P000000001', '00010', '00010', 'IDNLF1')");
        DB::insert("insert into pitems (ebeln, ebelp, posnr, idnlf)" .
            " values ('P000000001', '00020', '00030', 'IDNLF2')");
        DB::insert("insert into pitems (ebeln, ebelp, posnr, idnlf)" .
            " values ('P000000002', '00030', '00050', 'IDNLF3')");
        DB::insert("insert into pitems (ebeln, ebelp, posnr, idnlf)" .
            " values ('P000000002', '00040', '00070', 'IDNLF4')");
        DB::insert("insert into pitems (ebeln, ebelp, posnr, idnlf)" .
            " values ('P000000003', '00050', '00090', 'IDNLF5')");
        DB::insert("insert into pitems (ebeln, ebelp, posnr, idnlf)" .
            " values ('P000000003', '00060', '00100', 'IDNLF6')");

        DB::insert("insert into pitemchg (ebeln, ebelp, ctype, cdate, cuser, cuser_name, oldval, newval, reason)" .
            " values ('P000000001', '00010', 'A', '2018-09-01 18:01:00', 'f20786', 'Furnizor 20786', 'oldvalue', 'newvalue', 'Negociere')");
        DB::insert("insert into pitemchg (ebeln, ebelp, ctype, cdate, cuser, cuser_name, oldval, newval, reason)" .
            " values ('P000000001', '00010', 'B', '2018-09-01 18:02:00', 'f20786', 'Furnizor 20786', 'oldvalue2', 'newvalue2', 'Negociere2')");
        DB::insert("insert into pitemchg (ebeln, ebelp, ctype, cdate, cuser, cuser_name, oldval, newval, reason)" .
            " values ('P000000002', '00040', 'C', '2018-09-01 18:03:00', 'Radu', 'Trandafir', 'oldvalue3', 'newvalue3', 'Negociere3')");

    }
}
